<?php

/**
 * file:    index.php
 * version: 23.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Settings
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
 * */

// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}
?>

<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - settings</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_settings.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>
            $(function() {
                $('.modules_toggle').click(function() {
                    $('#installed_module_list').slideToggle('fast');
                    return false;
                });
                $('.modules_toggle').click(function() {
                    $('.modules_toggle_image').toggle('fast');
                    return false;
                });
                $('.general_toggle').click(function() {
                    $('#general_table').slideToggle('fast');
                    return false;
                });
                $('.general_toggle').click(function() {
                    $('.general_toggle_image').toggle('fast');
                    return false;
                });
            });
        </script>
        <script language="Javascript" type="text/javascript">
            function updateSetting(setting,value) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //send update request
                xmlhttp.open("POST","update_setting.php",true);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("setting="+setting+"&value="+value); 
            }
        </script>        
    </head>
    <body>
        <div id="wrapper">

            <!--sidebar-->
<?php include '../includes/sidebar.php'; ?>                 

            <!--content-->
            <div id="content">
                <div id="add_module">
                    <div>
                        <form action="module_upload.php" method="post" enctype="multipart/form-data">
                            <table id="upload_module">
                                <tr>
                                    <td style="text-align: left;"><h3>Add Module</h3></td>
                                    <td style="text-align: right;">
                                        <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the module file to be uploaded and click the add button.  You can only upload modules packaged using the ZIP file format.<br /><br />Be sure to see the documentation section of the spt website for full details on the required contents of a module.</span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="file"  name="file" />
                                    </td>
                                </tr>
                                <?php
                                    if(isset($_SESSION['alert_message'])){
                                        echo "
                                            <tr>
                                                <td colspan=2 class=\"popover_alert_message\" >".$_SESSION['alert_message']."</td>
                                            </tr>";
                                    }
                                ?>
                            <tr>
                                <td colspan="2" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                            </tr>
                            </table>
                        </form>
                    </div>
                </div>
<?php
//check to see if there are any alerts
if ( isset ( $_SESSION['alert_message'] ) ) {
    //create alert popover
    echo "<div id=\"alert\">";

    //echo the alert message
    echo "<div>" . $_SESSION['alert_message'] . "<br /><br /><a href=\"\"><img src=\"../images/accept.png\" alt=\"close\" /></a></div>";

    //clear the alert session after it is written
    unset ( $_SESSION['alert_message'] );

    //close alert popover
    echo "</div>";
}
?>
                <div class="spt_table_header">
                    <h1>General</h1>
                    <a href="#" class="general_toggle"><img class="general_toggle_image" src="../images/bullet_toggle_minus.png" alt="minus" /><img class="general_toggle_image" src="../images/bullet_toggle_plus.png" style="display:none;" alt="plus" /></a>
                </div>
                <table id="general_table" class="spt_table">
                    <tr>
                        <td>Enable Twitter Feed</td>
                        <td>
                            
                            <input type="checkbox" name="twitter_enable" style="text-align:left;" <?php 
                                include('../spt_config/mysql_config.php');
                                $r = mysql_query('SELECT value FROM settings WHERE setting = "twitter_enable"');
                                while($ra = mysql_fetch_assoc($r)){
                                    if($ra['value'] == 1){
                                        echo "value=\"no\"";
                                        echo "onchange=\"updateSetting('twitter',this.value)\" ";
                                        echo "CHECKED";
                                    }else{
                                        echo "value=\"yes\"";
                                        echo "onchange=\"updateSetting('twitter',this.value)\" ";
                                    }
                                }
                            ?> />
                        </td>
                        <td style="text-align: right;">
                            <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Choose to disable or enable the twitter feed on the home page.</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Backup Database</td>
                        <td><a href="backup_db.php">Download</a></td>
                    </tr>
                    <tr>
                        <td>Restore Database</td>
                        <td>
                            <form action="restore_db.php" method="post" enctype="multipart/form-data" >
                                <input type="file" name="file" />
                                <input type="submit" value="Restore Now" />
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>System Time</td>
                        <td>Current: <?php print strftime('%c'); ?></td>
                        <td style="text-align: right;">
                            <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>If the date/time is wrong, have your system administrator update the system time on the server hosting this application.  A simple way to update the time would be to run a command such as this at the command line as root: <strong>ntpdate pool.ntp.org</strong>.</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Time Zone</td>
                        <td>
                            <?php 
                                //connect to database
                                include '../spt_config/mysql_config.php';
                                //get current timezone
                                $r = mysql_query("SELECT value FROM settings WHERE setting = 'time_zone'");
                                while($ra = mysql_fetch_assoc($r)){
                                    $tz = $ra['value'];
                                }
                            ?>
                                <select name="timezones" onchange="updateSetting('timezone',this.value)">
                                    <option value="-12.0" <?php if($tz == "-12.0"){echo "SELECTED";} ?>>(GMT -12:00) Eniwetok, Kwajalein</option>
                                    <option value="-11.0" <?php if($tz == "-11.0"){echo "SELECTED";} ?>>(GMT -11:00) Midway Island, Samoa</option>
                                    <option value="-10.0" <?php if($tz == "-10.0"){echo "SELECTED";} ?>>(GMT -10:00) Hawaii</option>
                                    <option value="-9.0" <?php if($tz == "-9.0"){echo "SELECTED";} ?>>(GMT -9:00) Alaska</option>
                                    <option value="-8.0" <?php if($tz == "-8.0"){echo "SELECTED";} ?>>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                    <option value="-7.0" <?php if($tz == "-7.0"){echo "SELECTED";} ?>>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                    <option value="-6.0" <?php if($tz == "-6.0"){echo "SELECTED";} ?>>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                    <option value="-5.0" <?php if($tz == "-5.0"){echo "SELECTED";} ?>>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                    <option value="-4.0" <?php if($tz == "-4.0"){echo "SELECTED";} ?>>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                    <option value="-3.5" <?php if($tz == "-3.5"){echo "SELECTED";} ?>>(GMT -3:30) Newfoundland</option>
                                    <option value="-3.0" <?php if($tz == "-3.0"){echo "SELECTED";} ?>>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                    <option value="-2.0" <?php if($tz == "-2.0"){echo "SELECTED";} ?>>(GMT -2:00) Mid-Atlantic</option>
                                    <option value="-1.0" <?php if($tz == "-1.0"){echo "SELECTED";} ?>>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
                                    <option value="0.0" <?php if($tz == "0.0"){echo "SELECTED";} ?>>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                    <option value="1.0" <?php if($tz == "1.0"){echo "SELECTED";} ?>>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
                                    <option value="2.0" <?php if($tz == "2.0"){echo "SELECTED";} ?>>(GMT +2:00) Kaliningrad, South Africa</option>
                                    <option value="3.0" <?php if($tz == "3.0"){echo "SELECTED";} ?>>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                    <option value="3.5" <?php if($tz == "3.5"){echo "SELECTED";} ?>>(GMT +3:30) Tehran</option>
                                    <option value="4.0" <?php if($tz == "4.0"){echo "SELECTED";} ?>>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                    <option value="4.5" <?php if($tz == "4.5"){echo "SELECTED";} ?>>(GMT +4:30) Kabul</option>
                                    <option value="5.0" <?php if($tz == "5.0"){echo "SELECTED";} ?>>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                    <option value="5.5" <?php if($tz == "5.5"){echo "SELECTED";} ?>>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                    <option value="5.75" <?php if($tz == "5.75"){echo "SELECTED";} ?>>(GMT +5:45) Kathmandu</option>
                                    <option value="6.0" <?php if($tz == "6.0"){echo "SELECTED";} ?>>(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                    <option value="7.0" <?php if($tz == "7.0"){echo "SELECTED";} ?>>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                    <option value="8.0" <?php if($tz == "8.0"){echo "SELECTED";} ?>>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                    <option value="9.0" <?php if($tz == "9.0"){echo "SELECTED";} ?>>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                    <option value="9.5" <?php if($tz == "9.5"){echo "SELECTED";} ?>>(GMT +9:30) Adelaide, Darwin</option>
                                    <option value="10.0" <?php if($tz == "10.0"){echo "SELECTED";} ?>>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                    <option value="11.0" <?php if($tz == "11.0"){echo "SELECTED";} ?>>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                    <option value="12.0" <?php if($tz == "12.0"){echo "SELECTED";} ?>>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>                                </select>
                                </select>
                        </td>
                    </tr>
                </table>
                <div class="spt_table_header">
                    <h1>APIs</h1>
                    <a href="#" class="api_toggle"><img class="api_toggle_image" src="../images/bullet_toggle_minus.png" alt="minus" /><img class="api_toggle_image" src="../images/bullet_toggle_plus.png" style="display:none;" alt="plus" /></a>
                </div>
                <table id="api_table" class="spt_table">
                    <tr>
                        <td>Google API Key</td>
                        <td><input type="text" name="google_api_key" <?php
                            //connect to database
                            include '../spt_config/mysql_config.php';
                            //get current API value
                            $r = mysql_query("SELECT value FROM settings WHERE setting = 'google_api'");
                            while ($ra = mysql_fetch_assoc($r)){
                                $api_key = $ra['value'];
                                echo "value=\"".$api_key."\" ";
                            }
                        ?>size="80" onchange="updateSetting('google_api',this.value)"/></td>
                    </tr>
                </table>                
                <div class="spt_table_header">
                    <h1>Modules</h1>
                    <a href="#" class="modules_toggle"><img class="modules_toggle_image" src="../images/bullet_toggle_minus.png" alt="minus" /><img class="modules_toggle_image" src="../images/bullet_toggle_plus.png" style="display:none;" alt="plus" /></a>
                </div>
                <span class="settings_button"><a href="#add_module"><img src="../images/package_add_sm.png" alt="add" /> Module</a></span>
                <table id="installed_module_list" class="spt_table">
                    <tr>
                        <td><h3>Name</h3></td>
                        <td><h3>Dependencies</h3></td>
                        <td><h3>Description</h3></td>
                        <td><h3>Uninstall</h3></td>
                    </tr>
<?php
//connect to database
include "../spt_config/mysql_config.php";

//pull in all installed modules from the modules table
$r = mysql_query ( 'SELECT * FROM settings_modules ORDER BY core, name' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    echo "
                    <tr>\n
                        <td>" . $ra['name'] . "</td>\n
                        <td class=\"td_center\">";

    //set the current module name to a temp variable
    $t = $ra['name'];

    //query for module dependencies
    $r2 = mysql_query ( "SELECT * FROM settings_modules_dependencies WHERE module = '$t'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
        echo $ra2['depends_on'] . "<br />";
    }

    echo "
                        <td  id=\"module_description\">" . $ra['description'] . "</td>\n
                        <td class=\"td_center\">";

    //check to see if the module is a core component or not and if there are any dependencies
    $r3 = mysql_query ( "SELECT * FROM settings_modules_dependencies WHERE depends_on = '$t'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    if ( mysql_num_rows ( $r3 ) > 0 || $ra['core'] == 1 ) {
        echo "--";
    } else {
        echo "<a href=\"module_uninstall.php?m=" . $t . "\"><img src=\"../images/package_delete_sm.png\" alt=\"delete\" /></a>";
    }

    echo "
                        </td>\n
                </tr>";
}
?>
                </table>
            </div>
        </div>
    </body>
</html>