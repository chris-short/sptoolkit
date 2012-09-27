<?php

/**
 * file:    index.php
 * version: 29.0
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
        <link rel="stylesheet" href="../includes/jquery-ui.css" type="text/css" />
        <link rel="stylesheet" href="spt_settings.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
        <script src="../includes/jquery.min.js"></script>
        <script src="../includes/jquery-ui.min.js"></script>
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
                $('.smtp_toggle').click(function() {
                    $('#smtp_table').slideToggle('fast');
                    return false;
                });
                $('.smtp_toggle').click(function() {
                    $('.smtp_toggle_image').toggle('fast');
                    return false;
                });
                $('.ldap_toggle').click(function() {
                    $('#ldap_table').slideToggle('fast');
                    return false;
                });
                $('.ldap_toggle').click(function() {
                    $('.ldap_toggle_image').toggle('fast');
                    return false;
                });
                $('.api_toggle').click(function() {
                    $('#api_table').slideToggle('fast');
                    return false;
                });
                $('.api_toggle').click(function() {
                    $('.api_toggle_image').toggle('fast');
                    return false;
                });

            });
        </script>
        <script>
            $(function() {
                $( "#tabs" ).tabs();
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

            <!--popovers-->
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
            <div id="add_smtp_server">
                <div>
                    <table id="add_smtp_server_table">
                        <tr>
                            <form method="POST" action="smtp_add.php" />
                                <tr>
                                    <td colspan=2 style="text-align: left;"><h3>Add SMTP Server</h3></td>
                                    <td style="text-align: right;">
                                        <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Add the appropriate SMTP information for a new SMTP server to be used within campaigns and/or as the system's mail relay for system based email notification.</span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Host</td>
                                    <td style="text-align: left;"><input type="text" name="host" /></td>
                                </tr>
                                <tr>
                                    <td>Port</td>
                                    <td style="text-align: left;"><input type="text" name="port" /></td>
                                </tr>
                                <tr>
                                    <td>SSL</td>
                                    <td style="text-align: left;"><input type="checkbox" name="ssl" /></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td style="text-align: left;"><input type="text" name="username" /></td>    
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td style="text-align: left;"><input type="password" name="password" /></td>
                                </tr>
                                <tr>
                                    <td>Default SMTP Server</td>
                                    <td style="text-align: left;"><input type="checkbox" name="default" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center;"><br /><img id="add_smtp_server_cancel" src="../images/cancel.png" alt="cancel" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                </tr>
                            </form>
                        </tr>
                    </table>
                 </div>
            </div>
            <div id="add_ldap_server">
                <div>
                    <table id="add_ldap_server_table">
                        <tr>
                            <form method="POST" action="ldap_add.php" />
                                <tr>
                                    <td colspan=2 style="text-align: left;"><h3>Add LDAP Server</h3></td>
                                    <td style="text-align: right;">
                                        <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Add the appropriate SMTP information for a new SMTP server to be used within campaigns and/or as the system's mail relay for system based email notification.</span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Host</td>
                                    <td style="text-align: left;"><input type="text" name="host" /></td>
                                </tr>
                                <tr>
                                    <td>Port</td>
                                    <td style="text-align: left;"><input type="text" name="port" /></td>
                                </tr>
                                <tr>
                                    <td>SSL</td>
                                    <td style="text-align: left;"><input type="checkbox" name="ssl" /></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td style="text-align: left;"><input type="text" name="username" /></td>    
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td style="text-align: left;"><input type="password" name="password" /></td>
                                </tr>
                                <tr>
                                    <td>Base DN</td>
                                    <td style="text-align: left;"><input type="text" name="basedn" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                </tr>
                            </form>
                        </tr>
                    </table>
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

            <!--content-->
            <div id="content">
                <!--content-new-->
                <br /><div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">General</a></li>
                        <li><a href="#tabs-2">SMTP</a></li>
                        <li><a href="#tabs-3">LDAP</a></li>
                        <li><a href="#tabs-4">APIs</a></li>
                        <li><a href="#tabs-5">Modules</a></li>
                    </ul>
                    <div id="tabs-1">
                        <table class="standard_table" >
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
                    </div>
                    <div id="tabs-2">
                        <button id="add_smtp_server_button" ><img src="../images/package_add_sm.png" alt="add" /> SMTP Server</button>                
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Host</h3></td>
                                <td><h3>Port</h3></td>
                                <td><h3>SSL?</h3></td>
                                <td><h3>Username</h3></td>
                                <td><h3>Default?</h3></td>
                                <td><h3>Actions</h3></td>
                            </tr>
                            <?php
                                //get all existing SMTP Servers
                                $r = mysql_query("SELECT value FROM settings WHERE setting = 'SMTP'");
                                while ($ra = mysql_fetch_assoc($r)){
                                    $smtp_setting = explode("|",$ra['value']);
                                    echo "
                                        <tr>
                                            <td>".$smtp_setting[0]."</td>
                                            <td>".$smtp_setting[1]."</td>
                                            <td>".$smtp_setting[2]."</td>
                                            <td>".$smtp_setting[3]."</td>
                                            <td>";
                                    if($smtp_setting[5] == "default"){
                                        echo "<img src=\"../images/accept_sm.png\" alt=\"default\" />";
                                    }else{}
                                    echo "</td>
                                    <td><a href=\"delete_smtp.php?smtp=".$ra['value']."\"><img src=\"../images/cancel_sm.png\" alt=\"delete\" /></a></td>
                                        </tr>
                                    ";
                                }
                            ?>
                        </table>  
                    </div>
                    <div id="tabs-3">
                        <span><a id="add_ldap_server_button"><img src="../images/package_add_sm.png" alt="add" /> LDAP Server</a></span>                
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Host</h3></td>
                                <td><h3>Port</h3></td>
                                <td><h3>SSL?</h3></td>
                                <td><h3>Username</h3></td>
                                <td><h3>Password</h3></td>
                                <td><h3>Base DN</h3></td>
                                <td><h3>Actions</h3></td>
                            </tr>
                            <?php
                                //get all existing ldap Servers
                                $r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
                                while ($ra = mysql_fetch_assoc($r)){
                                    $ldap_setting = explode("|",$ra['value']);
                                    echo "
                                        <tr>
                                            <td>".$ldap_setting[0]."</td>
                                            <td>".$ldap_setting[1]."</td>
                                            <td>".$ldap_setting[2]."</td>
                                            <td>".$ldap_setting[3]."</td>
                                            <td>";
                                    if(strlen($ldap_setting[4]) > 0){
                                        echo "********";
                                    }else{}
                                    echo "</td>
                                            <td>".$ldap_setting[5]."</td>
                                    <td><a href=\"delete_ldap.php?ldap=".$ra['value']."\"><img src=\"../images/cancel_sm.png\" alt=\"delete\" /></a></td>
                                        </tr>
                                    ";
                                }
                            ?>
                        </table>                
                    </div>
                    <div id="tabs-4">
                        <table class="standard_table" >
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
                    </div>
                    <div id="tabs-5">
                        <span><a id="add_module_button"><img src="../images/package_add_sm.png" alt="add" /> Module</a></span>
                        <table class="standard_table" >
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
            </div>
        </div>        
    </body>
    <!--scripts-->
    <script>
        $("#add_smtp_server_button").click(function () {
          $("#add_smtp_server").show();
          return false;
        });
        $("#add_smtp_server_cancel").click(function () {
          $("#add_smtp_server").hide();
          return false;
        });
    </script>
</html>
