<?php
/**
 * file:		user_form.php
 * version:		2.0
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
		<title>spt - add user form</title>
		
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
		<?php include('../includes/sidebar.php'); ?>

		<!--content-->
		<div id="content">
			<a href="../users/">&#60; back</a><br /><br />
			<?php
				//connect to database
				include "../spt_config/mysql_config.php";
				
				//pull parameter
				if(isset($_REQUEST['user']))
					{
						
						//set parameter to variable
						$user=$_REQUEST['user'];

						//validate the parameter is an acceptable value
						if($user!=1 && $user!=2)
							{
								//if the parameter passed is not valid set the error message and send them backe
								$_SESSION['user_alert_message'] = "invalid parameter passed";
								header('location:../users/');
								exit;
							}

						//pull in the current username from the username session
						$current_user=$_SESSION['username'];

						//edit current user
						if($user==1)
							{
							
								//create the sql statement to pull data about the current user
								$r=mysql_query("SELECT fname, lname, username FROM users WHERE username = '$current_user'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								$ra=mysql_fetch_assoc($r);

								//generate form for the user to modify their data
								echo "<form id=\"edit_current_user\" method=\"post\" action=\"edit_user.php\">\n";
								echo "<table class=\"spt_table\">\n";
								echo "<tr>\n";
								echo "<td>first name</td>\n";
								echo "<td><input id=\"fname\" type=\"text\" name=\"fname\" value=\"";
								echo $ra['fname'];
								echo "\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>last name</td>\n";
								echo "<td><input id=\"lname\" type=\"text\" name=\"lname\" value=\"";
								echo $ra['lname'];
								echo "\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>username</td>\n";
								echo "<td><input id=\"username\" type=\"text\" name=\"username\" value=\"".$ra['username']."\"/></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>password</td>\n";
								echo "<td><input id=\"password\" type=\"password\" name=\"password\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td><input type=\"submit\" value=\"submit\" /></td>\n";
								echo "</tr>\n";
								echo "</table>\n";
								echo "</form>\n";
							}
				
						//add user
						elseif($user==2 && $_SESSION['admin']==1)
							{
								echo "<form id=\"add_user\" method=\"post\" action=\"add_user.php\">\n";
								echo "<table class=\"spt_table\">\n";
								echo "<tr>\n";
								echo "<td>first name</td>\n";
								echo "<td><input id=\"fname\" type=\"text\" name=\"fname\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>last name</td>\n";
								echo "<td><input id=\"lname\" type=\"text\" name=\"lname\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>email</td>\n";
								echo "<td><input id=\"username\" type=\"text\" name=\"username\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>password</td>\n";
								echo "<td><input id=\"password\" type=\"password\" name=\"password\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>admin</td>\n";
								echo "<td><input id=\"admin\" type=\"checkbox\" name=\"a\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>disabled</td>\n";
								echo "<td><input id=\"disabled\" type=\"checkbox\" name=\"disabled\" /></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td><input type=\"submit\" value=\"submit\" /></td>\n";
								echo "</tr>\n";
								echo "</table>\n";
								echo "</form>\n";
							}
						
						//if a non admin is trying to add a user send them an error
						elseif($user==2 && $_SESSION['admin']!=1)
							{
								//set error message and send back to users page
								$_SESSION['user_alert_message'] = "you do not have sufficient priveleges to add a user";
								header('location:../users/');
								exit;
							}
					}
				//edit selected user
				elseif(isset($_REQUEST['u']))
					{	
						//set current user varaible with username from username session variable
						$current_user=$_SESSION['username'];

						//pull parameter and set to variable
						$u=$_REQUEST['u'];

						//validate that the email address entered is an actual email address
						if(!filter_var($u, FILTER_VALIDATE_EMAIL))
							{
								//set error message if not a valid email address
								$_SESSION['user_alert_message'] = "please attempt to edit only valid email addresses";
								header('location:../users/');
								exit;
							}						

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
								echo "<form id=\"edit_selected_user\" method=\"post\" action=\"edit_other_user.php?u=".$ra['username']."\">\n";
								echo "<table class=\"spt_table\">\n";
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
								echo "<td><input id=\"password\" type=\"password\" name=\"password\" /></td>\n";
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
								echo "<td><input type=\"submit\" value=\"submit\" /></td>\n";
								echo "</table>\n";
								echo "</form>\n";
							}
						else
							{
								//set error message if the entered username doesn't match an existing one, the user isn't admin or the user being edited is the same as the logged in user
								$_SESSION['user_alert_message'] = "you do not have the appropriate priveleges to edit this user";
								header('location:../users/');
								exit;
							}
					}
				else
					{
						//set error message
						$_SESSION['user_alert_message'] = "you must at least attempt to edit some user";
						header('location:../users/');
						exit;
					}
			?>
		</div>
	</body>
</html>