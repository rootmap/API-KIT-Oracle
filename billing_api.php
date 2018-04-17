<?php
$msisdn = $_REQUEST['msisdn'];
$account_number = $_REQUEST['account_number'];
$bill_date = $_REQUEST['bill_date'];
$bill_type = $_REQUEST['bill_type'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

if(!isset($_REQUEST['filter_type']))
{
	$filter_type = 0;
}
else
{
	$filter_type = $_REQUEST['filter_type'];
}

include('APIAccessLog.php');
$obj=new APIClassLog();

$auth=$obj->APILoginCheck($username,$password);

if($username=='bms' && $auth==1)
{
	
	include("api_method.php");
	$billing_details = $obj->getBillingDetails($msisdn,$account_number,$bill_date,$bill_type);
	if(empty($billing_details))
	{
		header('Content-Type:text/json'); 
		$response_array = array('success'=>false,
			'login_status'=> 1,
			'response'=>'File not found.');

		$obj->PushAPILog($_REQUEST,$response_array,"failed");

		echo json_encode($response_array);

	}
	else
	{
		$fileName=$billing_details;
		$fileGetUrl=$obj->fileGetURL($billing_details);
		$response_array = array('success'=>true,
			'login_status'=> 1,
			'file_name'=>$fileName,
			'file_get_url'=>$fileGetUrl,
			'response'=>'success | File Open in Browser');
		//$obj->PushAPILog($_REQUEST,$response_array,"success");

		//echo basename($billing_details);

		//die();
		header("Content-type: application/pdf");
		header("Content-Disposition: inline; filename=".basename($fileGetUrl));
		@readfile($fileGetUrl);
	}
	
}
elseif($username=='crm' && $auth==1)
{
	
	if(!isset($_REQUEST['filter_type']))
	{
		$filter_type=1;

	}
	include("api_method.php");
	$billing_details = $obj->getBillingDetails($msisdn,$account_number,$bill_date,$bill_type);
	if(empty($billing_details))
	{
		header('Content-Type:text/json'); 
		$response_array = array('success'=>false,
			'login_status'=> 1,
			'response'=>'File not found.');
		$obj->PushAPILog($_REQUEST,$response_array,"failed",$filter_type);
		echo json_encode($response_array);
	}
	else
	{
		$fileName=$billing_details;
		$fileGetUrl=$obj->fileGetURL($billing_details);
		
		

		if(!empty($filter_type))
		{
			$response_array = array('success'=>true,
			'login_status'=> 1,
			'file_name'=>$fileName,
			'file_get_url'=>$fileGetUrl,
			'response'=>'success');
			$obj->PushAPILog($_REQUEST,$response_array,"success",$filter_type);
			header('Content-Type:text/json'); 
			//echo $billing_details;
			
			echo json_encode($response_array);
		}
		else
		{
			$response_array = array('success'=>true,
			'login_status'=> 1,
			'file_name'=>$fileName,
			'file_get_url'=>$fileGetUrl,
			'response'=>'success | File Open in Browser');

			$obj->PushAPILog($_REQUEST,$response_array,"success",$filter_type);
			header("Content-type: application/pdf");
			header("Content-Disposition: inline; filename=".basename($fileGetUrl));
			@readfile($fileGetUrl);
		}
		



	}
	
}
else
{
	header('Content-Type:text/json'); 
	$response_array = array('success'=>false,
		'login_status'=> 0,
		'response'=>'Login Failed, Username/password is wrong' );
	$obj->PushAPILog($_REQUEST,$response_array,"failed");
	echo json_encode($response_array); 
}
?>