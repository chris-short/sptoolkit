<?php
/**
 * file:		index.php
 * version:		6.0
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
		<title>spt - dashboard</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_dashboard.css" type="text/css" />
	</head>

	<body>
		<div id="wrapper">

			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

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
