<?php
/**
 * Core BD Rest Client
*/
class bdCoreRestClient{




///////////////////////////////////////////////////////////////////
///parameters//////////////////////////////////////////////////////
//set the key start
public $keyStart = 'BDWS';
//set the user agent name
public $userAgent = 'BD PHP Rest Client 1.0.0';




///////////////////////////////////////////////////////////////////
///variables //////////////////////////////////////////////////////
//api key
public $apiKey = '';
//shared secret
public $sharedSecret = '';
//sets whether a session id should be kept (default is true)
public $keepSession = true;
//login data (apiKey=>key,sessId=>sessId)
public $loginData = array();
//object containing the soap client
public $apiClient;
//logs any soap errors that appear
public $apiErrors = array();
//path to the actual wsdl web service
public $apiPath = '';
//set the api handler
public $api;
//set the system to use security by default
public $forceInsecure = false;
//set full log to false by default
public $fullLog = false;

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////


/**
 * constructor
*/
function __construct(){

}


/**
 * starts up the client
*/
function startUp(){
	
	//set the full key
	$fullKey = $this->keyStart .' '. $this->apiKey;
	
	//test rest api client
	require_once('class.restClient.php');
	$this->api = new RestClient(array(
		'base_url' => $this->apiPath,
		'user_agent'=>$this->userAgent,
		'postJson'=>true,
		'headers'=>array(
			'Date'=>date('r'),
			'AccountId'=>$this->accId
		),
		'authKey'=>$fullKey,
		'sharedSecret'=>$this->sharedSecret
	   
	));
	$this->api->forceInsecure = $this->forceInsecure;
	$this->api->fullLog = $this->fullLog;
}

/**
 * runs a post request
*/
public function post($url, $parameters=array(), $headers=array()){
	//set result to false by default
	$result = false;
	//run a post
	$defHeaders = array(
		'Content-Type'=>'application/json',
	);
	//set the headers
	$headers = array_merge($defHeaders,$headers);
	try{
		$result = $this->api->post($url,$parameters,$headers);
	}
	catch(Exception $e){
		$this->logApiError($e);
	}
	return $result;
}

/**
 * runs a report - mainly for scheduled tasks
*/
function runReport($plainText=true,$doHeader=true){
	global $logReport;
	$output = '';
	foreach($logReport as $report){
		$output .= $report . ($plainText ? "\n" : '<br/>');
	}
	if($doHeader){
		if($plainText){
			header('Content-Type: text/plain');
		}
		echo $output;
		exit;
	}
	return $output;
}

/**
 * logs a soap error
 *@param object $e SoapFault object
*/
public function logApiError($e){
	$this->apiErrors[] = array(
		'code'=>$e->getCode(),
		'msg'=>$e->getMessage(),
	);
}


}
?>