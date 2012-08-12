<!--
 * file:    dashboard_module.php
 * version: 9.0
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

<script type="text/javascript" src="../includes/highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../includes/highcharts/js/modules/exporting.js"></script>
<article class="tabs">
    <section id="phish_pie">
        <a class="navigation" href="#phish_pie"><span>Phish Pie</span></a>
        <div id="phish_pie_filters">
            <form action="#phish_pie" method="POST">
                <table>
                    <tr>
                        <td colspan="2"><h3>Filters</h3></td>
                    </tr>
                    <tr>
                        <td>Campaign</td>
                        <td>

                                <select name="pp_campaign">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the campaign names
                                        $r = mysql_query("SELECT id,campaign_name FROM campaigns");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['pp_campaign']) && $_REQUEST['pp_campaign'] == $ra['id']){
                                                echo "<option value=\"".$ra['id']."\" selected >".$ra['campaign_name']."</option>";
                                            } else{
                                                echo "<option value=\"".$ra['id']."\">".$ra['campaign_name']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Browser</td>
                        <td>

                                <select name="pp_browser">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the browsers
                                        $r = mysql_query("SELECT DISTINCT(browser) as browser FROM campaigns_responses WHERE browser IS NOT NULL");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['pp_browser']) && $_REQUEST['pp_browser'] == $ra['browser']){
                                                echo "<option value=\"".$ra['browser']."\" selected>".$ra['browser']."</option>";
                                            }else{
                                                echo "<option value=\"".$ra['browser']."\">".$ra['browser']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><input type="image" src="../images/filter.png" alt="filter"/></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="phish_pie_container"></div>
    </section>
    <section id="bad_targets">
        <a class="navigation" href="#bad_targets"><span>Bad Targets</span></a>
                <div id="bad_targets_filters">
            <form action="#bad_targets" method="POST">
                <table>
                    <tr>
                        <td colspan="2"><h3>Filters</h3></td>
                    </tr>
                    <tr>
                        <td>Campaign</td>
                        <td>

                                <select name="bt_campaign">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the campaign names
                                        $r = mysql_query("SELECT id,campaign_name FROM campaigns");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['bt_campaign']) && $_REQUEST['bt_campaign'] == $ra['id']){
                                                echo "<option value=\"".$ra['id']."\" selected >".$ra['campaign_name']."</option>";
                                            } else{
                                                echo "<option value=\"".$ra['id']."\">".$ra['campaign_name']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Browser</td>
                        <td>

                                <select name="bt_browser">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the browsers
                                        $r = mysql_query("SELECT DISTINCT(browser) as browser FROM campaigns_responses WHERE browser IS NOT NULL");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['bt_browser']) && $_REQUEST['bt_browser'] == $ra['browser']){
                                                echo "<option value=\"".$ra['browser']."\" selected>".$ra['browser']."</option>";
                                            }else{
                                                echo "<option value=\"".$ra['browser']."\">".$ra['browser']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><input type="image" src="../images/filter.png" alt="filter"/></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="bad_targets_container"></div>
    </section>
    <section id="email_stats">
        <a class="navigation" href="#email_stats"><span>Email Stats</span></a>
        <div id="email_stats_filters">
            <form action="#email_stats" method="POST">
                <table>
                    <tr>
                        <td colspan="2"><h3>Filters</h3></td>
                    </tr>
                    <tr>
                        <td>Campaign</td>
                        <td>

                                <select name="es_campaign">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the campaign names
                                        $r = mysql_query("SELECT id,campaign_name FROM campaigns");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['es_campaign']) && $_REQUEST['es_campaign'] == $ra['id']){
                                                echo "<option value=\"".$ra['id']."\" selected >".$ra['campaign_name']."</option>";
                                            } else{
                                                echo "<option value=\"".$ra['id']."\">".$ra['campaign_name']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><input type="image" src="../images/filter.png" alt="filter"/></td>
                    </tr>
                </table>
            </form>
        </div>        
        <div id="email_stats_container"></div>
    </section>
    <section id="browser_stats">
        <a class="navigation" href="#browser_stats"><span>Browser Stats</span></a>
                <div id="browser_stats_filters">
            <form action="#browser_stats" method="POST">
                <table>
                    <tr>
                        <td colspan="2"><h3>Filters</h3></td>
                    </tr>
                    <tr>
                        <td>Campaign</td>
                        <td>

                                <select name="bs_campaign">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the campaign names
                                        $r = mysql_query("SELECT id,campaign_name FROM campaigns");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['bs_campaign']) && $_REQUEST['bs_campaign'] == $ra['id']){
                                                echo "<option value=\"".$ra['id']."\" selected >".$ra['campaign_name']."</option>";
                                            } else{
                                                echo "<option value=\"".$ra['id']."\">".$ra['campaign_name']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Browser</td>
                        <td>

                                <select name="bs_browser">
                                    <option value="All">All</option>
                                    <?php
                                        //connect to database
                                        include "../spt_config/mysql_config.php";

                                        //get all the browsers
                                        $r = mysql_query("SELECT DISTINCT(browser) as browser FROM campaigns_responses WHERE browser IS NOT NULL");
                                        while($ra = mysql_fetch_assoc ( $r)){
                                            if(isset($_REQUEST['bs_browser']) && $_REQUEST['bs_browser'] == $ra['browser']){
                                                echo "<option value=\"".$ra['browser']."\" selected>".$ra['browser']."</option>";
                                            }else{
                                                echo "<option value=\"".$ra['browser']."\">".$ra['browser']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><input type="image" src="../images/filter.png" alt="filter"/></td>
                    </tr>
                </table>
            </form>
        </div>        
        <div id="browser_stats_container"></div>
    </section>
    <section id="simple_stats">
        <a class="navigation" href="#simple_stats"><span>Simple Stats</span></a>
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
<?php
//scan the root directory
$dirs = scandir ( '../' );

foreach ( $dirs as $dir ) {
    //if dashboard_module.php exists in the directory include it
    if ( file_exists ( '../' . $dir . '/dashboard_module.php') && $dir != 'campaigns' ) {
        include "../" . $dir . "/dashboard_module.php";
    }
}
?>

            </table>
        </div>
    </section>
</article>
