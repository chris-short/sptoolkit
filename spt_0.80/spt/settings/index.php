<?php

/**
 * file:    index.php
 * version: 18.0
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
            function updateTwitter(value) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //send update request
                xmlhttp.open("POST","update_twitter.php",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("twitter_enable="+value);                  
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
                <span class="button"><a href="#add_module"><img src="../images/package_add_sm.png" alt="add" /> Module</a></span>
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
                                        echo "onchange=\"updateTwitter(this.value)\" ";
                                        echo "CHECKED";
                                    }else{
                                        echo "value=\"yes\"";
                                        echo "onchange=\"updateTwitter(this.value)\" ";
                                    }
                                }
                            ?> />
                        </td>

                        <td style="text-align: right;">
                            <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Choose to disable or enable the twitter feed on the home page.</span></a>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </table>
                <div class="spt_table_header">
                    <h1>Modules</h1>
                    <a href="#" class="modules_toggle"><img class="modules_toggle_image" src="../images/bullet_toggle_minus.png" alt="minus" /><img class="modules_toggle_image" src="../images/bullet_toggle_plus.png" style="display:none;" alt="plus" /></a>
                </div>
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