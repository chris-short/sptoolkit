<?php
/**
 * file:		target_delete.php
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

//pull in target
	$target_to_delete = $_REQUEST['u'];

//pull in group
	$group_name = $_REQUEST['g'];

//make sure the user is an admin
	if($_SESSION['admin']!=1)
		{
			$_SESSION['targets_alert_message'] = "you do not have permission to delete targets";
			header('location:../targets/#alert');
			exit;
		}
	
//ensure u only contains numbers
	if(preg_match('/[^0-9]/', $target_to_delete))
		{
			$_SESSION['targets_alert_message'] = "invalid characters in the parameter you were attempting to pass";
			header('location:../targets/#alert');
			exit;
		}

//pull in all target ids and compare to entered data
	//connect to database
	include "../spt_config/mysql_config.php";
	$r = mysql_query("SELECT DISTINCT id FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	while ($ra = mysql_fetch_assoc($r))
		{
			echo $target_to_delete."<br />";
			echo $ra['id']."<br /><br />";
			if(eregi($target_to_delete, $ra['id']))
				{
					mysql_query("DELETE FROM targets WHERE id = '$target_to_delete'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
				}
		}

//send user back to targets page with success message
$_SESSION['targets_alert_message'] = "target deleted successfully";
header('location:../targets/#alert');
exit;

?>