<html>
	<body>
		<h1>LDAP Tester :)</h1>
		<ul>
			<?php
				if(!$_GET['host']){
					//connect to db
					include '../spt_config/mysql_config.php';
					//get ldap functions
					include 'ldap.php';
					//get ldap servers
					$r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
					while ($ra = mysql_fetch_assoc($r)){
						$ldap_settings = explode("|",$ra['value']);						
						//connect
						$conn_status = ldap_connection($ldap_settings[0],$ldap_settings[1]);
						//bind
						$bind_status = ldap_bind_connection($conn_status,$ldap_settings[3],$ldap_settings[4]);
						if($bind_status){
							$bind_status = "success";
						}else{
							$bind_status = "failure";
						}
						echo "<li><b>".$ldap_settings[0]."</b></li>";
						echo "<ul>";
						echo "<li>Connection Status: ".$conn_status."</li>";
						echo "<li>Bind Status: ".$bind_status."</li>";
						echo "<li><a href=\"?type=group_dump&host=".$ldap_settings[0]."&port=".$ldap_settings[1]."&ssl=".$ldap_settings[2]."&username=".$ldap_settings[3]."&password=".$ldap_settings[4]."&basedn=".$ldap_settings[5]."\" >group dump</a></li>";
						echo "<li><a href=\"?type=user_dump&host=".$ldap_settings[0]."&port=".$ldap_settings[1]."&ssl=".$ldap_settings[2]."&username=".$ldap_settings[3]."&password=".$ldap_settings[4]."&basedn=".$ldap_settings[5]."\" >user dump</a></li>";
						echo "<li>";
						echo "user auth test:";
						echo "<form action=\"\" method=\"POST\" >";
						echo "<br />Username<input type=\"text\" name=\"username\" />";
						echo "<br />Password<input type=\"password\" name=\"password\" />";
						echo "<input type=\"hidden\" name=\"host\" value=\"".$ldap_settings[0]."\" />";
						echo "<input type=\"hidden\" name=\"port\" value=\"".$ldap_settings[1]."\" />";
						echo "<input type=\"hidden\" name=\"conn_status\" value=\"".$conn_status."\" />";
						echo "<input type=\"submit\" />";
						echo "</form>";
						if($_POST['username'] && $_POST['password'] && $_POST['host'] && $_POST['port']){
							//connect
							$test_conn_status = ldap_connection($ldap_settings[0],$ldap_settings[1]);
							//attempt bind
							$test_bind_status = ldap_bind_connection($test_conn_status,$ldap_settings[3],$ldap_settings[4]);
							//get user data
							$test_user_query = ldap_user_query($ldap_settings[0],$ldap_settings[1],$ldap_settings[3],$ldap_settings[4],$ldap_settings[5],$_POST['username']);
							//get dn for user
							$user_dn = $test_user_query[0][dn];
							//bind with user account
							$test_bind_status = ldap_bind_connection($test_conn_status,$user_dn,$_POST['password']);
							//echo bind status
							if(isset($test_bind_status)){
								if($test_bind_status){
									echo "success";
								}else{
									echo "failure";
								}
							}
							//clear bind status
							unset($test_bind_status);
						}
						echo "</li></ul><br /><br />";
					}
				}else if($_GET['type'] == "group_dump"){
					$host = $_GET['host'];
					$port = $_GET['port'];
					$username = $_GET['username'];
					$password = $_GET['password'];
					$basedn = $_GET['basedn'];

					include 'ldap.php';
					
					$ldap_group_dump = ldap_group_dump($host,$port,$username,$password,$basedn);
					echo "<b>uid list:</b><br />";
					foreach ($ldap_group_dump as $group) {
						if(strlen($group[uid][0])>0){
							echo $group[uid][0]."<br />";	
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
					echo "<b>uid list:</b><br />";
					foreach ($ldap_user_dump as $user) {
						if(strlen($user[uid][0])>0){
							echo $user[uid][0]."<br />";	
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