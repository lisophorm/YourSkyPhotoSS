<?php require_once('./Connections/php.mysql.class.php'); ?>
<?php
$db= new MySQL(DB,DBUSER,DBPASS);

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


if($db->records==0) {
	die("direct access not allowed");
}

	if($user['backgroundId']==1) {
		$photo_type="goth";
	} else if ($user['backgroundId']==0) {
		$photo_type="bbsh";
	} else {
		$photo_type="dsny";
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
	
	if ($_GET['share_type']=="TWITTER") {
		$landing_link=$user['shortlink'];
	} else {
		$landing_link="";
	}
	

	$share_texts = array(
	'goth' => 'Behold! The new ruler of the Seven Kingdoms has arrived. '.$landing_link.' #TheSkyDifference',
	'bbsh' => 'Protecting the world with Captain America. '.$landing_link.' #TheSkyDifference #SkyBroadbandShield',
	'dsny' => 'Celebrating Sky Movies Disney\'s 1st Birthday. '.$landing_link.' #TheSkyDifference'
);


switch($_GET['share_type']) {
	case "TWITTER":
		$incrementfield="twitter_share";
		$url="https://twitter.com/intent/tweet?text=".urlencode($share_texts[$photo_type])."&url=".$_GET['uuid'];
		//die($url);
	break;
	case "FACEBOOK":
		$incrementfield="facebook_share";
		$url="https://www.facebook.com/dialog/feed?app_id=1429486397297383&display=popup&caption=".urlencode("Your Sky Photo")."&redirect_uri=http://www.yourskyphoto.co.uk&link=".urlencode("http://www.yourskyphoto.co.uk/photo/".$_GET['uuid'])."&name=".urlencode($share_texts[$photo_type])."&picture="."http://www.yourskyphoto.co.uk/thumbs/".$user['filename'];
		//die($url);
		//https://www.facebook.com/dialog/feed?app_id=1429486397297383&display=popup&name=Celebrating+Sky+Movies+Disney%27s+1st+Birthday.+http%3A%2F%2Fis.gd%2F3dkSrz+%23TheSkyDifference&redirect_uri=https%3A%2F%2Fwww.yourskyphoto.co.uk%2Fphoto%2Ffb%2FCABD3BE7-5E22-429D-B770-C654541CEB34&picture=https%3A%2F%2Fwww.yourskyphoto.co.uk%2Fthumbs%2FCABD3BE7-5E22-429D-B770-C654541CEB34.jpg
		//$url="https://www.facebook.com/sharer/sharer.php?u=".urlencode("http://www.yourskyphoto.co.uk/photo.php?uuid=".$_GET['uuid']);
	break;
	default:
		die("direct access not allowed");
	break;
}


$result=$db->ExecuteSQL("update stats set ".$incrementfield."=".$incrementfield."+1 where uuid='".$db->SecureData($_GET['uuid'])."'");

if(!$result) {
	die("error updating stats".$db->lastQuery."-".$db->lastError);
}

//die("url:".$url);

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.


?>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Refresh" content="1; url=<?php echo $url; ?>" />
<title>Your Sky Photo</title>
<meta property="og:title" content="Your Sky Photo">
<meta property="og:type" content="activity">
<meta property="og:url" content="https://www.yourskyphoto.co.uk?showphoto.php?uuid="<?php echo $_GET['uuid']; ?>>
<meta property="og:image" content="https://www.yourskyphoto.co.uk/thumbs/<?php echo $user['filename']; ?>">
<meta property="og:site_name" content="Site Name">
<meta property="fb:admins" content="595373701"">
<meta property="fb:app_id" content="1429486397297383">
<meta property="og:description" content="this is the description">
</head>
<body>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-49351316-1']);
_gaq.push(['_trackSocial', '<?php echo strtolower($db->SecureData($_GET['share_type'])); ?>', 'share', '/photo']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 

ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>

</body>
</html>