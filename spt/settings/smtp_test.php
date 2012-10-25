<?php

/**
 * file:    smtp_test.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Settings
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
// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}

//check to see if something was posted
if($_POST){
    //validate and get host
    if(isset($_POST['current_host']) && is_int($_POST['current_host'])){
        $host = $_POST['current_host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:.#tabs-2' );
        exit;
    }
    //get test email
    if(isset($_POST['test_email']) && filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL) ){
        $test_email = $_POST['test_email'];
    }
    else{
        $_SESSION['alert_message'] = 'please enter a valid email address';
        header ( 'location:./?test_smtp_server='.$host.'#tabs-2' );
        exit;
    }
    //connect to database
    include '../spt_config/mysql_config.php';
    //get smtp settings for host
    $r = mysql_query("SELECT * FROM settings_smtp WHERE id='$host'");
    while($ra=mysql_fetch_assoc($r)){
        //prep email settings
        if(strlen($ra[1])){
            $relay_host = $ra[1];
        }
        if(strlen($ra[2])){
            $relay_port = $ra[2];
        }
        if(isset($ra[3]) && $ra[3] == 1){
            $ssl = 'yes';
        }else{
            $ssl = 'no';
        }
        if(strlen($ra[4])){
            $relay_username = $ra[4];
        }
        if(strlen($ra[5])){
            $relay_password = $ra[5];
        }
    }
    //send the email
    require_once '../includes/swiftmailer/lib/swift_required.php';
    if ( isset ( $relay_host ) AND isset ( $relay_username ) AND isset ( $relay_password ) ) {
        //Set relay port if set
        if ( ! isset ( $relay_port ) ) {
            $relay_port = 25;
        }
        if($ssl == "no"){
            //Create the Transport
            $transport = Swift_SmtpTransport::newInstance ( $relay_host, $relay_port )
                -> setUsername ( $relay_username )
                -> setPassword ( $relay_password )
            ;    
        }else{
            //Create the Transport
            $transport = Swift_SmtpTransport::newInstance ( $relay_host, $relay_port, 'ssl' )
                -> setUsername ( $relay_username )
                -> setPassword ( $relay_password )
            ;    
        }
    }
    if ( isset ( $relay_host ) AND ! isset ( $relay_username ) AND ! isset ( $relay_password ) ) {
        if($ssl == "no"){
            //Create the Transport
            $transport = Swift_SmtpTransport::newInstance ( $relay_host, $relay_port );    
        }else{
            //Create the Transport
            $transport = Swift_SmtpTransport::newInstance ( $relay_host, $relay_port, 'ssl' );    
        }
    }
    //Create the Mailer using your created Transport
    $mailer = Swift_Mailer::newInstance ( $transport );
    //To use the ArrayLogger
    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer -> registerPlugin ( new Swift_Plugins_LoggerPlugin ( $logger ) );
    //Prep message
    $subject = "Simple Phishing Toolkit Test Message";
    $sender_email = "noreply@sptoolkit.com";
    $sender_friendly = "Simple Phishing Toolkit";
    $reply_to = "noreply@sptoolkit.com";
    $fname = "Test";
    $lname = "Recipient";
    $message = "This is a test email.  Yay! It worked!";
    //Create a message
    $message = Swift_Message::newInstance ( $subject )
            -> setSubject ( $subject )
            -> setFrom ( array ( $sender_email => $sender_friendly ) )
            -> setReplyTo ( $reply_to )
            -> setTo ( array ( $test_email => $fname . ' ' . $lname ) )
            -> setContentType ( $content_type )
            -> setBody ( $message )
        ;
    //Send the message
    $mailer -> send ( $message, $failures );
    //Set alert message
    $_SESSION['alert_message'] = "your test message has been sent";
}
header('location:.#tabs-2');
exit;
?>
