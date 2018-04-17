<?php
//header('Content-Type:text/xml'); 
//include("conn.php");

function getBillingDetails($msisdn,$account_number,$bill_date,$bill_type,$filter_type=0)
{
	$file_name_search=$account_number;

	$file="../billing_pdf/".$file_name_search."*";
	$data=glob($file); 
	if(count($data)>0)
	{
		$files=$data[0];
		/*header('Content-Disposition: attachment; filename="'.basename($files).'"');
		header('Content-Length: '.filesize($files));
		readfile($files);// for download */
		if(empty($filter_type))
		{

			header("Content-type: application/pdf");
			header("Content-Disposition: inline; filename=".basename($files));
			@readfile($files);
		}
		elseif($filter_type==1)
		{
			return basename($files);
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}





?>