<?php
/**
 * file:		index.php
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
		<title>spt - campaigns</title>
		
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
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['campaigns_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['campaigns_alert_message']."</h2>";
							
							//unset the seession
							unset ($_SESSION['campaigns_alert_message']);				
						}
				?>
				<a href = "new_campaign.php">+ new campaign</a>
				<table class="spt_table">
					<tr>
						<td><h3>ID</h3></td>
						<td><h3>Name</h3></td>
						<td><h3>Template</h3></td>
						<td><h3>Domain</h3></td>
						<td><h3>Target Groups</h3></td>
						<td><h3>Responses (Links/Posts)</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					
					<?php
					
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//pull in list of all campaigns
						$r = mysql_query("SELECT campaigns.id, campaigns.campaign_name, campaigns.domain_name, campaigns.template_id, templates.name as name FROM campaigns JOIN templates ON campaigns.template_id = templates.id") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo	"
									<tr>
										<td><a href=\"responses.php?c=".$ra['id']."\">".$ra['id']."</a></td>\n
										<td>".$ra['campaign_name']."</td>\n
										<td><a href=\"../templates/".$ra['template_id']."/\">".$ra['name']."</a></td>\n
										<td>".$ra['domain_name']."</td>\n
										<td>
								";
								
								$campaign_id = $ra['id'];
								
								//pull in groups
								$r3=mysql_query("SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra3=mysql_fetch_assoc($r3))
									{
										echo	"<a href=\"responses.php?c=".$ra['id']."&amp;g=".$ra3['group_name']."\">".$ra3['group_name']."</a><br />\n";
									}
								echo "</td>";
										
								$r2 = mysql_query("SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra2=mysql_fetch_assoc($r2))
									{
										$link = $ra2['link'];
										$post = $ra2['post'];
										
									}
								
								echo	"<td><a href=\"responses.php?c=".$ra['id']."&amp;f=link\">".$link."</a> / <a href=\"responses.php?c=".$ra['id']."&amp;f=post\">".$post."</a></td>";
								echo	"<td><a href=\"delete_campaign.php?c=".$campaign_id."\">X</a></td>";
								echo	"</tr>";								
							}
					
					?>
				</table>
			</div>
		</div>
	</body>
</html>
