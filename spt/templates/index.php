<?php
/**
 * file:		index.php
 * version:		15.0
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

	// verify session is authenticated and not hijacked
	$includeContent = "../includes/is_authenticated.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_authenticated.php');
	}
	
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - templates</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_templates.css" type="text/css" />
	
		<!--scripts-->
		<script type="text/javascript" src="../includes/escape.js"></script>

	</head>
	<body>
		<div id="wrapper">
			<!--popovers-->
			<form method="post" action="upload_template.php" enctype="multipart/form-data">
				<div id="add_template">
					<div>
						<table id="add_template_zip">
							<tr>
								<td>Name</td>
								<td><input name="name" /></td>
								<td>
									<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Select the template file to be uploaded and click the add button.  You can only upload templates packaged using the ZIP file format.<br /><br />Be sure to see the documentation section of the spt website for full details on the required contents of a template.</span></a>
								</td>
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

			<form method="post" action="scrape_it.php" enctype="multipart/form-data">
				<div id="add_scrape">
					<div>
						<table id="add_scrape_table">
							<tr>
								<td>Name</td>
								<td><input name="name" /></td>
								<td>
									<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Enter the name, description and full URL for the site to be scraped as well as the details of the email that will be sent when using this template in a campaign.<br /><br />To find out what the <strong>correct and full</strong> URL is, browse to the site first in your browser.<br /><br />For example, if you enter <strong>http://www.targetsite.com</strong>  into your browser and the address changes to <strong>http://www.targetsite.com?sid=42</strong>, then that's the actual URL you want to enter here.  Anything else will most likely result in you scraping an error 302 page instead of the actual target site.<br /><br />The fake link is the link that will be presented in the email to the user that essentially masks the formulated phishing link<br /><br />The scraper may or may not always parse the target site correctly due to the extreme wide variety of website coding methodologies.  We recommend downloading and installing the editor module if you are comfortable with manually editing html so that you may fix any problems that might exist after the scrape.  Also, please let us know via the spt website contact form of any issues you see including the site you had problems with.<br /><br /><strong>NOTE:</strong>  After you scrape a site, you will wind up with 'index.htm', 'email.php' and 'return.htm' in a directory reflecting the template id within the templates directory.  You will need to manually edit these files by browsing to them or using the available edior module if you'd like to customize your scrapes further.</span></a>
								</td>
							</tr>
							<tr>
								<td>Description</td>
								<td><textarea name="description" cols=50 rows=4></textarea></td>
							</tr>
							<tr>
								<td>URL</td>
								<td><input name="url" /></td>
							</tr>
							<tr>
								<td></td>
								<td><td>
							</tr>
							<tr>
								<td><h3>Email</h3></td>
								<td></td>
							</tr>
							<tr>
								<td>Subject</td>
								<td><input name="email_subject" /></td>
							</tr>
							<tr>
								<td>From Address</td>
								<td><input name="email_from" /></td>
							</tr>
							<tr>
								<td>Title</td>
								<td><input name="email_title" /></td>
							</tr>
							<tr>
								<td>Message</td>
								<td><textarea name="email_message" cols=50 rows=4></textarea></td>
								<td>
									<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>You can enter the following variables that will be changed into their relavant values on runtime:<br /><br />@fname - Target's first name<br />@lname - Target's last name</span></a>
								</td>
							</tr>
							<tr>
								<td>Fake Link</td>
								<td><input name="email_fake_link" /></td>
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
				if(isset($_SESSION['alert_message']))
					{
						//create alert popover
						echo "<div id=\"alert\">";

						//echo the alert message
						echo "<div>".$_SESSION['alert_message']."<br /><br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";
						
						//unset the seession
						unset ($_SESSION['alert_message']);
						
						//close alert popover
						echo "</div>";
					}
			?>


			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<span class="button"><a href="#add_template"><img src="../images/plus_sm.png" alt="add" /> Template</a></span>
				<span class="button"><a href="#add_scrape"><img src="../images/plus_sm.png" alt="add" /> Scrape</a></span>
				<table class="spt_table">
					<tr>
						<td><h3>ID</h3></td>
						<td><h3>Name</h3></td>
						<td><h3>Description</h3></td>
						<td><h3>Screenshot</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					
					<?php
					
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//pull in list of all templates
						$r = mysql_query("SELECT * FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo	"
									<tr>
										<td><a href=\"".$ra['id']."\" target=\"_blank\">".$ra['id']."</a></td>\n
										<td><a href=\"".$ra['id']."\" target=\"_blank\">".$ra['name']."</a></td>\n
										<td>".$ra['description']."</td>\n
										<td><img class= \"drop_shadow\" src=\"".$ra['id']."/screenshot.png\" alt=\"missing screenshot\" /></td>\n
										<td><a href=\"delete_template.php?t=".$ra['id']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>\n
										
									</tr>\n";
							}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
