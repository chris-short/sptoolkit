<?php
/**
 * file:		delete_user.php
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

//connect to database
include "../spt_config/mysql_config.php";

//ensure the user is an admin
if($_SESSION['admin']!=1)
	{
		$_SESSION['user_alert_message'] = "you do not have the privileges to delete users";
		header('location:../users/');
		exit;
	}

//pull in user from parameter and validate
$username=$_REQUEST['u'];

//validate that the passed username is a valid email address
if(!filter_var($username, FILTER_VALIDATE_EMAIL))
	{
		$_SESSION['user_alert_message'] = "you can only delete a user if you pass a valid email address";
		header('location:../users/');
		exit;
	}

//ensure the user is not attempting to delete themselves
if($_SESSION['username']==$username)
	{
		$_SESSION['user_alert_message'] = "you cannot delete yourself";
		header('location:../users/');
		exit;
	}

//ensure the user is attempting to delete a valid username
$r = mysql_query('SELECT username FROM users') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra=mysql_fetch_assoc($r))
	{
		if($ra['username']==$username)
			{
				$count = 1;
			}
	}
if($count!=1)
	{
		$_SESSION['user_alert_message'] = "you are attempting to delete a user that does not exist";
		header('location:../users/');
		exit;
	}

//delete the specified user
mysql_query("DELETE FROM users WHERE username = '$username'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//send the user back to the users page with a success message
$_SESSION['user_alert_message'] = "user deleted successfully";
header('location:../users/');
exit;

?>
