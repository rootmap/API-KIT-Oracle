<?php 
/*  $conn = oci_pconnect('BMS', 'Bms@123', '192.168.7.51:1521/orcl');
//$conn = oci_pconnect('GSTORE', 'U{xP9PFf', '10.101.10.18:1881/SRVGSTORE');
    if (!$conn) {
        die('Could not connect: ' . oci_error());
    }
	
	echo 1;
   */
	include('APIAccessLog.php');
	$obj=new APIClassLog();

	//echo $obj->ExistAPIUSER();

	$insertArray=array(
		"NAME"=>"BMS",
		"USER_NAME"=>"bms",
		"PASSWORD"=>"bms@123",
		"API_TYPE"=>1,
	);
	echo $obj->Insert("BMS_API_USER",$insertArray);
	//echo $obj->Delete("BMS_API_USER",$insertArray);

?>
