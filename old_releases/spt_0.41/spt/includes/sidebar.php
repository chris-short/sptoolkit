<?php
/**
 * file:		sidebar.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Core Files
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

echo 
	"
		<div id=\"sidebar\">
			<img src=\"../images/logo.png\" alt=\"logo\" />
			<ul>
				<li><a href=\"../\"><img src=\"../images/dashboard.png\" alt=\"dashboard\" /></a></li>
			</ul>
			<ul>
	";
	//lists links dependent upon what modules are installed
	include '../spt_config/mysql_config.php';
	$results=mysql_query('SELECT * FROM modules WHERE enabled=1 AND name != "Dashboard" ORDER BY name') or die('<div id="die_error">There is a problem with the database...please try again later</div>');
	while($row=mysql_fetch_assoc($results))
		{
			echo "<li><a href=\"../".$row['directory_name']."/\">".$row['name']."</a></li>\n";
		}

echo
	"
			</ul>
				<div class=\"logout\">
			<ul>
				<li><a href=\"../login/logout.php\"><img src=\"../images/logout.png\" alt=\"logout\" class=\"center\" /></a></li>
			</ul>
		</div>
		<div id=\"spt\">
			<ul>
				<br />
				<li><a href=\"http://sptoolkit.com\">simple phishing toolkit</a></li>
				<li><a href=\"http://sptoolkit.com\">© 2011 the spt project</a></li>
			</ul>
		</div>
		</div>
	";
?>