<?php
	include("include_files/home_button.html");
	session_start();
	if(!isset($_SESSION["db_user"]) && !isset($_SESSION["db_password"]))
		die("<b id=red>Please login!</b>");
?>
<?php
	if(!empty($_SESSION["all_query"]))
	{
		$query = $_SESSION["all_query"];
		$i=1;
		foreach ($query as $temp) 
		{
			echo "<pre><b>SQL Query $i : </b>".$temp.";               </pre>";
			$i++;
		}
	}
	else
		echo "<b>No SQL Query!</b>";
?>
