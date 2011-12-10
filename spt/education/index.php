<?php
/**
 * file:		index.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Education
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
			$_SESSION['came_from']='education';
			
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
		<title>spt - education</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_education.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--popovers-->
			<form method="post" action="upload_package.php" enctype="multipart/form-data">
				<div id="add_package">
					<div>
						<table id="add_package_table">
							<tr>
								<td>Name</td>
								<td><input name="name" /></td>
								<td>
									<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Enter the new package's name and description.  You also have the option to upload a zip file with your package's content or leave the upload field blank and a default package will be created for you that you you may then edit to your liking.</span></a>
								</td>
							</tr>
							<tr>
								<td>Description</td>
								<td><textarea name="description" cols=50 rows=4></textarea></td>
							</tr>
							<tr>
								<td><i>(optional)</i></td>
								<td><input type="file"  name="file" /></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<br />
									<a href=""><img src="../images/x.png" alt="close" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="image" src="../images/plus.png" alt="add" />
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
			<?php
				//check to see if the alert session is set
				if(isset($_SESSION['education_alert_message']))
					{
						//create alert popover
						echo "<div id=\"alert\">";

						//echo the alert message
						echo "<div>".$_SESSION['education_alert_message']."<br /><br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";
						
						//unset the seession
						unset ($_SESSION['education_alert_message']);
						
						//close alert popover
						echo "</div>";
					}
			?>


			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<span class="button"><a href="#add_package"><img src="../images/plus_sm.png" alt="add" /> Package</a></span>
				<table class="spt_table">
					<tr>
						<td><h3>ID</h3></td>
						<td><h3>Name</h3></td>
						<td><h3>Description</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					
					<?php
					
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//pull in list of all templates
						$r = mysql_query("SELECT * FROM education") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo	"
									<tr>
										<td><a href=\"".$ra['id']."\" target=\"_blank\">".$ra['id']."</a></td>\n
										<td>".$ra['name']."</td>\n
										<td>".$ra['description']."</td>\n
										<td><a href=\"delete_package.php?t=".$ra['id']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>\n
										
									</tr>\n";
							}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
