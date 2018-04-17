<?php 
class APIClassLog
{

	private $dbUser="BMS";
	private $dbPass="Bms@123";
	private $dbAccessLink="192.168.7.51:1521/orcl";
	private $modeOfDevelopment=0;
	private $logAPITable="BMS_API_REQUEST_LOG";
	private $baseURL="http://localhost/bms_api/billing_pdf";
	private $billingPDFLocation="../billing_pdf/";

	private function Auth()
	{
		@$conn = oci_pconnect($this->dbUser, $this->dbPass, $this->dbAccessLink);
	    if (!$conn) {
	    	if($this->modeOfDevelopment==0)
	    	{
	    		print_r(oci_error()); die(); 
	    	}
	        else
	        {
	        	die('Please Check Your DB Connectivity'); // . oci_error()
	        }
	    }

	    return $conn;
	}

	public function fileGetURL($filename='')
	{
		$returnURL='';
		if(!empty($filename))
		{
			$returnURL=$this->baseURL.'/'.$filename;
		}
		return $returnURL;
	}

	public function APILoginCheck($username='',$password=''){
		if(!empty($username) && !empty($password))
		{
			$conn=$this->Auth();
			$mdnQuery = "SELECT count(ID) as IS_FOUND FROM BMS_API_USER WHERE USER_NAME='$username' AND PASSWORD='$password'";
			$stid = oci_parse($conn, $mdnQuery);
		    oci_execute($stid);
			
			while (($row = oci_fetch_array($stid, OCI_ASSOC)) != false) {
		        $value = $row['IS_FOUND'];
		    }
			
			if($value==0){
				return 0;
			}
			else{
				return 1;
			}
		}
		
		return 0;
	}


	public function Insert($table, $InsertArray=array()){
		$conn=$this->Auth();

		if(count($InsertArray)>0)
		{
			$count = 0;
        	$fields = '';
        	$columns = '';
			foreach ($InsertArray as $col => $val) {
	            if ($count++ != 0){ $fields .= ', '; $columns .= ', '; }
	            $columns .= "$col";
	            $fields .= "'$val'";
	        }
		    
		    $InsertQuery = "INSERT INTO ".$table." (".$columns.") VALUES (".$fields.")";	
			$stid = oci_parse($conn, $InsertQuery);
		    if(oci_execute($stid)){
		    	return 1;
		    }
			else{
				return 0;
			}
		}
		else
		{
			return 0;
		}		
	}

	public function RequestParam($param=array())
	{	
		$response="";
		if(!empty($param) && count($param)>0)
		{
			if(array_key_exists('password',$param))
			{
				$_REQUEST['password']="***************";
			}

			$response=json_encode($_REQUEST);
		}

		return $response;
	}

	public function ParamReturnREQ($param=array(),$key='')
	{	
		$response="";
		if(!empty($param) && count($param)>0)
		{
			if(array_key_exists($key,$param))
			{
				$response=$_REQUEST[$key];
			}

			
		}

		return $response;
	}

	public function PushAPILog($param,$response,$status,$api_type=0)
	{
		$requestParam=$this->RequestParam($param);

		$userName=$this->ParamReturnREQ($param,"username");
		$account_number=$this->ParamReturnREQ($param,"account_number");
		$msisdn=$this->ParamReturnREQ($param,"msisdn");
		$bill_date=$this->ParamReturnREQ($param,"bill_date");
		$bill_type=$this->ParamReturnREQ($param,"bill_type");
		
		$messageArray=array(
			"USER_NAME"=>$userName,
			"CLIENT_IP"=>$_SERVER['REMOTE_ADDR'],
			"ACCOUNT_NUMBER"=>$account_number,
			"MSISDN"=>$msisdn,
			"BILL_DATE"=>$bill_date,
			"BILL_TYPE"=>$bill_type,
			"PARAM"=>$requestParam,
			"STATUS"=>$status,
			"RESPONSE"=>json_encode($response),
			"API_TYPE"=>$api_type
		);

		$this->Insert($this->logAPITable,$messageArray);

	}

	public function Delete($table, $DeleteArray=array(),$deleteAll=false){
		$conn=$this->Auth();

		if(count($DeleteArray)>0)
		{
			$count = 0;
        	$fields = '';
			foreach ($DeleteArray as $col => $val) {
	            if ($count++ != 0){ $fields .= ' AND '; }
	            $fields .= "$col = '$val'";
	        }
	        if($deleteAll){
	        	$DeleteQuery = "DELETE FROM ".$table."";	
	        }
	        else{
	        	$DeleteQuery = "DELETE FROM ".$table." where ".$fields;
	        }

			$stid = oci_parse($conn, $DeleteQuery);
		    if(oci_execute($stid)){
		    	return 1;
		    }
			else{
				return 0;
			}
		}
		else
		{
			return 0;
		}		
	}

	public function getBillingDetails($msisdn,$account_number,$bill_date,$bill_type)
	{
		$file_name_search=$account_number;

		$file=$this->billingPDFLocation.$file_name_search."*";
		$data=glob($file); 
		if(count($data)>0)
		{
			$files=$data[0];
			/*header('Content-Disposition: attachment; filename="'.basename($files).'"');
			header('Content-Length: '.filesize($files));
			readfile($files);// for download */

				/*
				header("Content-type: application/pdf");
				header("Content-Disposition: inline; filename=".basename($files));
				@readfile($files);
				*/

				return basename($files);
		}
		else
		{
			return 0;
		}
	}

}