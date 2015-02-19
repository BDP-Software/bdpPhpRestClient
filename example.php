<?php
//start the session
session_start();

$incPath = '../lib/';

//grab the core client
require_once($incPath . 'class.bdCoreRestClient.php');
//grab the gprops client
require_once($incPath . 'class.gpropsRestClient.php');

//run the class
$api = new gPropsRestClient();
//set the api key
$api->apiKey = '#### YOUR API KEY ####';
//set the shared secret
$api->sharedSecret = '#### YOUR SHARED SECRET ####';
//set the wsdl path
$api->apiPath = 'https://api.bdphq.com/restapi';
//set the firm id
$api->accId = '### YOUR ACCOUNT ID ###';
//force insecurity when running local and unable to authenticate against ssl
//$api->forceInsecure = true;
$api->fullLog = true;
//start the soap client
$api->startUp();


//Retrieve Specific Property Data
//Use a valid property ID on your account
$propertyData = $api->getProperty('1');
echo'Got property data:';
print_r($propertyData);


echo"<br/>";
echo count($api->apiErrors) .' Api Errors';
var_dump($api->apiErrors);

?>