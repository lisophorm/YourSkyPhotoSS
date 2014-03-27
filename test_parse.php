<?php 

/*	Customer Types:  
	'sabb' - Existing SABB Customer second
	'snbb' - Existing Sky Customer Ð No BB first tickbox
	'tpc'  - Existing triple play customers both tickboxes
	'nsc'  - Non Sky customer no tickbox
	
	Photo Types:
	'goth' - Game of Thrones
	'bbsh' - Broadband Shield
	'dsny' - Disney
);
*/

// SET VARIABLES
$cust_type = 'tpc';
if($_GET['cust_type']) $cust_type = $_GET['cust_type'];
$photo_type = 'goth';
if($_GET['photo_type']) $photo_type = $_GET['photo_type'];
$uuid = '68BB94D0-F3E5-4C03-9459-FDE058F173AD';
$name = 'Becca';
$small_photo_url = 'https://www.yourskyphoto.co.uk/assets/img/test_photo.jpg';
$original_photo_url = 'https://www.yourskyphoto.co.uk/assets/img/test_photo.jpg';


// GET HTML
$html = file_get_contents("https://www.yourskyphoto.co.uk/template.php?name=".$name."&cust_type=".$cust_type."&small_photo_url=".urlencode($small_photo_url)."&original_photo_url=".urlencode($original_photo_url)."&photo_type=".$photo_type."&uuid=".$uuid);

echo $html;