<?php

//connect to database
include "spt_config/mysql_config.php";

//import new quick start campaign templates

//quick start campaign 1 (Amazon.com)

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}
$id++;

//sql statements
$sql = "INSERT INTO templates (id,name, description) VALUES ('$id','Amazon.com fake shipping information','This quick start campaign template is ready to go. It contains an email supposedly from Amazon.com with shipping information about a recently ordered product.  You may wish to use the editor to change the date in the email message.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/1/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/1/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/1/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/1/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/1/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 2 (***REMOVED***.com)

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}
$id++;

//sql statements
$sql = "INSERT INTO templates (id,name, description) VALUES ('$id','***REMOVED*** fake security update request','This quick start campaign template is ready to go. It contains an email supposedly from ***REMOVED***.com requesting the target to update their security information.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/2/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/2/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/2/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/2/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/2/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 3 (Delta.com)

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}
$id++;

//sql statements
$sql = "INSERT INTO templates (id,name, description) VALUES ('$id','Delta airlines fake flight information','This quick start campaign template is ready to go. It contains an email supposedly from Delta.com with flight information for an upcoming flight.  You may wish to use the editor to change the dates / times / cities in the email message.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/3/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/3/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/3/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/3/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/3/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 4 (UPS.com)

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}
$id++;

//sql statements
$sql = "INSERT INTO templates (id,name, description) VALUES ('$id','UPS fake package tracking information','This quick start campaign template is ready to go. It contains an email supposedly from UPS with tracking information for a package to be delivered.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/4/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/4/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/4/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/4/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/4/screenshot.png", "templates/" . $id . "/screenshot.png");

//delete the temp folders
rmdir('templates/quick_start_campaigns/4');
rmdir('templates/quick_start_campaigns/3');
rmdir('templates/quick_start_campaigns/2');
rmdir('templates/quick_start_campaigns/1');
rmdir('templates/quick_start_campaigns');



//delete some files
unlink('spt.css');
unlink('images/dashboard.png');
unlink('images/dashboard_sm.png');
unlink('images/email.png');
unlink('images/email_dm.png');
unlink('images/gear.png');
unlink('images/gear_sm.png');
unlink('images/left-arrow.png');
unlink('images/left-arrow_sm.png');
unlink('images/list.png');
unlink('images/list_sm.png');
unlink('images/logout.png');
unlink('images/plus.png');
unlink('images/plus_sm.png');
unlink('images/right-arrow.png');
unlink('images/right-arrow_sm.png');
unlink('images/thumbs-up.png');
unlink('images/thumbs-up_sm.png');
unlink('images/trash.png');
unlink('images/trash_sm.png');
unlink('images/x.png');
unlink('images/x_sm.png');


?>
