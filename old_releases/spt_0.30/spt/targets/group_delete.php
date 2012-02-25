<?php
/**
 * file:		group_delete.php
 * version:		3.0
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
			$_SESSION['targets_alert_message'] = "you do not have permission to delete groups";
			header('location:../targets/#alert');
			exit;
		}

//pull in group
	$group_to_delete = $_REQUEST['g'];

//connect to database
	include "../spt_config/mysql_config.php";

//ensure that the group is not part of an active campaign 
	$r = mysql_query("SELECT group_name FROM campaigns_and_groups WHERE group_name = '$group_to_delete'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	if(mysql_num_rows($r))
		{
			$_SESSION['targets_alert_message'] = "you cannot delete a group that is part of an active campaign";
			header('location:../targets/#alert');
			exit;	
		}

//pull in all group names and compare to entered data
	$r = mysql_query("SELECT DISTINCT group_name FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	while ($ra = mysql_fetch_assoc($r))
		{
			echo $group_to_delete."<br />";
			echo $ra['group_name']."<br /><br />";
			if(preg_match($group_to_delete, $ra['group_name']))
				{
					mysql_query("DELETE FROM targets WHERE group_name = '$group_to_delete'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
				}
		}

//send user back to targets page with success message
	$_SESSION['targets_alert_message'] = "group deleted successfully";
	header('location:../targets/#alert');
	exit;

?>