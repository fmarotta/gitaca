<?php
include_once "includes/functions.php";
include_once "includes/project_handler.php";

if (isset($_SERVER["HTTP_SESSION"])
	&& preg_match('/user=(.*)&/', $_SERVER["HTTP_SESSION"], $matches)) {

	$username = $matches[1];
}else {
	$error_msg = "Critical error: you should not be here! Please, go away.";
}


$path = "/srv/git/$username";
$repo = listdir_by_date($path);

/* Sort the files according to ctime */
function listdir_by_date($path)
{
    $dir = opendir($path);
    $list = array();
    $filelist = array();
    while($file = readdir($dir))
    {
        if ($file != '.' && $file != '..')
        {
            // add the filename, to be sure not to overwrite an array
            // key
            $ctime = filectime($path . $file) . ',' . $file;
            $list[$ctime] = $file;
		}
    }
    closedir($dir);
    krsort($list);

    foreach ($list as $item)
        $filelist[] = $item;
    return $filelist;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gitaca - Edit Project</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/JavaScript" src="js/forms.js"></script>
		<script type="text/JavaScript" src="js/editprj.js"></script>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <h1>Edit a Project</h1>
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>
		<p>Click on a project name to select it.</p>
		<ul id="prjlinks">
		<?php
		foreach($repo as $r)
    		echo '<li><button type="button" class="prjlink">'.$r.'</button></li>';
		?>
		</ul>

		<h3>NOTE: this function is yet to be implemented</h3>

        <p>Return to the <a href="/index.php">home page</a>.</p>
    </body>
</html>
