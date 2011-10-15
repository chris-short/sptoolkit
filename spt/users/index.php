<?php
/**
 * file:		index.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	User management
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
			$_SESSION['came_from']='users';
			
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
		<title>spt - users</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_users.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">

			<!--sidebar-->
			<div id="sidebar">
				<img src="../images/logo.png" alt="logo" alt="logo.png" />
				<ul>
				<?php
					//lists links dependent upon what modules are installed
					include '../spt_config/mysql_config.php';
					$results=mysql_query('SELECT * FROM modules WHERE enabled=1 ORDER BY name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
					while($row=mysql_fetch_assoc($results))
						{
							echo "<li><a href=\"../".$row['directory_name']."/\">".$row['name']."</a></li>\n";
						}
				?>
				</ul>
				<br />
				<div class="logout">
					<ul>
						<li><a href="../login/logout.php">logout</a></li>
					</ul>
				</div>
			</div>

			<!--content-->
			<div id="content">
				<?php 
					//check for alerts or notifications
					if(isset($_SESSION['user_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['user_alert_message']."</h2>";

							//clear out the error message
							unset($_SESSION['user_alert_message']);
						}

					//list currently logged in username and provide edit link
					echo '<a href="user_form.php?user=1">'.$_SESSION['username'].'</a> ';
					
					//check to see if user is admin give them additional options
					if($_SESSION['admin']==1)
						{
							echo " | <a href=\"user_form.php?user=2\"> + user</a>";
						}
				?>
				<div>
					<table class="spt_table">
						<tr>
							<td><h3>Name</h3></td>
							<td><h3>Email</h3></td>
							<td><h3>Admin</h3></td>
							<td><h3>Disabled</h3></td>
							<td colspan="2"><h3>Actions</h3></td>
						</tr>
						<?php
							//connect to database						
							include '../spt_config/mysql_config.php';
							
							//retrieve all user data to populate the user table
							$r=mysql_query('SELECT id, fname, lname, username, admin, disabled, admin FROM users') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							while($ra=mysql_fetch_assoc($r)){
								echo "<tr>\n<td>";
								echo $ra['fname']." ".$ra['lname'];
								echo "</td>\n<td>";
								echo $ra['username'];
								echo "</td>\n<td>";
								
								//determine if the specific user is an admin
								if($ra['admin']==1)
									{
										$admin_status='yes';
									}
								else
									{
										$admin_status='no';
									}
								echo $admin_status;
								echo "</td>\n<td>";
								
								//determine if the user is disabled
								if($ra['disabled']==1)
									{
										$disabled='yes';
									}
								else
									{
										$disabled='no';
									}
								echo $disabled;
								echo "</td>\n";
								
								//if the user is an admin and this record is not their own allow them to edit the user
								if($_SESSION['admin']==1 && $_SESSION['username']!=$ra['username'])
									{
										echo "<td><a href=\"user_form.php?u=";
										echo $ra['username'];
										echo "\">edit</a>";
										echo "</td>\n";
									}
								else
									{
										echo "<td></td>";
									}

								//if the user is an admin and this record is not their own allow them to delete the user
								if($_SESSION['admin']==1 && $_SESSION['username']!=$ra['username'])
									{
										echo "<td><a href=\"delete_user.php?u=";
										echo $ra['username'];
										echo "\">delete</a>";
										echo "</td>\n";
									}
								else
									{
										echo "<td></td>";
									}

								echo "</tr>\n";
							}
						?> 
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
