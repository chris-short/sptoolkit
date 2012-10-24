<?php
/**
 * file:    index.php
 * version: 53.0
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
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_campaigns.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
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
    var campaign_id;
    //determine if a campaign is set
    if ( campaign_id != null){
        //start the email function
        sendEmail(campaign_id);
    }
        </script>
    </head>
    <body>
        <div id="wrapper">
            <!--popovers-->
            <div id="add_campaign">
                <div>
                    <form method="post" action="start_campaign.php">
                        <table id="new_campaign">
                            <tr>
                                <td colspan="2"><h3>Add Campaign</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Specify a name for this campaign that will be displayed in the campaign list on the previous screen.  Use a descriptive name that will help you identify this campaign later.<br /><br />The Path  has been pre-populated for you with the hostname you are currently connecting to spt with.  You can create alternate DNS records that correspond with your campaigns and enter them here.  Whatever you specify in the path field is what will be used to formulate the unique link for each target.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td colspan="2"><input name="campaign_name" <?php
if ( isset ( $_SESSION['temp_campaign_name'] ) ) {
    echo "value=\"" . $_SESSION['temp_campaign_name'] . "\"";
    unset ( $_SESSION['temp_campaign_name'] );
}
?> /></td>
                            </tr>
                            <tr>
                                <td>Path</td>
                                <td colspan="2">
                                    <?php
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
                                    echo "\" size=\"45\"/>";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>Targets</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select one or more groups of targets (hold CTRL or COMMAND to multi-select) that will be included in this campaign</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Group(s)</td>
                                <td colspan="2">
                                    <select name = "target_groups[]" multiple="multiple" size="5" style="width: 80%;">
                                        <?php
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
                                        ?>    
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>Template</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the template that will be used for this campaign.  You can view/edit the email by going to the editor module and editing the respective email.php file.  Be careful, as editing this file will edit the email for all future campaigns that use this template.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Email/Webpage</td>
                                <td colspan="2">
                                    <select name = "template_id">
                                        <?php
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
                                        ?>	
                                    </select>
                                </td>
                            </tr>
                            <!--<tr>
                                <td>Email</td>
                                <td colspan="2">View/Edit Email link coming soon...</td>
                            </tr>-->
                            <tr>
                                <td colspan="3"><br /></td>
                            </tr>
                            <tr class="solid_border">
                                <td colspan="3"><h3><i>Optional Settings</i></h3></td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>Education</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the education package that the target will be directed to.  Select "Education on link click" if you would like the targets to bypass the template's webpage and go directly to training.  Select "Educate on form submission" if you would like the target to be directed to training after they have submitted a form on your template's webpage.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Package</td>
                                <td colspan="2">
                                    <select name = "education_id">
                                        <option value="0">None</option>
                                        <?php
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
                                        ?>	
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"><input type="radio" name="education_timing" value="1" <?php
                                        if ( isset ( $_SESSION['temp_education_timing'] ) && $_SESSION['temp_education_timing'] == 1 ) {
                                            echo "CHECKED";
                                        }
                                        ?>/> Educate on link click<br /><input type="radio" name="education_timing" value="2" <?php
                                                       if ( isset ( $_SESSION['temp_education_timing'] ) && $_SESSION['temp_education_timing'] == 2 ) {
                                                           echo "CHECKED";
                                                           unset ( $_SESSION['temp_education_timing'] );
                                                       }
                                        ?>/> Educate on form submission</td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>SMTP Relay</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter your SMTP relay's details if necessary.  You may also enter credentials if your SMTP requires authentication.  If you leave these fields blank, spt will act as an SMTP server and send emails directly to the destination's mail gateway based on the MX records published by your target's domain.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Host</td>
                                <td colspan="2"><input type="text" name="relay_host" size="30" <?php
                                                       if ( isset ( $_SESSION['temp_relay_host'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_relay_host'] . "\"";
                                                           unset ( $_SESSION['temp_relay_host'] );
                                                       }
                                        ?> /></td>
                            </tr>
                            <tr>
                                <td>Port</td>
                                <td colspan="2"><input type="text" name="relay_port" size="6" <?php
                                                       if ( isset ( $_SESSION['temp_relay_port'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_relay_port'] . "\"";
                                                           unset ( $_SESSION['temp_relay_port'] );
                                                       } else {
                                                           echo "value=\"25\"";
                                                       }
                                        ?> /></td>
                            </tr>
                            <tr>
                                <td>SSL</td>
                                <td colspan="2"><?php
                                                       $transports = stream_get_transports ();
                                                       if ( (array_search ( "ssl", $transports )) OR (array_search ( "tls", $transports )) ) {
                                                          echo "<input type=\"checkbox\" name=\"ssl\" ";
                                                           if ( isset ( $_SESSION['temp_ssl'] ) ) {
                                                               echo "CHECKED";
                                                               unset ( $_SESSION['temp_ssl'] );
                                                           }
                                                           echo "/><br />";
                                                       } else {
                                                           echo "<a class=\"tooltip\"><img src=\"../images/lightbulb_sm.png\" alt=\"help\" /><span>Missing SSL or TLS transport.</span></a>";
                                                       }
                                        ?></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td colspan="2"><input type="text" name="relay_username" <?php
                                    if ( isset ( $_SESSION['temp_relay_username'] ) ) {
                                        echo "value=\"" . $_SESSION['temp_relay_username'] . "\"";
                                        unset ( $_SESSION['temp_relay_username'] );
                                    }
                                        ?>/></td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td colspan="2"><input type="password" name="relay_password" /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>Throttling</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>By default spt will send an email every second (or 1000 ms).  You can change the default message delay to 100 ms and batches of 10 emails will be sent each second.  Or you can create as high as a 1 minute delay between each message by specifying 60000 ms between each message.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Delay</td>
                                <td colspan="2"><input type="text" name="message_delay" <?php
                                                       if ( isset ( $_SESSION['temp_message_delay'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_message_delay'] . "\"";
                                                           unset ( $_SESSION['temp_message_delay'] );
                                                       } else {
                                                           echo "value=\"1000\"";
                                                       }
                                        ?> />&nbsp;<i>ms</i> (100-60000)</td>

                            </tr>
                            <tr>
                                <td colspan="2"><h3>Shorten</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>If you want to further mask the phishing link in your emails using a popular URL shortening service, select the service below you'd like to shorten with.<br /><br />You may have to provide an API key for some services by opening the Shorten button at the top of this page.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><input type="radio" name="shorten_radio" value="Google" <?php
                                                       if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "google" ) {
                                                           echo "checked";
                                                           unset ( $_SESSION['temp_shorten'] );
                                                       }
                                        ?> />&nbsp;Google&nbsp;&nbsp;
                                    <input type="radio" name="shorten_radio" value="TinyURL" <?php
                                                       if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "tinyurl" ) {
                                                           echo "checked";
                                                           unset ( $_SESSION['temp_shorten'] );
                                                       }
                                        ?> />&nbsp;TinyURL&nbsp;&nbsp;
                                    <input type="radio" name="shorten_radio" value="None" <?php
                                           if ( isset ( $_SESSION['temp_shorten'] ) && $_SESSION['temp_shorten'] == "none" ) {
                                               echo "checked";
                                               unset ( $_SESSION['temp_shorten'] );
                                           }
                                        ?> />&nbsp;None&nbsp;&nbsp;</td>
                            </tr>
                            <?php
                            if ( isset ( $_SESSION['alert_message'] ) ) {
                                echo "<tr><td colspan=3 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                            }
                            ?>
                            <tr>
                                <td colspan="3" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="tooltip"><input type="image" src="../images/accept.png" alt="accept" /><span><b>WARNING:</b> When you click this button, you will be directed to the campaign response page for this new campaign and emails will begin to be sent.</span></a></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <div id="responses">
                <div>
                    <?php
//connect to database
                    include "../spt_config/mysql_config.php";

//pull in campaign id
                    if ( isset ( $_REQUEST['c'] ) ) {
                        $campaign_id = filter_var ( $_REQUEST['c'], FILTER_SANITIZE_NUMBER_INT );

                        //get campaign name
                        $r = mysql_query ( "SELECT campaign_name FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            $campaign_name = $ra['campaign_name'];
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
                            header ( 'location:./#alert' );
                            exit;
                        }
                    }

//pull in filter and group
                    if ( isset ( $_REQUEST['f'] ) ) {
                        $filter = filter_var ( $_REQUEST['f'], FILTER_SANITIZE_STRING );

                        //go ahead and preform validation
                        if ( $filter != "link" && $filter != "post" ) {
                            $_SESSION['alert_message'] = "please use a valid filter";
                            header ( 'location:./#alert' );
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
                            header ( 'location:./#alert' );
                            exit;
                        }
                    }

//if group and filter are both set send them back
                    if ( isset ( $filter ) && isset ( $group ) ) {
                        $_SESSION['alert_message'] = "you cannot pass both a filter and a group";
                        header ( 'location:./#alert' );
                        exit;
                    }

//pull data for entire campaign if group and filters are NOT set
                    if ( ! isset ( $group ) && ! isset ( $filter ) && isset ( $campaign_id ) ) {
                        $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                        //title the page with the campaign number
                        $title = $campaign_name . " :: All Responses";
                    }

//pull data if a group is set
                    if ( isset ( $group ) ) {
                        $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE targets.group_name = '$group' AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                        //title the page with the campaign number
                        $title = $campaign_name . " :: " . $group;
                    }

//pull data if a filter is set
                    if ( isset ( $filter ) ) {
                        //if filter is for links
                        if ( $filter == "link" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.link = 1 AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                            //title the page with the campaign number
                            $title = $campaign_name;

                            if ( isset ( $group ) ) {
                                $title .= " :: " . $group;
                            }

                            $title .= " :: Links";
                        }

                        //if filter is for posts
                        if ( $filter == "post" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns_responses.sent_time AS sent_time, campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.post != \"\"  AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

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
                        $r2 = mysql_query ( "SELECT relay_host, relay_port, date_sent, date_ended, campaign_name, domain_name, education_id, template_id, education_timing, shorten FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                            $date_sent = $ra2['date_sent'];
                            $date_ended = $ra2['date_ended'];
                            $campaign_name = $ra2['campaign_name'];
                            $phishing_domain = $ra2['domain_name'];
                            $shorten = $ra2['shorten'];
                            $education_id = $ra2['education_id'];
                            $template_id = $ra2['template_id'];
                            $relay_host = $ra2['relay_host'];
                            $relay_port = $ra2['relay_port'];
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
                        }echo "
                    </h1>
                </td>
                    <td class=\"right\">";
                        $r7 = mysql_query ( "SELECT status FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra7 = mysql_fetch_assoc ( $r7 ) ) {
                            if ( $ra7['status'] == 1 ) {
                                echo "<a href=\"change_status.php?c=" . $campaign_id . "&s=2\"><img src=\"../images/control_pause_blue.png\" alt=\"pause\" /></a>&nbsp;&nbsp;<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                            } else if ( $ra7['status'] == 2 ) {
                                echo "<a href=\"change_status.php?c=" . $campaign_id . "&s=1\"><img src=\"../images/control_play_blue.png\" alt=\"pause\" /></a>&nbsp;&nbsp;<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                            } else {
                                echo "<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>&nbsp;&nbsp;&nbsp;";
                            }
                        }
                        echo "
                        <form class=\"inline\" method=\"post\" action=\"./?c=" . $campaign_id . "#responses\"><input type=\"image\" src=\"../images/arrow_refresh.png\"  alt=\"refresh\" /></form>
                        <a class=\"tooltip\">
                        <img src=\"../images/lightbulb.png\" alt=\"help\" />
                        <span>This list provides you with a filtered view of campaign responses.  The title at the top left describes what filter is in place.  For each individual response you can see various metrics or analytics of the response itself such as the target's IP address, browser, browser version and Operating System.</span>
                        </a>&nbsp;&nbsp;&nbsp;
                        <a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>
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
                    <td>none</td>
                </tr>";
                        }
            echo "
                <tr>
                    <td>Phishing Domain</td>
                    <td>" . $phishing_domain . "</td>
                </tr>
                <tr>
                    <td>Relay</td>";
                    if ( strlen($relay_host) > 1 ) {
                        echo "<td>".$relay_host;
                        if(strlen($relay_port) > 1) {
                            echo ":".$relay_port;
                        }
                        echo "</td>";
                    }else{
                        echo "<td>none</td>";
                    }

                echo "
                <tr>
                    <td>Shortener Used</td>
                    <td>" . $shorten . "</td>
                </tr>
            </table>
            <br />
            <table id=\"response_table\">
                <tr>
                    <td><h3>Name</h3></td>
                    <td><h3>Email</h3></td>
                    <td><h3>Sent</h3></td>
                    <td><h3>Clicked</h3></td>
                    <td><h3>IP</h3></td>
                    <td><h3>Browser</h3></td>
                    <td><h3>OS</h3></td>";
                        if ( $education_timing != 1 ) {
                            echo "<td><h3>Post</h3></td>";
                        }
                        if ( $education_id != 0 ) {
                            echo "<td><h3>Trained</h3></td>";
                        }
                        echo "
                    <td><h3>Status</h3></td>
                </tr>";

                        //dump data into table
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            echo "<tr>";
                            echo "<td>" . $ra['fname'] . " " . $ra['lname'] . "</td>";
                            echo "<td>" . $ra['email'] . "</td>";
                            echo "<td>" . $ra['sent_time'] . "</td>";
                            if ( $ra['link'] == 1 ) {
                                echo "<td><a class=\"tooltip\">" . $ra['link_time'] . "<span>" . $ra['url'] . "</span></a></td>";
                            } else {
                                echo "<td><a class=\"tooltip\">N<span>" . $ra['url'] . "</span></a></td>";
                            }
                            echo "<td><a href=\"http://geomaplookup.net/?ip=" . $ra['ip'] . "\" target=\"blank\">" . $ra['ip'] . "</a></td>";
                            echo "<td>" . $ra['browser'] . " " . $ra['browser_version'] . "</td>";
                            echo "<td>" . $ra['os'] . "</td>";
                            if ( $education_timing == 1 ) {
                                
                            } else {
                                if ( strlen ( $ra['post'] ) < 1 ) {
                                    $post = 'N';
                                    echo "<td>" . $post . "</td>";
                                } else {
                                    $post = $ra['post'];
                                    $post_count = explode ( "<br />", $post );
                                    $post_count = count ( $post_count );
                                    echo "<td><a class=\"tooltip_sm\">" . $post_count . "<span>" . $post . "</span></a></td>";
                                }
                            }
                            if ( $education_id != 0 ) {
                                if ( $ra['trained'] == 1 ) {
                                    echo "<td>" . $ra['trained_time'] . "</td>";
                                } else {
                                    if ( $ra['link'] == 1 ) {
                                        echo "<td>Unknown</td>";
                                    } else {
                                        echo "<td>N</td>";
                                    }
                                }
                            }
                            $log = $ra['response_log'];
                            if ( strlen ( $log ) < 1 ) {
                                $log = "The message was attempted, but no log was recorded";
                            }
                            echo "<td id=\"target_" . $ra['target_id'] . "\"><a class=\"tooltip\"><img src=\"../images/message_status_" . $ra['sent'] . ".png\" alt=\"message_status\" /><span>" . $log . "</span></a></td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    }
                    ?>
                </div>
            </div>
            <div id="shorten">
                <div>
                    <form method="post" action="config_shorten.php">
                        <table id="config_shorten">
                            <tr>
                                <td colspan="2"><h3>Shorten Settings</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter your Google API key for the Google Shortener service to enable the ability to mask and shorten your URLs within campagins using goo.gl URLs.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="https://code.google.com/apis/console">Google API key</a></td>
                                <td><input type="text" name="google" <?php
                    include "../spt_config/mysql_config.php";
                    $sql = "SELECT api_key FROM campaigns_shorten WHERE service = 'google'";
                    $r = mysql_query ( $sql );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "value=\"" . $ra['api_key'] . "\" ";
                    }
                    ?> />
                                </td>
                            </tr>
                            <?php
                            if ( isset ( $_SESSION['alert_message'] ) ) {
                                echo "<tr><td colspan=3 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                            }
                            ?>
                            <tr>
                                <td colspan="3" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <?php
//check to see if the alert session is set
            if ( isset ( $_SESSION['alert_message'] ) ) {
//create alert popover
                echo "<div id=\"alert\">";

//echo the alert message
                echo "<div>" . $_SESSION['alert_message'] . "<br /><br /><a href=\"\"><img src=\"../images/accept.png\" alt=\"close\" /></a></div>";

//unset the seession
                unset ( $_SESSION['alert_message'] );

//close alert popover
                echo "</div>";
            }
            ?>
            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <span class="button"><a href="#add_campaign"><img src="../images/email_to_friend_sm.png" alt="add" /> Campaign</a></span>
                <span class="button"><a href="campaigns_export.php"><img src="../images/page_white_put_sm.png" alt="export" /> Export</a></span>
                <span class="button"><a href="#shorten"><img src="../images/cog_edit_sm.png" alt="config_shorten" />Shorten</a></span>
                <table class="spt_table">
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
                    $r = mysql_query ( "SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id ORDER BY campaigns.id DESC" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "
                                <tr>
                                    <td><a href=\"?c=" . $ra['id'] . "#responses\">" . $ra['campaign_name'] . "</a></td>\n
                                    <td>";

                        $campaign_id = $ra['id'];

                        //pull in groups
                        $r3 = mysql_query ( "SELECT group_name FROM campaigns_and_groups WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra3 = mysql_fetch_assoc ( $r3 ) ) {
                            echo "<a href=\"?c=" . $ra['id'] . "&amp;g=" . $ra3['group_name'] . "#responses\">" . $ra3['group_name'] . "</a><br />\n";
                        }
                        echo "</td>";
                        echo "<td><a href=\"../templates/" . $ra['template_id'] . "/\" target=\"_blank\">" . $ra['name'] . "</a></td>\n";
                        echo "<td><a href=\"../education/" . $ra['education_id'] . "/\" target=\"_blank\">" . $ra['education_name'] . "</a></td>\n";

                        $r2 = mysql_query ( "SELECT count(target_id) as count, sum(link) as link, sum(if(length(post) > 0, 1, 0)) as post FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                            $link = $ra2['link'];
                            $post = $ra2['post'];
                        }

                        echo "<td><a href=\"?c=" . $ra['id'] . "&amp;f=link#responses\">" . $link . "</a></td><td><a href=\"?c=" . $ra['id'] . "&amp;f=post#responses\">" . $post . "</a></td>";
                        echo "<td>";
                        $r5 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id' AND sent != 0" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        $r6 = mysql_query ( "SELECT sent FROM campaigns_responses WHERE campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        $sent = mysql_num_rows ( $r5 );
                        $total = mysql_num_rows ( $r6 );
                        $percentage = ceil ( ($sent / $total) * 100 );
                        echo "<progress id=\"message_progress\" max=\"100\" value=\"" . $percentage . "\"></progress>";
                        echo "</td>";
                        echo "<td><a href=\"delete_campaign.php?c=" . $campaign_id . "\"><img src=\"../images/report_delete_sm.png\" alt=\"delete\" /></a></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
