<?php
include_once "db_connect.php";
include_once "psl-config.php";

$error_msg = "";

if (isset($_POST['username'], $_POST['project'])) {
    // Sanitize and validate the data passed in
    $project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    //$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    //$share = filter_input(INPUT_POST, 'share', FILTER_SANITIZE_STRING);

	$project = 'marnetto';
	$username = 'fmarotta';

    //check if project already exists.
	$gitacaroot = "/srv/git/";
	if (file_exists($gitacaroot.$username."/".$project))
        $error_msg .= '<p class="error">A project with that name already exists.</p>';
	$error_msg = $gitacaroot.$username."/".$project;
    
    if (!empty($error_msg)) {
		// Create git repository
		exec("/usr/bin/git init --bare --shared=group $gitacaroot$username/$project");
		
		# TODO
		# if (isset($_POST['description'], $_POST['share']))
		
		# TODO
		# if (isset($_POST['description'], $_POST['share']))


        header("Location: /$username");
        exit();
    }
}
