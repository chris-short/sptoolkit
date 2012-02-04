<?php
/**
 * file:		start_campaign.php
 * version:		11.0
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

//ensure the campaign name is set
if(!isset($_POST['campaign_name']))
	{
		$_SESSION['alert_message'] = "you must give the campaign a name";
		header('location:./#alert');
		exit;
	}

//ensure a target group was selected
if(!isset($_POST['target_groups']))
	{
		$_SESSION['alert_message'] = "please select at least one target group";
		header('location:./#alert');
		exit;
	}

//ensure a template is selected
if(!isset($_POST['template_id']))
	{
		$_SESSION['alert_message'] = "please select a template";
		header('location:./#alert');
		exit;
	}

//recieve the entered values and put into variables
$campaign_name = filter_var($_POST['campaign_name'], FILTER_SANITIZE_STRING);
$spt_path = $_POST['spt_path'];
$target_groups = $_POST['target_groups'];
$template_id = filter_var($_POST['template_id'], FILTER_SANITIZE_NUMBER_INT);
$education_id = filter_var($_POST['education_id'], FILTER_SANITIZE_NUMBER_INT);
if(isset($_POST['education_timing'])){$education_timing = filter_var($_POST['education_timing'], FILTER_SANITIZE_NUMBER_INT);}

//connect to database
include "../spt_config/mysql_config.php";

//take each value in the array and validate that it is a valid group name
foreach($target_groups as $group)
	{
		$r = mysql_query("SELECT DISTINCT group_name FROM targets") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
		while($ra = mysql_fetch_assoc($r))
			{
				if($group == $ra['group_name'])
					{
						$match = 1;
					}
			}
		if(!isset($match))
			{
				$_SESSION['alert_message'] = "invalid group";
				header('location:./#alert');
				exit;
			}
	}


//validate the template exists
$r = mysql_query("SELECT id FROM templates") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
while($ra = mysql_fetch_assoc($r))
	{
		if($template_id==$ra['id'])
			{
				$match0 = 1;
			}
	}
if(!isset($match0))
	{
		$_SESSION['alert_message'] = "please select a valid template";
		header('location:./#alert');
		exit;
	}

//validate the education package exists
$r = mysql_query('SELECT id FROM education') or die ('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
while($ra = mysql_fetch_assoc($r))
	{
		if($education_id==$ra['id'] OR $education_id==0)
			{
				$match1 = 1;
			}
	}
if(!isset($match1))
	{
		$_SESSION['alert_message'] = "please select a valid education package";
		header('location:./#alert');
		exit;
	}

//validate the education timing if set
if(isset($education_timing))
	{
		if($education_timing == 1 OR $education_timing == 2)
			{
				$match2 = 1;
			}
	}
else
	{
		$education_timing = 0;
		$match2 = 1;
	}
if($match2 != 1)
	{
		$_SESSION['alert_message'] = "please select a valid education timing option";
		header('location:./#alert');
		exit;
	}

//create the campaign
mysql_query("INSERT INTO campaigns (campaign_name, template_id, domain_name, education_id, education_timing) VALUES ('$campaign_name', '$template_id', '$spt_path', '$education_id', '$education_timing')") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');

//get the id of this campaign
$r = mysql_query("SELECT MAX(id) as campaign_id FROM campaigns") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
while($ra = mysql_fetch_assoc($r))
	{
		$campaign_id = $ra['campaign_id'];
	}

//link the campaign id and group name while retrieving all applicable targets
foreach($target_groups as $group)
	{
		//link campaign id and group names
		mysql_query("INSERT INTO campaigns_and_groups (campaign_id, group_name) VALUES ('$campaign_id','$group')") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
		
		//retrieve all targets from group
		$r = mysql_query("SELECT id FROM targets WHERE group_name = '$group'") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
		while($ra = mysql_fetch_assoc($r))
			{
				$target_id = $ra['id'];

				//generate a random key
				$random_number = mt_rand(1000000000,9999999999);
				$response_id = sha1($random_number);

				//populate the campaign response table with placeholders for when the target clicks the links or posts data
				mysql_query("INSERT INTO campaigns_responses (target_id, campaign_id, response_id) VALUES ('$target_id', '$campaign_id', '$response_id')") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');
			}
	}

//get all the necessary email addresses
$r = mysql_query("SELECT targets.email as email, targets.id as id, campaigns_responses.response_id as response_id FROM campaigns_responses JOIN targets ON targets.id = campaigns_responses.target_id WHERE campaigns_responses.campaign_id = '$campaign_id'") or die('<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>');

//using the built-in php mail function which depends on the php.ini file and the servers mail settings
while($ra = mysql_fetch_assoc($r))
	{
		//set the current email address
		$current_target_email_address = $ra['email'];
		$current_target_id = $ra['id'];
		$current_response_id = $ra['response_id'];
		
		//formulate link
		$link = "http://".$spt_path."/campaigns/response.php?r=".$current_response_id;
		
		//pull in all the email variables from the specified template
		include "../templates/".$template_id."/email.php";

		//send the email and force the envelope sender if the email template specifies
		if(isset($f_sender))
			{
				mail($current_target_email_address, $subject, $message, $headers, $f_sender);
			}
		else
			{
				mail($current_target_email_address, $subject, $message, $headers );	
			}		
	}

//send them back after your finished sending emails
$_SESSION['alert_message'] = "emails have been sent...sit back and wait for the responses :)";
header('location:./#alert');

?>
