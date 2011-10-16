<?php
/**
 * file:		responses.php
 * version:		2.0
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
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - responses</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_campaigns.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--sidebar-->
			<?php include('../includes/sidebar.php'); ?>

			<!--content-->
			<div id="content">
				<a href="../campaigns/">< back</a>
				<?php
					
					//connect to database
					include "../spt_config/mysql_config.php";

					//pull in campaign id
					if(isset($_REQUEST['c']))
						{
							$campaign_id = $_REQUEST['c'];
							
							//go ahead and perform validation
							$r = mysql_query("SELECT DISTINCT campaign_id FROM campaigns_responses") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							while($ra = mysql_fetch_assoc($r))
								{
									if($ra['campaign_id'] == $campaign_id)
										{
											$campaign_match =1;
										}
								}
							if($campaign_match!=1)
								{
									$_SESSION['campaigns_alert_message'] = "please select a valid campaign";
									header('location:../campaigns/');
									exit;
								}
						}
					else
						{
							//kick them back if they do not have a campaign specified...there must be at least a campaign!
							$_SESSION['campaigns_alert_message'] = "please narrow it down to a specific campaign";
							header('location:../campaigns/');
							exit;
						}
						
					//pull in filter and group
					if(isset($_REQUEST['f']))
						{
							$filter = $_REQUEST['f'];
							
							//go ahead and preform validation
							if($filter!="link" && $filter!="post")
								{
									$_SESSION['campaigns_alert_message'] = "please use a valid filter";
									header('location:../campaigns/');
									exit;
								}
						}
					if(isset($_REQUEST['g']))
						{
							$group = $_REQUEST['g'];
							
							//go ahead and perform validation
							$r = mysql_query("SELECT DISTINCT group_name FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							while ($ra = mysql_fetch_assoc($r))
								{
									if($group == $ra['group_name'])
										{
											$group_match = 1;
										}
								}
							if($group_match!=1)
								{
									$_SESSION['campaigns_alert_message'] = "please select a valid group";
									header('location:../campaigns/');
									exit;
								}
							
						}
					
					//if group and filter are both set send them back
					if(isset($filter) && isset($group))
						{
							$_SESSION['campaigns_alert_message'] = "you cannot pass both a filter and a group";
							header('location:../campaigns/');
							exit;
							
						}
					
					//pull data for entire campaign if group and filters are NOT set
					if(!isset($group) && !isset($filter))
						{
							$r = mysql_query("SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.name AS name FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						
							//title the page with the campaign number
							$title = "<h3>Campaign ".$campaign_id." Responses</h3>";
						}
					
					//pull data if a group is set
					if(isset($group))
						{
							$r = mysql_query("SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.name AS name FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE targets.group_name = '$group' AND campaigns_responses.campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							
							//title the page with the campaign number
							$title = "<h3>Campaign ".$campaign_id." Responses - ".$group."</h3>";

						}
						
					//pull data if a filter is set
					if(isset($filter))
						{
							//if filter is for links
							if($filter == "link")
								{
									$r = mysql_query("SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.name AS name FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.link = 1 AND campaigns_responses.campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									
									//title the page with the campaign number
									$title = "<h3>Campaign ".$campaign_id." Responses";
									
									if(isset($group))
										{
											$title .= " - ".$group." Group";	
										}
									
									$title .= " - Links</h3>";
								}
							
							//if filter is for posts
							if($filter == "post")
								{
									$r = mysql_query("SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.name AS name FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.post != \"\"  AND campaigns_responses.campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									
									//title the page with the campaign number
									$title = "<h3>Campaign ".$campaign_id." Responses";
									
									if(isset($group))
										{
											$title .= " - ".$group." Group";
										}
										
									$title .= " - Posts</h3>";
								}
						}
				
					//print the table header
					echo "
					<table class=\"spt_table\">
						<tr>
							<td colspan=\"5\">".$title."</td>
						</tr>
						<tr>
							<td><h3>ID</h3></td>
							<td><h3>Name</h3></td>
							<td><h3>Email</h3></td>
							<td><h3>Link</h3></td>
							<td><h3>Post</h3></td>
						</tr>
					";
					
					//dump data into table
					while($ra = mysql_fetch_assoc($r))
						{
							echo "<tr>";
							echo "<td>".$ra['target_id']."</td>";
							echo "<td>".$ra['name']."</td>";
							echo "<td>".$ra['email']."</td>";
							if($ra['link'] == 1)
								{
									$link = 'Y';
								}
							else
								{
									$link = 'N';	
								}
							echo "<td>".$link."</td>";
							echo "<td>".$ra['post']."</td>";
							echo "</tr>";
						}
				?>
					</table>
			</div>
		</div>
	</body>
</html>