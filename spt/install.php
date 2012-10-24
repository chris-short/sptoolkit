<?php
/**
 * file:    install.php
 * version: 21.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Installation
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
//Step 1 - Welcome & License
                if ( isset ( $_POST['step1'] ) && $_POST['step1'] == "complete" ) {
                    //set install status to step 2 if step 1 has already been completed
                    $_SESSION['install_status'] = 2;
                }

                if ( ! isset ( $_SESSION['install_status'] ) && ! isset ( $_POST['step1'] ) ) {
                    echo "
                <form id=\"step_1\" method=\"post\" action=\"\">
                    <span>Ready to install spt?<br /><br />Read the license agreements listed and agree by clicking the button below.</span>
                    <br /><br />
                    <span>Licenses</span>
                    <ul>
                        <li><a href=\"license.htm\" target=\"_blank\">spt</a></li>
                        <li><a href=\"http://shop.highsoft.com/highcharts.html\" target=\"_blank\">highcharts</a></li>
                        <li><a href=\"includes/swiftmailer/LICENSE\" target=\"_blank\">swiftmailer</a></li>
                        <li><a href=\"includes/tiny_mce/license.txt\" target=\"_blank\">tinymce</a></li>
                    </ul>
                    <br /><br />
                    <input type=\"hidden\" name=\"step1\" value=\"complete\" />
                    <input type=\"submit\" value=\"I Agree!\" />
                </form>	";
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
                    echo "
            <tr>
                <td colspan=2>Let's do some basic environmental checks before we get started.  Please remediate any problems listed below that have a red X next to them.  Hover over the red X icon for further explanation.</td>
            </tr>";

                    //Ensure all files are readable, writeable and executable.
                    echo "
            <tr>
                <td>Appropriate Permissions</td>";

                    function check_permission ( $dir ) {
                        $d = opendir ( $dir );
                        while ( ($file = readdir ( $d ) ) ) {
                            if ( $file == '.' || $file == '..' )
                                continue;
                            $file = $dir . '/' . $file;
                            if ( ! is_readable ( $file ) || ! is_writeable ( $file ) || (is_dir ( $file ) && ( ! is_executable ( $file ) || check_permission ( $file ))) ) {
                                return TRUE;
                            }
                        }
                        return FALSE;
                    }

                    $permission_error = check_permission ( '.' );

                    if ( $permission_error ) {
                        echo "
                <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/cancel.png\" alt=\"problem\" /><span>The account that PHP runs under needs read, write and execute permissions for spt to function properly.  Visit sptoolkit.com for troubleshooting information on how to ensure you have the correct permissions set.<br /><br />If you are using WAMP, this may incorrectly state that permissions are not correct because Windows, in some cases does not accurately report if a file is executable or not.  99% of WAMP installs do not have permissions problems.</span></a></td>";
                    } else {
                        echo "
                <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>";
                    }

                    echo "</tr>";

                    //Verify proc_open is enabled
                    echo "
        <tr>
            <td>PHP proc_open enabled</td>";

                    if ( ! function_exists ( 'proc_open' ) ) {
                        echo "
            <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/cancel.png\" alt=\"problem\" /><span>PHP's proc_open function must not be disabled to ensure that SwiftMailer can successfully send emails.  Ensure this function is not disabled in your php.ini file.  You can visit sptoolkit.com for more information.</span></a></td>";
                    } else {
                        echo "
            <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>";
                        $proc_open_good = "true";
                    }

                    echo "</tr>";

                    //Verify CuRL is installed
                    echo "
        <tr>
            <td>PHP cURL Installed</td>";

                    $loaded_extensions = get_loaded_extensions ();

                    if ( ! in_array ( 'curl', $loaded_extensions ) ) {
                        echo "
            <td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/cancel.png\" alt=\"problem\" /><span>cURL must be installed to scrape websites.  A quick cURL version check came up empty handed.  If this is expected or you don't plan to use the spt scraper proceed on.  Otherwise check out sptoolkit.com for installation information.</span></a></td>";
                    } else {
                        echo "
            <td class=\"td_center\"><img src=\"images/accept.png\" alt=\"success\" /></td>";
                        $curl_good = "true";
                    }

                    echo "</tr>";

                    //Ensure all enviromental checks pass
                    if ( $permission_error OR ! isset ( $proc_open_good ) OR ! isset ( $curl_good ) ) {
                        $enviro_checks = 0;
                    } else {
                        $enviro_checks = 1;
                    }

                    //Provide buttons to check again or proceed with caution
                    if ( $enviro_checks == 0 ) {
                        echo "
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
                        <input type=\"submit\" value=\"Proceed Anyways\" />
                </form>
            </td>
        </tr>";
                    }

                    //Provide a button to proceed if all checks pass
                    if ( $enviro_checks == 1 ) {
                        echo "
        <tr>
            <td></td>
            <td>
                <form id=\"step_2\" method=\"post\" action=\"\">
                    <input type=\"hidden\" name=\"step2\" value=\"complete\" />
                    <input type=\"submit\" value=\"Proceed!\" />
                </form>
            </td>
        </tr>";
                    }

                    //End Table
                    echo "</table>";
                }

//Step 3 - Install Database
                if ( isset ( $_POST['step3'] ) && $_POST['step3'] == "complete" ) {
                    //validate the database config data
                    $host = $_POST['host'];
                    if(!empty($host)){
                        $_SESSION['temp_host'] = $host;
                    }
                    $port = $_POST['port'];
                    if(!empty($port)){
                        $_SESSION['temp_port'] = $port;
                    }
                    $username = trim ( $_POST['username'] );
                    if(!empty($username)){
                        $_SESSION['temp_username'] = $username;
                    }
                    $password = trim ( $_POST['password'] );
                    if(!empty($password)){
                        $_SESSION['temp_password'] = $password;
                    }
                    $database = trim ( $_POST['database'] );
                    if(!empty($database)){
                        $_SESSION['temp_database'] = $database;
                    }

                    //validate that all fields were entered
                    if ( ! $host || ! $port || ! $username || ! $password || ! $database ) {
                        $_SESSION['install_status'] = 3;
                        echo "
            Please enter something in all fields!<br /><br />
            <form id=\"empty_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //validate the host has only acceptable characters and at least one period
                    if ( preg_match ( '/[^a-zA-Z0-9-\.]/', $host ) ) {
                        $_SESSION['install_status'] = 3;
                        echo "
            Please enter a valid hostname!<br /><br />
            <form id=\"host_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //validate that the port is of valid length
                    if ( $port < 1 || $port > 65535 ) {
                        $_SESSION['install_status'] = 3;
                        echo "
            Please enter a port number between 1 and 65535<br /><br />
            <form id=\"port_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //connect to MySQL server
                    $link = mysql_connect ( $host . ":" . $port, $username, $password );

                    if ( ! $link ) {
                        $_SESSION['install_status'] = 3;
                        echo "
            Could not connect to " . $host . ":" . $port . ".<br /><br />
            MySQL Error: " . mysql_error () . "<br /><br />
            <form id=\"host_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        ;
                        exit;
                    }

                    //connect to database
                    $dbcheck = mysql_select_db ( $database );

                    if ( ! $dbcheck ) {
                        $_SESSION['install_status'] = 3;
                        echo "
            Could not connect to " . $database . ".<br /><br />
            MySQL Error: " . mysql_error () . "<br /><br />
            <form id=\"host_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        ;
                        exit;
                    }

                    //if you get connected run all the install files
                    $dirs = scandir ( '.' );

                    //for each directory look for sql_install.php
                    foreach ( $dirs as $dir ) {
                        if ( is_dir ( $dir ) ) {
                            //if sql_install.php exists in the directory, run it and delete it
                            if ( file_exists ( $dir . '/sql_install.php' ) ) {
                                include $dir . "/sql_install.php";
                                unlink ( $dir . "/sql_install.php" );
                            }
                        }
                    }

                    //run the salt install script
                    include "salt_install.php";

                    //delete the salt install script
                    unlink ( "salt_install.php" );

                    //populate the mysql_config.php file in the spt_config directory
                    function f_and_r ( $find, $replace, $path ) {
                        $find = "#" . $find . "#";
                        $globarray = glob ( $path );
                        if ( $globarray )
                            foreach ( $globarray as $filename ) {
                                $source = file_get_contents ( $filename );
                                $source = preg_replace ( $find, $replace, $source );
                                file_put_contents ( $filename, $source );
                            }
                    }

                    f_and_r ( "mysql_host\s=\s'(.*?)';", "mysql_host='" . $host . ":" . $port . "';", "spt_config/mysql_config.php" );
                    f_and_r ( "mysql_user\s=\s'(.*?)';", "mysql_user='" . $username . "';", "spt_config/mysql_config.php" );
                    f_and_r ( "mysql_password\s=\s'(.*?)';", "mysql_password='" . $password . "';", "spt_config/mysql_config.php" );
                    f_and_r ( "mysql_db_name\s=\s'(.*?)';", "mysql_db_name='" . $database . "';", "spt_config/mysql_config.php" );

                    //echo back the install tables and successfull database configuration
                    echo "Database connectivity has been established and the following tables have been installed into " . $database . ":<br />";
                    echo "<ul>";

                    $r = mysql_query ( 'SHOW TABLES' );
                    while ( $ra = mysql_fetch_row ( $r ) ) {
                        echo "<li>" . $ra[0] . "</li>";
                    }
                    echo "</ul>";

                    //set the install status to move along to step 3
                    $_SESSION['install_status'] = 4;

                    //provide the button to move along
                    echo "
        <form id=\"database_install_complete\" method=\"post\" action\"\">
            <input type=\"submit\" value=\"Continue!\" />
        </form>";
                    exit;
                }

                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 3 ) {
                    echo "
        <form id=\"step_3\" method=\"post\" action=\"\">
            <span>Do you have a database we can use?<br /><br />...If not, you'll need one (MySQL at that).  Please come back when you have one.</span>
            <br /><br />
            <table>
                <tr>
                    <td>Host</td>
                    <td><input type=\"text\" name=\"host\" ";
                    if(isset($_SESSION['temp_host'])){
                        echo "value=\"".$_SESSION['temp_host']."\" ";
                        unset($_SESSION['temp_host']);
                    }
                    else{
                        echo "value=\"localhost\"";
                    }
                    echo "
                        /></td>
                </tr>
                <tr>
                    <td>Port</td>
                    <td><input type=\"text\" name=\"port\" ";
                    if(isset($_SESSION['temp_port'])){
                        echo "value=\"".$_SESSION['temp_port']."\" ";
                        unset($_SESSION['temp_port']);
                    }
                    else{
                        echo "value=\"3306\"";
                    }
                    echo "
                         /></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><input type=\"text\" name=\"username\" ";
                    if(isset($_SESSION['temp_username'])){
                        echo "value=\"".$_SESSION['temp_username']."\" ";
                        unset($_SESSION['temp_username']);
                    }
                    echo "
                        /></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type=\"password\" name=\"password\" ";
                        if(isset($_SESSION['temp_password'])){
                        echo "value=\"".$_SESSION['temp_password']."\" ";
                        unset($_SESSION['temp_password']);
                    }
                    echo "
                    autocomplete=\"off\"/></td>
                </tr>
                <tr>
                    <td>Database</td>
                    <td><input type=\"database\" name=\"database\" ";
                    if(isset($_SESSION['temp_database'])){
                        echo "value=\"".$_SESSION['temp_database']."\" ";
                        unset($_SESSION['temp_database']);
                    }
                    echo "
                    /></td>
                </tr>
                    <input type=\"hidden\" name=\"step3\" value=\"complete\" />
                <tr>
                    <td><br /><input type=\"submit\" value=\"Install Database!\" /></td>
                </tr>
            </table>
        </form>	";
                }

//Step 4 - Configure Salt
                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 4 ) {
                    $salt = '';

                    //generate salt
                    function genRandomString () {
                        $length = 50;
                        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                        $salt = 'p';
                        for ( $p = 0; $p < $length; $p ++  ) {
                            $salt .= $characters[mt_rand ( 0, strlen ( $characters ) - 1 )];
                        }
                        return $salt;
                    }

                    $salt = genRandomString ();


                    //enter salt value into database
                    include "spt_config/mysql_config.php";
                    mysql_query ( "INSERT INTO salt (salt) VALUES ('$salt')" );

                    $_SESSION['install_status'] = 5;
                }

//Step 5 - Configure First User
                if ( isset ( $_POST['step5'] ) && $_POST['step5'] == "complete" ) {
                    //validate that the newly entered username is a valid email address
                    $new_username = $_POST['username'];
                    if(!empty($new_username)){
                        $_SESSION['temp_new_username'] = $new_username;
                    }
                    $new_fname = $_POST['first_name'];
                    if(!empty($new_fname)){
                        $_SESSION['temp_new_fname'] = $new_fname;
                    }
                    $new_lname = $_POST['last_name'];
                    if(!empty($new_lname)){
                        $_SESSION['temp_new_lname'] = $new_lname;
                    }
                    if ( ! filter_var ( $new_username, FILTER_VALIDATE_EMAIL ) ) {
                        echo "
            Please enter a valid email address<br /><br />
            <form id=\"username_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //validate that the username is not too long
                    if ( strlen ( $new_username ) > 50 ) {
                        echo "
            This email address is too long<br /><br />
            <form id=\"username_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //make sure its only letters
                    if ( preg_match ( '/[^a-zA-Z]/', $new_fname ) ) {
                        echo "
            Your first name may only contain letters<br /><br />
            <form id=\"first_name_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //make sure the first name is between 1 and 50 characters
                    if ( strlen ( $new_fname ) > 50 || strlen ( $new_fname ) < 1 ) {
                        echo "
            Your first name must be between 1 and 50 characters<br /><br />
            <form id=\"first_name_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //make sure the last name is between 1 and 50 characters
                    if ( strlen ( $new_lname ) > 50 || strlen ( $new_lname ) < 1 ) {
                        echo "
            Your last name must be between 1 and 50 characters<br /><br />
            <form id=\"last_name_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //make sure the last name only contains letters
                    if ( preg_match ( '/[^a-zA-Z]/', $new_lname ) ) {
                        echo "
            Your last name may only contain letters<br /><br />
            <form id=\"last_name_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //validate the password if it is set
                    if ( ! empty ( $_POST['password'] ) ) {
                        //pull in password to temp variable
                        $temp_p = $_POST['password'];

                        //validate the password doesn't have any characters that are not allowed
                        if ( preg_match ( '/[$+*"=&%]/', $temp_p ) ) {
                            echo "
                Your password contains special characters that are not allowed ($+*\"=&%)<br /><br />
                <form id=\"password_error\" method=\"post\" action\"\">
                    <input type=\"submit\" value=\"< back\" />
                </form>";
                            exit;
                        }

                        //validate that the password is an acceptable length
                        if ( strlen ( $temp_p ) > 15 || strlen ( $temp_p ) < 8 ) {
                            echo "
                The password must be between 8 and 15 characters in length<br /><br />
                <form id=\"password_error\" method=\"post\" action\"\">
                    <input type=\"submit\" value=\"< back\" />
                </form>";
                            exit;
                        }

                        //validate the password matches the validated one
                        if ( $_POST['password'] != $_POST['password_check'] ) {
                            echo "
                Your passwords do not match...try again<br /><br />
                <form id=\"password_error\" method=\"post\" action\"\">
                    <input type=\"submit\" value=\"< back\" />
                </form>";
                            exit;
                        }

                        //connect to database
                        include 'spt_config/mysql_config.php';

                        //get the salt value
                        $r = mysql_query ( "SELECT salt FROM salt" );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            $salt = $ra['salt'];
                        }

                        //pass temp password to new variable that has been salted and hashed
                        $p = sha1 ( $salt . $temp_p . $salt );
                    } else {
                        echo "
            Your must enter a password.<br /><br />
            <form id=\"password_error\" method=\"post\" action\"\">
                <input type=\"submit\" value=\"< back\" />
            </form>";
                        exit;
                    }

                    //add first user to database
                    include "spt_config/mysql_config.php";
                    mysql_query ( "INSERT INTO users(fname, lname, username, password, admin, disabled) VALUES ('$new_fname','$new_lname','$new_username','$p','1','0')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

                    //add first user to target list in test group
                    mysql_query("INSERT INTO targets(fname, lname, email, group_name) VALUES('$new_fname','$new_lname','$new_username', 'Admins - Test')") or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );                    
                    
                    //unset new user sessions
                    unset($_SESSION['temp_new_username']);
                    unset($_SESSION['temp_new_fname']);
                    unset($_SESSION['temp_new_lname']);

                    $_SESSION['install_status'] = 6;
                }

                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 5 ) {
                    echo "
        Lets create the first user!  Enter the Information below that you will use to log into SPT for the first time.<br /><br />
        <form id=\"initial_user\" method=\"post\" action\"\">
            <table>
                <tr>
                    <td>First Name</td>
                    <td><input type=\"text\" name=\"first_name\" ";
                    if(isset($_SESSION['temp_new_fname'])){
                        echo "value=\"".$_SESSION['temp_new_fname']."\" ";
                        unset($_SESSION['temp_new_fname']);
                    }
                    echo "
                        /></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type=\"text\" name=\"last_name\" ";
                        if(isset($_SESSION['temp_new_lname'])){
                            echo "value=\"".$_SESSION['temp_new_lname']."\" ";
                            unset($_SESSION['temp_new_lname']);
                        }
                    echo "
                        /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type=\"text\" name=\"username\" ";
                    if(isset($_SESSION['temp_new_username'])){
                        echo "value=\"".$_SESSION['temp_new_username']."\" ";
                        unset($_SESSION['temp_new_username']);
                    }
                    echo "
                        /></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type=\"password\" name=\"password\" /></td>
                </tr>
                <tr>
                    <td>Re-Enter</td>
                    <td><input type=\"password\" name=\"password_check\" /></td>
                </tr>
                    <input type=\"hidden\" name=\"step5\" value=\"complete\" />
                <tr>
                    <td><br /><input type=\"submit\" value=\"Create User\" /></td>
                    <td></td>
                </tr>
            </table>
        </form>";
                }

//Step 6 - Send User to login page
                if ( isset ( $_SESSION['install_status'] ) && $_SESSION['install_status'] == 6 ) {
                    echo "
        You have successfully installed the Simple Phishing Toolkit!<br /><br />
        You may now proceed to the login screen which will delete this install file.<br /><br />
        <form id=\"done\" method=\"post\" action=\"index.php\">
            <input type=\"hidden\" value=\"delete_install\" name=\"delete_install\" />
            <input type=\"submit\" value=\"Proceed to Login\" />
        </form>";
                }
                ?>
            </div>
        </div>
    </body>
</html>