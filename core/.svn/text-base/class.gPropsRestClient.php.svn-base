<?php
/**
 * BD Properties Rest Client
*/
class gPropsRestClient extends bdCoreRestClient{

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
 * Gets property data
 *@param int $pId Property Id
*/
function getProperty($pId=0){
	//run the query
	$output = false;
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	try{
		$result = $this->api->get("property/". $pId,array(),array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		//echo $result->response; exit;
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}

/**
 * Gets all property data
 *@param int $pId Property Id
*/
function getProperties($sQuery=0){
	//run the query
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	$output = false;
	try{
		$result = $this->api->get("props/". $sQuery,array(),array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		//echo $result->response; exit;
		
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}

/**
 * creates a new detail request - this covers viewins, details about properties and general newsletter requests
 *@param array $rData Request Data
*/
function newRequest($rData=array()){
	//run the query
	//$result = $this->post("property/michael",array('pId'=>$pId)); //example post - use later for updating
	$output = false;
	try{
		//$this->api->options['postJson'] = false;
		//$this->api->options['postArray'] = true;
		$result = $this->api->put("request/",$rData,array('Content-Type'=>'application/json'));
		$output = json_decode($result->response,true);
		//var_dump($this->api->curlDebugArr);
		echo $result->response; exit;
		
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	//return the response
	return $output;
}




}
?>