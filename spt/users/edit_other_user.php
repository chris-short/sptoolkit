<?php
/**
 * file:		edit_other_user.php
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

//connect to database
include "../spt_config/mysql_config.php";

//ensure the currently logged in user is not trying to edit their own record
if($_SESSION['username']==$_REQUEST['u'])
	{
		$_SESSION['user_alert_message'] = "don't attempt to use this method to edit your own information";
		header('location:../users/#alert');
		exit;
	}

//ensure the currently logged in user is an admin and has permission to be using this form
$logged_in_user = $_SESSION['username'];
$r = mysql_query("SELECT admin FROM users WHERE username = '$logged_in_user'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra=mysql_fetch_assoc($r))
	{
		if($ra['admin']!=1)
			{
				$_SESSION['user_alert_message'] = "you do not have permission to edit other people's information";
				header('location:../users/#alert');
				exit;
			}
	}

//go ahead and get their original username
$original_username = $_REQUEST['u'];

//get the new username
$new_username = $_POST['u_new'];

//see if the username has changed and if it has validate it
if($original_username != $new_username)
	{
		//validate that the newly entered username is a valid email address
		if(!filter_var($new_username, FILTER_VALIDATE_EMAIL))
			{
				$_SESSION['user_alert_message'] = "you must enter a valid email address as the username";
				header('location:../users/#alert');
				exit;
			}

		//validate that the username is not too long
		if(strlen($new_username) > 50)
			{
				$_SESSION['user_alert_message']="the username is too long";
				header('location:../users/#alert');
				exit;
			}

		//validate that the entered username is not already taken
		$r = mysql_query('SELECT username FROM users') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		while($ra=mysql_fetch_assoc($r))
			{
				if($ra['username']==$new_username)
					{
						$_SESSION['user_alert_message'] = "this email address is already taken";
						header('location:../users/#alert');
						exit;
					}
			}
	}

//validate the first name
$new_fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);

//make sure its under 50 characters
if(strlen($new_fname) > 50)
	{
		$_SESSION['user_alert_message'] = "your first name is too long, please shorten below 50 characters";
		header('location:../users/#alert');
		exit;
	}

//make sure its over 1 character
if(strlen($new_fname) < 1)
	{
		$_SESSION['user_alert_message'] = "your first name must be at least 1 character long";
		header('location:../users/');
		exit;
	}

//validate the last name
$new_lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);

//make sure its under 50 characters
if(strlen($new_lname) > 50)
	{
		$_SESSION['use_error_message'] = "your last name is too long, please shorten below 50 characters";
		header('location:../users/#alert');
		exit;
	}

//make sure its at least 1 character in length
if(strlen($new_lname) < 1)
	{
		$_SESSION['user_alert_message'] = "your last name must be at least 1 character long";
		header('location:../users/#alert');
		exit;
	}

//validate the password if it is set
if(!empty($_POST['password']))
	{
		//pull in password to temp variable
		$temp_p = $_POST['password'];
				
		//validate that the password is an acceptable length
		if(strlen($temp_p) > 15 || strlen($temp_p) < 8)
			{
				$_SESSION['user_alert_message']="you must enter a valid password length";
				header('location:../users/#alert');
				exit;
			}
		
		//pass temp password to new variable that has been salted and hashed
		$p = sha1($_SESSION['salt'].$temp_p.$_SESSION['salt']);
	}

//validate and set the admin and disabled parameters
if(isset($_REQUEST['admin']))
	{
		$admin = 1;
	}
if(isset($_REQUEST['disabled']))
	{
		$disabled = 1;
	}
//all entered variables should have been validated by now

//input variables to database except username and password
mysql_query("UPDATE users SET fname = '$new_fname' , lname = '$new_lname' , admin = '$admin' , disabled = '$disabled' WHERE username = '$original_username'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//if password was entered update it
if(isset($p))
	{
		mysql_query("UPDATE users SET password = '$p' WHERE username = '$original_username'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	}

//if username changed, change it
if($original_username != $new_username)
	{
		mysql_query("UPDATE users SET username = '$new_username' WHERE username = '$original_username'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	}


//send the user back to the users page once they've edited the user successfully
$_SESSION['user_alert_message'] = "you have successfully edited the user";
header('location:../users/#alert');
exit;
?>