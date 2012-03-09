<?php

/**
 * file:    email.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Email template - Quick Start campaign templates (mailbox quota reached)
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
//the variable @link will be generated by the application, just place the
//variable @link somewhere in the email.

//You can also use @fname (first name), @lname (last name) and @url (phishing url).
$subject = 'Mailbox quota reached';

//This will set the sender's name and email address as well as reply to address
$sender_email = "emailadmin@mail.com";
$sender_friendly = "Email Administrator";
$reply_to = "no-reply@mail.com";

//Set the Content Type and transfer encoding
$content_type = "text/html; charset=utf-8";

//Set the fake link
$fake_link = "https://login.live.com/login.srf?cbcxt=out&vv=910&wa=wsignin1.0&wtrealm=urn:federation:MicrosoftOnline";

//This will populate the body of the email
$message = '<html><body>';
$message .= 'Your mailbox is currently using 99.7% of its quota limit.  You cannot send or receive email until you have updated your mailbox account. To update your mailbox account, <a href=@url>Click Here</a>.<br /><br />Failure to update your account may result to loss of important information in your mailbox or cause limited access to it.  We are sincerely sorry for any inconvenience this might cause.<br /><br />Thanks for your cooperation,<br />Helpdesk';
$message .= '</body></html>';
?>