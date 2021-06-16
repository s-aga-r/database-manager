<?php
	include("include_files/home_button.html");
	session_start();
	if(!isset($_SESSION["db_user"]) && !isset($_SESSION["db_password"]))
		die("<b id=red>Please login!</b>");
	else
	{
		session_destroy();
		echo "<b id=green>Exited!</b>";
	}
?>
