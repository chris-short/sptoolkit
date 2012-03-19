<?php
/**
 * file:    index.php
 * version: 41.0
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
            <div id="add_campaign">
                <div>
                    <form method="post" action="start_campaign.php">
                        <table id="new_campaign">
                            <tr>
                                <td colspan="2"><h3>Add Campaign</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Specify a name for this campaign that will be displayed in the campaign list on the previous screen.  Use a descriptive name that will help you identify this campaign later. The Path  has been pre-populated for you with the hostname you are currently connecting to spt with.  You can create alterante DNS records that correspond with your campaigns and enter them here.  Whatever you specify in the path field is what your targets will be linked to.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td colspan="2"><input name="campaign_name" /></td>
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
                                    echo "<input type=\"text\" name=\"spt_path\" value=\"" . $path . "\" size=\"45\"/>";
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
                                            echo "<option>" . $ra['group_name'] . "</option>";
                                        }
                                        ?>	
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>Template</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the template that will be used for this campaign.  You can view/edit the email by clicking the link next to Email.  Be careful, as editing the email will edit the email for all future campaigns that use this template.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Webpage</td>
                                <td colspan="2">
                                    <select name = "template_id">
                                        <?php
//connect to database
                                        include('../spt_config/mysql_config.php');

//query for all groups
                                        $r = mysql_query ( 'SELECT id, name FROM templates' );
                                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                            echo "<option value=" . $ra['id'] . ">" . $ra['name'] . "</option>";
                                        }
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
                                            echo "<option value=" . $ra['id'] . ">" . $ra['name'] . "</option>";
                                        }
                                        ?>	
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"><input type="radio" name="education_timing" value="1" /> Educate on link click<br /><input type="radio" name="education_timing" value="2" /> Educate on form submission</td>
                            </tr>
                            <tr>
                                <td colspan="2"><h3>SMTP Relay</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter your SMTP relay's details if necessary.  You may also enter credentials if your SMTP requires authentication.  If you leave these fields blank, spt will act as an SMTP server and send emails directly to the destination's mail gateway based on the MX records published by your target's domain.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Host</td>
                                <td colspan="2"><input type="text" name="relay_host" size="30"/></td>
                            </tr>
                            <tr>
                                <td>Port</td>
                                <td colspan="2"><input type="text" name="relay_port" size="6" value="25" /></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td colspan="2"><input type="text" name="relay_username" /></td>
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
                                <td colspan="2"><input type="text" name="message_delay" value="1000" />&nbsp;<i>ms</i> (100-60000)</td>
                            </tr>
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
                        $r = mysql_query ( "SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                        //title the page with the campaign number
                        $title = $campaign_name . " :: All Responses";
                    }

//pull data if a group is set
                    if ( isset ( $group ) ) {
                        $r = mysql_query ( "SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE targets.group_name = '$group' AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                        //title the page with the campaign number
                        $title = $campaign_name . " :: " . $group;
                    }

//pull data if a filter is set
                    if ( isset ( $filter ) ) {
                        //if filter is for links
                        if ( $filter == "link" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.link = 1 AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                            //title the page with the campaign number
                            $title = $campaign_name;

                            if ( isset ( $group ) ) {
                                $title .= " :: " . $group;
                            }

                            $title .= " :: Links";
                        }

                        //if filter is for posts
                        if ( $filter == "post" ) {
                            $r = mysql_query ( "SELECT campaigns_responses.target_id AS target_id, campaigns_responses.campaign_id AS campaign_id, campaigns_responses.link AS link, campaigns_responses.post AS post, targets.id AS id, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, campaigns_responses.sent AS sent, campaigns_responses.response_log AS response_log FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id WHERE campaigns_responses.post != \"\"  AND campaigns_responses.campaign_id = '$campaign_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

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
                        $r2 = mysql_query ( "SELECT date_sent, date_ended, campaign_name, domain_name, education_id, template_id, education_timing FROM campaigns WHERE id = '$campaign_id'" );
                        while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                            $date_sent = $ra2['date_sent'];
                            $date_ended = $ra2['date_ended'];
                            $campaign_name = $ra2['campaign_name'];
                            $formulated_url = "http://" . $ra2['domain_name'] . "/campaigns/response.php?r=response_key";
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
                    <td>Date Sent</td>
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
            </table>
            <br />
            <table id=\"response_table\">
                <tr>
                    <td><h3>ID</h3></td>
                    <td><h3>First Name</h3></td>
                    <td><h3>Last Name</h3></td>
                    <td><h3>Email</h3></td>
                    <td><h3>Link</h3></td>
                    <td><h3>Clicked at</h3></td>
                    <td><h3>IP</h3></td>
                    <td><h3>Browser</h3></td>
                    <td><h3>Version</h3></td>
                    <td><h3>OS</h3></td>
                    <td><h3>Post</h3></td>                    
                    <td><h3>Status</h3></td>
                </tr>";

                        //dump data into table
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            echo "<tr>";
                            echo "<td>" . $ra['target_id'] . "</td>";
                            echo "<td>" . $ra['fname'] . "</td>";
                            echo "<td>" . $ra['lname'] . "</td>";
                            echo "<td>" . $ra['email'] . "</td>";
                            if ( $ra['link'] == 1 ) {
                                $link = 'Y';
                            } else {
                                $link = 'N';
                            }
                            echo "<td>" . $link . "</td>";
                            echo "<td>" . $ra['link_time'] . "</td>";
                            echo "<td><a href=\"http://geomaplookup.net/?ip=" . $ra['ip'] . "\" target=\"blank\">" . $ra['ip'] . "</a></td>";
                            echo "<td>" . $ra['browser'] . "</td>";
                            echo "<td>" . $ra['browser_version'] . "</td>";
                            echo "<td>" . $ra['os'] . "</td>";
                            if ( strlen ( $ra['post'] ) < 1 ) {
                                $post = 'N';
                                echo "<td>" . $post . "</td>";
                            } else {
                                $post = $ra['post'];
                                $post_count = explode ( "<br />", $post );
                                $post_count = count ( $post_count );
                                echo "<td><a class=\"tooltip_sm\">" . $post_count . "<span>" . $post . "</span></a></td>";
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

            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <span class="button"><a href="#add_campaign"><img src="../images/email_to_friend_sm.png" alt="add" /> Campaign</a></span>
                <span class="button"><a href="campaigns_export.php"><img src="../images/page_white_put_sm.png" alt="export" /> Export</a></span>
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
                    $r = mysql_query ( "SELECT campaigns.id, campaigns.campaign_name, campaigns.template_id, campaigns.education_id, templates.name as name, education.name as education_name FROM campaigns JOIN templates ON campaigns.template_id = templates.id LEFT JOIN education ON campaigns.education_id = education.id" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
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
