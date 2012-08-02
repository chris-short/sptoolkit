<?php
/**
 * file:    install.php
 * version: 11.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Upgrade (0.6 - 0.7)
 * copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
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
session_start();

//turn off PHP error reporting, some platforms report error on missing file, which is
// handled via the script itself
error_reporting(0);
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
if (isset($_POST['step1']) && $_POST['step1'] == "complete") {
    //set install status to step 2 if step 1 has already been completed
    $_SESSION['install_status'] = 2;
}

if (!isset($_SESSION['install_status']) && !isset($_POST['step1'])) {
    echo
    "
        <form id=\"step_1\" method=\"post\" action=\"\">
                <span>Welcome to the <strong>Ghost Fish</strong> (v0.70) upgrade!<br /><br />Read the license agreements and important items listed below before clicking begin.  By clicking begin you are agreeing with the licenses and stating that you understand the important items.</span>
                <br /><br />
                    <span>Licenses</span>
                    <ul>
                        <li><a href=\"license.htm\" target=\"_blank\">spt</a></li>
                        <li><a href=\"http://shop.highsoft.com/highcharts.html\" target=\"_blank\">highcharts</a></li>
                        <li><a href=\"includes/swiftmailer/LICENSE\" target=\"_blank\">swiftmailer</a></li>
                        <li><a href=\"includes/tiny_mce/license.txt\" target=\"_blank\">tinymce</a></li>
                    </ul>
                <span>Important:</span>
                <ul>
                    <li><strong>Backup your database BEFORE continuing!</strong></li>
                    <li>Existing template email.php files will not work in this release due to changes in code structure.  Scrape your templates again or see article <a href=\"http://www.sptoolkit.com/documentation/204-the-email-php-file/\" target=\"_blank\">204: The email.php file</a> for more information.</li>
                    <li>All existing education packaged and templates will be left installed, but will be replaced with new 0.70 versions.  You may want to delete your old education and templates once you have copied any customizations into the new education and templates.  Existing education packages will not have the ability to track training completion.</li>
                    <li>Inline education on the Quick Start templates (education that occured without selecting an education package at campaign start) has been eliminated so that education completion tracking can occur.</li>
                </ul>
                <input type=\"hidden\" name=\"step1\" value=\"complete\" />
                <input type=\"submit\" value=\"Begin!\" />
        </form>
    ";
}

//Step 2 - Environmental Checks
if (isset($_POST['step2']) && $_POST['step2'] == "complete") {
    //set install status to step 2 if step 1 has already been completed
    $_SESSION['install_status'] = 3;
}

if (isset($_SESSION['install_status']) && $_SESSION['install_status'] == 2) {

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

    foreach (glob("*") as $entity) {
        if (is_dir($entity)) {
            foreach (glob($entity . "/" . "*") as $sub_entity) {
                if (is_dir($sub_entity)) {
                    foreach (glob($sub_entity . "/" . "*") as $sub_sub_entity) {
                        if (!is_readable($sub_sub_entity) || !is_writable($sub_sub_entity) || !is_executable($sub_sub_entity)) {
                            $permission_error = 1;
                        }
                    }
                } else if (!is_readable($sub_entity) || !is_writable($sub_entity) || !is_executable($sub_entity)) {
                    $permission_error = 1;
                }
            }
        } else if (!is_readable($entity) || !is_writable($entity) || !is_executable($entity)) {
            $permission_error = 1;
        }
    }

    if (isset($permission_error)) {
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

    //Verify proc_open is enabled
    echo "
        <tr>
            <td>PHP proc_open enabled</td>";

    if (!function_exists('proc_open')) {
        echo "
            <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/cancel.png\" alt=\"problem\" /><span>PHP's proc_open function must not be disabled to ensure that SwiftMailer can successfully send emails.  Ensure this function is not disabled in your php.ini file.  You can visit sptoolkit.com for more information.</span></a></td>";
    } else {
        echo "
            <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>";
        $proc_open_good = "true";
    }

    echo "</tr>";

//Ensure all upgraded files were successfully copied over their replacements
    echo "<tr>
                <td>All Files in Place</td>";

    function checkVersion($path, $expected) {
        //read in the file
        $file = file_get_contents($path);

        //check to see if the expected version exists
        if (!preg_match('#\* version:.*.' . $expected . '\s#', $file)) {
            return $path;
        }
    }

    //initialize array
    $failures = array();

    //Check these files using the function above
    array_push($failures, checkVersion("index.php", "23.0"));
    array_push($failures, checkVersion("campaigns/campaigns_export.php", "8.0"));
    array_push($failures, checkVersion("campaigns/config_shorten.php", "1.0"));
    array_push($failures, checkVersion("campaigns/index.php", "52.0"));
    array_push($failures, checkVersion("campaigns/response.php", "7.0"));
    array_push($failures, checkVersion("campaigns/send_emails.php", "17.0"));
    array_push($failures, checkVersion("campaigns/spt_campaigns.css", "12.0"));
    array_push($failures, checkVersion("campaigns/start_campaign.php", "30.0"));
    array_push($failures, checkVersion("campaigns/trained.php", "1.0"));
    array_push($failures, checkVersion("dashboard/index.php", "23.0"));
    array_push($failures, checkVersion("education/index.php", "21.0"));
    array_push($failures, checkVersion("quickstart/index.php", "12.0"));
    array_push($failures, checkVersion("quickstart/spt_quickstart.css", "4.0"));
    array_push($failures, checkVersion("targets/index.php", "39.0"));
    array_push($failures, checkVersion("targets/target_upload_batch.php", "23.0"));
    array_push($failures, checkVersion("templates/index.php", "38.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //array_push($failures, checkVersion("dir/file.php", "xx.0"));
    //initialize array
    $fails = array();

    //take out empties
    foreach ($failures as $failure) {
        if (strlen($failure) > 0) {
            array_push($fails, $failure);
        }
    }

    if (count($fails) > 0) {
        echo
        "
                                        <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/x.png\" alt=\"problem\" /><span>The following files did not report the expected version.  Please ensure you've uploaded and overwitten these files with the files in the upgrade download.<br /><br /><ul>";
        foreach ($fails as $fail) {
            echo "<li>" . $fail . "</li>";
        }
        echo
        "
                                        </ul></span></a></td>
                                ";
    } else {
        echo
        "
                                        <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>
                                ";
    }

    echo "</tr>";


    //Ensure all enviromental checks pass
    if ( isset($permission_error) OR $proc_open_good != "true" OR count($fails) > 0 ) {
        $enviro_checks = 0;
    } else {
        $enviro_checks = 1;
    }

    //Provide buttons to check again or proceed with caution
    if ($enviro_checks == 0) {
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
    if ($enviro_checks == 1) {
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
if (isset($_SESSION['install_status']) && $_SESSION['install_status'] == 3) {

//connect to database
    include "spt_config/mysql_config.php";

//// campaign tables changes
//Create campaigns_shorten table
    $sql = "CREATE TABLE `campaigns_shorten` (
                    `service` varchar(255) NOT NULL,
                    `api_key` varchar(255) NOT NULL )";
    mysql_query($sql) or die(mysql_error());

//Modify campaigns table
    $sql = "ALTER TABLE campaigns
                    ADD COLUMN `ssl` int(1) NOT NULL,
                    ADD COLUMN `shorten` varchar(255) NOT NULL ";
    mysql_query($sql) or die(mysql_error());

//Modify campaigns_responses table                
    $sql = "ALTER TABLE campaigns_responses
                    ADD COLUMN `sent_time` datetime DEFAULT NULL,
                    ADD COLUMN `trained` int(1) NOT NULL,
                    ADD COLUMN `trained_time` datetime DEFAULT NULL,
                    ADD COLUMN `url` longtext NOT NULL ";
    mysql_query($sql) or die(mysql_error());

    
////insert quick start templates
//first sql statement (prevents some problems)
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST01 v0.70] Amazon shipping information','An email from Amazon.com with shipping information about a recent order.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());

//figure out the campaign id
    $r = mysql_query("SELECT MAX(id) as max FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
    while ($ra = mysql_fetch_assoc($r)) {
        $id = $ra['max'];
    }

//remaining sql statements
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST02 v0.70] ***REMOVED*** security update','An email from ***REMOVED***.com requesting the target to update their security information.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST03 v0.70] Delta flight information','An email from Delta.com with flight information for an upcoming flight.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST04 v0.70] UPS package tracking','An email from UPS with tracking information for a package to be delivered.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST05 v0.70] DGXT Virus','An email IT Services about a virus found in the targets mailbox.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST06 v.070] Mailbox quota reached','An email from the Helpdesk about a mailbox over quota situation.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST07 v0.70] Mailbox migration required','An email from the Helpdesk about actions required to be done for a mailbox migration.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST08 v0.70] Elavon Merchant Account','An email from Elavon about a merchant account to be closed if no action is taken.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST09 v0.70] Helpdesk support portal','An email from Helpdesk about a new support and information portal now available.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST10 v0.70] Woodgrove bank','An email from Woodgrove Bank about online access to your account being closed if no action taken.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST11 v0.70] Coho Vineyard','An email from Coho Vineyard & Winery with information for a recent order just shipped.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('[QST12 v0.70] 419 scam','An email from a Scottish lawyer wanting help in moving millions of dollars...legally of course.  [Email template only, no web site]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO templates (name, description) VALUES ('OWA 2010 login','A hand crafted copy of the Outlook Web App 2010 login page that uses no content from original OWA login page.  If you chose to not educate, this tempalte comes with different return.htm pages, just rename them to change the return page displayed to the target once they submit the form. ')";
    mysql_query($sql) or die(mysql_error());


//set initial counter values
    $install_count = 13;
    $folder = 1;
    $i = 0;

//move files
    do {
//make directory for files
        mkdir('templates/' . $id);
//move files
        $sourceDir = "templates/temp_upload/" . $folder . "/";
        $targetDir = "templates/" . $id . "/";
        if ($dh = opendir($sourceDir)) {
            while (false !== ($fileName = readdir($dh))) {
                if (!in_array($fileName, array('.', '..'))) {
                    rename($sourceDir . $fileName, $targetDir . $fileName);
                }
            }
        }
        //delete the temp folder
        rmdir('templates/temp_upload/' . $folder);
        //increment counters
        $id++;
        $folder++;
        $i++;
    } while ($i < $install_count);

////insert default education packages
//first sql statement (prevents some problems)
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE01 v0.70] Phishing Video','Displays content about being phished including a YouTube video from Symantec about phishing.  [Requires Internet access to YouTube]')";
    mysql_query($sql) or die(mysql_error());

//figure out the education id
    $r = mysql_query("SELECT MAX(id) as max FROM education") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
    while ($ra = mysql_fetch_assoc($r)) {
        $id = $ra['max'];
    }

//remaining sql statements

    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE02 v0.70] Infected Video','Displays content about being infected with malware including a YouTube video from Symantec about various types of malware.  [Requires Internet access to YouTube]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE03 v0.70] APWG Phishing Education','Provides a link to open in a new window APWG phishing education page.  [Requires Internet access to antiphishing.org]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE04 v0.70] Phising Game','Displays content about being phished including an embedded Shockwave Flash game from OnGuardOnline.gov about phishing.  [Requires Internet access to OnGuardOnline.gov]')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE05 v0.70] Phishing Image 1','Displays local content about being phished.  [No Internet access required].')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE06 v0.70] Infected Image 1','Displays local content about being infected with malware.  [No Internet access required].')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE07 v0.70] Infected Image 2','Displays local content about being infected with malware.  [No Internet access required].')";
    mysql_query($sql) or die(mysql_error());
    $sql = "INSERT INTO `education` (name, description) VALUES ('[QSE08 v0.70] Phishing Image 2','Displays local content about being phished.  [No Internet access required].')";
    mysql_query($sql) or die(mysql_error());

//change editor to a non-core module
    mysql_query ("UPDATE modules SET core = 0 WHERE name = 'Editor'");

//set initial counter values
    $install_count = 8;
    $folder = 1;
    $i = 0;

//move files
    do {
        //make directory for files
        mkdir('education/' . $id);
        //move files
        $sourceDir = "education/temp_upload/" . $folder . "/";
        $targetDir = "education/" . $id . "/";
        if ($dh = opendir($sourceDir)) {
            while (false !== ($fileName = readdir($dh))) {
                if (!in_array($fileName, array('.', '..'))) {
                    rename($sourceDir . $fileName, $targetDir . $fileName);
                }
            }
        }
        //delete the temp folder
        rmdir('education/temp_upload/' . $folder);
        //increment counters
        $id++;
        $folder++;
        $i++;
    } while ($i < $install_count);

//delete some files
    unlink ( 'images/qs_1_1.png' );
    unlink ( 'images/qs_1_2.png' );
    unlink ( 'images/qs_1_3a.png' );
    unlink ( 'images/qs_1_3a.png' );
    unlink ( 'images/qs_2_1.png' );
    unlink ( 'images/qs_2_2.png' );
    unlink ( 'images/qs_3_1.png' );
    unlink ( 'images/qs_5_1.png' );
    unlink ( 'images/qs_5_2.png' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );
    //unlink ( 'xx.xx' );

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
