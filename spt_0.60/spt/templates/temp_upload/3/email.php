<?php

/**
 * file:    email.php
 * version: 4.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Email template - Quick Start campaign templates (Delta.com)
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

//create travel dates
$start_day = mktime( 0, 0, 0, date("m")  , date("d")+20, date("Y") );
$start_date = date( "D, M d, Y", $start_day );

$end_day = mktime( 0, 0, 0, date("m")  , date("d")+26, date("Y") );
$end_date = date( "D, M d, Y", $end_day );

//table contents
$table = "<table><tr><td>Date</td><td>Time</td><td>Flight / Class</td><td>Status</td><td>City</td><td>Seat / Cabin / Meals</td></tr><tr><td>$start_date</td><td>515P</td><td>DELTA 116 / U</td><td>OK</td><td>LV NYC-KENNEDY</td><td>45A / COACH / F</td></tr><tr><td></td><td>916P</td><td></td><td></td><td>AR SAN FRANCISCO</td><td></td></tr><tr><td>$end_date</td><td>1230P</td><td>DELTA 1837 / K</td><td>OK</td><td>LV SAN FRANCISCO</td><td>32A / COACH / V</td></tr><tr><td></td><td>1044P</td><td></td><td></td><td>AR NYC-KENNEDY</td><td></td></tr></table>";

//this is the email template

//populate the variables below with what you want the email to look like
//the variable @link will be generated by the application, just place the
//variable @link somewhere in the email.

//You can also use @fname (first name), @lname (last name) and @url (phishing url).

//This will populate the subject line of the email that is sent
$subject = 'Your Delta Reciept and Itinerary';

//This will set the sender's name and email address as well as reply to address
$sender_email = "notify@delta.com";
$sender_friendly = "Delta Ticketing";
$reply_to = "no-reply@delta.com";

//Set the Content Type and transfer encoding
$content_type = "text/html; charset=utf-8";

//Set the fake link
$fake_link = "https://www.delta.com/traveling_checkin/itineraries_checkin/index.jsp";

//This will populate the body of the email
$message = '<html><body>';
$message .= 'Thank you for choosing Delta. We encourage you to review this information before your trip. If you need to contact Delta or check on your flight information, go to delta.com, call 800-221-1212 or call the number on the back of your SkyMiles card.<br /><br />Now, managing your travel plans just got easier. You can exchange, reissue and refund electronic tickets at delta.com. Take control and make changes to your itineraries at delta.com/itineraries.<br /><br />Speed through the airport. <a href=@url>Check-in online</a> for your flight.<br /><br /><a href=@url>Flight Information</a><br /><br />DELTA CONFIRMATION #: <a href=@url>AOOC09</a><br />TICKET #: <a href=@url>98937987009837</a><br /><br />'. $table .'<br /><br /><br />Baggage and check-in requirements vary by airport and airline, so please check with the operating carrier on your ticket.<br /><br />Please review the <a href=@url>check-in requirements and baggage guidelines</a> for details.<br /><br />You must be checked in and at the gate at least 15 minutes before your scheduled departure time for travel inside the United States.<br /><br />You must be checked in and at the gate at least 45 minutes before your scheduled departure time for international travel.<br /><br />For tips on flying safely with laptops, cell phones, and other battery-powered devices, please visit http://SafeTravel.dot.gov.<br /><br />Do you have comments about our service? Please email us to share them with us.<br /><br />-----------------------------------------------------------------------------<br /><br /><br />Conditions of Carriage<br /><br />Air transportation on Delta and the Delta Connection carriers is subject to the <a href=@url>conditions of carriage</a>. They include terms governing, for example:<br /><br /><a href=@url>Limits on our liability</a> for personal injury or death of passengers, and for loss, damage or delay of goods and baggage.<br /><br /><a href=@url>Claim restrictions</a>, including time periods within which you must file a claim or bring an action against us.<br /><br /><a href=@url>Our right to change terms</a> of the contract.<br /><br /><a href=@url>Check-in requirements</a> and other rules establishing when we may refuse carriage.<br /><br /><a href=@url>Our rights and limits of our liability</a> for delay or failure to perform service, including schedule changes, substitution of alternative air carriers or aircraft, and rerouting.<br /><br /><a href=@url>Our policy on overbooking flights</a>, and your rights if we deny you boarding due to an oversold flight.<br /><br />These terms are incorporated by reference into our contract with you. You may view these <a href=@url>conditions of carriage on delta.com</a>, or by requesting a copy from any Delta agent.';
$message .= '</body></html>';
?>