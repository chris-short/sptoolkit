Document sections, search for these to jump to that section:
	INTRODUCTION
	LICENSE
	INSTALLATION
	MORE INFORMATION
	CONTACT US
	WANT TO CONTRIBUTE?
	CREDITS
	CONTRIBUTORS

###### INTRODUCTION ######

"Millions for defense, but not one cent for education!" 

The spt project is a small step toward securing the mind as opposed to securing computers. Millions are spent safeguarding information systems, but under trained and susceptible minds then operate them. A simple, targeted link is all it takes to bypass the most advanced security protections. The link is clicked, the deed is done. 

spt was developed from the ground up to provide a simple and easy to use framework to identify your weakest links so that you can patch the human vulnerability.



##### LICENSE #####

spt is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, under version 3 of the License.

spt is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with spt.  If not, see http://www.gnu.org/licenses.



##### INSTALLATION ######

Are you ready to install the spt and try it for yourself?  Great!  Check out our documentation library on the spt website at http://www.sptoolkit.com/documentation for full details on installing and using the spt.  

Installation quick steps are presented below.

## Allowable characters ##

When working with with any of the input fields inside the spt, the following groups of characters are allowed:

	a-z
	A-Z
	0-9
	_
	-
	.
	! (not in email addresses)
	[space] (not in email addresses)
	@ (only in email addresses)

## The Basics ##

	1.  Create and configure the MySQL database.  spt will need a MySQL database to house its data, so go ahead and create that database and configure the associated user account for the new database with ALL PRIVILEGES assigned to it.  Be sure you record the database name, user name and password in a safe place–you’ll need it soon to install spt!
	2.  Extract the spt files from the archive.
	3.  Create a new directory on your web server, such as “spt” and upload the files to the directory.
	
## Install spt ##

	1.  Open your web browser and navigate to the location where you uploaded the files and browse to install.php.  For example, http://www.myhost.com/spt/install.php.  If you accidentally just go to the root of the folder you placed the files in, you’ll be prompted to start the installation by clicking the right pointing arrow.
	2.  When prompted to accept the GNU General Public License, click the “I Agree!” button.  For reference, you can read the full text of the license in the license.htm file included in the root of the extracted files.
	3.  On the next page, you’ll need to provide those database details from earlier.  The default server and database ports are provided, be sure to change them if your installation will require something else.  Enter in the remaining required information and click the “Install Database!” button to get things moving along.
	4.  If all goes well, you’ll see a listing of tables that have been successfully created.  Click “Continue!” to move on.
	5.  If instead you see an error indicated, click the “<back” button to go back and enter the database information again.
	6.  Now it’s time to create your first user, for you!  Enter your first and last name, email address and password and click the “Create User” button to continue on.
	7.  If you receive any errors, such as for an invalid email address or a password that does not meet the complexity requirements, click the “<back” button and try it again.
	8.  Once you enter the required information successfully, you  will receive confirmation.  Click the “Proceed to Login” button to get logged into the spt!
	9.  Now it’s time to login using the email address and password you entered in the previous step.  See, that was easy!
	

	
##### MORE INFORMATION #####

To learn more about the spt, please visit our website at http://www.sptoolkit.com.  You can view the source code and download the current (stable) version of the spt at the code repository, located at https://bitbucket.org/onespt/sptoolkit.



##### CONTACT US #####

If you need to contact us about the spt for any reason, please use the contact form located on our website at http://www.sptoolkit.com/contact.



##### WANT TO CONTRIBUTE? #####

The spt is an open source project, and we'd be more than happy to hear your ideas or suggestions for additions.  Perhaps you want to donate a new template you've developed or have an awesome module that can be loaded into the spt?  Please let us know by contacting us as detailed above.



##### CREDITS #####

Please see the credits.txt file included in the root of the extracted files for a listing of other's work we've used or referenced in the spt.



##### CONTRIBUTORS #####

The spt is a collaborative effort of two full-time information security professionals who had an idea and some time to make it happen.

 - Derek (1 or onespt)
 - Will (42 or 42spt)

Please see the full details of the spt project team members on our website at http://www.sptoolkit.com/who. 


Thanks for using the spt!
- the spt project!
 