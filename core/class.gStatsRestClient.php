<?php
/**
 * BD Properties Statistics Rest Client
*/
class gStatsRestClient extends bdCoreRestClient{

///////////////////////////////////////////////////////////////////
///parameters//////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////
///variables //////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////


/**
 * constructor
*/
function __construct(){
	parent :: __construct();
}

/**
 * Gets general stats
*/
function getStats($searchData=0){
	//run the query
	$output = false;
	/*
	$searchData = array(
		'fromDate'=>"26 Mar 2007 19:37:58 +0000",
		'toDate'=>'26 Mar 2014 19:37:58 +0000',
		'area'=>'',
		'town'=>'edinburgh'
		'district'=>'barnton',
		'priceFrom'=>'450000',
		'priceTo'=>'500000'
	);
	*/
	//set the data input string
	$dataInputString = urlencode(json_encode($searchData));
	//var_dump($dataInputString); exit;
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	try{
		$result = $this->api->get("stats/". $dataInputString,array(),array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		echo $result->response; exit;
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}


/**
 * get property enquiries
*/
function getPropertyEnquiries($searchData){
	//run the query
	$output = false;
	/*
	$searchData = array(
		'fromDate'=>"26 Mar 2007 19:37:58 +0000",
		'toDate'=>'26 Mar 2014 19:37:58 +0000',
		'pId'=>'',
	);
	*/
	//set the data input string
	$dataInputString = urlencode(json_encode($searchData));
	//var_dump($dataInputString); exit;
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	try{
		$result = $this->api->get("propertyenquiries/". $dataInputString,array(),array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		echo $result->response; exit;
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}

/**
 * gets enquiries for a range of properties
*/
function getEnquiries($searchData){
	//run the query
	$output = false;
	/*
	$searchData = array(
		'fromDate'=>"26 Mar 2007 19:37:58 +0000",
		'toDate'=>'26 Mar 2014 19:37:58 +0000',
		'pId'=>'',
	);
	*/
	//set the data input string
	$dataInputString = urlencode(json_encode($searchData));
	//var_dump($dataInputString); exit;
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	try{
		$result = $this->api->get("enquiries/". $dataInputString,array(),array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		//echo $result->response; exit;
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}


}
?>