<!--
 * file:    dashboard_module.php
 * version: 3.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Module Template
 * copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
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
-->

<h1><a href="../module_name">Module name</a></h1>
<table>
    <tr>
        <td>Module data item of interest</td>
        <?php
        //what is this item?
        $r = mysql_query ( "SELECT ??? FROM your_module_table" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
        echo "<td>"Your data or variables go here"</td>";
        ?>
    </tr>
    <tr>
        <td>Module data item of interest</td>
        <?php
        //what is this item?
        $r = mysql_query ( "SELECT ??? FROM your_module_table" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
        echo "<td>"Your data or variables go here"</td>";
        ?>
    </tr>
</table>