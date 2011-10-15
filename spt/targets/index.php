<?php
/**
 * file:		index.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Target management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:		GNU/GPL, see license.htm.
 * 
 * This file is part of the Simple Phishing Toolkit (spt).
 * 
 * spt is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, under version 3 of the License.
 *
 * spt is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with spt.  If not, see <http://www.gnu.org/licenses/>.
**/

	//start session
	session_start();
	
	//check for authenticated session
	if($_SESSION['authenticated']!=1)
		{
			//for potential return
			$_SESSION['came_from']='targets';
			
			//set error message and send them back to login
			$_SESSION['login_error_message']="login first";
			header('location:../');
			exit;
		}
	
	//check for session hijacking
	elseif($_SESSION['ip']!=md5($_SESSION['salt'].$_SERVER['REMOTE_ADDR'].$_SESSION['salt']))
		{
			//set error message and send them back to login
			$_SESSION['login_error_message']="your ip address must have changed, please authenticate again";
			header('location:../');
			exit;
		}
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - targets</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_targets.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--sidebar-->
			<div id="sidebar">
				<img src="../images/logo.png" alt="logo.png"/>
				<ul>
				<?php
					//lists links dependent upon what modules are installed
					include '../spt_config/mysql_config.php';
					$results=mysql_query('SELECT * FROM modules WHERE enabled=1 ORDER BY name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
					while($row=mysql_fetch_assoc($results)){
						echo "<li><a href=\"../";
						echo $row['directory_name'];
						echo "/\">";
						echo $row['name'];
						echo "</a></li>\n";
						}
				?>
				</ul>
				<br />
				<div class="logout"><ul><li><a href="../login/logout.php">logout</a></li></ul></div>
			</div>

			<!--content-->
			<div id="content">
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['targets_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['targets_alert_message']."</h2>";
							
							//unset the seession
							unset ($_SESSION['targets_alert_message']);				
						}
					if(isset($_SESSION['bad_row_stats']))
						{
							//count how many stats there are
							$count = count($_SESSION['bad_row_stats']);
							
							//start the list
							echo "<ul>";
							
							//echo all bad row stats
							while($count > 0)
								{
									echo "<li><h2>".$_SESSION['bad_row_stats'][($count-1)]."</h2></li>";
									$count--;
								}
							
							//end the list
							echo "</ul>";
							
							//unset bad row stat session
							unset ($_SESSION['bad_row_stats']);
						}
				?>
				
				<form action="target_upload_single.php" method="post" enctype="multipart/form-data">
					<table class="spt_table">
						<tr>
							<td><h3>+ Add One</h3></td>
							<td></td>
						</tr>
						<tr>
							<td>Name</td>
							<td><input type="text" name="name" /></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><input type="text" name="email" /></td>
						</tr>
						<tr>
							<td>Group</td>
							<td>
								<select name="group_name">
									<option value="Select an Existing Group...">Select an Existing Group...</option>
									<?php
										//connect to database
										include "../spt_config/mysql_config.php";
										
										//pull in current group names
										$r = mysql_query("SELECT DISTINCT group_name FROM targets ORDER BY group_name") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
										while ($ra = mysql_fetch_assoc($r))
											{
												echo "<option value=\"".$ra['group_name']."\">".$ra['group_name']."</option>";
											}
									?>
								</select>
								, or add 
								<input type="text" name="group_name_new" />
								as a new group
							</td>
						</tr>
						<tr>	
							<td>
								<input type="submit" />
							</td>
							<td></td>
						</tr>
					</table>
				</form>
				<table class="spt_table">
					<tr>
						<td><h3>+ Add Bunches</h3></td>
					</tr>
					<tr>
						<td>			
							<form action="target_upload_batch.php" method="post" enctype="multipart/form-data">
								<input type="file"  name="file" />
								<input type="submit" value="Upload" />
							</form>
						</td>
					</tr>
					<tr>				
						<td><span>*import only csv files</span><br /></td>
					</tr>
					<tr>
						<td><span>*columns should include <strong>name</strong>, <strong>email</strong>, <strong>group</strong> only and in that order</span></td>
					</tr>
				</table>
				<table class="spt_table">
					<tr>
						<td><h3>Groups</h3></td>						
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Name</td>
						<td># of People</td>
						<td>Delete</td>
					</tr>
					<?php
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//query for a list of groups ordered alphabetically
						$r = mysql_query("SELECT DISTINCT group_name FROM targets ORDER BY group_name") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo "<tr>";
								echo "<td><a href=\"group_list.php?g=".$ra['group_name']."\">".$ra['group_name']."</a></td>";
								$group_name = $ra['group_name'];
								$r1 = mysql_query("SELECT COUNT(group_name) FROM targets WHERE group_name = '$group_name'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra1 = mysql_fetch_assoc($r1))
									{
										echo "<td>".$ra1['COUNT(group_name)']."</td>";
									}
								echo "<td><a href=\"group_delete.php?g=".$ra['group_name']."\">X</a></td>";
								echo "</tr>";
							}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
