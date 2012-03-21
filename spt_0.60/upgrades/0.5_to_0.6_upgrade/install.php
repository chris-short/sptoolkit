<?php

/**
 * file:    install.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Upgrade (0.5 - 0.6)
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
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

//starts php session
session_start ();

//turn off PHP error reporting, some platforms report error on missing file, which is
// handled via the script itself
error_reporting ( 0 );
?>

<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - simple phishing toolkit</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="includes/spt.css" type="text/css" />
    </head>

    <body>

        <!--login wrapper -->
        <div id="login_wrapper">

            <!--logo-->
            <div id="login_logo"><img src="images/logo.png" alt="logo"/></div>
            <div id="login_form">

                <?php
//Step 1 - Begin Installation of v0.6 Upgrade
                if ( isset ( $_POST['step1'] ) && $_POST['step1'] == "complete" ) {
                    //set install status to step 2 if step 1 has already been completed
                    $_SESSION['install_status'] = 2;
                }

                if ( ! isset ( $_SESSION['install_status'] ) && ! isset ( $_POST['step1'] ) ) {
                    echo
                    "
        <form id=\"step_1\" method=\"post\" action=\"\">
                <span>Click below to begin the upgrade to spt v0.6, <strong>Flying Fish</strong>.<br /><br /><strong>Please be sure to backup your database BEFORE continuing!</strong></span>
                <br /><br />
                <input type=\"hidden\" name=\"step1\" value=\"complete\" />
                <input type=\"submit\" value=\"Begin!\" />
        </form>
    ";
                }

//Step 2 - Environmental Checks
                if ( isset ( $_POST['step2'] ) && $_POST['step2'] == "complete" ) {
                    //set install status to step 2 if step 1 has already been completed
                    $_SESSION['install_status'] = 3;
                }

                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 2 ) {

                    //Start Table
                    echo "<table id=\"enviro_checks\">";

                    //Environmental check introduction
                    echo
                    "
            <tr>
                    <td colspan=2>First, a couple of checks.  If there are any problems you will see an X below.  Hover over it for more information on the problem.</td>
            </tr>
        ";

                    //Ensure all files are readable, writeable and executable.
                    echo
                    "
            <tr>
                    <td>Appropriate Permissions</td>
    ";

                    foreach ( glob ( "*" ) as $entity ) {
                        if ( is_dir ( $entity ) ) {
                            foreach ( glob ( $entity . "/" . "*" ) as $sub_entity ) {
                                if ( is_dir ( $sub_entity ) ) {
                                    foreach ( glob ( $sub_entity . "/" . "*" ) as $sub_sub_entity ) {
                                        if ( ! is_readable ( $sub_sub_entity ) || ! is_writable ( $sub_sub_entity ) || ! is_executable ( $sub_sub_entity ) ) {
                                            $permission_error = 1;
                                        }
                                    }
                                } else if ( ! is_readable ( $sub_entity ) || ! is_writable ( $sub_entity ) || ! is_executable ( $sub_entity ) ) {
                                    $permission_error = 1;
                                }
                            }
                        } else if ( ! is_readable ( $entity ) || ! is_writable ( $entity ) || ! is_executable ( $entity ) ) {
                            $permission_error = 1;
                        }
                    }

                    if ( isset ( $permission_error ) ) {
                        echo
                        "
            <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/cancel.png\" alt=\"problem\" /><span>The account that PHP runs under needs read, write and execute permissions for spt to function properly.  Visit sptoolkit.com for troubleshooting information on how to ensure you have the correct permissions set.<br /><br />If you are using WAMP, this may incorrectly state that permissions are not correct because Windows, in some cases does not accurately report if a file is executable or not.  99% of WAMP installs do not have permissions problems, so just click \"Proceed Anyways\".</span></a></td>
        ";
                    } else {
                        echo
                        "
            <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>
        ";
                    }

                    echo "</tr>";

                    //Ensure all enviromental checks pass
                    if ( isset ( $permission_error ) ) {
                        $enviro_checks = 0;
                    } else {
                        $enviro_checks = 1;
                    }

                    //Provide buttons to check again or proceed with caution
                    if ( $enviro_checks == 0 ) {
                        echo
                        "
    <tr>
        <td>
                <form id=\"step_2_1\" method=\"post\" action=\"\">
                        <input type=\"hidden\" name=\"step1\" value=\"complete\" />
                        <input type=\"submit\" value=\"Check Again\" />
                </form>
        </td>
        <td>
                <form id=\"step_2_2\" method=\"post\" action=\"\">
                        <input type=\"hidden\" name=\"step2\" value=\"complete\" />
                        <input type=\"submit\" value=\"Upgrade Anyways!\" />
                </form>
        </td>
    </tr>
    ";
                    }

                    //Provide a button to proceed if all checks pass
                    if ( $enviro_checks == 1 ) {
                        echo
                        "
    <tr>
        <td></td>
        <td>
                <form id=\"step_2\" method=\"post\" action=\"\">
                        <input type=\"hidden\" name=\"step2\" value=\"complete\" />
                        <input type=\"submit\" value=\"Upgrade!\" />
                </form>
        </td>
    </tr>
    ";
                    }

                    //End Table
                    echo "</table>";
                }

//Step 3 - Upgrade Database
                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 3 ) {

//connect to database
                    include "spt_config/mysql_config.php";

//add campaign table modifications
                    $sql = "ALTER TABLE campaigns
                ADD COLUMN `date_ended` varchar(255) NOT NULL,
                ADD COLUMN `message_delay` int(10) NOT NULL,
                ADD COLUMN `status` int(1) NOT NULL,
                ADD COLUMN `spt_path` varchar(255) NOT NULL,
                ADD COLUMN `relay_host` varchar(255) NOT NULL,
                ADD COLUMN `relay_username` varchar(255) NOT NULL,
                ADD COLUMN `relay_password` varchar(255) NOT NULL,
                ADD COLUMN `relay_port` int(5) NOT NULL
                ";
                    mysql_query ( $sql ) or die ( mysql_error () );

                    $sql = "ALTER TABLE campaigns_responses
                ADD COLUMN `sent` int(1) NOT NULL,
                ADD COLUMN `response_log` longtext NOT NULL
                ";
                    mysql_query ( $sql );

                    $sql = "UPDATE campaigns_responses SET sent = 1";
                    mysql_query ( $sql );

////insert quick start templates
////first sql statement (prevents some problems)
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Amazon shipping information','An email from Amazon.com with shipping information about a recently order.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );

//figure out the campaign id
                    $r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        $id = $ra['max'];
                    }

//remaining sql statements
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] ***REMOVED*** security update','An email from ***REMOVED***.com requesting the target to update their security information.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Delta flight information','An email from Delta.com with flight information for an upcoming flight.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] UPS package tracking','An email from UPS with tracking information for a package to be delivered.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] DGXT Virus','An email IT Services about a virus found in the targets mailbox.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox quota reached','An email from the Helpdesk about a mailbox over quota situation.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox migration required','An email from the Helpdesk about actions required to be done for a mailbox migration.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Elavon Merchant Account','An email from Elavon about a merchant account to be closed if no action is taken.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Helpdesk support portal','An email from Helpdesk about a new support and information portal now available.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Woodgrove bank','An email from Woodgrove Bank about online access to your account being closed if no action taken.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] Coho Vineyard','An email from Coho Vineyard & Winery with information for a recent order just shipped.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('[QS] 419 scam','An email from a Scottish lawyer wanting help in moving millions of dollars...legally of course.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO templates (name, description) VALUES ('OWA 2010 login','A hand crafted copy of the Outlook Web App 2010 login page that uses no content from original OWA login page.  Comes with three different return.htm pages, just rename them to change the return page displayed to the target once they submit the form.  [No Internet access required]')";
                    mysql_query ( $sql ) or die ( mysql_error () );

//set initial counter values
                    $install_count = 13;
                    $folder = 1;
                    $i = 0;

//move files
                    do {
//make directory for files
                        mkdir ( 'templates/' . $id );
//move files
                        $sourceDir = "templates/temp_upload/" . $folder . "/";
                        $targetDir = "templates/" . $id . "/";
                        if ( $dh = opendir ( $sourceDir ) ) {
                            while ( false !== ($fileName = readdir ( $dh )) ) {
                                if ( ! in_array ( $fileName, array ( '.', '..' ) ) ) {
                                    rename ( $sourceDir . $fileName, $targetDir . $fileName );
                                }
                            }
                        }
                        //delete the temp folder
                        rmdir ( 'templates/temp_upload/' . $folder );
                        //increment counters
                        $id ++;
                        $folder ++;
                        $i ++;
                    } while ( $i < $install_count );

////insert default education packages
//first sql statement (prevents some problems)
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Phished 1','Displays content about being phished including a Youtube video from Symantec about phishing.  [Requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );

//figure out the campaign id
                    $r = mysql_query ( "SELECT MAX(id) as max FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        $id = $ra['max'];
                    }

//remaining sql statements

                    $sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Infected 1','Displays content about being infected with malware including a Youtube video from Symantec about various types of malware.  [Requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] APWG Phishing Education Landing Page','Displays the full and unmodified content of the APWG phishing education landing page.  [Requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Flash game from OnGuardOnline.gov','Displays content about being phished including an embedded Shockwave Flash game from OnGuardOnline.gov about phishing.  [Requires Internet access to YouTube]')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 2','Displays content about being phished.  [No Internet access required].')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 2','Displays content about being infected with malware.  [No Internet access required].')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 3','Displays content about being infected with malware.  [No Internet access required].')";
                    mysql_query ( $sql ) or die ( mysql_error () );
                    $sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 3','Displays content about being phished.  [No Internet access required].')";
                    mysql_query ( $sql ) or die ( mysql_error () );

//set initial counter values
                    $install_count = 8;
                    $folder = 1;
                    $i = 0;

//move files
                    do {
                        //make directory for files
                        mkdir ( 'education/' . $id );
                        //move files
                        $sourceDir = "education/temp_upload/" . $folder . "/";
                        $targetDir = "education/" . $id . "/";
                        if ( $dh = opendir ( $sourceDir ) ) {
                            while ( false !== ($fileName = readdir ( $dh )) ) {
                                if ( ! in_array ( $fileName, array ( '.', '..' ) ) ) {
                                    rename ( $sourceDir . $fileName, $targetDir . $fileName );
                                }
                            }
                        }
                        //delete the temp folder
                        rmdir ( 'education/temp_upload/' . $folder );
                        //increment counters
                        $id ++;
                        $folder ++;
                        $i ++;
                    } while ( $i < $install_count );

//delete some files
                    unlink ( 'spt.css' );
                    unlink ( 'images/dashboard.png' );
                    unlink ( 'images/dashboard_sm.png' );
                    unlink ( 'images/email.png' );
                    unlink ( 'images/email_dm.png' );
                    unlink ( 'images/gear.png' );
                    unlink ( 'images/gear_sm.png' );
                    unlink ( 'images/left-arrow.png' );
                    unlink ( 'images/left-arrow_sm.png' );
                    unlink ( 'images/list.png' );
                    unlink ( 'images/list_sm.png' );
                    unlink ( 'images/logout.png' );
                    unlink ( 'images/plus.png' );
                    unlink ( 'images/plus_sm.png' );
                    unlink ( 'images/right-arrow.png' );
                    unlink ( 'images/right-arrow_sm.png' );
                    unlink ( 'images/thumbs-up.png' );
                    unlink ( 'images/thumbs-up_sm.png' );
                    unlink ( 'images/trash.png' );
                    unlink ( 'images/trash_sm.png' );
                    unlink ( 'images/x.png' );
                    unlink ( 'images/x_sm.png' );
                    unlink ( 'modules/module_cleanup.php' );

                    echo "
                        Upgrade complete!  Click Finish below to proceed to the login page and delete this installation file.<br /><br />
                        <form method=\"post\" action=\".\">
                                <input type=\"hidden\" name=\"delete_install\" value=\"1\" />
                                <input type=\"submit\" value=\"Finish!\" />
                        </form>
                    ";
                }
                ?>
            </div>
        </div>
    </body>
</html>
