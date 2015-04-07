<?php
	include_once("inc/facebook.php");
	include_once("config.php");

	$maxDelayTime = 8; // Set the max delay in seconds between api requests
	$maxGroups = 1; // Set the max amount of groups to post to

	
	if( $facebookUser ) { // user is logged in => get groups
	  	try {
			$getGroupsMemberOf = 'SELECT gid, name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid = ' . $facebookUser . ')'; // Get groups I'm a member of
			// $getGroupsAdminOf = 'SELECT gid, name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid = ' . $facebookUser . ' AND administrator = 'true')'; // Get groups I'm an admin of
			// $getPagesAdminOf = 'SELECT page_id, name, page_url FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid = ' . $facebookUser . ')'; // Get pages I'm an admin of

			$groups = $facebook->api( array ('method' => 'fql.query', 'query' => $getGroupsMemberOf) ); // FQL === Facebook Query Language
		} 
		catch( FacebookApiException $e ) {
			echo $e->getMessage();
			$facebookUser = null;
	  	}
	}


	if( $facebookUser && !empty($groups) ) {

		foreach( $groups as $group ) {

			if($maxGroups > 0) {

				$maxGroups = $maxGroups - 1;

				// POST to GROUP_ID/feed with the publish_stream
				$post_url = '/' . $group["gid"] . '/feed';

				$message = array(
					'message' => 'Hello World!',
					'link' => 'http://YOUR_LINK.com',
				);
				
				if ($facebookUser) {
					writeToLogs("\n\n\n Posting to Facebook Walls [" . date("Y-m-d h:i:sa", time()) . "]");
					writeToLogs("\n ----------------------------------------");

					$groupUrl = "http://www.facebook.com/groups/" . $group["gid"];

				  	try {
						$postResult = $facebook->api($post_url, 'post', $message);

						$logMessage = "\n SUCCESS: posting message to $groupUrl";
						writeToLogs($logMessage);
						echo "SUCCESS: posting message to <a href='$groupUrl' target='_blank'>" . $group['name'] . "</a><br>";
					} 
					catch (FacebookApiException $e) {
						$logMessage = "\n FAIL: posting message to '" . $groupUrl . "' with ERROR: " . $e->getMessage();
						writeToLogs($logMessage);
						echo "<br>FAIL: posting message to <a href='$groupUrl' target='_blank'>" . $group['name'] . "</a> with ERROR: " . $e->getMessage();
				  	}

				  	$delayTime = rand(3, $maxDelayTime);
	 				sleep($delayTime);
				}
			}
		}
	}


	function writeToLogs($textToWrite) {
		$currentfile = "logs.txt";
		$updatedFile = file_get_contents($currentfile);
		$updatedFile .= $textToWrite;
		file_put_contents($currentfile, $updatedFile);
	}
?>