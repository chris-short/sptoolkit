<?php
/**
 * file:		index.php
 * version:		8.0
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
		<title>spt - campaigns</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_campaigns.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['campaigns_alert_message']))
						{
							//create alert popover
							echo "<div id=\"alert\">";

							//echo the alert message
							echo "<div>".$_SESSION['campaigns_alert_message']."<br /><br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";
							
							//unset the seession
							unset ($_SESSION['campaigns_alert_message']);				

							//close alert popover
							echo "</div>";
						}
				?>
				<span class="button"><a href="#add_campaign"><img src="../images/plus_sm.png" alt="add" /> Campaign</a></span>
				<table class="spt_table">
					<tr>
						<td><h3>ID</h3></td>
						<td><h3>Name</h3></td>
						<td><h3>Template</h3></td>
						<td><h3>Target Groups</h3></td>
						<td><h3>Responses (Links/Posts)</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					
					<?php
					
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//pull in list of all campaigns
						$r = mysql_query("SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, templates.name as name FROM campaigns JOIN templates ON campaigns.template_id = templates.id") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo	"
									<tr>
										<td><a href=\"?c=".$ra['id']."#responses\">".$ra['id']."</a></td>\n
										<td>".$ra['campaign_name']."</td>\n
										<td><a href=\"../templates/".$ra['template_id']."/\">".$ra['name']."</a></td>\n
										<td>
								";
								
								$campaign_id = $ra['id'];
								
								//pull in groups
								$r3=mysql_query("SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra3=mysql_fetch_assoc($r3))
									{
										echo	"<a href=\"?c=".$ra['id']."&amp;g=".$ra3['group_name']."#responses\">".$ra3['group_name']."</a><br />\n";
									}
								echo "</td>";
										
								$r2 = mysql_query("SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra2=mysql_fetch_assoc($r2))
									{
										$link = $ra2['link'];
										$post = $ra2['post'];
										
									}
								
								echo	"<td><a href=\"?c=".$ra['id']."&amp;f=link#responses\">".$link."</a> / <a href=\"?c=".$ra['id']."&amp;f=post#responses\">".$post."</a></td>";
								echo	"<td><a href=\"delete_campaign.php?c=".$campaign_id."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>";
								echo	"</tr>";								
							}
					
					?>
				</table>
			</div>

			<!--new campaign-->
			<div id="add_campaign">
				<div>
					<form method="post" action="start_campaign.php">
						<table id="new_campaign">
							<tr>
								<td>Name</td>
								<td><input name="campaign_name" /></td>
								<td>
									<a class="tooltip"><img src="../images/lightbulb.png" alt="lightbulb.png" /><span>To start a new campaign, specify the campaign name, select one or more groups of targets and then select the template to be used.<br /><br /><strong>WARNING:</strong>  Emails will be sent as soon as you click the email icon.</span></a>
								</td>
							</tr>
							<tr>
								<?php
									//pull current host and path
									$path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
									
									//strip off the end
									$path = preg_replace('/\/campaigns.*/','',$path);

									//create a hidden field with the path of spt
									echo "<input type=\"hidden\" name=\"spt_path\" value=\"".$path."\" />";
									  
								?>
							</tr>
							<tr>
								<td>Group(s)</td>
								<td>
									<select name = "target_groups[]" multiple="multiple" size="5" style="width: 100%;">
										<?php
											//connect to database
											include('../spt_config/mysql_config.php');
											
											//query for all groups
											$r = mysql_query('SELECT DISTINCT group_name FROM targets');
											while($ra=mysql_fetch_assoc($r))
												{
													echo "<option>".$ra['group_name']."</option>";
												}
										?>	
									</select>
								</td>
							</tr>
							<tr>
								<td>Template</td>
								<td>
									<select name = "template_id">
										<?php
											//connect to database
											include('../spt_config/mysql_config.php');
											
											//query for all groups
											$r = mysql_query('SELECT id, name FROM templates');
											while($ra=mysql_fetch_assoc($r))
												{
													echo "<option value=\"".$ra['id']."\">".$ra['name']."</option>";
												}
										?>	
									</select>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><a href=""><img src="../images/x.png" alt="x" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/email.png" alt="email" /></td>
							</tr>
						</table>
					</form>
				</div>
			</div>

			<!--responses-->
			<div id="responses">
				<div>
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
										header('location:../campaigns/#alert');
										exit;
									}
							}
							
						//pull in filter and group
						if(isset($_REQUEST['f']))
							{
								$filter = $_REQUEST['f'];
								
								//go ahead and preform validation
								if($filter!="link" && $filter!="post")
									{
										$_SESSION['campaigns_alert_message'] = "please use a valid filter";
										header('location:../campaigns/#alert');
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
										header('location:../campaigns/#alert');
										exit;
									}
								
							}
						
						//if group and filter are both set send them back
						if(isset($filter) && isset($group))
							{
								$_SESSION['campaigns_alert_message'] = "you cannot pass both a filter and a group";
								header('location:../campaigns/#alert');
								exit;
								
							}
						
						//provide the close button
						echo "<span><a href=\".\"><img src=\"../images/x.png\" alt=\"close\"></a></span>";

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
						<table id=\"response_table\">
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
								if(strlen($ra['post'])<1)
									{
										$post = 'N';
									}
								else
									{
										$post = $ra['post'];
									}
								echo "<td>".$post."</td>";
								echo "</tr>";
							}
						
						echo "</table>";
					?>
				</div>
			</div>
		</div>
	</body>
</html>
