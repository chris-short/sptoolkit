<!--
 * file:    dashboard_module.php
 * version: 4.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Campaign management
 * copyright:   Copyright (C) 2011 The SPT Project. All rights reserved.
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

<article class="tabs">
    <section id="risk_pie">
        <a href="#risk_pie"><span>Risk Pie</span></a>
        <div>Pie Chart of overall status of links/posts/etc</div>
    </section>
    <section id="bad_targets">
        <a href="#bad_targets"><span>Bad Targets</span></a>
        <div>bar chart of targets who fall for phishing the most</div>
    </section>
    <section id="email_stats">
        <a href="#email_stats"><span>Email Stats</span></a>
        <div>pie chart of email status unknown/failed/success</div>
    </section>
    <section id="browser_stats">
        <a href="#browser_stats"><span>Browser Stats</span></a>
        <div>pie chart of browsers</div>
    </section>
    <section id="simple_stats">
        <a href="#simple_stats"><span>Simple Stats</span></a>
        <div>    
            <table>
                <tr>
                    <td>Number of Campaigns</td>
                    <?php
                    //determine how many campaigns there are
                    $r = mysql_query ( "SELECT * FROM campaigns" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    echo "<td>" . mysql_num_rows ( $r ) . "</td>";
                    ?>
                </tr>
                <tr>
                    <td>Number of Phished Targets</td>
                    <?php
                    //determine how many targets have been phished
                    $r = mysql_query ( "SELECT * FROM campaigns_responses" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    echo "<td>" . mysql_num_rows ( $r ) . "</td>";
                    ?>
                </tr>
                <tr>
                    <td>Total Links Clicked</td>
                    <?php
                    //determine how many targets have clicked the link
                    $r = mysql_query ( "SELECT SUM(link) AS total_count FROM campaigns_responses" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "<td>" . $ra['total_count'] . "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <td>Total Posts</td>
                    <?php
                    //determine how many targets have posted data
                    $r = mysql_query ( "SELECT COUNT(post) AS total_count FROM campaigns_responses WHERE post != \"\"" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "<td>" . $ra['total_count'] . "</td>";
                    }
                    ?>
                </tr>
            </table>
        </div>
    </section>
</article>
