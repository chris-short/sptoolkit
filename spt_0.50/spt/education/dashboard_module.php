<!--
 * file:		dashboard_module.php
 * version:		2.0
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
-->

<h1><a href="../education">Education</a></h1>
<table>
    <tr>
        <td>Number of Packages</td>
        <?php
            //determine how many campaigns there are
            $r = mysql_query("SELECT * FROM education") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
            echo "<td>".mysql_num_rows($r)."</td>";
        ?>
    </tr>
</table>



