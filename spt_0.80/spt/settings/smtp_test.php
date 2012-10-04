<?php

/**
 * file:    smtp_test.php
 * version: 1.0
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
    if(isset($_POST['current_host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['current_host']) ){
        $host = $_POST['current_host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./?test_smtp_server=true#tabs-2' );
        exit;
    }
    //get test email
    if(isset($_POST['test_email']) && filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL) ){
        $test_email = $_POST['test_email'];
    }
    else{
        $_SESSION['alert_message'] = 'please enter a valid email address';
        header ( 'location:./?test_smtp_server=true#tabs-2' );
        exit;
    }
    //connect to database
    include '../spt_config/mysql_config.php';
    //get smtp settings for host
    $r = mysql_query("SELECT value FROM settings WHERE setting='smtp'");
    while($ra=mysql_fetch_assoc($r)){
        $smtp_setting = explode("|",$ra['value']);
        if($smtp_setting[0] == $host){
            //store match
            $test_smtp_setting = $smtp_setting; 
        }
    }

    //prep email settings
    if(strlen($smtp_setting[0])){
        $relay_host = $smtp_setting[0];
    }
    if(strlen($smtp_setting[1])){
        $relay_port = $smtp_setting[1];
    }
    if(isset($smtp_setting[2]) && $smtp_setting[2] == 1){
        $ssl = 'yes';
    }else{
        $ssl = 'no';
    }
    if(strlen($smtp_setting[3])){
        $relay_username = $smtp_setting[1];
    }
    if(strlen($smtp_setting[4])){
        $relay_password = $smtp_setting[1];
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
