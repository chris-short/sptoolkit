<?php
/**
 * file:		response.php
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

session_start();

//if values are being posted recieve all parameters into an array
if ($_POST)
	{
		$post = implode(',', array_keys($_POST));
		
		//pull in session variables
		$target_id = $_SESSION['target_id'];
		$campaign_id = $_SESSION['campaign_id'];
		$template_id = $_SESSION['template_id'];
		
		//connect to database
		include "../spt_config/mysql_config.php";
		
		//insert post metrics into database
		mysql_query("UPDATE campaigns_responses SET post = '$post' WHERE campaign_id = '$campaign_id' AND target_id = '$target_id'");
		
		//send them back to the template
		header('location:../templates/'.$template_id.'/return.htm');
		exit;
		
	}
//collect all URL parameters from the email link and generate sessions and record the link click to the appropriate target
else
	{
		//get parameters
		$campaign_id = $_REQUEST['c'];
		$target_id = $_REQUEST['t'];
		
		//validate that the campaign parameter is only numbers, if it isn't...confuse them :>
		if(preg_match('/[^0-9]/', $campaign_id) || preg_match('/[^0-9]/', $target_id))
			{
				header('location:http://127.0.0.1');
				exit;
			}
			
		//validate that the campaign parameter is of resonable length
		if(strlen($campaign_id) > 10 || strlen($target_id) > 10)
			{
				header('location:http://127.0.0.1');
				exit;
			}
			
		//connect to the database
		include "../spt_config/mysql_config.php";
		
		//validate that the campaign id and target IDs exist
		$r = mysql_query("SELECT target_id, campaign_id FROM campaigns_responses");
		while($ra = mysql_fetch_assoc($r))
			{
				if($ra['target_id'] == $target_id && $ra['campaign_id'] == $campaign_id)
					{
						$match = 1;
					}
			}

		//if a match happened record that they clicked the link
		if($match == 1)
			{
				mysql_query("UPDATE campaigns_responses SET link = 1 WHERE campaign_id = '$campaign_id' AND target_id = '$target_id'");
				
				//determine what template this campaign uses
				$r = mysql_query("SELECT template_id FROM campaigns WHERE id = '$campaign_id'");
				while($ra = mysql_fetch_assoc($r))
					{
						$template_id = $ra['template_id'];
						
						//set session variables
						$_SESSION['campaign_id'] = $campaign_id;
						$_SESSION['target_id'] = $target_id;
						$_SESSION['template_id'] = $template_id;
						
						//send the user to the appropriate template
						header('location:../templates/'.$template_id.'/');
						exit;
						
					}
			}
		else
			{
				header('location:http://127.0.0.1');
				exit;
			}
	}
	
?>