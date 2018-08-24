<?php
	if (isset($_SERVER["HTTP_SESSION"]) && preg_match("/user=(.*)&/", $_SERVER["HTTP_SESSION"], $matches)) {
		$http_username = $matches[1];

		$head_string = '<li>Hello '.$http_username.', welcome back.</li>'.
			'<li><a href="/'.$http_username.'">Go to your Projects</a></li>'.
			'<li><a href="/public">Go to the Public Projects</a></li>'.
			'<li><a href="/logout_handler.php">Logout</a></li>';
	}else {
		$head_string = '<li>Who are you? <a href="/web-site/login.php">Login</a></li>'.
		'<li>Do we know each other? <a href="/web-site/register.php">Register</a></li>'.
		'<li><a href="/public">Go to the Public Projects</a></li>';
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gitaca</title>
        <link rel="stylesheet" href="/web-site/styles/main.css" />
    </head>
    <body>
    	<ul id="auth_status" class="nav navbar-nav">
		<?php echo $head_string; ?>
    	</ul>
    </body>
</html>
