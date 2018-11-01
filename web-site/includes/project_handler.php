<?php
include_once "db_connect.php";
include_once "psl-config.php";

$error_msg = "";

// FIXME: this program should not know gitacaroot! only server.js needs 
// to know it.

// TODO: divide the handlers for new project, edit project and remove 
// project

if (isset($_POST['username'], $_POST['project'])) {
	// Sanitize and validate the data passed in
	$project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	$roers = filter_input(INPUT_POST, 'roers', FILTER_SANITIZE_STRING);
	$rwers = filter_input(INPUT_POST, 'rwers', FILTER_SANITIZE_STRING);

	// Check if project already exists.
	$gitacaroot = "/srv/git/";
	if (file_exists($gitacaroot.$username."/".$project))
		$error_msg .= '<p class="error">A project with that name already exists.</p>';
	//$error_msg .= $gitacaroot.$username."/".$project;

	if (empty($error_msg)) {
		// Ask gitaca server to create user dir and update server conf
		$server_name = 'http://fmarotta.dynu.net';
		$server_port = 4007;
		$url = "$server_name:$server_port/newprj";
		$fields = array("prjpath" => $gitacaroot.$username."/".$project,
						"description" => $description,
						"roers" => $roers,
						"rwers" => $rwers);
		$post_string = http_build_query($fields, '', '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		if ($response == "OK")
			header("Location: /$username");
		else
			header('Location: /web-site/error.php?err='.$response);

		exit();
	}
}

if (isset($_POST['username'], $_POST['editproject'])) {
	// Sanitize and validate the data passed in
	$project = filter_input(INPUT_POST, 'editproject', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$description = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
	$roers = filter_input(INPUT_POST, 'roers', FILTER_SANITIZE_STRING);
	$rwers = filter_input(INPUT_POST, 'rwers', FILTER_SANITIZE_STRING);

	// Check if project exists.
	$gitacaroot = "/srv/git/";
	if (!file_exists($gitacaroot.$username."/".$project))
		$error_msg .= '<p class="error">Error: this project does not exist.</p>';

	if (empty($error_msg)) {
		// Ask gitaca server to create user dir and update server conf
		$server_name = 'http://fmarotta.dynu.net';
		$server_port = 4007;
		$url = "$server_name:$server_port/editprj";
		$fields = array("prjpath" => $gitacaroot.$username."/".$project,
						"prjaction" => $gitacaroot.$username."/".$project,
						"roers" => $roers,
						"rwers" => $rwers);
		$post_string = http_build_query($fields, '', '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		if ($response == "OK")
			header("Location: /$username");
		else
			header('Location: /web-site/error.php?err='.$response);

		exit();
	}
}

// TODO: remove only for me or for rwers as well?
if (isset($_POST['username'], $_POST['rmproject'])) {
	// Sanitize and validate the data passed in
	$project = filter_input(INPUT_POST, 'rmproject', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);

	// Check if project exists.
	$gitacaroot = "/srv/git/";
	if (!file_exists($gitacaroot.$username."/".$project))
		$error_msg .= '<p class="error">Error: this project does not exist.</p>';

	if (empty($error_msg)) {
		// Ask gitaca server to remove the project
		$server_name = 'http://fmarotta.dynu.net';
		$server_port = 4007;
		$url = "$server_name:$server_port/rmprj";
		$fields = array("prjpath" => $gitacaroot.$username."/".$project,
						"prjaction" => $gitacaroot.$username."/".$project,
						"roers" => $roers,
						"rwers" => $rwers);
		$post_string = http_build_query($fields, '', '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		if ($response == "OK")
			header("Location: /$username");
		else
			header('Location: /web-site/error.php?err='.$response);

		exit();
	}
}
