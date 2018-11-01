#!/usr/bin/node

// Server that listens for requests from gitaca web client and runs the
// appropriate commands

// Modules
const bodyParser = require('/usr/lib/node_modules/body-parser')
const child_process = require('child_process')
const express = require('/usr/lib/node_modules/express')
const fs = require('fs')
const http = require('http')
const ip = require('/usr/lib/node_modules/ip')
const Pty = require('/usr/lib/node_modules/node-pty')
const winston = require('/home/fmarotta/gitaca/node_modules/winston')

// Config
// TODO: config file
const baseDir = '/home/fmarotta/gitaca/'
const gitDir = '/srv/git/'
const logFile = baseDir+'log/server.log'
const userConfigFile = '/srv/http/gitaca/config/gitaca_user.conf'
const repoConfigFile = '/srv/http/gitaca/config/gitaca_repo.conf'
const serverIp = ip.address()
const serverPort = 4007

// Logging
const logger = winston.createLogger({
    exitOnError: true,
    transports: [
        new winston.transports.File({
            filename: logFile,
            level: 'info',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            )
        }),
        new winston.transports.Console({
            level: 'debug',
            format: winston.format.simple()
        })
    ]
})

// Initializations
const app = express()
const server = http.createServer(app)
server.listen(serverPort, function() {
	logger.debug("Server listening on port "+serverPort)
})

// Allow cross-origin requests
app.use(function(req, res, next) {
	res.header("Access-Control-Allow-Origin", "*");
	res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
	next();
})

// Allow to serve the contents of this directory
app.use(express.static(__dirname))

// Allow post requests
app.use(bodyParser.urlencoded({extended: false}))
app.use(bodyParser.json())

// Router {{{
app.post('/register', function(req, res) {
	var username = req.body.username
	var userdir = gitDir+username+'/'
	logger.debug("New post request from"+username)

	fs.mkdirSync(userdir)
	fs.symlinkSync(gitDir+'public/gitaca', userdir+'gitaca')
	
	var setenvif = 'SetEnvIf Request_URI "^/'+username+'" GIT_PROJECT_ROOT='+gitDir+username+"\n";
	var aliasmatch = 'AliasMatch ^/'+username+"(/|/gitweb\.cgi)?$ /usr/share/gitweb/gitweb.cgi\n";
	var locationmatch = '<LocationMatch "^/'+username+'/?$">'+"\n"+
    '	AuthType form'+"\n"+
    '	AuthName "Authentication for Gitaca wrapper"'+"\n"+
    '	AuthFormProvider socache dbd'+"\n"+
    '	AuthnCacheProvideFor dbd'+"\n"+
    '	AuthnCacheContext gitaca'+"\n"+
    '	AuthDBDUserPWQuery "SELECT password FROM members WHERE username=%s"'+"\n"+
    '	AuthFormLoginRequiredLocation "https://git.fmarotta.dynu.net/web-site/login.php"'+"\n"+
    '	Session On'+"\n"+
    '	SessionEnv On'+"\n"+
    '	SessionCookieName session path=/'+"\n"+
    '	SessionCryptoPassphrase "my-pw"'+"\n"+
    '	Require valid-user'+"\n"+
	'</LocationMatch>'+"\n\n";

	fs.appendFileSync(userConfigFile, setenvif)
	fs.appendFileSync(userConfigFile, aliasmatch)
	fs.appendFileSync(userConfigFile, locationmatch)

	// NOTE: you should edit sudo configuration file to allow the user 
	// `git' to run this command
	child_process.execSync('sudo /usr/bin/systemctl reload httpd.service')

	res.send('OK')
})

app.post('/newprj', function(req, res) {
	var prjpath = req.body.prjpath
	var description = req.body.description
	var roers = req.body.roers
	var rwers = req.body.rwers
	var user = prjpath.split('/')[0]
	var prjname = prjpath.split('/')[1]

	// Create the repository
	var output = child_process.execSync('/usr/bin/git init --bare --shared=group '+prjpath);
	logger.debug(output)

	if (output != 'Initialized empty shared Git repository in '+prjpath+"/\n") {
		res.send(output);
		return
	}

	// Add the description and the owner
	try {
		fs.appendFileSync(prjpath+'/description', description)
	} catch (err) {
		res.send(err)
		return
	}
	try {
		var listItem = prjname + ' ' + user
		fs.appendFileSync(gitDir+user+'/projects_list', listItem)
	} catch (err) {
		res.send(err)
		return
	}

	// Edit the configuration files
	try {
		var repoConfig = "";
		fs.appendFileSync(repoConfigFile, repoConfig);
	} catch (err) {
		res.send(err)
		return
	}

	res.send('OK');
	return 
})
// }}}

// functions {{{
function getEnv() {
    // Adapted from the source code of the module pty.js
    var env = {}

    Object.keys(process.env).forEach(function (key) {
      env[key] = process.env[key]
    })

    // Make sure we didn't start our
    // server from inside tmux.
    delete env.TMUX
    delete env.TMUX_PANE

    // Make sure we didn't start
    // our server from inside screen.
    // http://web.mit.edu/gnu/doc/html/screen_20.html
    delete env.STY
    delete env.WINDOW

    // Delete some variables that
    // might confuse our terminal.
    delete env.WINDOWID
    delete env.TERMCAP
    delete env.COLUMNS
    delete env.LINES

    // Set $TERM to screen. This disables multiplexers
    // that have login hooks, such as byobu.
    env.TERM = "screen"

    // Set the home directory.
    env.HOME = '/home/fmarotta/'

    return env
}

function getDirContents(path) {
	return new Promise((resolve, reject) => {
		fs.readdir(path, function(err, files) {
			if (err)
				reject(Error(err))

			var i
			var stats
			var contents = []

			for (i = 0; i < files.length; i++) {
				contents[i] = new Object

				stats = fs.lstatSync(path+files[i])
				if (stats.isDirectory()) {
					contents[i].contentType = 'dir'
					contents[i].contentName = files[i]
				}else if (stats.isFile()) {
					contents[i].contentType = 'file'
					contents[i].contentName = files[i]
				}
			}

			resolve(contents)
		})
	})
}

function serverExec(command) {
	if (pty !== null) {
		try {
			// SIGTERM is not noticed by pty.on('exit'); that is, the
			// resulting signal is 0.
			//process.kill(pty.pid, 'SIGSTOP')
			process.kill(pty.pid, 'SIGTERM')
			pty = null
		}catch (e) {
			logger.info(e)
			// TODO
		}
	}

	pty = Pty.spawn('/bin/bash', command, {
		name: 'dumb',
		cols: 256,
		rows: 16,
		cwd: process.cwd(),
		env: getEnv()
	})
}
// }}}
