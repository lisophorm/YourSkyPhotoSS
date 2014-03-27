<?php 

$asset_prefix="/";
 
require_once( 'Connections/php.mysql.class.php' );

$db = new MySQL( DB, DBUSER, DBPASS );

$user = $db->ExecuteSQL( "SELECT
users.id,
users.isSynced,
users.usertype,
users.uuid,
users.tablet_id,
users.firstName,
users.lastName,
users.emailAddress,
users.addressLineOne,
users.addressLineTwo,
users.mobileNumber,
users.postcode,
users.existingBroadbandCustomer,
users.existingTVCustomer,
users.current_location,
users.added,
users.server_result,
users.posts,
users.last_scrape,
users.offline,
users.winner,
userphoto.uuid,
userphoto.filename,
userphoto.backgroundId,
userphoto.shortlink
FROM
users
INNER JOIN userphoto ON users.uuid = userphoto.uuid 
where users.uuid='".$db->SecureData($_GET['uuid'])."'
 limit 1" );
//var_dump($user);
//echo "error:".$db->lastError;
if ( !$user ) {
    die( "mysql error:" + $db->lastError + " query:" + $db->lastQuery );
} else {
 //   var_dump( $user );
//	echo "backgroubnd:".$user['backgroundId'];
}

if($db->records==0) {
	die("direct access not allowed");
}

/*
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
*/

// URL FUNCTION
function shortenURL($url) {
	$result=file_get_contents("http://is.gd/create.php?format=simple&url=".$url);
	return $result;
}
	
	if(trim($user['existingBroadbandCustomer'])=="1" && trim($user['existingTVCustomer'])=="1") {
		$cust_type = 'tpc';
	} else if(trim($user['existingTVCustomer'])==1) {
		$cust_type = 'sabb';
	} else if(trim($user['existingBroadbandCustomer'])==1) {
		$cust_type = 'snbb';
	} else {
		$cust_type = 'nsc';
	}
	
	if($user['backgroundId']==1) {
		$photo_type="goth";
	} else if ($user['backgroundId']==0) {
		$photo_type="bbsh";
	} else {
		$photo_type="dsny";
	}
	

$uuid=$user['uuid'];
	
	
$small_photo_url = 'https://www.yourskyphoto.co.uk/thumbs/'.$user['filename'];
$original_photo_url = 'https://www.yourskyphoto.co.uk/upload/'.$user['filename']; 

// USER LINKS

$user_landing_url = $photo_landing_url.$uuid;
$user_landing_url_fb = shortenURL($photo_landing_url.$uuid);
$user_landing_url_tw = shortenURL($photo_landing_url.$uuid);

// TEMPLATE OPTIONS

$share_texts = array(
	'goth' => 'Behold! The new ruler of the Seven Kingdoms has arrived. ##landing_link## #TheSkyDifference',
	'bbsh' => 'Protecting the world with Captain America. ##landing_link##  #TheSkyDifference #SkyBroadbandShield',
	'dsny' => 'Celebrating Sky Movies Disney\'s 1st Birthday. ##landing_link## #TheSkyDifference'
);

$fb_share_text = str_replace('##landing_link##', "", $share_texts[$photo_type]);
$tw_share_text = str_replace('##landing_link##', $user['shortlink'], $share_texts[$photo_type]);
	
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Sky</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo $asset_prefix ?>assets/css/normalize.css">
		<link rel="stylesheet" href="<?php echo $asset_prefix ?>assets/css/style.css">
        <meta property="og:title" content="Your Sky Photo">
        <meta property="og:type" content="activity">
        <meta property="og:url" content="https://www.yourskyphoto.com">
        <meta property="og:image" content="https://www.yourskyphoto.co.uk/thumbs/" <?php echo $user['filename']; ?>">
        <meta property="og:site_name" content="Your Sky Photo">
        <meta property="fb:admins" content="595373701"">
        <meta property="fb:app_id" content="1429486397297383">
        <meta property="og:description" content="<?php echo $share_texts[$photo_type]; ?>">
	</head>
	<body>
		<div class="wrap">
			<div class="row header">
				<img src="<?php echo $asset_prefix ?>assets/img/sky_header_logo.png" alt="Sky" width="141" height="92" />
			</div>
			<div class="row photo">
				<img src="<?php echo $original_photo_url; ?>" alt="test_photo" width="254" height="191" />
			</div>
			<div class="row buttons">
				<a class="button" href="http://www.yourskyphoto.co.uk/sharephoto.php?share_type=FACEBOOK&uuid=<?php echo $user['uuid']; ?>"><img src="<?php echo $asset_prefix ?>assets/img/share_facebook.png" alt="Share" width="272" height="50" /></a>
				<a class="button" href="http://www.yourskyphoto.co.uk/sharephoto.php?share_type=TWITTER&uuid=<?php echo $user['uuid']; ?>"><img src="<?php echo $asset_prefix ?>assets/img/share_twitter.png" alt="Twitter" width="272" height="50" /></a>
				<a class="button" href="/download.php?uuid=<?php echo $uuid ?>"><img src="<?php echo $asset_prefix ?>assets/img/download_photo.png" alt="Download Photo" width="272" height="52" /></a>
				<div class="clear"></div>
			</div>
			<div class="row fb_follow">
				<a target="_blank" href="https://www.facebook.com/sky">
					<img width="143" height="30" alt="Connect With Us" src="<?php echo $asset_prefix ?>assets/img/fbconnect.gif" style="border:none;">
				</a>
			</div>
			<div class="clear"></div>
		</div>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-49351316-1']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 

ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</body>
</html>