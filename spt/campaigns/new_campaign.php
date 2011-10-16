<?php
/**
 * file:		new_campaign.php
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
		<title>spt - new campaign</title>
		
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
				<a href="../campaigns">< back</a>
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['new_campaign_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['new_campaign_alert_message']."</h2>";
							
							//unset the seession
							unset ($_SESSION['new_campaign_alert_message']);				
						}
				?>
				<form method="post" action="start_campaign.php">
					<table class="spt_table">
						<tr>
							<td>Campaign Name</td>
							<td><input name="campaign_name" /></td>
						</tr>
						<tr>
							<td>SPT Path</td>
							<td><input name="spt_path" /></td>
							<td><i>*this is the hostname and path to spt (example: sub.domain.com/spt).  Do not include the trailing slash.</i></td>
						</tr>
						<tr>
							<td>Target Group(s)</td>
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
							<td><i>*hold ctrl or cmd to select multiple</i></td>
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
							<td><i>*go to the templates module to customize the template</i></td>
						</tr>
						<tr>
							<td><input type="submit" /></td>
							<td colspan=2><b><i>< emails ARE sent when this button is clicked!</i></b></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>



?>