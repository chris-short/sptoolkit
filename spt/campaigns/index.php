<?php
/**
 * file:    index.php
 * version: 73.0
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
        <title>spt - campaigns</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/jquery-ui.css" type="text/css" />
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_campaigns.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
        <script src="../includes/jquery.min.js"></script>
        <script src="../includes/jquery-ui.min.js"></script>
        <script>
            $(function() {
                $( "#tabs, #wizard" ).tabs();
            });
        </script>
        <script type="text/javascript">
            //get campaign id
            <?php
                if ( isset ( $_REQUEST['c'] ) ) {
                    echo "campaign_id = \"" . $_REQUEST['c'] . "\";";
                }
            ?>
            //set function that will update the progress bar
            function getProgress(campaign_id){
                //begin new request
                xmlhttp = new XMLHttpRequest();
                //send update request
                xmlhttp.onreadystatechange=function() {
                    if(xmlhttp.readyState==4){
                        //update progress bar
                        document.getElementById("message_progress").value = xmlhttp.responseText;
                    }
                }
                xmlhttp.open("POST","update_progress.php",true);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("c="+campaign_id);
            }
            //set function that will send emails
            function sendEmail(campaign_id){
                //begin new request
                xmlhttp = new XMLHttpRequest();
                //send update request
                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==4){
                        var campaign_status = xmlhttp.responseText;
                        //loop after a second if status of campaign is still active
                        if(campaign_status != "stop"){
                            setTimeout("sendEmail("+campaign_id+")", 1000);
                            getProgress(campaign_id);
                        }                 
                    }
                }
                xmlhttp.open("POST","send_emails.php",true);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("c="+campaign_id);
            }
            //determine if a campaign is set
            if ( campaign_id != null){
                //start the email function if this campaign is active
                <?php
                    //get campaign id
                    if(isset($_GET['c'])){
                        $c = $_GET['c'];
                        if(!preg_match('/^[0-9]{1,}$/',$c)){
                            $_SESSION['alert_message'] = "please provide a valid campaign id";
                            header('location:.');
                            exit;
                        }
                        //connect to db
                        include '../spt_config/mysql_config.php';
                        $r = mysql_query("SELECT status, cron_id FROM campaigns WHERE id = '$c'");
                        while($ra=mysql_fetch_assoc($r)){
                            if($ra['status'] == 1 && strlen($ra['cron_id']) != 8){
                                echo "sendEmail(campaign_id);";
                            }
                        }
                    }
                ?>
            }
        </script>
    </head>
    <body>
        <!--alert-->
        <?php include '../includes/alert.php'; ?>                 
        <div id="wrapper">
            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>                 
            <!--popovers-->
            <?php
                if(isset($_GET['add_campaign']) && $_GET['add_campaign'] == "true"){
                    echo '
                        <div id="add_campaign">
                                <form method="post" action="start_campaign.php">
                                    <div id="wizard">
                                    <ul>
                                        <li><a href="#wizard-1">Name & Path*</a></li>
                                        <li><a href="#wizard-2">Targets*</a></li>
                                        <li><a href="#wizard-3">Schedule</a></li>
                                        <li><a href="#wizard-4">Template</a></li>
                                        <li><a href="#wizard-5">Education</a></li>
                                        <li><a href="#wizard-6">SMTP Relay</a></li>
                                        <li><a href="#wizard-7">Throttling</a></li>
                                        <li><a href="#wizard-8">Shortener</a></li>
                                        <li><a href="#wizard-9">Audit</a></li>
                                    </ul>
                                    <div id="wizard-1">
                                    <table class="new_campaign_table">
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Specify a name for this campaign that will be displayed in the campaign list on the previous screen.  Use a descriptive name that will help you identify this campaign later.<br /><br />The Path  has been pre-populated for you with the hostname you are currently connecting to spt with.  You can create alternate DNS records that correspond with your campaigns and enter them here.  Whatever you specify in the path field is what will be used to formulate the unique link for each target.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td colspan="2"><input type="text" name="campaign_name"';
                    if ( isset ( $_SESSION['temp_campaign_name'] ) ) {
                        echo "value=\"" . $_SESSION['temp_campaign_name'] . "\"";
                        unset ( $_SESSION['temp_campaign_name'] );
                    }
                    echo '
                                            size="45" /></td>
                                        </tr>
                                        <tr>
                                            <td>Path</td>
                                            <td colspan="2">';
                    //pull current host and path
                    $path = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    //strip off the end
                    $path = preg_replace ( '/\/campaigns.*/', '', $path );
                    //create a hidden field with the path of spt
                    echo "<input type=\"text\" name=\"spt_path\" value=\"";
                    if ( isset ( $_SESSION['temp_spt_path'] ) ) {
                        echo $_SESSION['temp_spt_path'];
                        unset ( $_SESSION['temp_spt_path'] );
                    } else {
                        echo $path;
                    }
                    echo '" size="45"/>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-2">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select one or more groups of targets (hold CTRL or COMMAND to multi-select) that will be included in this campaign</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Group(s)</td>
                                            <td colspan="2">
                                                <select name = "target_groups[]" multiple="multiple" size="5">';
                    //connect to database
                    include('../spt_config/mysql_config.php');
                    //query for all groups
                    $r = mysql_query ( 'SELECT DISTINCT group_name FROM targets' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        if ( isset ( $_SESSION['temp_target_groups'] ) && in_array ( $ra['group_name'], $_SESSION['temp_target_groups'] ) ) {
                            echo "<option selected=\"selected\">" . $ra['group_name'] . "</option>";
                        } else {
                            echo "<option>" . $ra['group_name'] . "</option>";
                        }
                    }
                    unset ( $_SESSION['temp_target_groups'] );
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-3">
                                        <table>
                                            <tr><td><br /></td></tr>
                                            <tr>
                                                <td></td>
                                                <td style="text-align: right;">
                                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Uncheck the background checkbox if you would like for the campaign to only be run while you watch the response popover.  This will be required if your server does not support cron jobs.<br /><br />Select a Month, Day, Hour and Minute if you would like for the campaign to start at the selected time in the future.</span></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Background</td>
                                                <td><input type="checkbox" name="background" value="Yes" CHECKED /></td>
                                            </tr>
                                            <tr>
                                                <td>Start Date</td>
                                                <td>
                                                    <select name="start_month">
                                                        <option value="-">Month...</option>
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                    <select name="start_day">
                                                        <option value="-">Day...</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Set Time</td>
                                                <td>
                                                    <select name="start_hour">
                                                        <option value="-">Hour...</option>
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                    </select>
                                                    <select name="start_minute">
                                                        <option value="-">Minute...</option>
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                        <option value="32">32</option>
                                                        <option value="33">33</option>
                                                        <option value="34">34</option>
                                                        <option value="35">35</option>
                                                        <option value="36">36</option>
                                                        <option value="37">37</option>
                                                        <option value="38">38</option>
                                                        <option value="39">39</option>
                                                        <option value="40">40</option>
                                                        <option value="41">41</option>
                                                        <option value="42">42</option>
                                                        <option value="43">43</option>
                                                        <option value="44">44</option>
                                                        <option value="45">45</option>
                                                        <option value="46">46</option>
                                                        <option value="47">47</option>
                                                        <option value="48">48</option>
                                                        <option value="49">49</option>
                                                        <option value="50">50</option>
                                                        <option value="51">51</option>
                                                        <option value="52">52</option>
                                                        <option value="53">53</option>
                                                        <option value="54">54</option>
                                                        <option value="55">55</option>
                                                        <option value="56">56</option>
                                                        <option value="57">57</option>
                                                        <option value="58">58</option>
                                                        <option value="59">59</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div id="wizard-4">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the template that will be used for this campaign.  You can view/edit the email by going to the editor module and editing the respective email.php file.  Be careful, as editing this file will edit the email for all future campaigns that use this template.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Email/Webpage</td>
                                            <td colspan="2">
                                                <select name = "template_id">';
                    //connect to database
                    include('../spt_config/mysql_config.php');
                    //query for all groups
                    $r = mysql_query ( 'SELECT id, name FROM templates' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        if ( isset ( $_SESSION['temp_template_id'] ) && $_SESSION['temp_template_id'] == $ra['id'] ) {
                            echo "<option value=" . $ra['id'] . " selected=\"selected\">" . $ra['name'] . "</option>";
                        } else {
                            echo "<option value=" . $ra['id'] . ">" . $ra['name'] . "</option>";
                        }
                    }
                    unset ( $_SESSION['temp_template_id'] );
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                        <!--<tr>
                                            <td>Email</td>
                                            <td colspan="2">View/Edit Email link coming soon...</td>
                                        </tr>-->
                                    </table>
                                    </div>
                                    <div id="wizard-5">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the education package that the target will be directed to.  Select "Education on link click" if you would like the targets to bypass the template\'s webpage and go directly to training.  Select "Educate on form submission" if you would like the target to be directed to training after they have submitted a form on your template\'s webpage.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Package</td>
                                            <td colspan="2">
                                                <select name = "education_id">';
                    //connect to database
                    include('../spt_config/mysql_config.php');
                    //query for all groups
                    $r = mysql_query ( 'SELECT id, name FROM education' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        if ( isset ( $_SESSION['temp_education_id'] ) && $_SESSION['temp_education_id'] == $ra['id'] ) {
                            echo "<option value=" . $ra['id'] . " selected=\"selected\" >" . $ra['name'] . "</option>";
                        }
                        echo "<option value=" . $ra['id'] . ">" . $ra['name'] . "</option>";
                    }
                    unset ( $_SESSION['temp_education_id'] );
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="2"><input type="radio" name="education_timing" value="1"';
                    if ( !isset ( $_SESSION['temp_education_timing'] ) OR $_SESSION['temp_education_timing'] != 2 ) {
                        echo "CHECKED";
                    }
                    echo '/> Educate on link click<br /><input type="radio" name="education_timing" value="2"';
                    if ( isset ( $_SESSION['temp_education_timing'] ) && $_SESSION['temp_education_timing'] == 2 ) {
                        echo "CHECKED";
                        unset ( $_SESSION['temp_education_timing'] );
                    }
                    echo '            
                                            /> Educate on form submission</td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-6">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the SMTP relay you would like for the emails to be routed through.  You may CTRL or COMMAND click multiple relays and each target will be assigned an SMTP relay in a "round robin" sort of manner.  By selecting None, the emails will be sent directly from spt using Swiftmailer.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Relay</td>
                                            <td colspan="2">
                                                <select name = "relay_host[]" multiple="multiple" size="5">
                                                    <option value="-">None - Direct SMTP</option>';
                    //get smtp servers
                    $r = mysql_query("SELECT id, host FROM settings_smtp");
                    while($ra = mysql_fetch_assoc($r)){
                        $smtp_id = $ra['id'];
                        $smtp_host = $ra['host'];
                        //get potentially previously entered entries
                        if(isset($_SESSION['temp_relay_host'])){
                            $temp_relay_host = $_SESSION['temp_relay_host'];
                            if(in_array($smtp_id, $temp_relay_host)){
                                echo '<option value='.$smtp_id.'selected="selected">'.$smtp_host.'</option>';
                            }
                        }else{
                            echo '<option value='.$smtp_id.'>'.$smtp_host.'</option>';
                        }
                    }
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-7">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>By default spt will send an email every second (or 1000 ms).  You can change the default message delay to 100 ms and batches of 10 emails will be sent each second.  Or you can create as high as a 1 minute delay between each message by specifying 60000 ms between each message.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Delay</td>
                                            <td colspan="2"><input type="text" name="message_delay"';
                    if ( isset ( $_SESSION['temp_message_delay'] ) ) {
                        echo "value=\"" . $_SESSION['temp_message_delay'] . "\"";
                        unset ( $_SESSION['temp_message_delay'] );
                    } else {
                        echo "value=\"1000\"";
                    }
                    echo '                    
                                            />&nbsp;<i>ms</i> (100-60000)</td>

                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-8">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>If you want to further mask the phishing link in your emails using a popular URL shortening service, select the service below you\'d like to shorten with.<br /><br />You may have to provide an API key for some services by opening the Shorten button at the top of this page.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><input type="radio" name="shorten_radio" value="Google"';
                    if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "google" ) {
                        echo "checked";
                        unset ( $_SESSION['temp_shorten'] );
                    }
                    echo '                    
                                                    />&nbsp;Google&nbsp;&nbsp;
                                                <input type="radio" name="shorten_radio" value="TinyURL"';
                   if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "tinyurl" ) {
                       echo "checked";
                       unset ( $_SESSION['temp_shorten'] );
                   }
                   echo '
                                                    />&nbsp;TinyURL&nbsp;&nbsp;
                                                <input type="radio" name="shorten_radio" value="None"';
                   if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "none" ) {
                       echo "checked";
                       unset ( $_SESSION['temp_shorten'] );
                   }
                   echo '
                                                    />&nbsp;None&nbsp;&nbsp;</td>
                                        </tr>';
                    if ( isset ( $_SESSION['alert_message'] ) ) {
                        echo "<tr><td colspan=3 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                    }
                    echo '
                                    </table>
                                    </div>
                                    <div id="wizard-9">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the software that you would like to retrieve version information for.  The OS and Browser are pre-selected as there is very little reason not to accumulate this information.  However, Java and some of the other browser plugins may pop-up alerts to the target and by default these are disabled.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Get Operating System</td>
                                            <td><input type="checkbox" CHECKED DISABLED/></td>
                                        </tr>
                                        <tr>
                                            <td>Get Browser Version</td>
                                            <td><input type="checkbox" CHECKED DISABLED/></td>
                                        </tr>
                                        <tr>
                                            <td>Get Java Version</td>
                                            <td><input name="check_java" type="checkbox" ';
                    if(isset($_SESSION['temp_check_java']) && $_SESSION['temp_check_java'] == "on"){
                        echo "CHECKED";
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Get Flash Version</td>
                                            <td><input name="check_flash" type="checkbox" ';
                    if(isset($_SESSION['temp_check_flash']) && $_SESSION['temp_check_flash'] == "on"){
                        echo "CHECKED";
                    }
                    echo '
                                            /></td>
                                        </tr>
                                    </table>
                                    </div>
                                    <table>
                                        <tr>
                                            <td colspan="3" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="tooltip"><input type="image" src="../images/accept.png" alt="accept" /><span><b>WARNING:</b> If not using background or scheduling, emails will begin sending immediatly upon pushing the button.</span></a></td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                        </div>';
                }
                if(isset($_GET['edit_campaign']) && $_GET['edit_campaign'] == "true"){
                    //get campaign id
                    $campaign_id = $_GET['c'];
                    //get details regarding this campaign
                    $r = mysql_query("SELECT * FROM campaigns WHERE id = '$campaign_id'");
                    while($ra = mysql_fetch_assoc($r)){
                        $template_id = $ra['template_id'];
                        $campaign_name = $ra['campaign_name'];
                        $domain_name = $ra['domain_name'];
                        $education_id = $ra['education_id'];
                        $education_timing = $ra['education_timing'];
                        $message_delay = $ra['message_delay'];
                        $spt_path = $ra['spt_path'];
                        $encrypt = $ra['encrypt'];
                        $shorten = $ra['shorten'];
                        $cron_id = $ra['cron_id'];
                        $check_java = $ra['check_java'];
                        $check_flash = $ra['check_flash'];
                    }
                    echo '
                        <div id="edit_campaign">
                                <form method="post" action="edit_campaign.php">
                                    <div id="wizard">
                                    <ul>
                                        <li><a href="#wizard-1">Name & Path*</a></li>
                                        <li><a href="#wizard-2">Schedule</a></li>
                                        <li><a href="#wizard-3">Template</a></li>
                                        <li><a href="#wizard-4">Education</a></li>
                                        <li><a href="#wizard-5">SMTP Relay</a></li>
                                        <li><a href="#wizard-6">Throttling</a></li>
                                        <li><a href="#wizard-7">Shortener</a></li>
                                        <li><a href="#wizard-8">Audit</a></li>
                                    </ul>
                                    <div id="wizard-1">
                                    <table class="edit_campaign_table">
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Specify a name for this campaign that will be displayed in the campaign list on the previous screen.  Use a descriptive name that will help you identify this campaign later.<br /><br />The Path  has been pre-populated for you with the hostname you are currently connecting to spt with.  You can create alternate DNS records that correspond with your campaigns and enter them here.  Whatever you specify in the path field is what will be used to formulate the unique link for each target.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td colspan="2"><input type="text" name="campaign_name" value="'.$campaign_name.'" size="45" /></td>
                                        </tr>
                                        <tr>
                                            <td>Path</td>
                                            <td colspan="2">
                                                <input type="text" name="spt_path" value="'.$spt_path.'" size="45"/>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-2">
                                        <table>
                                            <tr><td><br /></td></tr>
                                            <tr>
                                                <td></td>
                                                <td style="text-align: right;">
                                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Uncheck the background checkbox if you would like for the campaign to only be run while you watch the response popover.  This will be required if your server does not support cron jobs.<br /><br />Select a Month, Day, Hour and Minute if you would like for the campaign to start at the selected time in the future.</span></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Background</td>
                                                <td><input type="checkbox" name="background" value="Yes" CHECKED /></td>
                                            </tr>
                                            <tr>
                                                <td>Start Date</td>
                                                <td>';
                    $crontab_output = shell_exec('crontab -l|sed -n \'/'.$cron_id.'/p\'');
                    preg_match_all('/(\d\d|\d)/',$crontab_output,$matches);
                    $minute = $matches[0][0];
                    $hour = $matches[0][1];
                    $day = $matches[0][2];
                    $month = $matches[0][3];
                    echo '
                                                    <select name="start_month">
                                                        <option value="-">Month...</option>
                                                        <option value="1"';
                    if($month == 1){
                        echo "SELECTED";
                    }
                    echo '>January</option>
                                                        <option value="2"';
                    if($month == 2){
                        echo "SELECTED";
                    }
                    echo '>February</option>
                                                        <option value="3"';
                    if($month == 3){
                        echo "SELECTED";
                    }
                    echo '>March</option>
                                                        <option value="4"';
                    if($month == 4){
                        echo "SELECTED";
                    }
                    echo '>April</option>
                                                        <option value="5"';
                    if($month == 5){
                        echo "SELECTED";
                    }
                    echo '>May</option>
                                                        <option value="6"';
                    if($month == 6){
                        echo "SELECTED";
                    }
                    echo '>June</option>
                                                        <option value="7"';
                    if($month == 7){
                        echo "SELECTED";
                    }
                    echo '>July</option>
                                                        <option value="8"';
                    if($month == 8){
                        echo "SELECTED";
                    }
                    echo '>August</option>
                                                        <option value="9"';
                    if($month == 9){
                        echo "SELECTED";
                    }
                    echo '>September</option>
                                                        <option value="10"';
                    if($month == 10){
                        echo "SELECTED";
                    }
                    echo '>October</option>
                                                        <option value="11"';
                    if($month == 11){
                        echo "SELECTED";
                    }
                    echo '>November</option>
                                                        <option value="12"';
                    if($month == 12){
                        echo "SELECTED";
                    }
                    echo '>December</option>
                                                    </select>
                                                    <select name="start_day">
                                                        <option value="-">Day...</option>
                                                        <option value="1"';
                    if($day == 1){
                        echo "SELECTED";
                    }
                    echo '>1</option>
                                                        <option value="2"';
                    if($day == 2){
                        echo "SELECTED";
                    }
                    echo '>2</option>
                                                        <option value="3"';
                    if($day == 3){
                        echo "SELECTED";
                    }
                    echo '>3</option>
                                                        <option value="4"';
                    if($day == 4){
                        echo "SELECTED";
                    }
                    echo '>4</option>
                                                        <option value="5"';
                    if($day == 5){
                        echo "SELECTED";
                    }
                    echo '>5</option>
                                                        <option value="6"';
                    if($day == 6){
                        echo "SELECTED";
                    }
                    echo '>6</option>
                                                        <option value="7"';
                    if($day == 7){
                        echo "SELECTED";
                    }
                    echo '>7</option>
                                                        <option value="8"';
                    if($day == 8){
                        echo "SELECTED";
                    }
                    echo '>8</option>
                                                        <option value="9"';
                    if($day == 9){
                        echo "SELECTED";
                    }
                    echo '>9</option>
                                                        <option value="10"';
                    if($day == 10){
                        echo "SELECTED";
                    }
                    echo '>10</option>
                                                        <option value="11"';
                    if($day == 11){
                        echo "SELECTED";
                    }
                    echo '>11</option>
                                                        <option value="12"';
                    if($day == 12){
                        echo "SELECTED";
                    }
                    echo '>12</option>
                                                        <option value="13"';
                    if($day == 13){
                        echo "SELECTED";
                    }
                    echo '>13</option>
                                                        <option value="14"';
                    if($day == 14){
                        echo "SELECTED";
                    }
                    echo '>14</option>
                                                        <option value="15"';
                    if($day == 15){
                        echo "SELECTED";
                    }
                    echo '>15</option>
                                                        <option value="16"';
                    if($day == 16){
                        echo "SELECTED";
                    }
                    echo '>16</option>
                                                        <option value="17"';
                    if($day == 17){
                        echo "SELECTED";
                    }
                    echo '>17</option>
                                                        <option value="18"';
                    if($day == 18){
                        echo "SELECTED";
                    }
                    echo '>18</option>
                                                        <option value="19"';
                    if($day == 19){
                        echo "SELECTED";
                    }
                    echo '>19</option>
                                                        <option value="20"';
                    if($day == 20){
                        echo "SELECTED";
                    }
                    echo '>20</option>
                                                        <option value="21"';
                    if($day == 21){
                        echo "SELECTED";
                    }
                    echo '>21</option>
                                                        <option value="22"';
                    if($day == 22){
                        echo "SELECTED";
                    }
                    echo '>22</option>
                                                        <option value="23"';
                    if($day == 23){
                        echo "SELECTED";
                    }
                    echo '>23</option>
                                                        <option value="24"';
                    if($day == 24){
                        echo "SELECTED";
                    }
                    echo '>24</option>
                                                        <option value="25"';
                    if($day == 25){
                        echo "SELECTED";
                    }
                    echo '>25</option>
                                                        <option value="26"';
                    if($day == 26){
                        echo "SELECTED";
                    }
                    echo '>26</option>
                                                        <option value="27"';
                    if($day == 27){
                        echo "SELECTED";
                    }
                    echo '>27</option>
                                                        <option value="28"';
                    if($day == 28){
                        echo "SELECTED";
                    }
                    echo '>28</option>
                                                        <option value="29"';
                    if($day == 29){
                        echo "SELECTED";
                    }
                    echo '>29</option>
                                                        <option value="30"';
                    if($day == 30){
                        echo "SELECTED";
                    }
                    echo '>30</option>
                                                        <option value="31"';
                    if($day == 31){
                        echo "SELECTED";
                    }
                    echo '>31</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Set Time</td>
                                                <td>
                                                    <select name="start_hour">
                                                        <option value="-">Hour...</option>
                                                        <option value="0"';
                    if($hour == 0){
                        echo "SELECTED";
                    }
                    echo '>0</option>
                                                        <option value="1"';
                    if($hour == 1){
                        echo "SELECTED";
                    }
                    echo '>1</option>
                                                        <option value="2"';
                    if($hour == 2){
                        echo "SELECTED";
                    }
                    echo '>2</option>
                                                        <option value="3"';
                    if($hour == 3){
                        echo "SELECTED";
                    }
                    echo '>3</option>
                                                        <option value="4"';
                    if($hour == 4){
                        echo "SELECTED";
                    }
                    echo '>4</option>
                                                        <option value="5"';
                    if($hour == 5){
                        echo "SELECTED";
                    }
                    echo '>5</option>
                                                        <option value="6"';
                    if($hour == 6){
                        echo "SELECTED";
                    }
                    echo '>6</option>
                                                        <option value="7"';
                    if($hour == 7){
                        echo "SELECTED";
                    }
                    echo '>7</option>
                                                        <option value="8"';
                    if($hour == 8){
                        echo "SELECTED";
                    }
                    echo '>8</option>
                                                        <option value="9"';
                    if($hour == 9){
                        echo "SELECTED";
                    }
                    echo '>9</option>
                                                        <option value="10"';
                    if($hour == 10){
                        echo "SELECTED";
                    }
                    echo '>10</option>
                                                        <option value="11"';
                    if($hour == 11){
                        echo "SELECTED";
                    }
                    echo '>11</option>
                                                        <option value="12"';
                    if($hour == 12){
                        echo "SELECTED";
                    }
                    echo '>12</option>
                                                        <option value="13"';
                    if($hour == 13){
                        echo "SELECTED";
                    }
                    echo '>13</option>
                                                        <option value="14"';
                    if($hour == 14){
                        echo "SELECTED";
                    }
                    echo '>14</option>
                                                        <option value="15"';
                    if($hour == 15){
                        echo "SELECTED";
                    }
                    echo '>15</option>
                                                        <option value="16"';
                    if($hour == 16){
                        echo "SELECTED";
                    }
                    echo '>16</option>
                                                        <option value="17"';
                    if($hour == 17){
                        echo "SELECTED";
                    }
                    echo '>17</option>
                                                        <option value="18"';
                    if($hour == 18){
                        echo "SELECTED";
                    }
                    echo '>18</option>
                                                        <option value="19"';
                    if($hour == 19){
                        echo "SELECTED";
                    }
                    echo '>19</option>
                                                        <option value="20"';
                    if($hour == 20){
                        echo "SELECTED";
                    }
                    echo '>20</option>
                                                        <option value="21"';
                    if($hour == 21){
                        echo "SELECTED";
                    }
                    echo '>21</option>
                                                        <option value="22"';
                    if($hour == 22){
                        echo "SELECTED";
                    }
                    echo '>22</option>
                                                        <option value="23"';
                    if($hour == 23){
                        echo "SELECTED";
                    }
                    echo '>23</option>
                                                        
                                                    </select>
                                                    <select name="start_minute">
                                                        <option value="-">Minute...</option>
                                                        <option value="0"';
                    if($minute == 0){
                        echo "SELECTED";
                    }
                    echo '>0</option>
                                                        <option value="1"';
                    if($minute == 1){
                        echo "SELECTED";
                    }
                    echo '>1</option>
                                                        <option value="2"';
                    if($minute == 2){
                        echo "SELECTED";
                    }
                    echo '>2</option>
                                                        <option value="3"';
                    if($minute == 3){
                        echo "SELECTED";
                    }
                    echo '>3</option>
                                                        <option value="4"';
                    if($minute == 4){
                        echo "SELECTED";
                    }
                    echo '>4</option>
                                                        <option value="5"';
                    if($minute == 5){
                        echo "SELECTED";
                    }
                    echo '>5</option>
                                                        <option value="6"';
                    if($minute == 6){
                        echo "SELECTED";
                    }
                    echo '>6</option>
                                                        <option value="7"';
                    if($minute == 7){
                        echo "SELECTED";
                    }
                    echo '>7</option>
                                                        <option value="8"';
                    if($minute == 8){
                        echo "SELECTED";
                    }
                    echo '>8</option>
                                                        <option value="9"';
                    if($minute == 9){
                        echo "SELECTED";
                    }
                    echo '>9</option>
                                                        <option value="10"';
                    if($minute == 10){
                        echo "SELECTED";
                    }
                    echo '>10</option>
                                                        <option value="11"';
                    if($minute == 11){
                        echo "SELECTED";
                    }
                    echo '>11</option>
                                                        <option value="12"';
                    if($minute == 12){
                        echo "SELECTED";
                    }
                    echo '>12</option>
                                                        <option value="13"';
                    if($minute == 13){
                        echo "SELECTED";
                    }
                    echo '>13</option>
                                                        <option value="14"';
                    if($minute == 14){
                        echo "SELECTED";
                    }
                    echo '>14</option>
                                                        <option value="15"';
                    if($minute == 15){
                        echo "SELECTED";
                    }
                    echo '>15</option>
                                                        <option value="16"';
                    if($minute == 16){
                        echo "SELECTED";
                    }
                    echo '>16</option>
                                                        <option value="17"';
                    if($minute == 17){
                        echo "SELECTED";
                    }
                    echo '>17</option>
                                                        <option value="18"';
                    if($minute == 18){
                        echo "SELECTED";
                    }
                    echo '>18</option>
                                                        <option value="19"';
                    if($minute == 19){
                        echo "SELECTED";
                    }
                    echo '>19</option>
                                                        <option value="20"';
                    if($minute == 20){
                        echo "SELECTED";
                    }
                    echo '>20</option>
                                                        <option value="21"';
                    if($minute == 21){
                        echo "SELECTED";
                    }
                    echo '>21</option>
                                                        <option value="22"';
                    if($minute == 22){
                        echo "SELECTED";
                    }
                    echo '>22</option>
                                                        <option value="23"';
                    if($minute == 23){
                        echo "SELECTED";
                    }
                    echo '>23</option>
                                                        <option value="24"';
                    if($minute == 24){
                        echo "SELECTED";
                    }
                    echo '>24</option>
                                                        <option value="25"';
                    if($minute == 25){
                        echo "SELECTED";
                    }
                    echo '>25</option>
                                                        <option value="26"';
                    if($minute == 26){
                        echo "SELECTED";
                    }
                    echo '>26</option>
                                                        <option value="27"';
                    if($minute == 27){
                        echo "SELECTED";
                    }
                    echo '>27</option>
                                                        <option value="28"';
                    if($minute == 28){
                        echo "SELECTED";
                    }
                    echo '>28</option>
                                                        <option value="29"';
                    if($minute == 29){
                        echo "SELECTED";
                    }
                    echo '>29</option>
                                                        <option value="30"';
                    if($minute == 30){
                        echo "SELECTED";
                    }
                    echo '>30</option>
                                                        <option value="31"';
                    if($minute == 31){
                        echo "SELECTED";
                    }
                    echo '>31</option>
                                                        <option value="32"';
                    if($minute == 32){
                        echo "SELECTED";
                    }
                    echo '>32</option>
                                                        <option value="33"';
                    if($minute == 33){
                        echo "SELECTED";
                    }
                    echo '>33</option>
                                                        <option value="34"';
                    if($minute == 34){
                        echo "SELECTED";
                    }
                    echo '>34</option>
                                                        <option value="35"';
                    if($minute == 35){
                        echo "SELECTED";
                    }
                    echo '>35</option>
                                                        <option value="36"';
                    if($minute == 36){
                        echo "SELECTED";
                    }
                    echo '>36</option>
                                                        <option value="37"';
                    if($minute == 37){
                        echo "SELECTED";
                    }
                    echo '>37</option>
                                                        <option value="38"';
                    if($minute == 38){
                        echo "SELECTED";
                    }
                    echo '>38</option>
                                                        <option value="39"';
                    if($minute == 39){
                        echo "SELECTED";
                    }
                    echo '>39</option>
                                                        <option value="40"';
                    if($minute == 40){
                        echo "SELECTED";
                    }
                    echo '>40</option>
                                                        <option value="41"';
                    if($minute == 41){
                        echo "SELECTED";
                    }
                    echo '>41</option>
                                                        <option value="42"';
                    if($minute == 42){
                        echo "SELECTED";
                    }
                    echo '>42</option>
                                                        <option value="43"';
                    if($minute == 43){
                        echo "SELECTED";
                    }
                    echo '>43</option>
                                                        <option value="44"';
                    if($minute == 44){
                        echo "SELECTED";
                    }
                    echo '>44</option>
                                                        <option value="45"';
                    if($minute == 45){
                        echo "SELECTED";
                    }
                    echo '>45</option>
                                                        <option value="46"';
                    if($minute == 46){
                        echo "SELECTED";
                    }
                    echo '>46</option>
                                                        <option value="47"';
                    if($minute == 47){
                        echo "SELECTED";
                    }
                    echo '>47</option>
                                                        <option value="48"';
                    if($minute == 48){
                        echo "SELECTED";
                    }
                    echo '>48</option>
                                                        <option value="49"';
                    if($minute == 49){
                        echo "SELECTED";
                    }
                    echo '>49</option>
                                                        <option value="50"';
                    if($minute == 50){
                        echo "SELECTED";
                    }
                    echo '>50</option>
                                                        <option value="51"';
                    if($minute == 51){
                        echo "SELECTED";
                    }
                    echo '>51</option>
                                                        <option value="52"';
                    if($minute == 52){
                        echo "SELECTED";
                    }
                    echo '>52</option>
                                                        <option value="53"';
                    if($minute == 53){
                        echo "SELECTED";
                    }
                    echo '>53</option>
                                                        <option value="54"';
                    if($minute == 54){
                        echo "SELECTED";
                    }
                    echo '>54</option>
                                                        <option value="55"';
                    if($minute == 55){
                        echo "SELECTED";
                    }
                    echo '>55</option>
                                                        <option value="56"';
                    if($minute == 56){
                        echo "SELECTED";
                    }
                    echo '>56</option>
                                                        <option value="57"';
                    if($minute == 57){
                        echo "SELECTED";
                    }
                    echo '>57</option>
                                                        <option value="58"';
                    if($minute == 58){
                        echo "SELECTED";
                    }
                    echo '>58</option>
                                                        <option value="59"';
                    if($minute == 59){
                        echo "SELECTED";
                    }
                    echo '>59</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div id="wizard-3">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the template that will be used for this campaign.  You can view/edit the email by going to the editor module and editing the respective email.php file.  Be careful, as editing this file will edit the email for all future campaigns that use this template.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Email/Webpage</td>
                                            <td colspan="2">
                                                <select name = "template_id">';
                    //connect to database
                    include('../spt_config/mysql_config.php');
                    //query for all groups
                    $r = mysql_query ( 'SELECT id, name FROM templates' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        if ( isset ( $_SESSION['temp_template_id'] ) && $_SESSION['temp_template_id'] == $ra['id'] ) {
                            echo "<option value=" . $ra['id'] . "\" selected=\"selected\">" . $ra['name'] . "</option>";
                        } else {
                            echo "<option value=\"" . $ra['id'] . "\" ";
                            if(isset($template_id) && $template_id == $ra['id']){
                                echo "selected=\"selected\" ";
                            }
                            echo ">" . $ra['name'] . "</option>";
                        }
                    }
                    unset ( $_SESSION['temp_template_id'] );
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                        <!--<tr>
                                            <td>Email</td>
                                            <td colspan="2">View/Edit Email link coming soon...</td>
                                        </tr>-->
                                    </table>
                                    </div>
                                    <div id="wizard-4">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the education package that the target will be directed to.  Select "Education on link click" if you would like the targets to bypass the template\'s webpage and go directly to training.  Select "Educate on form submission" if you would like the target to be directed to training after they have submitted a form on your template\'s webpage.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Package</td>
                                            <td colspan="2">
                                                <select name = "education_id">';
                    //connect to database
                    include('../spt_config/mysql_config.php');
                    //query for all groups
                    $r = mysql_query ( 'SELECT id, name FROM education' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        if ( isset ( $_SESSION['temp_education_id'] ) && $_SESSION['temp_education_id'] == $ra['id'] ) {
                            echo "<option value=" . $ra['id'] . " selected=\"selected\" >" . $ra['name'] . "</option>";
                        }else{
                            echo "<option value=" . $ra['id'] . "\" ";
                            if(isset($education_id) && $education_id == $ra['id']){
                                echo "selected=\"selected\"";
                            }
                            echo ">" . $ra['name'] . "</option>";    
                        }
                    }
                    unset ( $_SESSION['temp_education_id'] );
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="2"><input type="radio" name="education_timing" value="1"';
                    if ( !isset ( $_SESSION['temp_education_timing'] ) OR $_SESSION['temp_education_timing'] != 2 ) {
                        echo "CHECKED";
                    }else if(isset($education_timing) && $education_timing == 1){
                        echo "CHECKED";
                    }
                    echo '/> Educate on link click<br /><input type="radio" name="education_timing" value="2"';
                    if ( isset ( $_SESSION['temp_education_timing'] ) && $_SESSION['temp_education_timing'] == 2 ) {
                        echo "CHECKED";
                        unset ( $_SESSION['temp_education_timing'] );
                    }else if(isset($education_timing) && $education_timing == 2){
                        echo "CHECKED";
                    }
                    echo '            
                                            /> Educate on form submission</td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-5">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter your SMTP relay\'s details if necessary.  You may also enter credentials if your SMTP requires authentication.  If you leave these fields blank, spt will act as an SMTP server and send emails directly to the destination\'s mail gateway based on the MX records published by your target\'s domain.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Relay</td>
                                            <td colspan="2">
                                                <select name = "relay_host[]" multiple="multiple" size="5">';
                    //see if none is selected
                    $s = mysql_query("SELECT relay_host FROM campaigns_responses WHERE campaign_id = '$campaign_id'");
                    while($sa = mysql_fetch_assoc($s)){
                        if($sa['relay_host'] == '-'){
                            $match = 1;
                        }
                    }
                    //display none correctly
                    if($match == 1){
                        echo '<option value="-" selected="selected">None - Direct SMTP</option>';
                    }else{
                            echo '<option value="-">None - Direct SMTP</option>';
                    }
                    $match = 0;
                    //get smtp servers
                    $r = mysql_query("SELECT id, host FROM settings_smtp");
                    while($ra = mysql_fetch_assoc($r)){
                        $smtp_id = $ra['id'];
                        $smtp_host = $ra['host'];
                        //get current relay status
                        $s = mysql_query("SELECT relay_host FROM campaigns_responses WHERE campaign_id = '$campaign_id'");
                        while($sa = mysql_fetch_assoc($s)){
                            if($sa['relay_host'] == $smtp_id){
                                $match = 1;
                            }
                        }
                        //get potentially previously entered entries or if is currently selected
                        if(isset($_SESSION['temp_relay_host'])){
                            $temp_relay_host=$_SESSION['temp_relay_host'];
                        }
                        if((isset($temp_relay_host) && in_array($smtp_id, $temp_relay_host)) OR $match == 1){
                            echo '<option value="'.$smtp_id.'" selected="selected">'.$smtp_host.'</option>';
                        }else{
                            echo '<option value="'.$smtp_id.'">'.$smtp_host.'</option>';
                        }
                        $match = 0;
                    }
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-6">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>By default spt will send an email every second (or 1000 ms).  You can change the default message delay to 100 ms and batches of 10 emails will be sent each second.  Or you can create as high as a 1 minute delay between each message by specifying 60000 ms between each message.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Delay</td>
                                            <td colspan="2"><input type="text" name="message_delay"';
                    if ( isset ( $_SESSION['temp_message_delay'] ) ) {
                        echo "value=\"" . $_SESSION['temp_message_delay'] . "\"";
                        unset ( $_SESSION['temp_message_delay'] );
                    } else if (isset($message_delay)){
                        echo "value=\"" . $message_delay . "\"";
                    } else {
                        echo "value=\"1000\"";
                    }
                    echo '                    
                                            />&nbsp;<i>ms</i> (100-60000)</td>

                                        </tr>
                                    </table>
                                    </div>
                                    <div id="wizard-7">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>If you want to further mask the phishing link in your emails using a popular URL shortening service, select the service below you\'d like to shorten with.<br /><br />You may have to provide an API key for some services by opening the Shorten button at the top of this page.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><input type="radio" name="shorten_radio" value="Google"';
                    if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "Google" ) {
                        echo "checked";
                        unset ( $_SESSION['temp_shorten'] );
                    } else if (isset($shorten) && $shorten == "Google"){
                        echo "checked";
                    }
                    echo '                    
                                                    />&nbsp;Google&nbsp;&nbsp;
                                                <input type="radio" name="shorten_radio" value="TinyURL"';
                   if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "TinyURL" ) {
                       echo "checked";
                       unset ( $_SESSION['temp_shorten'] );
                   } else if (isset($shorten) && $shorten == "TinyURL"){
                     echo "checked";
                   }
                   echo '
                                                    />&nbsp;TinyURL&nbsp;&nbsp;
                                                <input type="radio" name="shorten_radio" value="None"';
                   if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "None" ) {
                       echo "checked";
                       unset ( $_SESSION['temp_shorten'] );
                   }
                   echo '
                                                    />&nbsp;None&nbsp;&nbsp;</td>
                                        </tr>';
                    if ( isset ( $_SESSION['alert_message'] ) ) {
                        echo "<tr><td colspan=3 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                    }
                    echo '
                                    </table>
                                    </div>
                                    <div id="wizard-8">
                                    <table>
                                        <tr><td><br /></td></tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the software that you would like to retrieve version information for.  The OS and Browser are pre-selected as there is very little reason not to accumulate this information.  However, Java and some of the other browser plugins may pop-up alerts to the target and by default these are disabled.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Get Operating System</td>
                                            <td><input type="checkbox" CHECKED DISABLED/></td>
                                        </tr>
                                        <tr>
                                            <td>Get Browser Version</td>
                                            <td><input type="checkbox" CHECKED DISABLED/></td>
                                        </tr>
                                        <tr>
                                            <td>Get Java Version</td>
                                            <td><input name="check_java" type="checkbox" ';
                    if(isset($_SESSION['temp_check_java']) && $_SESSION['temp_check_java'] == "yes"){
                        echo "CHECKED";
                    }else if(isset($check_java) && $check_java == 1){
                        echo "CHECKED";
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Get Flash Version</td>
                                            <td><input name="check_flash" type="checkbox" ';
                    if(isset($_SESSION['temp_check_flash']) && $_SESSION['temp_check_flash'] == "yes"){
                        echo "CHECKED";
                    }else if(isset($check_flash) && $check_flash == 1){
                        echo "CHECKED";
                    }
                    echo '
                                            /></td>
                                        </tr>
                                    </table>
                                    </div>
                                    <table>
                                        <tr>
                                            <td colspan="3" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="tooltip"><input type="image" src="../images/accept.png" alt="accept" /><span><b>WARNING:</b> When you click this button, you will be directed to the campaign response page for this new campaign and emails will begin to be sent.</span></a></td>
                                        </tr>
                                    </table>
                                </div>
                                <input type="hidden" name="campaign_id" value="'.$campaign_id.'" />
                            </form>
                        </div>';
                }
                if(isset($_GET['responses']) && $_GET['responses'] == "true"){
                    echo '
                        <div id="responses">
                            <div>';
                    //connect to database
                    include "../spt_config/mysql_config.php";
                    //pull in campaign id
                    if ( isset ( $_REQUEST['c'] ) ) {
                        $campaign_id = filter_var ( $_REQUEST['c'], FILTER_SANITIZE_NUMBER_INT );
                        //get campaign name and status
                        $r = mysql_query ( "SELECT campaign_name, status FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            $campaign_name = $ra['campaign_name'];
                            $campaign_status = $ra['status'];
                            if($campaign_status == 0){
                                $tab_return = "#tabs-2";
                            }
                            if($campaign_status == 1){
                                $tab_return = "#tabs-3";
                            }
                            if($campaign_status == 2){
                                $tab_return = "#tabs-4";
                            }
                            if($campaign_status == 3){
                                $tab_return = "#tabs-5";
                            }
                        }
                        //go ahead and perform validation
                        $r = mysql_query ( "SELECT DISTINCT campaign_id FROM campaigns_responses" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            if ( $ra['campaign_id'] == $campaign_id ) {
                                $campaign_match = 1;
                            }
                        }
                        if ( $campaign_match != 1 ) {
                            $_SESSION['alert_message'] = "please select a valid campaign";
                            header ( 'location:.'.$tab_return );
                            exit;
                        }
                    }
                    //pull in filter and group
                    if ( isset ( $_REQUEST['f'] ) ) {
                        $filter = filter_var ( $_REQUEST['f'], FILTER_SANITIZE_STRING );

                        //go ahead and preform validation
                        if ( $filter != "link" && $filter != "post" ) {
                            $_SESSION['alert_message'] = "please use a valid filter";
                            header ( 'location:.'.$tab_return );
                            exit;
                        }
                    }
                    if ( isset ( $_REQUEST['g'] ) ) {
                        $group = filter_var ( $_REQUEST['g'], FILTER_SANITIZE_STRING );
                        //go ahead and perform validation
                        $r = mysql_query ( "SELECT DISTINCT group_name FROM targets" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            if ( $group == $ra['group_name'] ) {
                                $group_match = 1;
                            }
                        }
                        if ( ! isset ( $group_match ) ) {
                            $_SESSION['alert_message'] = "please select a valid group";
                            header ( 'location:.'.$tab_return );
                            exit;
                        }
                    }
                    //if group and filter are both set send them back
                    if ( isset ( $filter ) && isset ( $group ) ) {
                        $_SESSION['alert_message'] = "you cannot pass both a filter and a group";
                        header ( 'location:.'.$tab_return );
                        exit;
                    }
                    //pull data for entire campaign if group and filters are NOT set
                    if ( ! isset ( $group ) && ! isset ( $filter ) && isset ( $campaign_id ) ) {
                        $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.response_id AS response_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.java AS java, campaigns_responses.flash AS flash, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.campaign_id = '$campaign_id' ORDER BY targets.fname ASC, targets.lname ASC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        //title the page with the campaign number
                        $title = $campaign_name . " :: All Responses";
                    }
                    //pull data if a group is set
                    if ( isset ( $group ) ) {
                        $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.response_id AS response_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.java AS java, campaigns_responses.flash AS flash, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE targets.group_name = '$group' AND campaigns_responses.campaign_id = '$campaign_id' ORDER BY targets.fname ASC, targets.lname ASC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        //title the page with the campaign number
                        $title = $campaign_name . " :: " . $group;
                    }
                    //pull data if a filter is set
                    if ( isset ( $filter ) ) {
                        //if filter is for links
                        if ( $filter == "link" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.response_id AS response_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.java AS java, campaigns_responses.flash AS flash, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.link = 1 AND campaigns_responses.campaign_id = '$campaign_id' ORDER BY targets.fname ASC, targets.lname ASC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                            //title the page with the campaign number
                            $title = $campaign_name;
                            if ( isset ( $group ) ) {
                                $title .= " :: " . $group;
                            }
                            $title .= " :: Links";
                        }
                        //if filter is for posts
                        if ( $filter == "post" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.response_id AS response_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.java AS java, campaigns_responses.flash AS flash, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.post != \"\"  AND campaigns_responses.campaign_id = '$campaign_id' ORDER BY targets.fname ASC, targets.lname ASC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                            //title the page with the campaign number
                            $title = $campaign_name;
                            if ( isset ( $group ) ) {
                                $title .= " :: " . $group;
                            }
                            $title .= " :: Posts";
                        }
                    }
                    if ( isset ( $_REQUEST['c'] ) ) {
                        //get basic campaign data
                        $r2 = mysql_query ( "SELECT date_sent, date_ended, campaign_name, domain_name, education_id, template_id, education_timing, check_java, check_flash, shorten FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                            $date_sent = $ra2['date_sent'];
                            $date_ended = $ra2['date_ended'];
                            $campaign_name = $ra2['campaign_name'];
                            $formulated_url = "http://" . $ra2['domain_name'] . "/campaigns/response.php?r=response_key";
                            $shorten = $ra2['shorten'];
                            $education_id = $ra2['education_id'];
                            $template_id = $ra2['template_id'];
                            $education_timing = $ra2['education_timing'];
                            if ( $education_timing == 1 ) {
                                $education = "On Link Click";
                            }
                            if ( $education_timing == 2 ) {
                                $education = "On Form Submission ";
                            }
                            if ( $education_timing == 0 ) {
                                $education = "None";
                            }
                            $r4 = mysql_query ( "SELECT name FROM templates WHERE id = '$template_id'" );
                            while ( $ra4 = mysql_fetch_assoc ( $r4 ) ) {
                                $template_name = $ra4['name'];
                            }
                            $java = $ra2['check_java'];
                            $flash = $ra2['check_flash'];
                        }
                        //get progress status information    
                        $r5 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id' AND sent != 0" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        $r6 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        $sent = mysql_num_rows ( $r5 );
                        $total = mysql_num_rows ( $r6 );
                        $percentage = ceil ( ($sent / $total) * 100 );
                        //print the table header
                        echo "
                                <table id=\"campaign_list_header\">
                                    <tr>
                                        <td class=\"left\">
                                            <h1>";
                        if ( isset ( $title ) ) {
                            echo $title;
                        }
                        echo "
                                            </h1>
                                        </td>
                                        <td class=\"right\">";
                        $r7 = mysql_query ( "SELECT status FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra7 = mysql_fetch_assoc ( $r7 ) ) {
                            if ( $ra7['status'] == 1 ) {
                                echo "<a href=\"change_status.php?c=" . $campaign_id . "&s=2\"><img src=\"../images/control_pause_blue.png\" alt=\"pause\" /></a>&nbsp;&nbsp;<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                            } else if ( $ra7['status'] == 2 ) {
                                echo "<a href=\"change_status.php?c=" . $campaign_id . "&s=1\"><img src=\"../images/control_play_blue.png\" alt=\"pause\" /></a>&nbsp;&nbsp;<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                            } else if($percentage != 100){
                                echo "<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>&nbsp;&nbsp;&nbsp;";
                            }
                        }
                        echo "
                                            <form class=\"inline\" method=\"post\" action=\"./?c=" . $campaign_id ."&responses=true". $tab_return ."\"><input type=\"image\" src=\"../images/arrow_refresh.png\"  alt=\"refresh\" /></form>
                                            <a class=\"tooltip\">
                                            <img src=\"../images/lightbulb.png\" alt=\"help\" />
                                            <span>This list provides you with a filtered view of campaign responses.  The title at the top left describes what filter is in place.  For each individual response you can see various metrics or analytics of the response itself such as the target's IP address, browser, browser version and Operating System.</span>
                                            </a>&nbsp;&nbsp;&nbsp;
                                            <a href=\".".$tab_return."\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table class=\"left\">
                                    <tr>
                                        <td>Date Started</td>
                                        <td>" . $date_sent . "</td>
                                    </tr>
                                    <tr>
                                        <td>Date Ended</td>
                                        <td>" . $date_ended . "</td>
                                    </tr>
                                    <tr>
                                        <td>Template</td>
                                        <td><a href=\"../templates/" . $template_id . "\" target=\"_blank\">" . $template_name . "</a></td>
                                    </tr>
                                    <tr>
                                        <td>Phishing URL</td>
                                        <td>" . $formulated_url . "</td>
                                    </tr>
                                    <tr>
                                        <td>Shortener Used</td>
                                        <td>" . $shorten . "</td>
                                    </tr>";
                        if ( $education_id != 0 ) {
                            $r3 = mysql_query ( "SELECT name FROM education WHERE id = '$education_id'" );
                            while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                                $education_name = $ra3['name'];
                            }
                            echo "
                                    <tr>
                                        <td>Education Package</td>
                                        <td><a href=\"../education/" . $education_id . "\" target=\"_blank\">" . $education_name . "</a></td>
                                    </tr>
                                    <tr>
                                        <td>Education Timing</td>
                                        <td>" . $education . "</td>
                                    </tr>";
                        } else {
                            echo "
                                    <tr>
                                        <td>Education</td>
                                        <td>None</td>
                                    </tr>";
                        }
                        echo "
                                    <tr>
                                        <td>Software Audit</td>
                                        <td>OS, Browser";
                        if($java == "1"){
                            echo ", Java";
                        }
                        if($flash == "1"){
                            echo ", Flash";
                        }
                        echo "</td>
                                    </tr>
                        ";
                        echo "
                                </table>
                                <br />
                                <table id=\"response_table\">
                                    <tr>
                                        <td><h3>Name</h3></td>
                                        <td><h3>Email</h3></td>
                                        <td><h3>Sent</h3></td>
                                        <td><h3>Clicked</h3></td>
                                        <td><h3>IP</h3></td>";
                        if ( $education_timing != 1 ) {
                            echo "
                                        <td><h3>Post</h3></td>";
                        }
                        if ( $education_id != 0 ) {
                            echo "
                                        <td><h3>Trained</h3></td>";
                        }
                        echo "
                                        <td><h3>Software</h3></td>";
                        echo "
                                        <td><h3>Status</h3></td>
                                    </tr>";
                        //dump data into table
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            echo "
                                    <tr>";
                            if(isset($previous_response_id) && $previous_response_id == $ra['response_id']){
                                echo "
                                            <td></td>
                                            <td></td>";
                            }else{
                                echo "
                                            <td>" . $ra['fname'] . " " . $ra['lname'] . "</td>
                                            <td>" . $ra['email'] . "</td>";
                            }
                            $previous_response_id = $ra['response_id'];
                            echo "
                                        <td>" . $ra['sent_time'] . "</td>";
                            if ( $ra['link'] == 1 ) {
                                echo "
                                        <td><a class=\"tooltip\">" . $ra['link_time'] . "<span>" . $ra['url'] . "</span></a></td>";
                            } else {
                                echo "
                                        <td><a class=\"tooltip\">N<span>" . $ra['url'] . "</span></a></td>";
                            }
                            echo "
                                        <td><a href=\"http://geomaplookup.net/?ip=" . $ra['ip'] . "\" target=\"blank\">" . $ra['ip'] . "</a></td>";
                            if ( $education_timing == 1 ) {
                            } else {
                                if ( strlen ( $ra['post'] ) < 1 ) {
                                    $post = 'N';
                                    echo "
                                        <td>" . $post . "</td>";
                                } else {
                                    $post = $ra['post'];
                                    $post_count = explode ( "<br />", $post );
                                    $post_count = count ( $post_count );
                                    echo "
                                        <td><a class=\"tooltip_sm\">" . $post_count . "<span>" . $post . "</span></a></td>";
                                }
                            }
                            if ( $education_id != 0 ) {
                                if ( $ra['trained'] == 1 ) {
                                    echo "
                                        <td>" . $ra['trained_time'] . "</td>";
                                } else {
                                    if ( $ra['link'] == 1 ) {
                                        echo "
                                        <td>Unknown</td>";
                                    } else {
                                        echo "
                                        <td>N</td>";
                                    }
                                }
                            }
                               echo "
                                        <td><a class=\"tooltip_sm\"><img src=\"../images/application_cascade_sm.png\" alt=\"applications\" /><span>";
                            echo "OS: ".$ra['os']."<br />";
                            echo "Browser: ".$ra['browser']." ".$ra['browser_version']."<br />";
                            if(strlen($ra['java']) > 0){
                                echo "Java: ".$ra['java']."<br />";
                            }
                            if(strlen($ra['flash']) > 0){
                                echo "Flash: ".$ra['flash']."<br />";
                            }
                            echo "
                                        </span></a></td>";
                         $log = $ra['response_log'];
                            if ( strlen ( $log ) < 1 ) {
                                $log = "The message was attempted, but no log was recorded";
                            }
                            echo "
                                        <td id=\"target_" . $ra['target_id'] . "\"><a class=\"tooltip\"><img src=\"../images/message_status_" . $ra['sent'] . ".png\" alt=\"message_status\" /><span>" . $log . "</span></a></td>";
                            echo "
                                    </tr>";
                        }
                        echo "
                                </table>
                            </div>
                        </div>";
                    }
                }
            ?>
            <!--content-->
            <div id="content">
                <br />
                <div id="tabs">
                    <?php
                        //get count of scheduled campaigns
                        $r = mysql_query("SELECT count(id) as count FROM campaigns WHERE status = 0");
                        while($ra = mysql_fetch_assoc($r)){
                            $scheduled = $ra['count'];
                        }
                        //get count of active campaigns
                        $r = mysql_query("SELECT count(id) as count FROM campaigns WHERE status = 1");
                        while($ra = mysql_fetch_assoc($r)){
                            $active = $ra['count'];
                        }
                        //get count of inactive campaigns
                        $r = mysql_query("SELECT count(id) as count FROM campaigns WHERE status = 2");
                        while($ra = mysql_fetch_assoc($r)){
                            $inactive = $ra['count'];
                        }
                        //get count of finished campaigns
                        $r = mysql_query("SELECT count(id) as count FROM campaigns WHERE status = 3");
                        while($ra = mysql_fetch_assoc($r)){
                            $finished = $ra['count'];
                        }

                    ?>
                    <ul>
                        <li><a href="#tabs-1">Overview</a></li>
                        <li><a href="#tabs-2">Scheduled<?php if(isset($scheduled) && $scheduled > 0){echo ' ['.$scheduled.']';}?></a></li>
                        <li><a href="#tabs-3">Active<?php if(isset($active) && $active > 0){echo ' ['.$active.']';}?></a></li>
                        <li><a href="#tabs-4">Inactive<?php if(isset($inactive) && $inactive > 0){echo ' ['.$inactive.']';}?></a></li>
                        <li><a href="#tabs-5">Finished<?php if(isset($finished) && $finished > 0){echo ' ['.$finished.']';}?></a></li>
                    </ul>
                    <div id="tabs-1">
                        <a href="?add_campaign=true#tabs-1" id="add_campaign_button" class="popover_button" ><img src="../images/email_to_friend_sm.png" alt="add" /> Campaign</a>
                        <a href="campaigns_export.php" id="campaign_export_button" class="popover_button" ><img src="../images/page_white_put_sm.png" alt="export" /> Export</a>
                        <table class="standard_table">
                            <?php
                                include '../spt_config/mysql_config.php';
                                //get data for summary table
                                $scheduled = mysql_query("SELECT count(distinct(campaigns.id)) AS scheduled, count(campaigns_responses.response_id) AS scheduled_targets FROM campaigns LEFT JOIN campaigns_responses ON campaigns.id = campaigns_responses.campaign_id WHERE campaigns.status = 0");
                                while($scheduled_results = mysql_fetch_assoc($scheduled)){
                                    $scheduled_counter = $scheduled_results['scheduled'];
                                    $scheduled_targets = $scheduled_results['scheduled_targets'];
                                }
                                $active = mysql_query("SELECT count(distinct(campaigns.id)) AS active, count(campaigns_responses.response_id) AS active_targets  FROM campaigns LEFT JOIN campaigns_responses ON campaigns.id = campaigns_responses.campaign_id WHERE campaigns.status = 1");
                                while($active_results = mysql_fetch_assoc($active)){
                                    $active_counter = $active_results['active'];
                                    $active_targets = $active_results['active_targets'];
                                }
                                $inactive = mysql_query("SELECT count(distinct(campaigns.id)) AS inactive, count(campaigns_responses.response_id) AS inactive_targets FROM campaigns LEFT JOIN campaigns_responses ON campaigns.id = campaigns_responses.campaign_id WHERE campaigns.status = 2");
                                while($inactive_results = mysql_fetch_assoc($inactive)){
                                    $inactive_counter = $inactive_results['inactive'];
                                    $inactive_targets = $inactive_results['inactive_targets'];
                                }
                                $finished = mysql_query("SELECT count(distinct(campaigns.id)) AS finished, count(campaigns_responses.response_id) AS finished_targets FROM campaigns LEFT JOIN campaigns_responses ON campaigns.id = campaigns_responses.campaign_id WHERE campaigns.status = 3");
                                while($finished_results = mysql_fetch_assoc($finished)){
                                    $finished_counter = $finished_results['finished'];
                                    $finished_targets = $finished_results['finished_targets'];
                                }
                            echo '
                            <tr>
                                <td></td>
                                <td colspan=2 style="text-align:center">Scheduled</td>
                                <td colspan=2 style="text-align:center">Active</td>
                                <td colspan=2 style="text-align:center">Inactive</td>
                                <td colspan=2 style="text-align:center">Finished</td>
                            </tr>
                            <tr>
                                <td>Campaigns</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lightgray">'.$scheduled_counter.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:yellow">'.$active_counter.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lightskyblue">'.$inactive_counter.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lime">'.$finished_counter.'</td>
                            </tr>
                            <tr>
                                <td>Targets</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lightgray">'.$scheduled_targets.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:yellow">'.$active_targets.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lightskyblue">'.$inactive_targets.'</td>
                                <td colspan=2 style="text-align:center;font-size:48px;background-color:lime">'.$finished_targets.'</td>
                            </tr>
                        </table>';
                        ?>
                    </div>
                    <div id="tabs-2">
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Name</h3></td>
                                <td><h3>Target Groups</h3></td>
                                <td><h3>Template</h3></td>
                                <td><h3>Education</h3></td>
                                <td><h3>Start Time</h3></td>
                                <td><h3>Actions</h3></td>
                            </tr>
                            <?php
                                //connect to database
                                include "../spt_config/mysql_config.php";
                                //pull in list of all campaigns
                                $r = mysql_query ( "SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id WHERE campaigns.status = 0 ORDER BY campaigns.id DESC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                    echo "
                                            <tr>
                                                <td><a href=\"?c=" . $ra['id'] . "&edit_campaign=true#tabs-2\">" . $ra['campaign_name'] . "</a></td>\n
                                                <td>";
                                    $campaign_id = $ra['id'];
                                    //pull in groups
                                    $r3 = mysql_query ( "SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                                        echo "<a href=\"?c=" . $ra['id'] . "&amp;g=" . $ra3['group_name'] . "&responses=true#tabs-2\">" . $ra3['group_name'] . "</a><br />\n";
                                    }
                                    echo "</td>";
                                    echo "<td><a href=\"../templates/" . $ra['template_id'] . "/\" target=\"_blank\">" . $ra['name'] . "</a></td>\n";
                                    echo "<td><a href=\"../education/" . $ra['education_id'] . "/\" target=\"_blank\">" . $ra['education_name'] . "</a></td>\n";
                                    //get cron_id
                                    $r1 = mysql_query("SELECT cron_id FROM campaigns WHERE id = '$campaign_id'");
                                    while($ra1 = mysql_fetch_assoc($r1)){
                                        $cron_id = $ra1['cron_id'];
                                        $crontab_output = shell_exec('crontab -l|sed -n \'/'.$cron_id.'/p\'');
                                        preg_match_all('/(\d\d|\d)/',$crontab_output,$matches);
                                        $minute = $matches[0][0];
                                        $hour = $matches[0][1];
                                        $day = $matches[0][2];
                                        $month = $matches[0][3];
                                        if(strlen($minute) == 1){
                                            $minute = "0".$minute;
                                        }
                                        if(strlen($hour) == 1){
                                            $hour = "0".$hour;
                                        }
                                        if(strlen($day) == 1){
                                            $day = "0".$day;
                                        }
                                        if(strlen($month) == 1){
                                            $month = "0".$month;
                                        }
                                        $date = $month."-".$day." ".$hour.":".$minute;
                                        echo "<td>".$date."</td>";
                                    }
                                    echo "<td><a href=\"delete_campaign.php?c=" . $campaign_id . "&tab_return=2\"><img src=\"../images/report_delete_sm.png\" alt=\"delete\" /></a></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <div id="tabs-3">
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Name</h3></td>
                                <td><h3>Target Groups</h3></td>
                                <td><h3>Template</h3></td>
                                <td><h3>Education</h3></td>
                                <td><h3>Links</h3></td>
                                <td><h3>Posts</h3></td>
                                <td><h3>Progress</h3></td>
                                <td><h3>Delete</h3></td>
                            </tr>
                            <?php
                                //connect to database
                                include "../spt_config/mysql_config.php";
                                //pull in list of all campaigns
                                $r = mysql_query ( "SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id WHERE campaigns.status = 1 ORDER BY campaigns.id DESC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                    echo "
                                            <tr>
                                                <td><a href=\"?c=" . $ra['id'] . "&responses=true#tabs-3\">" . $ra['campaign_name'] . "</a></td>\n
                                                <td>";
                                    $campaign_id = $ra['id'];
                                    //pull in groups
                                    $r3 = mysql_query ( "SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                                        echo "<a href=\"?c=" . $ra['id'] . "&amp;g=" . $ra3['group_name'] . "&responses=true#tabs-3\">" . $ra3['group_name'] . "</a><br />\n";
                                    }
                                    echo "</td>";
                                    echo "<td><a href=\"../templates/" . $ra['template_id'] . "/\" target=\"_blank\">" . $ra['name'] . "</a></td>\n";
                                    echo "<td><a href=\"../education/" . $ra['education_id'] . "/\" target=\"_blank\">" . $ra['education_name'] . "</a></td>\n";
                                    $r2 = mysql_query ( "SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                                        $link = $ra2['link'];
                                        $post = $ra2['post'];
                                    }
                                    echo "<td><a href=\"?c=" . $ra['id'] . "&amp;f=link&responses=true#tabs-3\">" . $link . "</a></td><td><a href=\"?c=" . $ra['id'] . "&amp;f=post&responses=true#tabs-3\">" . $post . "</a></td>";
                                    echo "<td>";
                                    $r5 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id' AND sent != 0" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    $r6 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    $sent = mysql_num_rows ( $r5 );
                                    $total = mysql_num_rows ( $r6 );
                                    $percentage = ceil ( ($sent / $total) * 100 );
                                    echo "<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                                    echo "</td>";
                                    echo "<td><a href=\"delete_campaign.php?c=" . $campaign_id . "&tab_return=3\"><img src=\"../images/report_delete_sm.png\" alt=\"delete\" /></a></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <div id="tabs-4">
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Name</h3></td>
                                <td><h3>Target Groups</h3></td>
                                <td><h3>Template</h3></td>
                                <td><h3>Education</h3></td>
                                <td><h3>Links</h3></td>
                                <td><h3>Posts</h3></td>
                                <td><h3>Progress</h3></td>
                                <td><h3>Delete</h3></td>
                            </tr>
                            <?php
                                //connect to database
                                include "../spt_config/mysql_config.php";
                                //pull in list of all campaigns
                                $r = mysql_query ( "SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id WHERE campaigns.status = 2 ORDER BY campaigns.id DESC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                    echo "
                                            <tr>
                                                <td><a href=\"?c=" . $ra['id'] . "&responses=true#tabs-4\">" . $ra['campaign_name'] . "</a></td>\n
                                                <td>";
                                    $campaign_id = $ra['id'];
                                    //pull in groups
                                    $r3 = mysql_query ( "SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                                        echo "<a href=\"?c=" . $ra['id'] . "&amp;g=" . $ra3['group_name'] . "&responses=true#tabs-4\">" . $ra3['group_name'] . "</a><br />\n";
                                    }
                                    echo "</td>";
                                    echo "<td><a href=\"../templates/" . $ra['template_id'] . "/\" target=\"_blank\">" . $ra['name'] . "</a></td>\n";
                                    echo "<td><a href=\"../education/" . $ra['education_id'] . "/\" target=\"_blank\">" . $ra['education_name'] . "</a></td>\n";
                                    $r2 = mysql_query ( "SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                                        $link = $ra2['link'];
                                        $post = $ra2['post'];
                                    }
                                    echo "<td><a href=\"?c=" . $ra['id'] . "&amp;f=link&responses=true#tabs-4\">" . $link . "</a></td><td><a href=\"?c=" . $ra['id'] . "&amp;f=post&responses=true#tabs-4\">" . $post . "</a></td>";
                                    echo "<td>";
                                    $r5 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id' AND sent != 0" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    $r6 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    $sent = mysql_num_rows ( $r5 );
                                    $total = mysql_num_rows ( $r6 );
                                    $percentage = ceil ( ($sent / $total) * 100 );
                                    echo "<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                                    echo "</td>";
                                    echo "<td><a href=\"delete_campaign.php?c=" . $campaign_id . "&tab_return=4\"><img src=\"../images/report_delete_sm.png\" alt=\"delete\" /></a></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <div id="tabs-5">
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Name</h3></td>
                                <td><h3>Target Groups</h3></td>
                                <td><h3>Template</h3></td>
                                <td><h3>Education</h3></td>
                                <td><h3>Links</h3></td>
                                <td><h3>Posts</h3></td>
                                <td><h3>End Time</h3></td>
                                <td><h3>Delete</h3></td>
                            </tr>
                            <?php
                                //connect to database
                                include "../spt_config/mysql_config.php";
                                //pull in list of all campaigns
                                $r = mysql_query ( "SELECT campaigns.date_ended as date_ended, campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id WHERE campaigns.status = 3 ORDER BY campaigns.id DESC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                    echo "
                                            <tr>
                                                <td><a href=\"?c=" . $ra['id'] . "&responses=true#tabs-5\">" . $ra['campaign_name'] . "</a></td>\n
                                                <td>";
                                    $campaign_id = $ra['id'];
                                    //pull in groups
                                    $r3 = mysql_query ( "SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                                        echo "<a href=\"?c=" . $ra['id'] . "&amp;g=" . $ra3['group_name'] . "&responses=true#tabs-4\">" . $ra3['group_name'] . "</a><br />\n";
                                    }
                                    echo "</td>";
                                    echo "<td><a href=\"../templates/" . $ra['template_id'] . "/\" target=\"_blank\">" . $ra['name'] . "</a></td>\n";
                                    echo "<td><a href=\"../education/" . $ra['education_id'] . "/\" target=\"_blank\">" . $ra['education_name'] . "</a></td>\n";
                                    $r2 = mysql_query ( "SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                    while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                                        $link = $ra2['link'];
                                        $post = $ra2['post'];
                                    }
                                    echo "<td><a href=\"?c=" . $ra['id'] . "&amp;f=link&responses=true#tabs-4\">" . $link . "</a></td><td><a href=\"?c=" . $ra['id'] . "&amp;f=post&responses=true#tabs-4\">" . $post . "</a></td>";
                                    echo "<td>".$ra['date_ended']."</td>";
                                    echo "<td><a href=\"delete_campaign.php?c=" . $campaign_id . "&tab_return=5\"><img src=\"../images/report_delete_sm.png\" alt=\"delete\" /></a></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
