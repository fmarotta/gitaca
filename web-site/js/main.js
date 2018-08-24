$(document).ready(function(){
	$.get("/server_info.php", function(data, status){
		if (status !== "success")
			console.log("Error while getting server info!");

		var _SERVER = JSON.parse(data);
		console.log(_SERVER);
		var headString;
		if (_SERVER["HTTP_SESSION"] !== undefined && _SERVER["HTTP_SESSION"] !== "") {
			var httpUsername = _SERVER["HTTP_SESSION"].match(/user=(.*)&/)[1];
			if (httpUsername !== "public")
				headString = '<li>Hello '+httpUsername+', welcome back.</li>'+
					'<li><a href="/web-site/new_project.php">New Project</a></li>'+
					'<li><a href="/web-site/edit_project.php">Edit Project</a></li>'+
					'<li><a href="/logout_handler.php">Logout</a></li>';
			else
				headString = '<li>Who are you? <a href="/web-site/login.php">Login</a></li>'+
				'<li>Do we know each other? <a href="/web-site/register.php">Register</a></li>';
		}else if (window.location.pathname != "/public")
			// FIXME at the moment, if the user types "fmarotta" he reaches the
			// page, then is redirected to the login page. However, the user
			// should not be able to get here in the first place
			//window.location = "/web-site/login.php";

		$("#auth_status").html(headString);
    });
});
