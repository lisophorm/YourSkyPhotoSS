<?php
require_once( 'Connections/php.mysql.class.php' );
error_reporting( E_ALL );
$cust_type = 'tpc';
if($_GET['cust_type']) $cust_type = $_GET['cust_type'];
$photo_type = 'goth';
if($_GET['photo_type']) $photo_type = $_GET['photo_type'];
$urn = '19p3486719K4386';
$name = 'Becca';
$small_photo_url = 'https://www.yourskyphoto.co.uk/assets/img/test_photo.jpg';
$original_photo_url = 'https://www.yourskyphoto.co.uk/assets/img/test_photo.jpg';
function update( $urn, $value )
{
    $db = new MySQL( DB, DBUSER, DBPASS );
    
    if ( $value == "COMPLETE" ) {
        $issynced = 1;
    } else {
        $issynced = 0;
    }
    
    $result = $db->Update( "users", array(
         "issynced" => $issynced,
        "files_present" => $issynced,
        "$issynced" => $value 
    ), array(
         "urn" => $urn 
    ) );
    
    
    if ( !$result ) {
        die( "mysql error on updae status:" . $db->lastError . " query:" . $db->lastQuery );
    }
    
}

// DUPLICATE FUNCTION FOR TESTING
function shortenURL( $url )
{
    $result = file_get_contents( "http://is.gd/create.php?format=simple&url=" . $url );
    return $result;
}
function get_file_extension( $file_name )
{
    return substr( strrchr( $file_name, '.' ), 1 );
}
function makeThumb( $filename, $newwidth, $destpath )
{
    global $url;
    if ( get_file_extension( $filename ) != "jpg" ) {
        return false;
    }
    /*
     * PHP GD
     * resize an image using GD library
     */
    if ( !file_exists( $filename ) ) {
        return false;
    }
    list( $width, $height ) = getimagesize( $filename );
    if ( !$width ) {
        return false;
    }
    $ratio     = $width / $height;
    $newheight = $newwidth * $ratio;
    // Load
    $thumb     = imagecreatetruecolor( 266, 200 );
    $source    = imagecreatefromjpeg( $filename );
    // Resize
    imagecopyresized( $thumb, $source, 0, 0, 0, 0, 266, 200, $width, $height );
    // Output and free memory
    //the resized image will be 400x300
    $newpath = $destpath . "/" . basename( $filename );
    if ( !imagejpeg( $thumb, $newpath, 90 ) ) {
        return false;
    } else {
        return $url . "/" . $newpath;
    }
}
$db    = new MySQL( DB, DBUSER, DBPASS );
$users = $db->ExecuteSQL( "SELECT
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
userphoto.filename
FROM
users
INNER JOIN userphoto ON users.uuid = userphoto.uuid 
where ISNULL(users.isSynced)
 limit 20" );
//var_dump($user);
//echo "error:".$db->lastError;
if ( !$users ) {
    die( "mysql error:" + $db->lastError + " query:" + $db->lastQuery );
} else {
    var_dump( $users );
}
if($db->records==0) {
	die("No records to process");
} else if($db->records==1) {
	$user=$users;
	$users= array();
	array_push($users,$user);
}
echo "query: -" . $db->lastQuery . "<br/>";
if ( count( $users ) > 0 ) {
	//die("total users".count($users));
    for ( $i = 0; $i < count( $users ); $i++ ) {
       	$user = $users[ $i ];
        echo "<br/>unique id:" . $user[ 'uuid' ] . "<br/>";
        //echo "<br/>name:".$user['firstname'];
        $uuid      = $user[ 'uuid' ];
		$result=$db->ExecuteSQL("insert ignore into stats (urn,added) values (".$user[ 'uuid' ].",NOW())");
        /*$result=$db->Insert(array("uuid"=>$user[ 'uuid' ] ),"stats",true);
		if(!$result) {
			die("error updating stats ".$db->lastQuery);
		}*/

        
			
			//$result=$db->Update("userphoto",array("shortlink"=>$image_c_shortlink),array("uuid"=>$user[ 'uuid' ] ));
			
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
	
	$urn = $user[ 'uuid' ];
	$name = ucwords($user[ 'firstName' ]);
	$small_photo_url = 'https://www.yourskyphoto.co.uk/' . makeThumb( "upload/" . $user[ 'filename' ], 280, "thumbs" );
	$original_photo_url = 'https://www.yourskyphoto.co.uk/upload/'.$user[ 'filename' ];
	
	// template url
	
	$templateurl="https://www.yourskyphoto.co.uk/template.php?name=".$name."&cust_type=".$cust_type."&small_photo_url=".urlencode($small_photo_url)."&original_photo_url=".urlencode($original_photo_url)."&photo_type=".$photo_type."&urn=".$urn;
	
	echo "####".$templateurl."#####";
	
	// GET HTML
	$html = file_get_contents($templateurl);

	echo $html;
		
		// sends out the fuckin email
		
	require_once($_SERVER['DOCUMENT_ROOT'].'/phpmailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	$mail->SMTPDebug = true;
	$mail->do_debug = 0;

	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "ssl://smtp.sendgrid.net"; // sets the SMTP server
	$mail->Port       = 465;   
	$mail->Username   = "wasserman"; // SMTP account username
	$mail->Password   = "k0st0golov";        // SMTP account password

	$mail->AddReplyTo("noreply@yourskyphoto.co.uk","Sky");
	
	$mail->SetFrom("noreply@yourskyphoto.co.uk","Sky");
	$mail->CharSet="UTF-8";
	
	$mail->AddAddress($user['emailAddress'],$user['firstName']." ".$user['lastname']);
	//$mail->AddAddress("bratcliffe@wmgllc.com","Becca");
	//$mail->AddAddress("emyhill@wmgllc.com","ed");
	//$mail->AddAddress("aflorio@wmgllc.com","Alfo");
	
	
	$mail->Subject    = "Your Sky photo";
	//$mail->AddBCC("bratcliffe@wmgllc.com","Becca");
	$mail->AddBCC("aflorio@wmgllc.com","Alfo");
	$mail->AltBody    = "Please use an html compatible viewer!\n\n"; // optional, comment out and test
	
	
	$mail->MsgHTML($html);
	
	$mail->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', '{"unique_args": {"urn":"'.$uuid.'"},"category": "yourskyphoto"}' ) );

	//$basefile=urldecode(basename($_POST['file']));
	//$mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']."/rendered/".$basefile,"logo_2u",$basefile); // attachment
	
	if(!$mail->Send()) {
	  $emailresult= $mail->ErrorInfo;

	} else {
	 $emailresult="SUCCESS";
	}
	
	$result=$db->Update("users",array("issynced"=>1,"server_result"=>$emailresult),array("uuid"=>$uuid ));

	
	if(!$result) {
		echo "error inserting email:".$db->lastError;
	}


		
    }
} else {
    echo "Nothingtododo";
}