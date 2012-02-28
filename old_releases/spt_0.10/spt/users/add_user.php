<?php
/**
 * file:		add_user.php
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

//validate that the currently logged in user is an admin
if($_SESSION['admin']!=1)
	{
		$_SESSION['user_alert_message'] = "you do not have permission to add a user";
		header('location:../users/#alert');
		exit;
	}

//validate the entered username
$new_username = $_POST['username'];

//validate that the newly entered username is a valid email address
if(!filter_var($new_username, FILTER_VALIDATE_EMAIL))
	{
		$_SESSION['user_alert_message'] = "you must enter a valid email address";
		header('location:../users/#alert');
		exit;
	}

//validate that the username is not too long
if(strlen($new_username) > 50)
	{
		$_SESSION['user_alert_message']="the username is too long";
		header('location:../#alert');
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


//validate the first name
$new_fname = $_POST['fname'];

//make sure its only letters
if(preg_match('/[^a-zA-Z]/', $new_fname))
	{
		$_SESSION['user_alert_message'] = "only letters are allowed in the first name field";
		header('location:../users/#alert');
		exit;
	}

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
		header('location:../users/#alert');
		exit;
	}

//validate the last name
$new_lname = $_POST['lname'];

//make sure its under 50 characters
if(strlen($new_lname) > 50)
	{
		$_SESSION['use_error_message'] = "your last name is too long, please shorten below 50 characters";
		header('location:../users/#alert');
		exit;
	}

//make sure it only contains letters
if(preg_match('/[^a-zA-Z]/', $new_lname))
	{
		$_SESSION['user_alert_message'] = "only letters are allowed in the last name field";
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
		
		//validate the password doesn't have any characters that are not allowed
		if(preg_match('/[$+*"=&%]/', $temp_p))
			{ 
				$_SESSION['user_alert_message']="you must enter a valid password";
				header('location:../users/#alert');
				exit;
			} 
		
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
else
	{
		$_SESSION['user_alert_message'] = "you must enter a password";
		header('location"../users/#alert');
		exit;
	}

//set checkbox values to numbers
if(isset($_REQUEST['a']))
	{
		$a = 1;
	}
else
	{
		$a = 0;
	}
if(isset($_REQUEST['disabled']))
	{
		$disabled = 1;
	}
else
	{
		$disabled = 0;
	}


mysql_query("INSERT INTO users(fname, lname, username, password, admin, disabled) VALUES ('$new_fname','$new_lname','$new_username','$p','$a','$disabled')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

$_SESSION['user_alert_message'] = "user added successfully";
header('location:../users/#alert');
exit;
?>