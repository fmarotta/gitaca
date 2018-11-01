<?php
include_once "includes/functions.php";
include_once "includes/project_handler.php";

if (isset($_SERVER["HTTP_SESSION"])
	&& preg_match('/user=(.*)&/', $_SERVER["HTTP_SESSION"], $matches)) {

	$username = $matches[1];
}else {
	$error_msg = "Critical error: you should not be here! Please, go away.";
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gitaca - New Project</title>
        <script type="text/JavaScript" src="js/forms.js"></script>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <!-- Registration form to be output if the POST variables are not
        set or if the registration script caused an error. -->
        <h1>Create new project</h1>
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>
        <form method="post" name="new_project_form" action="<?php echo esc_url($_SERVER["PHP_SELF"]); ?>">
			<input type="hidden" name="username" id="username" value="<?php echo $username; ?>" /><br>
            Project Name: <input type="text" name="project" id="project" /><br>
            Description: <input type="text" name="description" id="description" /><br><br>
			Owner: <?php echo $username; ?><br>
            Readonliers: <input type="text" name="share" id="roers" /><br>
            Readwriters: <input type="text" name="share" id="rwers" /><br>
            <input type="submit" 
                   value="Create" 
                   onclick="return prjformhash(this.form,
								   this.form.username,
                                   this.form.project,
                                   this.form.description,
                                   this.form.roers,
                                   this.form.rwers);" /> 
        </form>
        <p>Return to the <a href="/index.php">home page</a>.</p>
        <p>TODO: <a href="/index.php">abort</a>.</p>
    </body>
</html>
