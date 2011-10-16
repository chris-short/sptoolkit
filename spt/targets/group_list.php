<?php
/**
 * file:		group_list.php
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
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - group list</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_targets.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--sidebar-->
			<?php include('../includes/sidebar.php'); ?>

			<!--content-->
			<div id="content">	
				<a href="../targets/">< Back</a>
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['group_list_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['group_list_alert_message']."</h2>";
							
							//unset the seession
							unset ($_SESSION['group_list_alert_message']);				
						}
				?>
				<table class="spt_table">
					<tr>
						<td><h3>Name</h3></td>
						<td><h3>Email</h3></td>
						<td><h3>Group</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					<?php
						//connect to database
						include "../spt_config/mysql_config.php";
						
						$group = $_REQUEST['g'];
						
						//ensure the group name is under 50 characters
						if(strlen($group) > 50)
							{
								$_SESSION['targets_alert_message'] = "group names cannot be over 50 characters";
								header("location:../targets/");
								exit;
							}
						
						//ensure the group name passed only has letters in it
						if(preg_match('/[^a-zA-Z\s_0-9]/', $group))
							{
								$_SESSION['targets_alert_message'] = "group names may only contain letters";
								header("location:../targets/");
								exit;
							}
							
						//ensure that the group name exists in the database
						$r = mysql_query("SELECT DISTINCT group_name FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								if($ra['group_name'] == $group)
									{
										$match = 1;
									}
							}
						if($match!=1)
							{
								$_SESSION['targets_alert_message'] = "this group does not exist";
								header("location:../targets/");
								exit;
							}
						
						//query for a list of groups ordered alphabetically
						$r = mysql_query("SELECT id, name, email, group_name FROM targets WHERE group_name = '$group' ORDER BY name") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo "<tr>";
								echo "<td>".$ra['name']."</td>";
								echo "<td>".$ra['email']."</td>";
								echo "<td>".$ra['group_name']."</td>";
								echo "<td align = center><a href=\"target_delete.php?g=".$group."&u=".$ra['id']."\">X</a></td>";
								echo "</tr>";		
							}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
