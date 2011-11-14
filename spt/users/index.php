<?php
/**
 * file:		index.php
 * version:		6.0
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
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_users.css" type="text/css" />
	</head>
	<body>
		<div id="edit_user">
			<div>
				<?php
					//connect to database
					include "../spt_config/mysql_config.php";
					
					//set parameter to variable
					$current_user=$_SESSION['username'];

					//create the sql statement to pull data about the current user
					$r=mysql_query("SELECT fname, lname, username FROM users WHERE username = '$current_user'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
					$ra=mysql_fetch_assoc($r);

					//generate form for the user to modify their data
					echo 
						"
							<form id=\"edit_current_user\" method=\"post\" action=\"edit_user.php\">\n
								<table id=\"edit_current_user\">\n
									<tr>\n
										<td>first name</td>\n
										<td><input id=\"fname\" type=\"text\" name=\"fname\" value=\"".$ra['fname']."\" /></td>\n
										<td><a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>You can edit the details of your own user account here.  Your password must be 8-15 characters long.</span></a></td>\n	
									</tr>\n
									<tr>\n
										<td>last name</td>\n
										<td><input id=\"lname\" type=\"text\" name=\"lname\" value=\"".$ra['lname']."\" /></td>\n
									</tr>\n
									<tr>\n
										<td>username</td>\n
										<td><input id=\"username\" type=\"text\" name=\"username\" value=\"".$ra['username']."\"/></td>\n
									</tr>\n
									<tr>\n
										<td>password</td>\n
										<td><input id=\"password\" type=\"password\" name=\"password\" autocomplete=\"off\"/></td>\n
									</tr>\n
									<tr>\n
										<td></td>
										<td>
											<a href=\"\"><img src=\"../images/x.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type=\"image\" src=\"../images/thumbs-up.png\" alt=\"edit\" />
										</td>\n
									</tr>\n
								</table>\n
							</form>\n
						";
				?>
			</div>
		</div>
		<div id="add_user">
			<div>
				<form id="add_user_table" method="post" action="add_user.php">
					<table id="add_user_table">
						<tr>
							<td>first name</td>
							<td><input id="fname" type="text" name="fname" /></td>
							<td>
								<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Enter the first name, last name, valid email address and initial password (8-15 characters in length) for the new spt user.  You can also select to have the user's new account be disabled initially (useful for pre-staging accounts) and whether or not the new user should be an admin in the spt.</span></a>
							</td>
						</tr>
						<tr>
							<td>last name</td>
							<td><input id="lname" type="text" name="lname" /></td>
						</tr>
						<tr>
							<td>email</td>
							<td><input id="username" type="text" name="username" /></td>
						</tr>
						<tr>
							<td>password</td>
							<td><input id="password" type="password" name="password" autocomplete="off" /></td>
						</tr>
						<tr>
							<td>admin</td>
							<td><input id="admin" type="checkbox" name="a" /></td>
						</tr>
						<tr>
							<td>disabled</td>
							<td><input id="disabled" type="checkbox" name="disabled" /></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<a href=""><img src="../images/x.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="image" src="../images/plus.png" alt="add" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<div id="edit_other_user">
			<div>
				<?php
					//set current user varaible with username from username session variable
					$current_user=$_SESSION['username'];

					//determine if user parameter is set
					if(isset($_REQUEST['u']))
						{
							//pull parameter and set to variable
							$u=$_REQUEST['u'];

							//validate that the email address entered is an actual email address
							if(!filter_var($u, FILTER_VALIDATE_EMAIL))
								{
									//set error message if not a valid email address
									$_SESSION['user_alert_message'] = "please attempt to edit only valid email addresses";
									header('location:../users/#alert');
									exit;
								}						

							//connect to database
							include "../spt_config/mysql_config.php";

							//verify the entry is an actual email address in the database
							$r=mysql_query("SELECT * FROM users") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							while($ra=mysql_fetch_assoc($r))
								{
									if($ra['username']==$u)
										{
											$count=1;
										}	
								}
							if($count==1 && $_SESSION['admin']==1 && $u!=$current_user)
								{				
									$r=mysql_query("SELECT * FROM users WHERE username = '$u'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									$ra=mysql_fetch_assoc($r);
									echo "<form id=\"edit_others\" method=\"post\" action=\"edit_other_user.php?u=".$ra['username']."\">\n";
									echo "<table id=\"edit_others\">\n";
									echo "<tr>\n";
									echo "<td>first name</td>\n";
									echo "<td><input id=\"fname\" type=\"text\" name=\"fname\" value=\"";
									echo $ra['fname'];
									echo "\"/></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td>lname</td>\n";
									echo "<td><input id=\"lname\" type=\"text\" name=\"lname\" value=\"";
									echo $ra['lname'];
									echo "\"/></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td>email</td>\n";
									echo "<td><input id=\"username\" type=\"text\" name=\"u_new\" value=\"";
									echo $ra['username'];
									echo "\"/></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td>password</td>\n";
									echo "<td><input id=\"password\" type=\"password\" name=\"password\" autocomplete=\"off\" /></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td>admin</td>\n";
									echo "<td><input id=\"admin\" type=\"checkbox\" name=\"admin\" ";
									if($ra['admin']==1)
										{
											echo "checked";
										}
									echo "/></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td>disabled</td>\n";
									echo "<td><input id=\"disabled\" type=\"checkbox\" name=\"disabled\" ";
									if($ra['disabled']==1)
										{
											echo "checked";
										}
									echo "/></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td></td><td><a href=\".\"><img src=\"../images/x.png\" alt=\"cancel\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
									echo "<input type=\"image\" src=\"../images/thumbs-up.png\" /></td>\n";
									echo "</table>\n";
									echo "</form>\n";
								}
							else
								{
									//set error message if the entered username doesn't match an existing one, the user isn't admin or the user being edited is the same as the logged in user
									$_SESSION['user_alert_message'] = "you do not have the appropriate priveleges to edit this user";
									header('location:../users/#alert');
									exit;
								}
						}
				?>
			</div>	
		</div>
		<?php 
			//check for alerts or notifications
			if(isset($_SESSION['user_alert_message']))
				{
					//create alert popover
					echo "<div id=\"alert\">";

					//echo the alert message
					echo "<div>".$_SESSION['user_alert_message']."<br /><br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";

					//clear out the error message
					unset($_SESSION['user_alert_message']);

					//close alert popover
					echo "</div>";
				}
		?>

		<div id="wrapper">

			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<span class="button"><a href="#edit_user"><img src="../images/gear_sm.png" alt="edit" /> <?php echo $_SESSION['username']; ?></a></span>
				<?php
					//check to see if user is admin give them additional options
					if($_SESSION['admin']==1)
						{
							echo "<span class=\"button\"><a href=\"#add_user\"><img src=\"../images/plus_sm.png\" alt=\"add\" /> User</a></span>";
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
										echo "<td><a href=\"?u=";
										echo $ra['username'];
										echo "#edit_other_user\"><img src=\"../images/gear_sm.png\" alt=\"edit\" /></a>";
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
										echo "\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a>";
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
