<?php

//connect to database
include "spt_config/mysql_config.php";





//import new quick start campaign templates

//quick start campaign 1 (Amazon.com)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Amazon.com shipping information','An email supposedly from Amazon.com with shipping information about a recently ordered product.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/1/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/1/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/1/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/1/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/1/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 2 (***REMOVED***.com)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] ***REMOVED*** security update','An email supposedly from ***REMOVED***.com requesting the target to update their security information.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/2/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/2/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/2/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/2/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/2/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 3 (Delta.com)


//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Delta flight information','An email supposedly from Delta.com with flight information for an upcoming flight.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/3/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/3/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/3/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/3/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/3/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 4 (UPS.com)


//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] UPS package tracking','An email supposedly from UPS with tracking information for a package to be delivered.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/4/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/4/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/4/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/4/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/4/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 5 (DGXT Virus)


//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] DGXT Virus','An email supposedly from IT Services about a virus found in the targets mailbox.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/5/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/5/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/5/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/5/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/5/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 6 (mailbox quota reached)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox quota reached','An email supposedly from the Helpdesk about a mailbox over quota situation.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/6/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/6/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/6/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/6/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/6/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 7 (mailbox migration required)


//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox migration required','An email supposedly from the Helpdesk about actions required to be done for a mailbox migration.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/7/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/7/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/7/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/7/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/7/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 8 (Elavon)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Elavon Merchant Account','An email supposedly from Elavon about a merchant account to be closed if no action is taken.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/8/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/8/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/8/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/8/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/8/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 9 (Helpdesk support portal)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Helpdesk support portal','An email supposedly from Helpdesk about a new support and information portal now available.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/9/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/9/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/9/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/9/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/9/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 10 (Woodgrove bank)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Woodgrove bank','An email supposedly from Woodgrove Bank about online access to your account being closed if no action taken.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/10/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/10/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/10/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/10/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/10/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 11 (Coho Vineyard & Winery)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Coho Vineyard','An email supposedly from Coho Vineyard & Winery with information for a recent order just shipped.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/11/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/11/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/11/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/11/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/11/screenshot.png", "templates/" . $id . "/screenshot.png");


//quick start campaign 12 (419 scam)

//sql statement
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] 419 scam','An email supposedly a Scottish lawyer wanting help in moving millions of dollars...legally of course.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//make directory for files
mkdir( "templates/".$id );

//move files to their final destination
rename("templates/quick_start_campaigns/12/default.css", "templates/" . $id . "/default.css");
rename("templates/quick_start_campaigns/12/email.php", "templates/" . $id . "/email.php");
rename("templates/quick_start_campaigns/12/index.htm", "templates/" . $id . "/index.htm");
rename("templates/quick_start_campaigns/12/logo.png", "templates/" . $id . "/logo.png");
rename("templates/quick_start_campaigns/12/screenshot.png", "templates/" . $id . "/screenshot.png");

//delete the temp folders
rmdir('templates/quick_start_campaigns/12');
rmdir('templates/quick_start_campaigns/11');
rmdir('templates/quick_start_campaigns/10');
rmdir('templates/quick_start_campaigns/9');
rmdir('templates/quick_start_campaigns/8');
rmdir('templates/quick_start_campaigns/7');
rmdir('templates/quick_start_campaigns/6');
rmdir('templates/quick_start_campaigns/5');
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
