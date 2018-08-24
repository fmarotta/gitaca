<?php
include_once "db_connect.php";
include_once "psl-config.php";

$error_msg = "";

if (isset($_POST['username'], $_POST['project'])) {
    // Sanitize and validate the data passed in
    $project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

	//check if project already exists.
	$gitacaroot = "/srv/git/";
	if (file_exists($gitacaroot.$username."/".$project))
        $error_msg .= '<p class="error">A project with that name already exists.</p>';
	//$error_msg .= $gitacaroot.$username."/".$project;
    
    if (empty($error_msg)) {
		// Ask gitaca server to create user dir and update server conf
		$server_name = 'http://fmarotta.dynu.net';
		$server_port = 4007;
		$url = "$server_name:$server_port/newprj";
		$fields = array("prjpath" => $gitacaroot.$username."/".$project);
		$post_string = http_build_query($fields, '', '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		# TODO if (isset($_POST['description'], $_POST['roers and rwers']))

		if ($response == "OK")
			header("Location: /$username");
		else
			header('Location: /web-site/error.php?err='.$response);
		
        exit();
	}
}

if (isset($_POST['username'], $_POST['newprj'])) {
    // Sanitize and validate the data passed in
    $project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

	//check if project exists.
	$gitacaroot = "/srv/git/";
	if (!file_exists($gitacaroot.$username."/".$project))
        $error_msg .= '<p class="error">Error: this project does not exist.</p>';
	$error_msg .= $gitacaroot.$username."/".$project;
    
    if (empty($error_msg)) {
		// Ask gitaca server to create user dir and update server conf
		$server_name = 'http://fmarotta.dynu.net';
		$server_port = 4007;
		$url = "$server_name:$server_port/editprj";
		$fields = array("prjaction" => $gitacaroot.$username."/".$project);
		$post_string = http_build_query($fields, '', '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		# TODO if (isset($_POST['description'], $_POST['share']))

		if ($response == "OK")
			header("Location: /$username");
		else
			header('Location: /web-site/error.php?err='.$response);
		
        exit();
	}
}
