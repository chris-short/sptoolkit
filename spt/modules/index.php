<?php
/**
 * file:		index.php
 * version:		3.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Module management
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
			$_SESSION['came_from']='modules';
			
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
		<title>spt - modules</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_modules.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">

			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">

				<?php
					//check to see if there are any alerts
					if(isset($_SESSION['module_alert_message']))
						{
							//echo the alert message
							echo "<h2>".$_SESSION['module_alert_message']."</h2>";
							
							//clear the alert session after it is written
							unset($_SESSION['module_alert_message']);
						}
				?>
				<div>
					<table id="module_load" class="spt_table">
						<tr>
							<td><h3>+ Add Module</h3></td>
							<td>
								<form action="module_upload.php" method="post" enctype="multipart/form-data">
									<input type="file"  name="file" />
									<input type="submit" value="Upload" /> &#60; click here to upload
								</form>
							</td>
						</tr>
					</table>
				</div>
				<div>
					<table id="installed_module_list" class="spt_table">
						<tr>
							<td><h3>Name</h3></td>
							<td><h3>Author</h3></td>
							<td><h3>Dependencies</h3></td>
							<td><h3>Description</h3></td>
							<td><h3>Uninstall</h3></td>
						</tr>
						<?php
							//connect to database
							include "../spt_config/mysql_config.php";
							
							//pull in all installed modules from the modules table
							$r=mysql_query('SELECT * FROM modules ORDER BY core, name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
							while($ra=mysql_fetch_assoc($r))
								{
									echo "
											<tr>\n
												<td>".$ra['name']."</td>\n
												<td>".$ra['author']."</td>\n
												<td class=\"td_center\">";

									//set the current module name to a temp variable
									$t = $ra['name'];

									//query for module dependencies
									$r2=mysql_query("SELECT * FROM modules_dependencies WHERE module = '$t'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									while($ra2=mysql_fetch_assoc($r2))
										{
											echo $ra2['depends_on']."<br />";
										}
									
									echo "
												<td class=\"table_description\">".$ra['description']."</td>\n
												<td class=\"td_center\">";
									
									//check to see if the module is a core component or not and if there are any dependencies
									$r3=mysql_query("SELECT * FROM modules_dependencies WHERE depends_on = '$t'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									if(mysql_num_rows($r3) > 0 || $ra['core']==1)
										{
											echo "--";
										}
									else
										{
											echo "<a href=\"module_uninstall.php?m=".$t."\"><h2>X</h2></a>";
										}

									echo "
												</td>\n
											</tr>";
								}
						?>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
