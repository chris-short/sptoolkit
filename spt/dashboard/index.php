<?php
/**
 * file:		index.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Dashboard management
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
			$_SESSION['came_from']='dashboard';
			
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
		<title>spt - dashboard</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_dashboard.css" type="text/css" />
	</head>

	<body>
		<div id="wrapper">

			<!--sidebar-->
			<div id="sidebar">
				<img src="../images/logo.png" alt="logo" />
				<ul>
				<?php
					//lists links dependent upon what modules are installed
					include '../spt_config/mysql_config.php';
					$results=mysql_query('SELECT * FROM modules WHERE enabled=1 ORDER BY name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
					while($row=mysql_fetch_assoc($results))
						{
							echo "<li><a href=\"../".$row['directory_name']."/\">".$row['name']."</a></li>\n";
						}
				?>
				</ul>
				<br />
				<div class="logout">
					<ul>
						<li><a href="../login/logout.php">logout</a></li>
					</ul>
				</div>
			</div>

			<!--content-->
			<div id="content">
				<?php
					
					//scan the root directory
					$dirs = scandir('../');
					
					//for each directory look for dashboard_module.php
					foreach($dirs as $dir)
						{
							if(is_dir('../'.$dir))
								{
									//if dashboard_module.php exists in the directory include it
									if(file_exists('../'.$dir.'/dashboard_module.php'))
										{
											echo "<div class=\"dashboard_module\">";
											include "../".$dir."/dashboard_module.php";
											echo "</div>";
										}
								}
						}
				?>
			</div>
		</div>
	</body>
</html>
