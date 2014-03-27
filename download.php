<?php require_once('./Connections/php.mysql.class.php'); ?>
<?php
$db= new MySQL(DB,DBUSER,DBPASS);

$user=$db->Select("users",array("uuid"=>$_GET['uuid']));

if($db->records==0) {
	die("user not found -".$db->lastQuery);
}

$photo=$db->Select("userphoto",array("uuid"=>$_GET['uuid']));

if(!isset($_GET['no_index'])) {
	$result=$db->ExecuteSQL("update stats set download_from_email=download_from_email+1 where uuid='".$db->SecureData($_GET['uuid'])."'");
	
	if(!$result) {
		die("error updating stats".$db->lastQuery."-".$db->lastError);
	}
}

if(!file_exists("upload/".$photo['filename'])) {
	die("photo not found");
}
$file =  "upload/".$photo['filename'];
//echo "path: $file";
//echo "size:".filesize($file);
//readfile($file);
//die();

if (strpos($file, '../') !== false ||
    strpos($file, "..\\") !== false ||
    strpos($file, '/..') !== false ||
    strpos($file, '\..') !== false ||
	strpos($file, '.php') !== false ||
	strpos($file, '://') !== false)
{
    die("death to the hacker");
}



if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
  
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
 header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;



$db->CloseConnection();
?>
