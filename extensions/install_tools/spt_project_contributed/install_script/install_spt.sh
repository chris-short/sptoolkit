#!/bin/bash


#
# file:    install_spt.sh
# version: 8.0
# package: Simple Phishing Toolkit (spt)
# component:	Installation
# copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
# license: GNU/GPL, see license.htm.
# 
# This file is part of the Simple Phishing Toolkit (spt).
# 
# spt is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, under version 3 of the License.
#
# spt is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with spt.  If not, see <http://www.gnu.org/licenses/>.
#


install()
{
    apt-get update
    DEBIAN_FRONTEND=noninteractive apt-get -y \
        -o DPkg::Options::=--force-confdef \
        -o DPkg::Options::=--force-confold \
        install $@
}

cleanup_apt()
    {
        rm -r /var/cache/apt/*
        mkdir /var/cache/apt/archives
        mkdir /var/cache/apt/archives/partial
    }

	
#Get the required configuration items
wwwroot="/var/www"
echo && echo
echo -e "\E[1;31m** This installation script does NOT validate your input as right or wrong, so be careful to enter everything correctly! **\E[0;37m" && echo
read -s -p "Please enter the password for the MySQL 'root' account:  " rootpass && echo

if [ "$(ls -A $wwwroot)" ]
   then
      echo "Please enter the folder into which spt should be installed."
	  read -p "Type spt for '/spt', or enter another name (root installation NOT allowed, $wwwroot is NOT empty):  " sptfolder
	  if [ -z "$sptfolder" ]
         then
		    sptfolder="spt"
            echo -e "\E[1;31mThe spt installation folder has been set to spt since you left the value null.\E[0;37m"
      fi			
   else
	  echo "Please enter the folder into which spt should be installed."
	  read -p "Type spt for '/spt', or leave null for into the root (root installation allowed, but NOT recommended):  " sptfolder
fi

echo && echo
read -p "Please enter the first name of the first spt admin account:  "  adminfirstname
read -p "Please enter the last name of the first spt admin account:  "  adminlastname
read -p "Please enter the email address of the first spt admin account:  "  adminemail
read -s -p "Please enter the password for the first spt admin account (8 - 14 characters!):  " adminpasstemp
echo && echo


echo -e "\E[1;31mThis installation script, and the spt application, will require Intenret access.\E[0;37m"
read -p "Do you need to configure this server with HTTP proxy information to reach the Internet? [ y / n ]  "  proxyrequired
proxyrequired="$(echo ${proxyrequired^^})"
if [ "$proxyrequired" == "Y" ]
    then
        proxyat="@"
        proxycolon=":"
        proxyserver="http_proxy=http://"
        read -p "Please enter the HTTP proxy server IP address:  "  proxyip
        read -p "Please enter the HTTP proxy server port (typically 80 or 8080):  "  proxyport
        read -p "Do you need to provide a username and password for this HTTP proxy server? [ y / n ]  "  proxycredentialsrequired
        proxycredentialsrequired="$(echo ${proxycredentialsrequired^^})"
        if [ "$proxycredentialsrequired" == "Y" ]
            then
                read -p "Please enter the username for the HTTP proxy server:  "  proxyuser
                read -s -p "Please enter the password for the HTTP proxy server:  "  proxypassword
                echo && echo -e "\E[1;31mNote:  the provided credentials are stored in cleartext in '/etc/environment'.\E[0;37m"
                proxyserver=$proxyserver$proxyuser$proxycolon$proxypassword$proxyat$proxyip$proxycolon$proxyport
                echo $proxyserver >> /etc/environment
            else
                proxyserver=$proxyserver$proxyip$proxycolon$proxyport
                echo $proxyserver >> /etc/environment
        fi
    else
        echo
fi
unset proxyip
unset proxyport
unset proxyuser
unset proxypassword

read -p "Do you need to configure this server as an SMTP relay (not typically needed)? [ y / n ]  "  relayrequired
proxyrequired="$(echo ${proxyrequired^^})"
if [ "$proxyrequired" == "Y" ]
    then
        phpfile="/etc/php5/apache2/php.ini"
        phpinsertstring="sendmail_path = /usr/sbin/sendmail -t -i"
        sed -i "/sendmail_path/a$phpinsertstring" $phpfile
    else
        echo
fi


#Create database and user, assign permissions
sptuser="$(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-15})"
sptpass="$(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-80})"
mysql -uroot -p$rootpass -e "CREATE DATABASE spt"
mysql -uroot -p$rootpass -e "CREATE USER $sptuser IDENTIFIED BY '$sptpass'"
mysql -uroot -p$rootpass -e "GRANT ALL PRIVILEGES ON spt.* TO '$sptuser'@'%' WITH GRANT OPTION"
unset rootpass


#Install required packages
install php5-curl php5-cli php5-ldap zip


#Restart Apache after package installation
/etc/init.d/apache2 restart


#Download, extract, copy and set permissions on spt
#Figure out path
if [ -z "$sptfolder" ]
   then
      sptpath="/var/www"
	  CDPATH=/var:.
   else
      sptpath="/var/www/"$sptfolder
      mkdir $sptpath
      CDPATH=/var/www:.
fi
#Pull down current source
wget -O sptoolkit.zip "http://www.sptoolkit.com/?aid=####&sa=1"
#Extract master into new directory
unzip -q sptoolkit.zip -d spt_extracted
#Move files into place
mv spt_extracted/spt/* $sptpath
#Get rid of master and extracts
rm -r spt_extracted
rm sptoolkit*
#Set permissions
chmod 775 -R $sptpath
#Change group
chgrp www-data -R $sptpath


#Modify the spt MySQL config file
configfile="$sptpath/spt_config/mysql_config.php"
oldhost="mysql_host = 'replace_me';"
newhost="mysql_host = 'localhost:3306';"
sed -i "s/$oldhost/$newhost/" $configfile
olduser="mysql_user = 'replace_me';"
newuser="mysql_user = '$sptuser';"
sed -i "s/$olduser/$newuser/" $configfile
oldpassword="mysql_password = 'replace_me';"
newpassword="mysql_password = '$sptpass';"
sed -i "s/$oldpassword/$newpassword/" $configfile
olddb="mysql_db_name = 'replace_me';"
newdb="mysql_db_name = 'spt';"
sed -i "s/$olddb/$newdb/" $configfile


#Create the salt
sptsalt="$(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-50})"
saltfile="$sptpath/login/get_salt.php"
oldsalt="salt='replace_me';"
newsalt="salt='$sptsalt';"
sed -i "s/$oldsalt/$newsalt/" $saltfile


#Create the encrypt key
sptencryptkey="$(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-50})"
encryptfile="$sptpath/spt_config/encrypt_config.php"
oldencryptkey="spt_encrypt_key='replace_me';"
newencryptkey="spt_encrypt_key='$sptencryptkey';"
sed -i "s/$oldencryptkey/$newencryptkey/" $encryptfile


#Run the SQL installation scripts
if [ -z "$sptfolder" ]
   then
      cd www
   else
      cd $sptfolder
fi
newtext="include('$sptpath/spt_config/mysql_config.php');"
#Campaigns
sqlfile="$sptpath/campaigns/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php campaigns/sql_install.php
#Education
sqlfile="$sptpath/education/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php education/sql_install.php
#Settings
sqlfile="$sptpath/settings/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php settings/sql_install.php
#Targets
sqlfile="$sptpath/targets/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php targets/sql_install.php
#Templates
sqlfile="$sptpath/templates/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php templates/sql_install.php
#Users
sqlfile="$sptpath/users/sql_install.php"
sed -i "/<?php/a$newtext" $sqlfile
php users/sql_install.php


#Create the first spt user
adminpassstring=$sptsalt$adminpasstemp$sptsalt
adminpasshashed="$(echo -n "$adminpassstring" | openssl dgst -sha1)"
sqlcmd="INSERT INTO users(fname, lname, username, password, admin, disabled) VALUES ('$adminfirstname','$adminlastname','$adminemail','$adminpasshashed','1','0')"
mysql -u$sptuser -p$sptpass -Dspt -e "$sqlcmd"
sqlcmd="INSERT INTO targets(fname, lname, email, group_name) VALUES('$adminfirstname','$adminlastname','$adminemail', 'Admins - Test')"
mysql -u$sptuser -p$sptpass -Dspt -e "$sqlcmd"
unset sptsalt
unset sptpass
unset sptuser
unset adminpassstring
unset adminpasshashed


#Clean up spt install files
rm $sptpath/install.php
rm $sptpath/salt_install.php
rm $sptpath/campaigns/sql_install.php
rm $sptpath/modules/sql_install.php
rm $sptpath/targets/sql_install.php
rm $sptpath/templates/sql_install.php
rm $sptpath/users/sql_install.php


#Clean apt cache
cleanup_apt


#Present output on screen, all done!
myip="$(/sbin/ifconfig eth0 | grep "inet addr" | awk -F: '{print $2}' | awk '{print $1}')"
echo "                                .D ~="
echo "   .8O?++++++++++++++++++++++++++D =="
echo " .D,                             ..    Z"
echo " D                                    NM                                 7MM"
echo "8,               .?ZDDNNND8$:       .OMM       ,Z8I.          IZ7,       7MM"
echo "O           .$MMMMMMMMMMMMMMMMMMNI  NMMM     DMMMMMM~   MMO~MMMMMMMM   MMMMMMMMM"
echo "I         IMMMMMMMMMMMMMMMMMMMMMMMMMMMMM    OMM.        MMMM      MMM    7MM"
echo "$       .ZMMMMM.MMMMMMMMMMMMMMMMMMMMMMMM    OMM~        MMM,       MM8   7MM"
echo "8.      ,MMM  .MMMMMMMMMMMMMMMMMMMMMMMMM    .NMMM?      MMM        MMM   7MM"
echo ",8      .MMM ,  MMMMMMMMMMMMMMMMMMMMMMMM       ZMMMM    MMM        MMM   7MM"
echo "  ~O8OZZ8DMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM          NMM.  MMM        MMD   7MM"
echo "           ?MMMMMMMMMMMMMMMMMMMMMMN=MMMM           MM,  MMMM      MMM    ?MM"
echo "              +OMMMMMMMMMMMMMMNZ    ?MMM    MM7:,$MM8   MMMMMD.,DMMM      MMM7$="
echo "                                     ,MM    .ZMMMMZ.    MMM .NMMMN         NMMM7"
echo "                                      +M                MMM"
echo "                                       ,                MMM"
echo "                                                        MMM"
echo "                                                        ~~~."
echo && echo && echo
echo -e "\E[1;32m  ** Installation of spt is complete.  Please visit \E[1;31mhttp://$myip/$sptfolder\E[1;32m in your favorite browser to start using spt. **"
echo -e "  ** Log into spt using the email address and password you provided previously. **"
echo
echo -e "\E[1;31m  ** Please harden your server and install an SSL certificate if this server is exposed to the Internet. **"
echo -e "\E[1;31m  ** Please be aware of any default pages and applications installed on your server (e.g. phpMyAdmin, etc). **"
echo
echo -e "\E[1;32m  ** Support:  http://www.sptoolkit.com/forums  |  Documentation:  http://www.sptoolkit.com/documentation **"
echo -e "\E[1;32m  ** Follow @sptoolkit (https://twitter.com/#!/sptoolkit) for updates and notifications. **"
echo && echo && echo
echo -e -n "\E[0;37m"

#References:
#String comparison:  http://tldp.org/LDP/abs/html/comparison-ops.html, http://tldp.org/LDP/Bash-Beginners-Guide/html/sect_07_01.html
#Empty directory:  http://www.cyberciti.biz/faq/linux-unix-shell-check-if-directory-empty/
#Color management: http://www.bashguru.com/2010/01/shell-colors-colorizing-shell-scripts.html
#OpenSSL SHA1:  http://stackoverflow.com/questions/7285059/hmac-sha1-in-bash
#Password creation:  http://www.howtogeek.com/howto/30184/10-ways-to-generate-a-random-password-from-the-command-line/
#Install subroutines for TKL:  http://www.turnkeylinux.org/forum/general/20100730/tklpatch-alfresco-33g-community-edition
