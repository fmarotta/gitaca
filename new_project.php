<?php
include_once "includes/functions.php";
include_once "includes/project_handler.php";
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
		<input type="hidden" name="username" id="username" value="<?php preg_match('/user=(.*)&/', $_SERVER["HTTP_SESSION"], $matches); echo $matches[1];?>" /><br>
            Project Name: <input type="text" name="project" id="project" /><br>
            Description: <input type="text" name="description" id="description" /><br>
            Share with: <input type="text" name="share" id="share" /><br>
            <input type="submit" 
                   value="Create" 
                   onclick="return prjformhash(this.form,
								   this.form.username,
                                   this.form.project,
                                   this.form.description,
                                   this.form.share);" /> 
        </form>
        <p>Return to the <a href="/index.php">home page</a>.</p>
        <p>TODO: <a href="/index.php">abort</a>.</p>
    </body>
</html>
