<?php


require_once("config.php");


// get the incoming variables
$in_material_type = (isset($_POST['hidden_material_type'])) ? preg_replace("/[^0-9]/", "", $_POST['hidden_material_type']) : "";
$in_material_id = (isset($_POST['hidden_material_id'])) ? preg_replace("/[^0-9]/", "", $_POST['hidden_material_id']) : "";
$in_upload_type = (isset($_POST['hidden_upload_type'])) ? preg_replace("/[^0-9]/", "", $_POST['hidden_upload_type']) : "";


// error checking
if ( ($in_material_type == "") || ($in_material_id == "") || ($in_upload_type == "") ) {exit;}


// setup variables
$ds = DIRECTORY_SEPARATOR;
$date = date("Y-m-d");
$today = date("Y-m-d");
$storeFolder = 'uploads';
$UPLOAD_TABLE = array( '', 'MSDS_File', 'FDA_File' );
$UPLOAD_TYPE = array( '', 'msds_id', 'fda_id' );
require_once(dirname(__FILE__)."/setup.php");


if (!empty($_FILES))
{
	for($i=0; $i<count($_FILES['file']['name']); $i++)
	{
		$targetPath = dirname( __FILE__ ).$ds.$storeFolder.$ds;
		$tempFile = $_FILES['file']['tmp_name'];
	    $targetFileName = $_FILES['file']['name'];

	    // construct the random filename
		$fileExt = strtolower(substr($targetFileName[$i], -4));
		$filenameRandom = substr(md5(rand(0, 1000000)), 0, 64).$fileExt;

	    // get the file extension
	    $ext = pathinfo($targetFileName[$i], PATHINFO_EXTENSION);
	    $ext = str_replace(".", "", strtolower($ext));

	    // get the filename without the extension
	    $mainFilename = pathinfo($targetFileName[$i], PATHINFO_FILENAME);


    	// save the file to the database
	    $q = $db->prepare("INSERT INTO `acdlacertified`.`File` (`id`, `filename_original`, `filename_random`, `type`, `created_at`) VALUES (NULL, :filename_original, :filename_random, :type, NOW())");

	    try {
	        $db->beginTransaction();
	        $q->execute(array(':filename_original' => $targetFileName[$i], ':filename_random' => $filenameRandom, ':type' => $in_upload_type));
	        $file_id = $db->lastInsertId();
	        $db->commit();
	    } catch(PDOExecption $e) {
	        $db->rollback();
	        print "Error inserting new File: " . $e->getMessage() . "</br>";
	    }


	    // attach the file to the correct material
	    $q = $db->prepare("INSERT INTO `acdlacertified`.`File_Material` (`id`, `material_type`, `material_id`, `file_id`) VALUES (NULL, :material_type, :material_id, :file_id)");

	    try {
	        $db->beginTransaction();
	        $q->execute(array(':material_type' => $in_material_type, ':material_id' => $in_material_id, ':file_id' => $file_id));
	        $db->commit();
	    } catch(PDOExecption $e) {
	        $db->rollback();
	        print "Error inserting new File_Material: " . $e->getMessage() . "</br>";
	    }

	    if (!file_exists($targetPath))
	    {
			mkdir($targetPath, 0777, true);
		}

	    $targetFile =  $targetPath.$filenameRandom;
	    move_uploaded_file($tempFile[$i],$targetFile);
	}
}

?>