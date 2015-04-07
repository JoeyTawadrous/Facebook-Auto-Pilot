<?php 
	$appId = 'YOUR_FACEBOOK_APP_ID'; 
	$appSecret = 'YOUR_FACEBOOK_APP_SECRET';
	$fbPermissions = 'publish_actions, user_groups'; 

	$facebook = new Facebook(array(
	  'appId'  => $appId,
	  'secret' => $appSecret
	));
	$facebookUser = $facebook->getUser();
?>