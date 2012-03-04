<?php

/**
 * file:    email.php
 * version: 8.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Outlook 2010 manual clone template
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

//this is the email template
//populate the variables below with what you want the email to look like
//
//the variable $link will be generated by the application, just place the
//variable $link somewhere in the email
//
//This will populate the subject line of the email that is sent
$subject = 'Update Your Webmail Account';

//This will force the sender to be what you set the from address to.  If you want to completely impersonate the domain in the envelope sender header field uncomment this out.
//$f_sender = "postmaster@microsoft.com";
//This will populate the headers of the message
$headers = "From: postmaster@microsoft.com\r\n";
$headers .= "Reply-To: postmaster@microsoft.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "X-Mailer: sent with the simple phishing toolkit www.sptoolkit.com\r\n";

//uncomment out this line if you'd like to use HTML in the email
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//This will populate the body of the email
$message = '<html><body>';
$message .= '<h3>Webmail</h3>';
$message .= '<br>The password requirements for Webmail have been changed recently.  Please follow the link below to update your Webmail account password to avoid an interruption in Webmail access.';
$message .= '<br><br><br>';
$message .= '<a href="' . $link . '">https://login.microsoft.com/login</a>';
$message .= '<br><br><br>';
$message .= '</body></html>';
?>