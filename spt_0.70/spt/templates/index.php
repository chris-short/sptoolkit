<?php
/**
 * file:    index.php
 * version: 39.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Template management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:	GNU/GPL, see license.htm.
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
        <title>spt - templates</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_templates.css" type="text/css" />
        <!--[if IE]>
        <link rel="stylesheet" href="../includes/spt_ie.css" type="text/css" />
        <![endif]-->
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <!--popovers-->
            <?php
            if ( isset ( $_REQUEST['editor'] ) && $_REQUEST['editor'] == 1 ) {
                include "../includes/editor.php";
            }
            ?>
            <form method="post" action="upload_template.php" enctype="multipart/form-data">
                <div id="add_template">
                    <div>
                        <table id="add_template_zip">
                            <tr>
                                <td colspan="3" style="text-align: left;"><h3>Add Template</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Select the template file to be uploaded and click the add button.  You can only upload templates packaged using the ZIP file format.<br /><br />Be sure to see the documentation section of the spt website for full details on the required contents of a template.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td><input name="name" <?php
            if ( isset ( $_SESSION['temp_name'] ) ) {
                echo "value = \"" . $_SESSION['temp_name'] . "\"";
                unset ( $_SESSION['temp_name'] );
            }
            ?>/></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td><textarea name="description" cols=50 rows=4><?php
                                           if ( isset ( $_SESSION['temp_description'] ) ) {
                                               echo $_SESSION['temp_description'];
                                           }
            ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="file"  name="file" /></td>
                            </tr>
                            <?php
                            if ( isset ( $_SESSION['alert_message'] ) ) {
                                echo "<tr><td colspan=2 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                            }
                            ?>
                            <tr>
                                <td colspan="2" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
            <form method="post" action="update_template.php" enctype="multipart/form-data">
                <div id="update_template">
                    <div>
                        <?php
                            if(isset($_REQUEST['id'])){
                                //retrieve template id
                                $template_id = $_REQUEST['id'];
                                //connect to database
                                include "../spt_config/mysql_config.php";
                                //query database for existing templates
                                $sql = "SELECT id FROM templates";
                                $r = mysql_query($sql);
                                $match = 0;
                                while($ra = mysql_fetch_assoc($r)){
                                    if($ra['id'] == $template_id){
                                        $match = 1;
                                    }
                                }
                                //if template id provided doesn't match existing id, throw alert
                                if($match == 0){
                                    $_SESSION['alert_message'] = 'please select an existing template';
                                    header ( 'location:./#alert' );
                                    exit;
                                }
                                //if it does match then grab information on this id
                                $sql = "SELECT * FROM templates WHERE id='$template_id'";
                                $r = mysql_query($sql);
                                while($ra = mysql_fetch_assoc($r)){
                                    $template_name = $ra['name'];
                                    $template_description = $ra['description'];
                                }
                            }
                        ?>
                        <table id="template_details">
                            <tr>
                                <td colspan="2" style="text-align: left;"><h3>Template Details</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Update the name and description of the template or preview the template with the provided link.<br /><br />To edit the email close this window and click on the pencil icon in the far right column of this template.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Name</strong></td>
                                <td colspan="2" style="text-align: left;"><input name="name" size="50" <?php
                                if ( isset ( $_SESSION['temp_template_name'] ) ) {
                                    echo "value=\"" . $_SESSION['temp_template_name'] . "\"";
                                    unset ( $_SESSION['temp_template_name'] );
                                }else{
                                    if(isset($_REQUEST['id'])){
                                        echo "value=\"" . $template_name . "\"";    
                                    }
                                }
                                ?>/></td>
                            </tr>
                            <tr>
                                <td><strong>Description</strong></td>
                                <td colspan="2" style="text-align: left;"><textarea name="description" style="text-align:left;" cols=50 rows=10><?php
                                if ( isset ( $_SESSION['temp_template_description'] ) ) {
                                    echo $_SESSION['temp_template_description'];
                                    unset ( $_SESSION['temp_template_description'] );
                                }else{
                                    if(isset($_REQUEST['id'])){
                                        echo $template_description;    
                                    }
                                }
                                ?></textarea></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                            </tr>
                            <?php
                                if(isset($_REQUEST['id'])){
                                    //get the email.php file for this template
                                    $file = file_get_contents ( $template_id . "/email.php" );
                                    //get the sender friendly name
                                    preg_match ( '#\$sender_friendly\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $sender_friendly = $matches[1];
                                    //get the sender email address
                                    preg_match ( '#\$sender_email\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $sender_email = $matches[1];
                                    //get the reply to address
                                    preg_match ( '#\$reply_to\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $reply_to = $matches[1];
                                    //get the subject
                                    preg_match ( '#\$subject\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $subject = $matches[1];
                                    //get the fake link
                                    preg_match ( '#\$fake_link\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $fake_link = $matches[1];
                                    //get the message
                                    preg_match ( '#\$message\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
                                    $message = $matches[1];
                                }
                            ?>
                            <tr>
                                <td class="template_detail_label">From Name:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){ echo $sender_friendly;} ?></td>
                            </tr>
                            <tr>
                                <td class="template_detail_label">From Email:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){ echo $sender_email; } ?></td>
                            </tr>
                            <tr>
                                <td class="template_detail_label">Reply To:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){echo $reply_to;} ?></td>
                            </tr>
                            <tr>
                                <td class="template_detail_label">Subject:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){echo $subject;} ?></td>
                            </tr>
                            <tr>
                                <td class="template_detail_label">Fake Link:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){echo $fake_link;} ?></td>
                            </tr>
                            <tr>
                                <td class="template_detail_label">Message:</td>
                                <td colspan="2" class="template_detail_detail"><?php if(isset($_REQUEST['id'])){echo $message;} ?></td>
                            </tr>
                            <tr>
                                <td><strong>Website</strong></td>
                                <td colspan="2" style="text-align: left;"><a href=<?php if(isset($_REQUEST['id'])){ echo "\"".$template_id."\"";}?> target="_blank">Click here for preview</a></td>
                            </tr>
                            <?php
                                if ( isset ( $_SESSION['alert_message'] ) ) {
                                    echo "<tr><td colspan=2 class=\"popover_alert_message\">" . $_SESSION['alert_message'] . "</td></tr>";
                                }
                            ?>
                            <input type="hidden" name="tempid" value=<?php if(isset($_REQUEST['id'])){echo "\"".$template_id."\""; }?> />
                            <tr>
                                <td colspan="3" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
            <form method="post" action="scrape_it.php" enctype="multipart/form-data">
                <div id="add_scrape">
                    <div>
                        <table id="add_scrape_table">
                            <tr>
                                <td colspan="2" style="text-align: left;"><h3>Scrape Live Website</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter the name, description and full URL for the site to be scraped as well as the details of the email that will be sent when using this template in a campaign.<br /><br />To find out what the <strong>correct and full</strong> URL is, browse to the site first in your browser.<br /><br />For example, if you enter <strong>http://www.targetsite.com</strong>  into your browser and the address changes to <strong>http://www.targetsite.com?sid=42</strong>, then that's the actual URL you want to enter here.  Anything else will most likely result in you scraping an error 302 page instead of the actual target site.<br /><br />The scraper may or may not always parse the target site correctly due to the extreme wide variety of website coding methodologies.  We recommend utilizing the editor module if you are comfortable with manually editing html to fix any problems that might exist after the scrape.  Also, please let us know via the spt website contact form of any issues you see including the site you had problems with.<br /><br /><strong>NOTE:</strong>  After you scrape a site, you will wind up with 'index.htm', 'email.php' and 'return.htm' in a directory reflecting the template id within the templates directory.  You will need to manually edit these files by browsing to them or using the editor module if you'd like to customize your scrapes further.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td colspan="2"><input name="name" <?php
                            if ( isset ( $_SESSION['temp_scrape_name'] ) ) {
                                echo "value=\"" . $_SESSION['temp_scrape_name'] . "\"";
                                unset ( $_SESSION['temp_scrape_name'] );
                            }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td colspan="2"><textarea name="description" cols=50 rows=4><?php
                                                       if ( isset ( $_SESSION['temp_description'] ) ) {
                                                           echo $_SESSION['temp_description'];
                                                           unset ( $_SESSION['temp_description'] );
                                                       }
                            ?></textarea></td>
                            </tr>
                            <tr>
                                <td>URL</td>
                                <td colspan="2"><input name="url" <?php
                                        if ( isset ( $_SESSION['temp_url'] ) ) {
                                            echo "value=\"" . $_SESSION['temp_url'] . "\"";
                                            unset ( $_SESSION['temp_url'] );
                                        }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td colspan="3"><br /></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: left;"><h3>Email</h3></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Use these fields to create an email for your template.  For more advanced editing, edit this template after you've completed the scrape by clicking the pencil icon next to the template.</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td colspan="2"><input name="email_subject" <?php
                                                       if ( isset ( $_SESSION['temp_email_subject'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_email_subject'] . "\"";
                                                           unset ( $_SESSION['temp_email_subject'] );
                                                       }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td>Sender Address</td>
                                <td colspan="2"><input name="email_from" <?php
                                                       if ( isset ( $_SESSION['temp_email_from'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_email_from'] . "\"";
                                                           unset ( $_SESSION['temp_email_from'] );
                                                       }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td>Sender Name</td>
                                <td colspan="2"><input name="email_from_friendly" <?php
                                                       if ( isset ( $_SESSION['temp_email_from_friendly'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_email_from_friendly'] . "\"";
                                                           unset ( $_SESSION['temp_email_from_friendly'] );
                                                       }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td>Reply To</td>
                                <td colspan="2"><input name="reply_to" <?php
                                                       if ( isset ( $_SESSION['temp_reply_to'] ) ) {
                                                           echo "value=\"" . $_SESSION['temp_reply_to'] . "\"";
                                                           unset ( $_SESSION['temp_reply_to'] );
                                                       }
                            ?>/></td>
                            </tr>
                            <tr>
                                <td>Message</td>
                                <td><textarea name="email_message" cols=50 rows=4><?php
                                                       if ( isset ( $_SESSION['temp_email_message'] ) ) {
                                                           echo $_SESSION['temp_email_message'];
                                                           unset ( $_SESSION['temp_email_message'] );
                                                       } else {
                                                           echo "@link";
                                                       }
                            ?></textarea></td>
                                <td style="text-align: right;">
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>You can enter the following variables that will be changed into their actual values on runtime:<br /><br />@fname - Target's first name<br />@lname - Target's last name<br />@url - Allows you to wrap your own text with the phishing URL (requires you to build your own anchor and put @url in the href attribute)<br />@link - Will be displayed to user as the fake link you provide.<br /><br />@url or @link <strong>must</strong> be present in the 'Message' field!</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Fake Link</td>
                                <td colspan="2"><input name="email_fake_link" <?php
                                        if ( isset ( $_SESSION['temp_email_fake_link'] ) ) {
                                            echo "value=\"" . $_SESSION['temp_email_fake_link'] . "\"";
                                            unset ( $_SESSION['temp_email_fake_link'] );
                                        }
                            ?>/></td>
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
                    </div>
                </div>
            </form>

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
                <span class="button"><a href="#add_template"><img src="../images/package_add_sm.png" alt="add" /> Template</a></span>
                <span class="button"><a href="#add_scrape"><img src="../images/world_add_sm.png" alt="add" /> Scrape</a></span>
                <table class="spt_table">
                    <tr>
                        <td style="text-align: left;"><h3>Name</h3></td>
                        <td style="text-align: left;"><h3>Description</h3></td>
                        <td><h3>Screenshot</h3></td>
                        <td style="width:75px;"><h3>Actions</h3></td>
                    </tr>

                    <?php
//connect to database
                    include "../spt_config/mysql_config.php";

//pull in list of all templates
                    $r = mysql_query ( "SELECT * FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "
                    <tr>
                        <td style=\"vertical-align:text-top; text-align: left;\"><a href=\"?id=".$ra['id']."#update_template\">" . $ra['name'] . "</a></td>\n
                        <td style=\"vertical-align:text-top; text-align: left; width:300px;\">" . $ra['description'] . "</td>\n
                        <td><img class= \"drop_shadow\" src=\"" . $ra['id'] . "/screenshot.png\" alt=\"missing screenshot\" /></td>\n
                        <td><a href=\"?editor=1&type=templates&id=" . $ra['id'] . "\"><img src=\"../images/pencil_sm.png\" /></a>&nbsp;&nbsp;<a href=\"copy_template.php?id=".$ra['id']."\"><img src=\"../images/page_copy_sm.png\" alt=\"copy\"/>&nbsp;&nbsp;<a href=\"delete_template.php?t=" . $ra['id'] . "\"><img src=\"../images/world_delete_sm.png\" alt=\"delete\" /></a></td>\n
                    </tr>\n";
                    }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>