<?php
	include("include_files/home_button.html");
	session_start();
	if(!isset($_SESSION["db_user"]) && !isset($_SESSION["db_password"]))
		die("<b id=red>Please login!</b>");
?>
<?php
	if(isset($_POST["drop"]))
	{
		if($_POST["database"]=="NULL")
			echo "<b id=red>Please select a database!</b>";
		else
		{
			$db_name = $_POST["database"];
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"]);
			if(!$connection)
				die("<b id=red>Failed to delete Database!</b>");
			else
			{
				$query = "DROP DATABASE $db_name";
				if(mysqli_query($connection, $query))
				{
					echo "<b id=green>",$db_name , " Deleted!</b>";
					$c = $_SESSION["query_counter"]+1;
					$_SESSION["all_query"][$c] = $query;
					$_SESSION["query_counter"]++;
				}
				else
					echo "<b id=red>Failed to delete Database!</b>";
			}
		}
	}
	elseif(isset($_POST["create"]))
	{
		if($_POST["new_db"]==NULL)
			echo "<b id=red>Please enter a name to create database!</b>";
		else
		{
			$db_name = str_replace(" ", "_", $_POST["new_db"]);
			if($db_name != $_POST["new_db"])
				echo "<p id=red>White space don't allowed that's why creating database with name <b>".$db_name."</b></p><br/>";
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"]);
			if(!$connection)
				die("<b id=red>Failed to create Database!</b>");
			else
			{
				$query = "CREATE DATABASE $db_name";
				if(mysqli_query($connection, $query))
				{
					echo "<b id=green>",$db_name , " Created!</b>";
					$c = $_SESSION["query_counter"]+1;
					$_SESSION["all_query"][$c] = $query;
					$_SESSION["query_counter"]++;
				}
				else
					echo "<b id=red>Failed to create Database!</b>";
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>DATABASE MANAGER</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<form name="" method="POST" action="?">
		 
		<?php
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"]);
			if(!$connection)
				die("<b>Connection failed!</b>");
			else
			{
				$result = mysqli_query($connection, "SHOW DATABASES");
				if(!$result)
					echo "Error! Showing database";
				else
				{
					?>
						<table border="0" cellpadding="0" cellspacing="5">
							<tr>
								<td>Select Database : </td>
								<td>
									<select name="database" id="select">
										<option value="NULL"></option>
										<?php
											$i = 0;
											while($temp = mysqli_fetch_assoc($result))
											{
												echo '<option value='.$temp["Database"].'>'.$temp["Database"].'</option>';	
												$_SESSION["Database"][$i] = $temp["Database"];
												$i++;
											}
											$_SESSION["count_db"] = $i;
										?> 
									</select>
								</td>
								<td><input type="submit" name="drop" value="Drop" id="button"></td>
								<?php echo "<td>(Total: ",$_SESSION["count_db"],")</td>"; ?>
							</tr>
							<tr>
								<td>Create New Database : </td>
								<td><input type="text" name="new_db" id="text"></td>
								<td><input type="submit" name="create" value="Create" id="button"></td>
							</tr>
						</table>
					<?php
				}	
			}
		?>

	</form>
</body>
</html>