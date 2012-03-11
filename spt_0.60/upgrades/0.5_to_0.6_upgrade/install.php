<?php

//connect to database
include "spt_config/mysql_config.php";

////insert quick start templates

//first sql statement (prevents some problems)
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Amazon shipping information','An email supposedly from Amazon.com with shipping information about a recently ordered product.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//remaining sql statements
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] ***REMOVED*** security update','An email supposedly from ***REMOVED***.com requesting the target to update their security information.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Delta flight information','An email supposedly from Delta.com with flight information for an upcoming flight.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] UPS package tracking','An email supposedly from UPS with tracking information for a package to be delivered.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] DGXT Virus','An email supposedly from IT Services about a virus found in the targets mailbox.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox quota reached','An email supposedly from the Helpdesk about a mailbox over quota situation.  When the link is clicked the target will be presented with an inline educational page about malware.')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox migration required','An email supposedly from the Helpdesk about actions required to be done for a mailbox migration.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Elavon Merchant Account','An email supposedly from Elavon about a merchant account to be closed if no action is taken.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Helpdesk support portal','An email supposedly from Helpdesk about a new support and information portal now available.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Woodgrove bank','An email supposedly from Woodgrove Bank about online access to your account being closed if no action taken.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Coho Vineyard','An email supposedly from Coho Vineyard & Winery with information for a recent order just shipped.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] 419 scam','An email supposedly a Scottish lawyer wanting help in moving millions of dollars...legally of course.  When the link is clicked the target will be presented with an inline educational page about malware.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());

//set initial counter values
$install_count = 12;
$folder = 1;
$i = 0;

//move files
do {
    //make directory for files
    mkdir('templates/'.$id);
    //move files
    $sourceDir = "templates/temp_upload/".$folder."/";
    $targetDir = "templates/".$id."/";
    if ( $dh = opendir($sourceDir) )
    {
        while(false !== ($fileName = readdir($dh)))
        {
            if (!in_array($fileName, array('.','..')))
            {
                rename($sourceDir.$fileName, $targetDir.$fileName);
            }
        }
    }
    //delete the temp folder
    rmdir('templates/temp_upload/'.$folder);
    //increment counters
    $id++;
    $folder ++;
    $i++;
} while ($i < $install_count);

////insert default education packages

//first sql statement (prevents some problems)
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Phished 1','Displays content about being phished including a Youtube video from Symantec about phishing.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//remaining sql statements

$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Infected 1','Displays content about being infected with malware including a Youtube video from Symantec about various types of malware.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] APWG Phishing Education Landing Page','Displays the full and unmodified content of the APWG phishing education landing page.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Flash game from OnGuardOnline.gov','Displays content about being phished including an embedded Shockwave Flash game from OnGuardOnline.gov about phishing.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 2','Displays content about being phished.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 2','Displays content about being infected with malware.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 3','Displays content about being infected with malware.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 3','Displays content about being phished.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());

//set initial counter values
$install_count = 8;
$folder = 1;
$i = 0;

//move files
do {
    //make directory for files
    mkdir('education/'.$id);
    //move files
    $sourceDir = "education/temp_upload/".$folder ."/";
    $targetDir = "education/".$id."/";
    if ( $dh = opendir($sourceDir) )
    {
        while(false !== ($fileName = readdir($dh)))
        {
            if (!in_array($fileName, array('.','..')))
            {
                rename($sourceDir.$fileName, $targetDir.$fileName);
            }
        }
    }
    //delete the temp folder
    rmdir('education/temp_upload/'.$folder);
    //increment counters
    $id++;
    $folder ++;
    $i++;
} while ($i < $install_count);



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
