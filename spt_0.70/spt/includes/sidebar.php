<?php

/**
 * file:    sidebar.php
 * version: 12.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Core Files
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license: GNU/GPL, see license.htm.
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
 * */

// show logo and dashboard link
//lists core modules in the order of usage for a new installation -> starting a campaign
echo "
    <div id=\"sidebar\">
        <img src=\"../images/logo.png\" alt=\"logo\" />
        <ul>
            <li><a href=\"../#phish_pie\"><img src=\"../images/house.png\" alt=\"dashboard\" /></a></li>
        </ul>        
        <ul>
            <li></li>
            <li><a href=\"../quickstart/#intro\">Quick Start</a></li>
            <li><a href=\"../targets\">Targets</a></li>
            <li><a href=\"../templates\">Templates</a></li>
            <li><a href=\"../education\">Education</a></li>
            <li><a href=\"../campaigns\">Campaigns</a></li>
            <li><a href=\"../users\">Users</a></li>
            <li><a href=\"../modules\">Modules</a></li>
        </ul>
        <div style=\"border-top: 1px solid #eee;\"></div> 
        <ul>
            <li></li>";

//lists links dependent upon what modules are installed
include '../spt_config/mysql_config.php';
$results = mysql_query ( 'SELECT * FROM modules WHERE core != "1" ORDER BY name' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $row = mysql_fetch_assoc ( $results ) ) {
    echo "<li><a href=\"../" . $row['directory_name'] . "/\">" . $row['name'] . "</a></li>\n";
}

$version = file_get_contents ( '../includes/version.txt' );

echo "
        </ul>
        <div class=\"logout\">
            <ul>
                <li><a href=\"../login/logout.php\"><img src=\"../images/door_out.png\" alt=\"logout\" class=\"center\" /></a></li>
            </ul>
        </div>
        <div id=\"spt\">
        <ul>
            <br />
            <li>simple phishing toolkit</li>
            <li>© the spt project<br /><br /></li>
            <li>" . $version . "</li>
            <li><a href=\"http://www.sptoolkit.com\" target=\"_blank\">sptoolkit.com</a> | <a href=\"https://twitter.com/#!/sptoolkit\" target=\"_blank\">@sptoolkit</a><br /><br /></li>
            <li><a href=\"http://www.sptoolkit.com/documentation\" target=\"_blank\">Documentation</a> | <a href=\"http://www.sptoolkit.com/forums\" target=\"_blank\">Support</a>
            <li><a href=\"http://www.sptoolkit.com/download\" target=\"_blank\">Download</a> | <a href=\"http://www.sptoolkit.com/contact\" target=\"_blank\">Contact</a>
        </ul>
        </div>
    </div>";
?>
