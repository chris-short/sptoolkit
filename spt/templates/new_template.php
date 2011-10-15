<?php
/**
 * file:		new_template.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Template management
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
			$_SESSION['came_from']='templates';
			
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
		<title>spt - new template</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_templates.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<!--sidebar-->
			<div id="sidebar">
				<img src="../images/logo.png" />
				<ul>
				<?php
					//lists links dependent upon what modules are installed
					include '../spt_config/mysql_config.php';
					$results=mysql_query('SELECT * FROM modules WHERE enabled=1 ORDER BY name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
					while($row=mysql_fetch_assoc($results)){
						echo "<li><a href=\"../";
						echo $row['directory_name'];
						echo "/\">";
						echo $row['name'];
						echo "</a></li>\n";
						}
				?>
				</ul>
				<br />
				<div class="logout"><ul><li><a href="../login/logout.php">logout</a></li></ul></div>
			</div>

			<!--content-->
			<div id="content">
				<a href="../templates">< back</a>
				<?php
					//check to see if the alert session is set
					if(isset($_SESSION['new_template_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['new_template_alert_message']."</h2>";
							
							//unset the seession
							unset ($_SESSION['new_template_alert_message']);				
						}
				?>
				<form method="post" action="upload_template.php" enctype="multipart/form-data">
					<table class="spt_table">
						<tr>
							<td>Name</td>
							<td><input name="name" /></td>
						</tr>
						<tr>
							<td>Description</td>
							<td><textarea name="description" cols=50 rows=4></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="file"  name="file" /></td>
						</tr>
						<tr>
							<td></td>
							<td>*zip files only<br />*make sure you have all the necessary parts to make a template file (click <a href="sample_template.zip">here</a> for a sample)</td>
						</tr>
						<tr>
							<td><input type="submit" value="Upload" /></td>
						</tr>
					</table>
				</form>
				<form method="post" action="scrape_it.php">
					<table class="spt_table">
						<tr>
							<td>URL</td>
							<td><input type="text" name="url" size=75 /></td>
						</tr>
						<tr>
							<td><input type="submit" value="Scrape!" /></td>
							<td></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
