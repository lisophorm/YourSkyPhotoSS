v<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
$photoboothUserJSON = $_POST['parameters'];

error_reporting(0);

chdir(dirname(__FILE__));
require_once('Connections/php.mysql.class.php');
$db = new MySQL(DB, DBUSER, DBPASS);

//array("uuid"=>$xml->uuid,"current_location"=>$xml->current_location,"firstname"=>$xml->firstname,"lastname"=>$xml->lastname,"email"=>$xml->email,"mobile"=>$xml->mobile,"added"=>$xml->added,"tablet_id"=>$xml->tablet_id)
$userData = json_decode($_POST['parameters'], true);

$debugdata = print_r($userData, true);





//if(!$result) {
//trigger_error("DB error inserting users".$db->lastError." on query:".$db->lastQuery, E_USER_ERROR);
//}

/*{"emailAddress":"user@address.com","lastName":"userLastName","addressLineOne":"userAddressLineOne","addressLineTwo":"userAddressLineTwo","firstName":"userFirstName","mobileNumber":"0733234202","postcode":"BBV 33D","existingBroadbandCustomer":"1","existingTVCustomer":"0","backgroundId":"2","uuid":"UUID-233423"} */

$allowedExts = array(
    "gif",
    "jpeg",
    "jpg",
    "png"
);
$temp        = explode(".", $_FILES["file"]["name"]);
$extension   = end($temp);
if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "application/octet-stream")) && in_array($extension, $allowedExts)) {
    if ($_FILES["file"]["error"] > 0) {
        $retuuidStatus = array(
            "status" => "Error",
            "message" => "return Code: " . $_FILES["file"]["error"]
        );
        
    } else {
        if (file_exists("upload/" . $_FILES["file"]["name"])) {
            $retuuidStatus = array(
                "status" => "Succsess",
                "message" => "Already uploaded in: " . "upload/" . $_FILES["file"]["name"]
            );
        } else {
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
            $retuuidStatus = array(
                "status" => "Succsess",
                "message" => "Stored in: " . "upload/" . $_FILES["file"]["name"]
            );
        }
    }
} else {
    $retuuidStatus = array(
        "status" => "Error",
        "message" => "Invalid File : " . $_FILES["file"]["type"] . " : " . $_FILES["file"]["size"] . " : " . $extension
    );
    
}

$result = $db->InsertUpdate(array(
    "uuid" => $userData['uuid'],
    "filename" => $_FILES["file"]["name"],
    "backgroundId" => $userData['backgroundId'],
    "debugdata" => $debugdata
), "userphoto", array(
    "uuid" => $userData['uuid']
));
if (!$result) {
	$retuuidStatus = array(
        "status" => "Error",
        "message" => "Error in mysql user photo : " . $db->lastError . " " . $db->lastQuery
    );
    trigger_error("Error in mysql userphoto : " . $db->lastError . " " . $db->lastQuery);
}

$result = $db->Insert($userData, "users",true);

if (!$result) {
    $retuuidStatus = array(
        "status" => "Error",
        "message" => "Error in mysql insert user : " . $db->lastError . " " . $db->lastQuery
    );
    trigger_error("Error in mysql insert user : " . $db->lastError . " " . $db->lastQuery);
}
header('Content-Type: application/json');
echo json_encode($retuuidStatus);
?>