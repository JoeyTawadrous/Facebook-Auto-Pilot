<?php 
	session_start();

	$appId = ''; 
	$appSecret = '';
	$requiredPermissions = 'public_profile, publish_actions, user_groups'; 
	$redirectURL = ''; // FB will redirect to this page with a code
 	
 	$minDelayTime = 20; // Set the min delay in seconds between api requests
	$maxDelayTime = 40; // Set the max delay in seconds between api requests
	$maxGroups = 1; // Set the max amount of groups to post to
?>