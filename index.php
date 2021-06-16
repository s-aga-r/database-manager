<?php
	session_start();
	if(!isset($_SESSION["query_counter"]))
		$_SESSION["query_counter"] = 0;
	if(isset($_POST["login"]))
	{
		$connection = mysqli_connect($_POST["host"], $_POST["user_name"], $_POST["password"]);
		if(!$connection)
			echo "<b id=red>Log In failed!</b>";
		else
		{
			$_SESSION["db_host"] = $_POST["host"];
			$_SESSION["db_user"] = $_POST["user_name"];
			if(empty($_POST["password"]))
				$_SESSION["db_password"] = "";
			else
				$_SESSION["db_password"] = $_POST["password"];
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
	<center>
		<?php
			if(isset($_SESSION["db_user"]) && isset($_SESSION["db_password"]))
			{
				?>
					<details id="details">
						<summary id="summary">LogIn Details</summary>
						<table border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td id="green">Host : </td>
								<td id="red"><?php echo $_SESSION["db_host"];?></td>
							</tr>
							<tr>
								<td id="green">User : </td>
								<td id="red"><?php echo $_SESSION["db_user"];?></td>
							</tr>
							<tr>
								<td id="green">Password : </td>
								<td id="red"><?php echo $_SESSION["db_password"];?></td>
							</tr>
						</table>
					</details>

					<div id="main_div">

						<h2>DATABASE MANAGER</h2>

						<a href="databases.php" target="_self" title="Create and Drop Database">
							<img src="images/database.png" class="img">
						</a>

						<a href="sql.php" target="_self"  title="See executed SQL Queries">
							<img src="images/sql.png" class="img">
						</a>

						<a href="tables.php" target="_self" title="Open, Delete, Edit and Create Table">
							<img src="images/table.png" class="img">
						</a>

						<a href="logout.php" target="_self" title="Log Out">
							<img src="images/logout.png" class="img">
						</a>

					</div>
				<?php
			}
			else
			{
				?>
					<form name="log_in" method="POST" accept="?">
						<fieldset id="fieldset">
							<legend id="legend">Log In</legend>
							<table border="0" cellpadding="5" cellspacing="5">
								<tr>
									<td>Host : </td>
									<td>
										<select name="host" id="select">
											<option value="localhost">localhost</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>User Name : </td>
									<td><input type="text" name="user_name" required="required" id="text"></td>
								</tr>
								<tr>
									<td>Password : </td>
									<td><input type="password" name="password" id="text"></td>
								</tr>
							</table>
							<input type="submit" name="login" value="Log In" id="button">
							<input type="reset" name="reset" value="Clear" id="button">
						</fieldset>
					</form>
				<?php
			}
		?>
	</center>
</body>
</html>