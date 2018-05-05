$(document).ready(function(){
	$.get("../server_info.php", function(data, status){
		if (status !== "success")
			throw "Error while getting server info!";

		var _SERVER = JSON.parse(data);
		var headString;
		if (typeof _SERVER["HTTP_SESSION"] !== undefined && _SERVER["HTTP_SESSION"] !== "") {
			var httpUsername = _SERVER["HTTP_SESSION"].match(/user=(.*)&/)[1];
			if (httpUsername !== "public")
				headString = 'Hello '+httpUsername+', welcome back. <a href="/new_project.php">New Project</a> <a href="/edit_project.php">Edit Project</a> <a href="/logout_handler.php">Logout</a>';
			else
				headString = 'Who are you? <a href="/login.php">Login</a>'+
				'Do we know each other? <a href="/register.php">Register</a>';
		}else
			alert("You shouldn't be here.");

		$("#username").html(headString);
    });
});
