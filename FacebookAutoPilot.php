<?php
	include_once("config.php");
	
	require_once __DIR__ . "/lib/facebook-php-sdk-v4-4.0-dev/autoload.php"; //include autoload from SDK folder
	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\GraphUser;
	use Facebook\FacebookRedirectLoginHelper;


	FacebookSession::setDefaultApplication($appId , $appSecret);
	$helper = new FacebookRedirectLoginHelper($redirectURL);

	try {
	  $session = $helper->getSessionFromRedirect();
	} 
	catch(FacebookRequestException $ex) {
		die("FacebookRequestException: " . $ex->getMessage());
	} 
	catch(\Exception $ex) {
		die("Exception: " . $ex->getMessage());
	}


	// if I'm logged in and ready to post on group pages
	if ($session) { 

		$groups = (new FacebookRequest(
			$session,
			'GET',
			'/me/groups'
		))->execute()->getGraphObject()->asArray();
		$_SESSION["groups"] = $groups["data"]; 

		echo "Total Groups: " . count($groups["data"]);

		if(isset($_SESSION["groups"])) {
			echo '<br>Hi, you are logged into Facebook [ <a href="?logOut=1">Log Out</a> ] ';
			
			writeToLogs("\n\n\nPosting to Facebook Walls [" . date("Y-m-d h:i:sa", time()) . "]");
			writeToLogs("\n----------------------------------------");

			for($i = 0; $i < $maxGroups; $i++) {
				
				if($_SESSION["groups"][$i]) {
					$group = $_SESSION["groups"][$i];

					// exclude certain groups
					$continue = true;
					if (strpos($group->name,'Science') !== false) { $continue = false; }
					else if (strpos($group->name,'UCC') !== false) { $continue = false; }
					else if(strpos($group->name,'Udemy') !== false) { $continue = false; }
					else if (strpos($group->name,'JCI') !== false) { $continue = false; }
					else if (strpos($group->name,'Cappamore') !== false) { $continue = false; }

					if($continue) {
						$postURL = '/' . $group->id . '/feed';

						$message = array(
							'message' => 'Hey guys!

							Thought I’d give something back to the community :)

						 	Check out v2 of AppLandr, which allows you to generate beautifully crafted (free or paid) landing pages for your mobile applications! And of course it’s on Product Hunt!
							
							Would love to hear your thoughts!
							
							http://applandr.com',
							'link' => 'http://applandr.com',
							'picture' => 'http://www.applandr.com/lib/images/dark.png'
						);
						
						$groupUrl = "http://www.facebook.com/groups/" . $group->id;

					  	try {
							$postRequest = new FacebookRequest($session, 'POST', $postURL, $message);
					  		$postRequest->execute();
					
							$logMessage = "\nSUCCESS: posting message to $groupUrl";
							writeToLogs($logMessage);
							echo "<br>SUCCESS: posting message to <a href='$groupUrl' target='_blank'>" . $group->name . "</a>";
						} 
						catch(FacebookRequestException $ex) {
							$logMessage = "\nFAIL: posting message to " . $groupUrl . " with ERROR: " . $e->getMessage();
							writeToLogs($logMessage);
							echo "<br>FAIL: posting message to <a href='$groupUrl' target='_blank'>" . $group->name . "</a> with ERROR: " . $e->getMessage();
						}

					  	$delayTime = rand($minDelayTime, $maxDelayTime);
		 				sleep($delayTime);
					}
				}
			}
		}
	} else { 
		$loginURL = $helper->getLoginUrl( array( 'scope' => $requiredPermissions ) );
		echo '<a href="'.$loginURL.'">Login with Facebook</a>'; 
	}


	if(isset($_GET["logOut"]) && $_GET["logOut"]==1){
		unset($_SESSION["groups"]);
		header("location: ". $redirectURL);
	}


	function writeToLogs($textToWrite) {
		$currentfile = "logs.txt";
		$updatedFile = file_get_contents($currentfile);
		$updatedFile .= $textToWrite;
		file_put_contents($currentfile, $updatedFile);
	}
?>