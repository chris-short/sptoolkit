<html>
	<body>
		<h1>LDAP Tester :)</h1>
		<ul>
			<?php
				if(!$_GET['host']){
					//connect to db
					include '../spt_config/mysql_config.php';
					//get ldap servers
					$r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
					while ($ra = mysql_fetch_assoc($r)){
						$ldap_settings = explode("|",$ra['value']);
						echo "<li>".$ldap_settings[0]."</li>";
						echo "<ul>";
						echo "<li><a href=\"?type=group_dump&host=".$ldap_settings[0]."&port=".$ldap_settings[1]."&ssl=".$ldap_settings[2]."&username=".$ldap_settings[3]."&password=".$ldap_settings[4]."&basedn=".$ldap_settings[5]."\" >group dump</a></li>";
						echo "<li><a href=\"?type=user_dump&host=".$ldap_settings[0]."&port=".$ldap_settings[1]."&ssl=".$ldap_settings[2]."&username=".$ldap_settings[3]."&password=".$ldap_settings[4]."&basedn=".$ldap_settings[5]."\" >user dump</a></li>";
						echo "</ul>";
					}
				}else if($_GET['type'] == "group_dump"){
					$host = $_GET['host'];
					$port = $_GET['port'];
					$username = $_GET['username'];
					$password = $_GET['password'];
					$basedn = $_GET['basedn'];

					include 'ldap.php';
					
					$ldap_group_dump = ldap_group_dump($host,$port,$username,$password,$basedn);
					echo "<b>displayname list:</b><br />";
					foreach ($ldap_group_dump as $group) {
						if(strlen($group[displayname][0])>0){
							echo $group[displayname][0]."<br />";	
						}
					}
					echo "<br /><br /><b>cn list:</b><br />";					
					foreach ($ldap_group_dump as $group) {
						if(strlen($group[cn][0])>0){
							echo $group[cn][0]."<br />";	
						}
					}

					echo "<br /><br />";
					echo "<b>raw:</b><br /><pre>";
					print_r($ldap_group_dump);
					echo "</pre>";
				}else if($_GET['type'] == "user_dump"){
					$host = $_GET['host'];
					$port = $_GET['port'];
					$username = $_GET['username'];
					$password = $_GET['password'];
					$basedn = $_GET['basedn'];

					include 'ldap.php';
					
					$ldap_user_dump = ldap_user_dump($host,$port,$username,$password,$basedn);
					echo "<b>displayname list:</b><br />";
					foreach ($ldap_user_dump as $user) {
						if(strlen($user[displayname][0])>0){
							echo $user[displayname][0]."<br />";	
						}
					}
					echo "<br /><br /><b>cn list:</b><br />";					
					foreach ($ldap_user_dump as $user) {
						if(strlen($user[cn][0])>0){
							echo $user[cn][0]."<br />";	
						}
					}

					echo "<br /><br />";
					echo "<b>raw:</b><br /><pre>";
					print_r($ldap_user_dump);
					echo "</pre>";
				}
			?>
		</ul>
	</body>
</html>