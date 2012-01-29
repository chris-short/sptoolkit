<?php
/**
 * file:		target_upload_single.php
 * version:		7.0
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

	// verify session is authenticated and not hijacked
	$includeContent = "../includes/is_authenticated.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_authenticated.php');
	}

	// verify user is an admin
	$includeContent = "../includes/is_admin.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_admin.php');
	}

//validate name is set and if so throw it in a variable
	if(isset($_POST['name']))
		{
			$name = $_POST['name'];
		}
	else
		{
			$_SESSION['alert_message'] = "you must enter a name";
			header('location:./#alert');
			exit;
		}
		
//validate email is set and if so throw it in a variable
	if(isset($_POST['email']))
		{
			$email = $_POST['email'];
		}
	else
		{
			$_SESSION['alert_message'] = "you must enter an email address";
			header('location:./#alert');
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
			$_SESSION['alert_message'] = "you must select an existing group or create a new group";
			header('location:./#alert');
			exit;
		}

//make sure that if they did not select an existing group that the new group is actually set
	if($group_name == "Select an Existing Group..." && !isset($group_name_new))
		{
			$_SESSION['alert_message'] = "you must select an existing group or create a new group";
			header('location:./#alert');
			exit;
		}
		
//do a little validation on the name
	if(preg_match('/[^a-zA-Z0-9_-\s!.()]/', $name))
		{
			$_SESSION['alert_message'] = "you have some invalid characters in the name";
			header('location:./#alert');
			exit;
		}
		
//do a little validation on the email
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$_SESSION['alert_message'] = "you must enter an actual email address";
			header('location:./#alert');
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
					$_SESSION["alert_message"] = "if your going to attempt to select a group that already exists, select one that already exists";
					header('location:./#alert');
					exit;
				}
		}

//if they are adding a new group name, validate it
	if(preg_match('/[^a-zA-Z0-9_-\s!.()]/', $group_name_new))
		{
			$_SESSION["alert_message"] = "there are invalid characters in the group name";
			header('location:./#alert');
			exit;
		}

//determine what the custom field names are
$r = mysql_query("SELECT * FROM targets");
$custom1 = mysql_field_name($r,4);
$custom2 = mysql_field_name($r,5);
$custom3 = mysql_field_name($r,6);

//pull in custom values
	if(isset($_POST['custom1']))
		{
			$custom1_value = $_POST['custom1'];
		}
	if(isset($_POST['custom2']))
		{
			$custom2_value = $_POST['custom2'];
		}
	if(isset($_POST['custom3']))
		{
			$custom3_value = $_POST['custom3'];
		}	
//enter the value in the database
	//if existing group is selected
	if($group_name != "Select an Existing Group..." && isset($group_name))
		{
			mysql_query("INSERT INTO targets (name, email, group_name, `$custom1`, `$custom2`, `$custom3`) VALUES ('$name', '$email', '$group_name', '$custom1_value', '$custom2_value', '$custom3_value')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		}
	else
		{
			mysql_query("INSERT INTO targets (name, email, group_name, `$custom1`, `$custom2`, `$custom3`) VALUES ('$name', '$email', '$group_name_new', '$custom1_value', '$custom2_value', '$custom3_value')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		}

	
//send user back to targets page with success message
$_SESSION['alert_message'] = "target added successfully";
header('location:./#alert');
exit;

?>