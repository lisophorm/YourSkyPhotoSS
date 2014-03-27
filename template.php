<?php  
require_once( 'Connections/php.mysql.class.php' );
error_reporting( E_ALL );
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
users.backgroundId,
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
userphoto.shortlink
FROM
users
INNER JOIN userphoto ON users.uuid = userphoto.uuid 
where users.uuid='".$_GET['uuid']."' limit 1" );
//var_dump($user);
//echo "error:".$db->lastError;
if ( !$user ) {
    die( "mysql error:" + $db->lastError + " query:" + $db->lastQuery );
} else {
   // var_dump( $user );
}


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
	
	if($user['backgroundId']==0) {
		$photo_type="goth";
	} else if ($user['backgroundId']==1) {
		$photo_type="bbsh";
	} else {
		$photo_type="dsny";
	}

// EMAILER URLS

$asset_prefix = 'https://www.yourskyphoto.co.uk/';
$photo_landing_url = 'https://www.yourskyphoto.co.uk/photo/';
$photo_landing_url_fb = 'https://www.yourskyphoto.co.uk/photo/fb/';
$photo_landing_url_twitter = 'https://www.yourskyphoto.co.uk/photo/tw/';

// USER CONFIG


	$uuid = $user[ 'uuid' ];
	$name = ucwords($user[ 'firstName' ]);
	$small_photo_url = 'https://www.yourskyphoto.co.uk/thumbs/'. $user[ 'filename' ];
	$original_photo_url = 'https://www.yourskyphoto.co.uk/upload/'.$user[ 'filename' ];

// USER LINKS

$user_landing_url = $photo_landing_url.$uuid;
$user_landing_url_fb = shortenURL($photo_landing_url.$uuid);
$user_landing_url_tw = shortenURL($photo_landing_url.$uuid);



// TEMPLATE OPTIONS

$upgrade_broadbank_link = 'http://www.sky.com/shop/broadband-talk/broadband-unlimited';
$broadband_shield_link = 'http://www.sky.com/products/broadband-talk/sky-broadband-shield/features/';
$join_sky_link = 'http://www.sky.com/shop/';
$find_local_store_link = 'http://www.sky.com/shop/store-locator/';
$terms_link = 'http://help.sky.com/my-account/terms-and-conditions/skycom-terms-and-conditions/'; //
$friend_link = 'http://www.sky.com/shop/'; //

/*	Customer Types:  
	sabb - Existing SABB Customer
	snbb - Existing Sky Customer � No BB
	tpc  - Existing triple play customers
	nsc  - Non Sky customer
*/
$options = array(
	'sabb' => array(
		'message' => '<p>Hi ##name##,</p>
 
<p>It was great to see you today and share with you the Sky Difference. It&rsquo;s knowing that these unbeatable services, the only thing like Sky, is Sky.</p>
 
<p>Sky Broadband Shield is a brand new tool that lets you filter which websites can be seen in your home. Choose between our PG, 13 or 18 age categories or customise your own.</p>
 
<p>It&rsquo;s free for all Sky Broadband customers, and includes features to help protect your family from sites that try and collect personal information or could disrupt your device.</p>
 
<p><a href="##broadband_shield_link##">Find out more about Sky Broadband Shield</a></p>',
		'friends' => false,
		'whats_on' => false
	),
	'snbb' => array(
		'message' => '<p>Hi ##name##,</p>
 
<p>It was great to see you today and share with you the Sky Difference. It&rsquo;s knowing that these unbeatable services, the only thing like Sky, is Sky.</p>
 
<p>You can get peace of mind with totally unlimited broadband from Sky, with absolutely no usage caps.</p>
 
<p>Plus, you can get Sky Broadband Shield, a brand new tool that lets you filter which websites can be seen in your home. Choose between our PG, 13 or 18 age categories or customise your own. It&rsquo;s free for all Sky Broadband customers, and includes features to help protect your family from sites that try and collect personal information or could disrupt your device.</p>
 
<p><a href="##upgrade_broadbank_link##">Upgrade to Sky Broadband Unlimited</a></p>
 
<p><a href="##broadband_shield_link##">Find out more about Sky Broadband Shield</a></p>',
		'friends' => true,
		'whats_on' => true
	),
	'tpc' => array(
		'message' => '<p>Hi ##name##,</p>
 
<p>It was great to see you today and share with you the Sky Difference. It&rsquo;s knowing that these unbeatable services, the only thing like Sky, is Sky.</p>
 
<p>Sky Broadband Shield is a brand new tool that lets you filter which websites can be seen in your home. Choose between our PG, 13 or 18 age categories or customise your own.</p>
 
<p>It&rsquo;s free for all Sky Broadband customers, and includes features to help protect your family from sites that try and collect personal information or could disrupt your device.</p>
 
<p><a href="##broadband_shield_link##">Find out more about Sky Broadband Shield</a></p>',
		'friends' => true,
		'whats_on' => true
	),
	'nsc' => array(
		'message' => '<p>Hi ##name##,</p>
 
<p>It was great to see you today and share with you the Sky Difference. It&rsquo;s knowing that these unbeatable services, the only thing like Sky, is Sky.</p>

<ul>
<li><span>Award winning TV at no extra cost with Sky Atlantic</span></li>
<li><span>The longest list of Catch Up TV channels in the UK</span></li>
<li><span>Sky Go, so you can watch TV anywhere you like at no extra cost</span></li>
<li><span>Sky Broadband Unlimited with absolutely no usage costs</span></li>
<li><span>Sky Broadband Shield so you can protect your family by choosing the websites you want and block the ones you don&rsquo;t</span></li>
</ul>

<p>Discover the Sky Difference by joining Sky TV and Sky Broadband.</p>
 
<p><a href="##join_sky_link##">Join Sky</a></p>

<p><a href="##find_local_store_link##">Find your local Sky store</a></p>',
		'friends' => false,
		'whats_on' => true
	)
);

$share_texts = array(
	'goth' => 'Behold! The new ruler of the Seven Kingdoms has arrived. ##landing_link## #TheSkyDifference',
	'bbsh' => 'Protecting the world with Captain America. ##landing_link##  #TheSkyDifference #SkyBroadbandShield',
	'dsny' => 'Celebrating Sky Movies Disney\'s 1st Birthday. ##landing_link## #TheSkyDifference'
);

// STYLE

$style_p = 'font-family: Verdana, Geneva, sans-serif;font-size:12px;margin:0 0 12px;';
$style_span = 'font-family: Verdana, Geneva, sans-serif;font-size:12px;';
$style_p_small = 'font-family: Verdana, Geneva, sans-serif;font-size:9px;margin:0 0 9px;';
$style_ul = 'list-style:bullet;margin:0 0 12px;padding:0 0 0 15px;';
$style_li = 'margin:0 0 3px;font-size:10px;';
$style_link = 'color:#0000ff;text-decoration:none;font-weight:bold;';
$style_dark_link = 'color:#000;text-decoration:underline;';

// SET TEMPLATE TEXTS

$friends = $options[$cust_type]['friends'];
$whats_on = $options[$cust_type]['whats_on'];

$message = str_replace(
	array(
		'<p>',
		'<ul>',
		'<li>',
		'<span>',
		'<a',
		'##name##',
		'##broadband_shield_link##',
		'##upgrade_broadbank_link##',
		'##join_sky_link##',
		'##find_local_store_link##'
	), array(
		'<p style="'.$style_p.'">',
		'<ul style="'.$style_ul.'">',
		'<li style="'.$style_li.'">',
		'<span style="'.$style_span.'">',
		'<a style="'.$style_link.'"',
		$name,
		$broadband_shield_link,
		$upgrade_broadbank_link,
		$join_sky_link,
		$find_local_store_link
	), 
	$options[$cust_type]['message']	
);

$fb_share_text = str_replace('##landing_link##', "", $share_texts[$photo_type]);
$tw_share_text = str_replace('##landing_link##', $user['shortlink'], $share_texts[$photo_type]);

	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Sky</title>
		<meta name="viewport" content="width=600" />
	</head>
	<body style="margin: 0; padding: 0;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr><td>	
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
			<tr><td background="https://www.yourskyphoto.co.uk/assets/img/background.gif" bgcolor="#f4f4f9" width="600" valign="top">
			<!--[if gte mso 9]>
			<v:rect xmlns:v="uuid:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;">
			<v:fill type="tile" src="https://www.yourskyphoto.co.uk/assets/img/background.gif" color="#f4f4f9" />
			<v:textbox style="mso-fit-shape-to-text:true" inset="0,0,0,0">
			<![endif]-->
			<div>
			
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
				<tr><td style="padding-top:38px">
					
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="542">
						<tr>
							<td width="3"><img src="https://www.yourskyphoto.co.uk/assets/img/blank.gif" alt="" width="3" height="1" /></td>
							<td width="250">
								<img src="https://www.yourskyphoto.co.uk/assets/img/blank.gif" alt="" width="250" height="1" />
								<?php echo $message; ?>	
													
							</td>
							<td width="14"><img src="https://www.yourskyphoto.co.uk/assets/img/blank.gif" alt="" width="14" height="1" /></td>
							<td width="272" valign="top">
								<img src="https://www.yourskyphoto.co.uk/assets/img/blank.gif" alt="" width="272" height="1" />
								<table align="center" border="0" cellpadding="0" cellspacing="0" width="272">
									<tr>	
										<td background="https://www.yourskyphoto.co.uk/assets/img/box.gif" bgcolor="#f4f4f9" width="272" height="206" style="width:266px;height:200px;padding:1px 3px 5px 3px;" align="center">
											<a href="<?php echo $user_landing_url ?>"><img src="<?php echo $small_photo_url ?>" alt="photo" width="266" height="200" style="width:266px;height:200px" /></a>
										</td>
									</tr>
									<tr>
										<td style="padding-top:15px">
											<a href="http://www.yourskyphoto.co.uk/sharephoto.php?share_type=FACEBOOK&uuid=<?php echo $user['uuid']; ?>"><img src="https://www.yourskyphoto.co.uk/assets/img/share_facebook.gif" alt="Share" width="272" height="50" /></a>
										</td>
									</tr>
									<tr>
										<td>
											<a href="http://www.yourskyphoto.co.uk/sharephoto.php?share_type=TWITTER&uuid=<?php echo $user['uuid']; ?>"><img src="https://www.yourskyphoto.co.uk/assets/img/share_twitter.gif" alt="Twitter" width="272" height="50" /></a>
										</td>
									</tr>
									<tr>
										<td>
											<a href="https://www.yourskyphoto.co.uk/download.php?uuid=<?php echo $uuid ?>"><img src="https://www.yourskyphoto.co.uk/assets/img/download_photo.gif" alt="Download Photo" width="272" height="52" /></a>
										</td>
									</tr>
								</table>
						
							</td>
						</tr>
					</table>
				
				</td></tr>
				<?php if($friends): ?><tr><td style="padding-top:12px">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="544">
						<tr><td>
							<a href="<?php echo $friend_link ?>"><img src="https://www.yourskyphoto.co.uk/assets/img/friends_invite.gif" alt="Friends Invite" width="544" height="103" /></a>
						</td></tr>
					</table>
				</td></tr><?php endif; ?>
				<?php if($whats_on): ?><tr><td style="padding-top:19px">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="538">
						<tr><td>
							<img src="https://www.yourskyphoto.co.uk/assets/img/whats_on_title.jpg" alt="What's on this month" width="229" height="18" />
						</td></tr>
						<tr><td style="padding-top:16px">
							<table align="center" border="0" cellpadding="0" cellspacing="0" width="538">
								<tr>
									<td><img src="https://www.yourskyphoto.co.uk/assets/img/whats_on_panel_1.gif" alt="whats_on_panel_1" width="183" height="182" /></td>
									<td><img src="https://www.yourskyphoto.co.uk/assets/img/whats_on_panel_2.jpg" alt="whats_on_panel_2" width="174" height="182" /></td>
									<td><img src="https://www.yourskyphoto.co.uk/assets/img/whats_on_panel_3.jpg" alt="whats_on_panel_3" width="160" height="182" /></td>
									<td><img src="https://www.yourskyphoto.co.uk/assets/img/whats_on_panel_4.gif" alt="whats_on_panel_4" width="20" height="182" /></td>
								</tr>
							</table>
						</td></tr>
					</table>
				</td></tr><?php endif; ?>
				<tr><td style="padding-top:19px;" align="center">
					<img src="https://www.yourskyphoto.co.uk/assets/img/sky_logo.gif" alt="sky_logo" width="99" height="74" />
				</td></tr>
				<tr><td style="padding:13px 150px 50px 150px" align="center">
					<p style="<?php echo $style_p_small ?>margin-bottom:20px;">For full terms and conditions <a href="<?php echo $terms_link ?>" style="<?php echo $style_dark_link ?>">click here</a></p>
					<p style="<?php echo $style_p_small ?>">Iron Man 3 &copy; 2013 Marvel, Game of Thrones SM, under licence from Home BoxOffice, Inc., Monsters University &copy; Disney/Pixar</p>
					<p style="<?php echo $style_p_small ?>">Sky Broadband Shield: Shield activation required. Devices must be connected to Sky Broadband. Protection of devices by anti-virus software recommended at all times. Further details at <a href="http://www.sky.com/shield/" style="<?php echo $style_dark_link ?>">sky.com/shield</a></p>
				</td></tr>
			</table>
			
			</div>
			<!--[if gte mso 9]>
			</v:textbox>
			</v:rect>
			<![endif]-->
			</td></tr>
			</table></td></tr>
 		</table>
 	</body>
</html>