<?php
/**
 * file:		target_upload_single.php
 * version:		2.0
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

//make sure the user is an admin
	if($_SESSION['admin']!=1)
		{
			$_SESSION['targets_alert_message'] = "you do not have permission to upload targets";
			header('location:../targets/#alert');
			exit;
		}

//validate name is set and if so throw it in a variable
	if(isset($_POST['name']))
		{
			$name = $_POST['name'];
		}
	else
		{
			$_SESSION['targets_alert_message'] = "you must enter a name";
			header('location:../targets/#alert');
			exit;
		}
		
//validate email is set and if so throw it in a variable
	if(isset($_POST['email']))
		{
			$email = $_POST['email'];
		}
	else
		{
			$_SESSION['targets_alert_message'] = "you must enter an email address";
			header('location:../targets/#alert');
			exit;
		}

//validate that at least one of the groups is set and if so see which one is set and throw it in a variable
	if(isset($_POST['group_name']) || isset($_POST['group_name_new']))
		{
			if(isset($_POST['group_name']))
				{
					$group_name = $_POST['group_name'];
				}
			if(isset($_POST['group_name_new']))
				{
					$group_name_new = $_POST['group_name_new'];
				}
			
		}
	else
		{
			$_SESSION['targets_alert_message'] = "you must select an existing group or create a new group";
			header('location:../targets/#alert');
			exit;
		}

//make sure that if they did not select an existing group that the new group is actually set
	if($group_name == "Select an Existing Group..." && !isset($group_name_new))
		{
			$_SESSION['targets_alert_message'] = "you must select an existing group or create a new group";
			header('location:../targets/#alert');
			exit;
		}
		
//do a little validation on the name
	if(preg_match('/[^A-Z\s\']/i', $name))
		{
			$_SESSION['targets_alert_message'] = "you have some invalid characters in the name";
			header('location:../targets/#alert');
			exit;
		}
		
//do a little validation on the email
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$_SESSION['targets_alert_message'] = "you must enter an actual email address";
			header('location:../targets/#alert');
			exit;
		}
		
//if they selected an existing group name lets ensure that they really selected an existing value
	//connect to database
	include "../spt_config/mysql_config.php";
	if(isset($group_name) && $group_name!= "Select an Existing Group...")
		{
			$r = mysql_query("SELECT DISTINCT group_name FROM targets");
			while ($ra = mysql_fetch_assoc($r))
				{
					if($ra['group_name'] == $group_name)
						{
							$match = 1;
						}
				}
			if($match!= 1)
				{
					$_SESSION["targets_alert_message"] = "if your going to attempt to select a group that already exists, select one that already exists";
					header('location:../targets/#alert');
					exit;
				}
		}

//if they are adding a new group name, validate it
	if(preg_match('/[^A-Z0-9\s_-]/i', $group_name_new))
		{
			$_SESSION["targets_alert_message"] = "there are invalid characters in the group name";
			header('location:../targets/#alert');
			exit;
		}
	
//enter the value in the database
	//if existing group is selected
	if($group_name != "Select an Existing Group..." && isset($group_name))
		{
			mysql_query("INSERT INTO targets (name, email, group_name) VALUES ('$name', '$email', '$group_name')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		}
	else
		{
			mysql_query("INSERT INTO targets (name, email, group_name) VALUES ('$name', '$email', '$group_name_new')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		}

	
//send user back to targets page with success message
$_SESSION['targets_alert_message'] = $counter." target added successfully";
header('location:../targets/#alert');
exit;

?>