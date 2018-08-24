<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gitaca - Login Form</title>
        <link rel="stylesheet" href="/web-site/styles/main.css" />
    </head>
    <body>
		<?php
		if (!isset($_SERVER["HTTP_REFERER"]))
			echo "<h3>You have to log in first. Note: you will be redirected to your home page.</h3>";
		?>
        <form method="POST" action="/login_handler.php">
            Username: <input type="text" name="httpd_username" /><br>
            Password: <input type="password" name="httpd_password" /><br>
            <input type="submit" name="login" value="Login" />
        </form>
        <p>Return to the <a href="/index.php">home page</a>.</p>
    </body>
</html>
