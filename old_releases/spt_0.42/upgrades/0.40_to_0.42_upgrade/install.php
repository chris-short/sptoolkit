<?php
/**
 * file:		install.php
 * version:		0.42
 * package:		Simple Phishing Toolkit (spt)
 * component:	Upgrade
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

	//starts php session
	session_start();
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - simple phishing toolkit</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="spt.css" type="text/css" />
	</head>
	
	<body>
		
		<!--login wrapper -->
		<div id="login_wrapper">
			
			<!--logo-->
			<div id="login_logo"><img src="images/logo.png" alt="logo"/></div>
			<div id="login_form">
			<?php

				//Step 1 - Begin Installation of v0.04 Upgrade
				if(isset($_POST['step1']) && $_POST['step1']=="complete")
					{
						//set install status to step 2 if step 1 has already been completed
						$_SESSION['install_status']=2;
					}

				if(!isset($_SESSION['install_status']) && !isset($_POST['step1']))
					{
						echo 
							"
								<form id=\"step_1\" method=\"post\" action=\"\">
									<span>Click the upgrade button below to upgrade to version 0.42.</span>
									<br /><br />
									<input type=\"hidden\" name=\"step1\" value=\"complete\" />
									<input type=\"submit\" value=\"Upgrade!\" />
								</form>
							";
					}

				//Step 2 - Upgrade Database
				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==2)
					{
						//connect to database
						include "spt_config/mysql_config.php";

						//setup mysql statement
						$sql = "ALTER TABLE `campaigns_responses` CHANGE `post` `post` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL";

						mysql_query($sql) or die(mysql_error());

						echo "Upgrade complete!<br /><br />
							<form method=\"post\" action=\".\">
								<input type=\"hidden\" name=\"delete_install\" value=\"1\" />
								<input type=\"submit\" value=\"Finish!\" />
							</form>
							";
					}
			?>
			</div>
		</div>
	</body>
</html>
