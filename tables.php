<?php
	include("include_files/home_button.html");
	session_start();
	if(!isset($_SESSION["db_user"]) && !isset($_SESSION["db_password"]))
		die("<b id=red>Please login!</b>");
?>
<?php
	if(isset($_POST["open"]))
	{
		if($_POST["database"]=="NULL")
			echo "<b id=red>Please select a database!</b>";
		else
		{
			$_SESSION["db_name"] = $_POST["database"];
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
			if(!$connection)
				die("<b id=red>Failed to show tables!</b>");
			else
			{
				$query = "SHOW TABLES";
				$result = mysqli_query($connection, $query);
				if(!$result)
					echo "<b id=red>Failed to show tables!</b>";
				else
				{
					$c = $_SESSION["query_counter"]+1;
					$_SESSION["all_query"][$c] = $query;
					$_SESSION["query_counter"]++;
					?>
						<form name="" method="POST" action=?>
							<table border="0" cellpadding="0" cellspacing="5">
								<tr>
									<td>Existing Tables : </td>
									<td>
										<select name="table" id="select">
											<option value="NULL"></option>
											<?php
												$i = 0;
												$tmp_name = "Tables_in_".$_SESSION["db_name"];
												while($temp = mysqli_fetch_assoc($result))
												{
													echo '<option value='.$temp[$tmp_name].'>'.$temp[$tmp_name].'</option>';	
													$_SESSION["Table"][$i] = $temp[$tmp_name];
													$i++;
												}	
												$_SESSION["count_table"] = $i;
											?> 
										</select>
									</td>
									<?php
										if($_SESSION["count_table"] != 0)
										{
											?>
												<td>Offset : </td>
												<td><input type="number" name="offset"></td>
												<td>Limit : </td>
												<td><input type="number" name="limit"></td>
											<?php
										}
									?>
									<td>
										<input type="submit" name="open_tb" value="Open" id="button">
										<input type="submit" name="drop_tb" value="Drop" id="button">
									</td>
									<?php
										echo "<td>(Total: ",$_SESSION["count_table"],")</td>";
									?>
								</tr>
								<tr>
									<td>Create new table : </td>
									<td><input type="text" name="new_table" id="text"></td>
									<td>Number of columns : </td>
									<td><input type="number" name="table_cols" id="text"></td>
									<td><input type="submit" name="create" value="Create" id="button"></td>
								</tr>
							</table>
						</form>
					<?php
				}
			}
		}
	}
	elseif(isset($_POST["open_tb"]))
	{
		if($_POST["table"]=="NULL")
			echo "<b id=red>Please select a table!</b>";
		else
		{
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
			if(!$connection)
				die("<b id=red>Failed to open table!</b>");
			else
			{
				$tb_name = $_POST["table"];
				$_SESSION["tb_name"] = $_POST["table"];
				$query_get_cols = "DESCRIBE ".$tb_name; 
					$result_get_cols = mysqli_query($connection, $query_get_cols);
					$query_show_table = NULL;
				if($_POST["offset"] || $_POST["limit"])
				{
					if($_POST["offset"] && $_POST["limit"])
					{
						$offset = $_POST["offset"];
						$limit   = $_POST["limit"];
						$query_show_table = "SELECT * FROM $tb_name LIMIT $offset, $limit";
					}
					elseif($_POST["offset"])
					{
						$offset = $_POST["offset"];
						$query_show_table = "SELECT * FROM $tb_name LIMIT 99999 OFFSET $offset";
					}
					elseif($_POST["limit"])
					{
						$limit   = $_POST["limit"];
						$query_show_table = "SELECT * FROM $tb_name LIMIT $limit";
					}
				}
				else
					$query_show_table = "SELECT * FROM ".$tb_name;
				$result_show_table = mysqli_query($connection, $query_show_table);		
				if(!$result_show_table || !$result_get_cols)
					echo "<b id=red>Failed to open table!</b>";
				else 
				{
					$c = $_SESSION["query_counter"]+1;
					$_SESSION["all_query"][$c] = $query_show_table;
					$_SESSION["query_counter"]++;
					$i=1;
					$key_find = "False";
					$_SESSION["pri_key"] = NULL;
					?>
						<form method="POST">
							<input type="submit" name="insert_old" value="Insert" id="button">
						</form>
						<table border="1" cellpadding="5" cellspacing="5">
							<tr>
								<?php
									while($col = mysqli_fetch_assoc($result_get_cols))
									{
										echo "<th id=green>".$col["Field"]."</th>";
										if($col["Key"] == "PRI" && $key_find == "False")
										{
											$_SESSION["pri_key"] = $col["Field"];
											$key_find = "True";
										}
										$attribute[$i] = $col["Field"];
										$i++;
									}
									$_SESSION["total_col"] = $i;
									if($_SESSION["pri_key"] != NULL)
										echo "<th id=red>ACTION</th>";
								?>
							</tr>
							<?php
								$count_rows = 0;
								while($row = mysqli_fetch_assoc($result_show_table))
								{
									echo "<tr>";
										for($j=1;$j<$i;$j++)
										{
											$temp = $attribute[$j];
											echo "<td>".$row[$temp]."</td>";	
											$_SESSION["attribute"][$j] = $temp;
										}
										if($_SESSION["pri_key"] != NULL)
										{
											$unique = $row[$_SESSION["pri_key"]];
											?>
												<form method="POST">
													<input type="hidden" name="id" value=<?php echo $unique; ?>>
													<td>
														<input type="submit" name="edit" value="Edit" id="button">
														<input type="submit" name="delete" value="Delete" id="button">
													</td>
												</form>
											<?php
										}
									echo "</tr>";
									$count_rows++;
								}
								$c = $i-1;
								echo "<br/>Total Row's : $count_rows<br/>Total Column's : $c";
							?>
						</table>
					<?php
				}				
			}
		}
	}
	elseif(isset($_POST["drop_tb"]))
	{
		if($_POST["table"]=="NULL")
			echo "<b id=red>Please select a table!</b>";
		else
		{
			$tb_name = $_POST["table"];
			$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
			if(!$connection)
				die("<b id=red>Failed to delete table!</b>");
			else
			{
				$query = "DROP TABLE $tb_name";
				if(mysqli_query($connection, $query))
				{
					echo "<b id=green>",$tb_name , " Deleted!<b>";
					$c = $_SESSION["query_counter"]+1;
					$_SESSION["all_query"][$c] = $query;
					$_SESSION["query_counter"]++;

				}
				else
					echo "<b id=red>Failed to delete Table!</b>";
			}
		}
	}
	elseif(isset($_POST["create"]))
	{
		if(empty($_POST["new_table"]) && empty($_POST["table_cols"]))
			echo "<b id=red>Please provide a name and number of columns to create a table!</b>";
		elseif(empty($_POST["new_table"]) || empty($_POST["table_cols"]) || $_POST["table_cols"] == 0)
		{
			if(empty($_POST["new_table"]))
				echo "<b id=red>Please provide name to create a new table!</b><br/>";
			if(empty($_POST["table_cols"]))
				echo "<b id=red>Please provide number of columns!</b><br/>";
			if($_POST["table_cols"] == "0")
				echo "<b id=red>Please provide a value more than 0</b><br/>";
		}
		else
		{
			$new_table = str_replace(" ", "_", $_POST["new_table"]);
			$count_tb = $_SESSION["count_table"];
			if(isset($_SESSION["Table"]))
				$all_tb = $_SESSION["Table"];
			$available = "False";
			for($i=0; $i<$count_tb; $i++)
			{
				if($new_table == $all_tb[$i])
				{
					echo "<b id=red>".$new_table." already exist in current database!</b>";
					$available = "True";
					break;
				}
			}	
			if($available == "False")
			{
				$new_table = str_replace(" ", "_", $_POST["new_table"]);
				if($new_table != $_POST["new_table"])
					echo "<b id=red>White space don't allowed that's why creating table with name ".$new_table."</b>";
				$_SESSION["temp_tb_name"] = $new_table;
				$tb_cols = $_POST["table_cols"];
				$_SESSION["temp_tb_cols"] = $_POST["table_cols"];
				$array = ["Name", "Type", "Length", "Primary Key", "Auto Increment", "Not Null", "Default"];
				echo "<form method=POST action=?>";
					echo "<table border=1 cellspacing= cellspacing=>";
						echo "<tr>";
							for($i=0; $i<sizeof($array); $i++)
								echo "<th>".$array[$i]."</th>";
						echo "<tr>";
						for($i=1; $i<=$tb_cols; $i++)
						{
							?>
								<tr>
									<td><input type="text" name=<?php echo "name".$i; ?> required="required" id="text"></td>
									<td>
										<select name=<?php echo "type".$i; ?> id="select">
											<option value="INT">INT</option>
											<option value="VARCHAR">VARCHAR</option>
											<option value="TEXT">TEXT</option>
										</select>
									</td>
									<td><input type="number" name=<?php echo "length".$i; ?> required="required" id="text"></td>
									<td><input type="radio" name="pri_key" required="required" value="<?php echo $i ;?>" id="radio"></td>
									<td><input type="radio" name="auto_inc" value="<?php echo $i ;?>" id="radio"></td>
									<td><input type="checkbox" name=<?php echo "not_null".$i; ?> value=<?php echo "not_null".$i; ?> id="check"></td>
									<td>
										<select name=<?php echo "default".$i; ?> id="select">
											<option value="NULL">NULL</option>
											<option value="0">0</option>
										</select>
									</td>
								</tr>
							<?php
						}
						echo "<tr><td colspan=7><center>
							<input type=submit value=Submit name=add_cols id=button>
							<input type=Reset value=Clear id=button>
						</center></td></tr>";
					echo "</table>";
				echo "</form>";
			}
		}
	}
	elseif(isset($_POST["add_cols"]))
	{
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to create table!</b>");
		else
		{
			$tb_name = $_SESSION["temp_tb_name"];
			$tb_cols = $_SESSION["temp_tb_cols"];
			$tb_pri_key = $_POST["pri_key"];
			for($i=1; $i<=$tb_cols; $i++)
			{
				$temp1 = "name".$i;
				$temp2 = "type".$i;
				$temp3 = "length".$i;
				$temp4 = "not_null".$i;
				$temp5 = "default".$i;
				$attr_names[$i]    = $_POST[$temp1];
				$attr_type[$i]     = $_POST[$temp2];
				$attr_length[$i]   = $_POST[$temp3];
				$attr_default[$i]  = $_POST[$temp5];

				if(isset($_POST[$temp4]))
					$attr_not_null[$i] = "YES";
				else
					$attr_not_null[$i] = "NO";

				if(isset($_POST["auto_inc"]))
					$tb_auto_inc = "YES";
				else
					$tb_auto_inc = "NO";

			}
			$db_name = $_SESSION["db_name"];
			$query_p1 = "CREATE TABLE ".$tb_name." ( ";
			$query_p2 = NULL;
			$primary_key = NULL;
			for($i=1; $i<=$tb_cols; $i++)
			{
				if($tb_pri_key == $i)
						$primary_key = $attr_names[$i];
				if($primary_key == $attr_names[$i] && $tb_auto_inc == "YES")
				{
					$temp = $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]) NOT NULL AUTO_INCREMENT, ";  
					$query_p2 = $query_p2.$temp;
				}
				elseif($attr_not_null[$i] == "YES")
				{
					$temp = $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]) NOT NULL, ";  
					$query_p2 = $query_p2.$temp;
				}
				elseif($attr_not_null[$i] == "NO")
				{
					$temp = $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]), ";  
					$query_p2 = $query_p2.$temp;
				}
				elseif($attr_default[$i] == "NULL")
				{
					$temp =  $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]) NULL, "; 
					$query_p2 = $query_p2.$temp; 
				}
				elseif($attr_default[$i] == "0")
				{
					$temp =  $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]) DEFAULT '0', ";  
					$query_p2 = $query_p2.$temp;
				}
				else
				{
					$temp = $attr_names[$i]." ".$attr_type[$i]."($attr_length[$i]), ";  
					$query_p2 = $query_p2.$temp;
				}
			}
			$temp =  "PRIMARY KEY($primary_key))";  
			$query_p2 = $query_p2.$temp;
			$query = $query_p1.$query_p2;
			if(mysqli_query($connection, $query))
			{
				echo "<b  id=green>".$tb_name." table created!</b>";
				$c = $_SESSION["query_counter"]+1;
				$_SESSION["all_query"][$c] = $query;
				$_SESSION["query_counter"]++;
				echo "<form method=POST>";
					echo "<input type=submit name=insert_new value=Insert id=button>";
				echo "</form>";
			}
			else
				echo "<b id=red>failed to create table ".$tb_name."</b>";
		}
	}
	elseif(isset($_POST["insert_old"]) || isset($_POST["insert_new"]))
	{
		if(isset($_POST["insert_old"]))
		{
			$tb_name = $_SESSION["tb_name"];
			$_SESSION["insert_tb_name"] = $tb_name;
		}
		elseif(isset($_POST["insert_new"]))
		{
			$tb_name = $_SESSION["temp_tb_name"];
			$_SESSION["insert_tb_name"] = $tb_name;
		}
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to insert!</b>");
		else
		{
			$query_get_cols = "DESCRIBE ".$tb_name; 
			$result_get_cols = mysqli_query($connection, $query_get_cols);
			if(!$result_get_cols)
				echo "<b id=red>Failed to insert!</b>";
			else
			{
				$i=0;
				echo "<i id=red><b>Note : </b> Blank field considerd as NULL value...</i>";
				echo "<form method=POST>";
					echo "<table border=0>";
						while($col = mysqli_fetch_assoc($result_get_cols))
						{
							$temp = $col["Field"];
							echo "<tr>";
								echo "<td><b>".$temp." : </b></td>";
								if($col["Key"] == "PRI")
								{
									if($col["Key"] == "PRI" && $col["Extra"] == "auto_increment")
									{
										if(preg_match("/varchar/", $col["Type"]))
											echo "<td><input type=text name=$temp placeholder=' Auto Increment' disabled=disabled id=text></td>";
										elseif(preg_match("/int/", $col["Type"]))
											echo "<td><input type=number name=$temp placeholder=' Auto Increment' disabled=disabled id=text></td>";
										elseif(preg_match("/text/", $col["Type"]))
											echo "<td><textarea name=$temp placeholder=' Auto Increment' disabled=disabled id=text_area></textarea></td>";
										else
											echo "<td><input type=text name=$temp placeholder=' Auto Increment' disabled=disabled required=required id=text></td>";
										$i++;
									}
									elseif($col["Key"] == "PRI")
									{
										if(preg_match("/varchar/", $col["Type"]))
											echo "<td><input type=text name=$temp required=required id=text></td>";
										elseif(preg_match("/int/", $col["Type"]))
											echo "<td><input type=number name=$temp required=required id=text></td>";
										elseif(preg_match("/text/", $col["Type"]))
											echo "<td><textarea name=$temp required=required id=text_area></textarea></td>";
										else
											echo "<td><input type=text name=$temp required=required id=text></td>";
										$i++;
									}
								}
								elseif($col["Key"] != "PRI")
								{
									if(preg_match("/varchar/", $col["Type"]))
										echo "<td><input type=text name=$temp id=text></td>";
									elseif(preg_match("/int/", $col["Type"]))
										echo "<td><input type=number name=$temp id=text></td>";
									elseif(preg_match("/text/", $col["Type"]))
										echo "<td><textarea name=$temp id=text_area></textarea></td>";
									else
										echo "<td><input type=text name=$temp id=text></td>";
									$i++;
								}
							echo "<tr>";
							$_SESSION["all_attr_insert"][$i] = $temp;
							$_SESSION["all_attr_type"][$i] = $col["Type"];
						}
					echo "</table>";
					echo "<input type=submit name=insert value=Insert id=button>
						  <input type=reset name=reset value=Clear id=button>";
				echo "</form>";
				$_SESSION["no_of_attr"] = $i;
			}
		}
	}
	elseif(isset($_POST["insert"]))
	{
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to insert!</b>");
		else
		{
			$j = $_SESSION["no_of_attr"];
			$query_p1 = "INSERT INTO ".$_SESSION["insert_tb_name"]." (";
			$query_p2 = NULL;
			$query_p3 = NULL;
			for($i=1;$i<=$j;$i++)
			{
				$temp = $_SESSION["all_attr_insert"][$i];
				if($i==$j)
					$query_p2 = $query_p2.$temp." ) VALUES (";
				else
					$query_p2 = $query_p2.$temp.", ";
			}
			$j = $_SESSION["no_of_attr"];
			$val = $_SESSION["all_attr_insert"];
			for($i=1;$i<=$j;$i++)
			{
				$temp = $_SESSION["all_attr_insert"][$i];
				if(empty($_POST[$temp]))
					$val[$i] = "NULL";
				else
					$val[$i] = $_POST[$temp];
			}
			$j = $_SESSION["no_of_attr"];
			$array_temp = $_SESSION["all_attr_type"];
			for($i=1;$i<=$j;$i++)
			{
				$temp = $val[$i];
				if($i==$j)
				{
					if(preg_match("/varchar/", $array_temp[$i]) || preg_match("/text/", $array_temp[$i]))
						$query_p3 = $query_p3."'".$temp."' )";
					elseif(preg_match("/int/", $array_temp[$i]))
						$query_p3 = $query_p3.$temp." )";
					else
						$query_p3 = $query_p3."'".$temp."' )";
				}
				else
				{
					if(preg_match("/varchar/", $array_temp[$i]) || preg_match("/text/", $array_temp[$i]))
						$query_p3 = $query_p3."'".$temp."' ,";
					elseif(preg_match("/int/", $array_temp[$i]))
						$query_p3 = $query_p3.$temp." ,";
					else
						$query_p3 = $query_p3."'".$temp."' ,";
				}
			}
			$query = $query_p1.$query_p2.$query_p3;
			if(mysqli_query($connection, $query))
			{
				echo "<b id=green>Data inserted!</b>";
				$c = $_SESSION["query_counter"]+1;
				$_SESSION["all_query"][$c] = $query;
				$_SESSION["query_counter"]++;
			}
			else
				echo "<b id=red>Failed to insert!</b>";
		}
	}
	elseif(isset($_POST["edit"]))
	{
		$id = $_POST["id"];
		$_SESSION["id"] = $_POST["id"];
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to edit record!</b>");
		else
		{
			$query = "SELECT * FROM ".$_SESSION["tb_name"]." WHERE ".$_SESSION["pri_key"]." = $id";
			$result = mysqli_query($connection, $query);
			if(!$result)
				die("<b id=red>Failed to edit record!</b>");
			else
			{
				$c = $_SESSION["query_counter"]+1;
				$_SESSION["all_query"][$c] = $query;
				$_SESSION["query_counter"]++;
				$row = mysqli_fetch_assoc($result);
				?>
					<form name="" method="POST" action="?">
						<table border="0" cellpadding="5" cellspacing="5">
							<?php
								for($i=1; $i<$_SESSION["total_col"]; $i++)
								{
									echo "<tr>";
										$temp = $_SESSION["attribute"][$i];
										echo "<td>".$temp." : </td>";
										echo "<td>";
											if($temp == $_SESSION["pri_key"])
												echo "<input type=text name=$temp placeholder=$id disabled=disabled id=text>"; 
											else
											{
												$data = $row[$temp];
												echo "<input type=text name=$temp value=$data id=text>";
											}
										echo "</td>";
									echo "</tr>";
								}
							?>
							<tr><td><input type="submit" name="update" value="Update" id="button"></td></tr>
						</table>
					</form>
				<?php
			}	
		}
	}
	elseif(isset($_POST["delete"]))
	{
		$id = $_POST["id"];
		$_SESSION["id"] = $_POST["id"];
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to delete record!</b>");
		else
		{
			$query = "DELETE FROM ".$_SESSION["tb_name"]." WHERE ".$_SESSION["pri_key"]."=".$_SESSION["id"];
			if(mysqli_query($connection, $query))
			{
				echo "<b id=green>Record deleted!</b><br/>";
				$c = $_SESSION["query_counter"]+1;
				$_SESSION["all_query"][$c] = $query;
				$_SESSION["query_counter"]++;
			}
			else
				echo "<b id=red>Failed to delete record!</b>";
		}
	}
	elseif(isset($_POST["update"]))
	{
		$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"], $_SESSION["db_name"]);
		if(!$connection)
			die("<b id=red>Failed to edit record!</b>");
		else
		{
			$k=1;
			$j=$_SESSION["total_col"]-1;
			$query_p1 = "UPDATE ".$_SESSION["tb_name"]." SET ";
			$query_p2 = NULL;
			$query_p3 = "WHERE ".$_SESSION["pri_key"]."=".$_SESSION["id"]; 
			for($i=1; $i<$_SESSION["total_col"]; $i++)
			{
				$temp = $_SESSION["attribute"][$i];
				if($temp != $_SESSION["pri_key"])
				{
					if($i==$j)
					{
						$assign[$k] = $_POST[$temp];
						$query_p2 = $query_p2."$temp='$assign[$k]' ";
						$k++;
					}
					else
					{
						$assign[$k] = $_POST[$temp];
						$query_p2 = $query_p2."$temp='$assign[$k]',";
						$k++;
					}
				}
			}
			$query =  $query_p1.$query_p2.$query_p3;
			if(mysqli_query($connection, $query))
			{
				echo "<b id=green>Record updated!</b><br/>";
				$c = $_SESSION["query_counter"]+1;
				$_SESSION["all_query"][$c] = $query;
				$_SESSION["query_counter"]++;
			}
			else
				echo "<b id=red>Failed to edit record!</b>";
		}
	}
	else
	{
		?>
			<!DOCTYPE html>
			<html>
			<head>
				<title>DATABASE MANAGER</title>
			</head>
			<body>
				<form name="" method="POST" action="?">
					<?php
						$connection = mysqli_connect($_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_password"]);
						if(!$connection)
							die("<b id=red>Connection failed!</b>");
						else
						{
							$result = mysqli_query($connection, "SHOW DATABASES");
							if(!$result)
								echo "<b id=red>Error! Showing database</b>";
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
											<td><input type="submit" name="open" value="Open" id="button"></td>
											<?php echo "<td>(Total: ",$_SESSION["count_db"],")</td>"; ?>
										</tr>
									</table>
								<?php
							}	
						}
					?>
				</form>
			</body>
			</html>
		<?php
	}
?>