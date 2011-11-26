<?php
/**
 * file:		custom_update.php
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
		
//make sure the user is an admin
	if($_SESSION['admin']!=1)
		{
			$_SESSION['targets_alert_message'] = "you do not have permission to upload targets";
			header('location:../targets/#alert');
			exit;
		}

//make sure parameters are set they are set
if(isset($_POST['c']) && isset($_POST['n']))
	{
		//determine which column we need to change
		$c=$_POST['c'];

		//determine what the new name will be
		$n=$_POST['n'];

		//connect to database
		include "../spt_config/mysql_config.php";

		//determine what the custom field names are
		$r = mysql_query("SELECT * FROM targets");
		$custom1 = mysql_field_name($r,4);
		$custom2 = mysql_field_name($r,5);
		$custom3 = mysql_field_name($r,6);

		if($c == "custom1")
			{
				$sql = "ALTER TABLE `targets` CHANGE `$custom1` `$n` varchar(255);";
				mysql_query($sql);
			}
		if($c == "custom2")
			{
				$sql = "ALTER TABLE `targets` CHANGE `$custom2` `$n` varchar(255);";
				mysql_query($sql);
			}
		if($c == "custom3")
			{
				$sql = "ALTER TABLE `targets` CHANGE `$custom3` `$n` varchar(255);";
				mysql_query($sql);
			}
	}
	
echo $sql;
?>

