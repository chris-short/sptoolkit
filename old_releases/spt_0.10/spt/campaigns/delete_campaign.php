<?php
/**
 * file:		delete_campaign.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Campaign management
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
			$_SESSION['came_from']='campaigns';
			
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

//validate that the currently logged in user is an admin
if($_SESSION['admin']!=1)
	{
		$_SESSION['campaigns_alert_message'] = "you do not have permission to delete a campaign";
		header('location:../campaigns/#alert');
		exit;
	}

//get campaign id
$campaign_id = $_REQUEST['c'];


//validate the campaign id
include "../spt_config/mysql_config.php";
$r = mysql_query("SELECT id FROM campaigns") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		if($ra['id']==$campaign_id)
			{
				$match = 1;
			}
	}
if($match!= 1)
	{
		$_SESSION['campaigns_alert_message'] = "you can only delete real campaign ids";
		header('location:../campaigns/#alert');
		exit;
	}

//delete all traces of this specific campaign
mysql_query("DELETE FROM campaigns WHERE id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
mysql_query("DELETE FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
mysql_query("DELETE FROM campaigns_responses WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//send them back to the campaigns home page
$_SESSION['campaigns_alert_message'] = "campaign deleted successfully";
header('location:../campaigns/#alert');
exit;

?>
