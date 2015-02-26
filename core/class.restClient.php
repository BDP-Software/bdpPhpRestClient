<?php

/**
* Usage:
*
* $api = new RestClient(array(
* 'base_url' => "http://api.twitter.com/1/",
* 'format' => "json"
* ));
* $result = $api->get("statuses/public_timeline");
* if($result->info->http_code < 400)
* json_decode($result->response);
*
* Configurable Options:
* headers - An associative array of HTTP headers and values to be
* included in every request.
* curl_options - cURL options to apply to every request. These will
* override any internally generated values.
* user_agent - User agent string.
* base_url - URL to use for the base of each request.
* format - Format to append to resource.
* username - Username to use for basic authentication. Requires password.
* password - Password to use for basic authentication. Requires username.
*
* Options can be set upon instantiation, or individually afterword:
*
* $api = new RestClient(array(
* 'format' => "json",
* 'user_agent' => "my-application/0.1"
* ));
*
* -or-
*
* $api = new RestClient;
* $api->set_option('format', "json");
* $api->set_option('user_agent', "my-application/0.1");
*/

class RestClient {
    
    public $options;
    public $handle; // cURL resource handle.
    
    // Populated after execution:
    public $response; // Response body.
    public $headers; // Parsed reponse header object.
    public $info; // Response info object.
    public $error; // Response error string.
    
	/**
	 * logs and runs a report - mainly for scheduled tasks
	*/
	function logReport($txt=''){
		global $logReport;
		if(!$logReport){
			$logReport = array();
		}	
		$logReport[] = $txt;
		global $fileLog;
		if($fileLog){
			$this->fileLog($txt,$fileLog);
		}
	}
	
	/**
	 * logs an array to the output
	*/
	function logArray($data){
		if(is_array($data)){
			foreach($data as $key => $val){
				$this->logReport($key .' :: '. $val);
			}
		}
	}

	
    public function __construct($options=array()){
        $this->options = array_merge(array(
            'headers' => array(),
            'curl_options' => array(),
            'user_agent' => "PHP RestClient/0.1",
            'base_url' => NULL,
            'format' => NULL,
            'username' => NULL,
            'password' => NULL
        ), $options);
    }
    
    public function set_option($key, $value){
        $this->options[$key] = $value;
    }
    
    public function get($url, $parameters=array(), $headers=array()){
        return $this->execute($url, 'GET', $parameters, $headers);
    }
    
    public function post($url, $parameters=array(), $headers=array()){
        return $this->execute($url, 'POST', $parameters, $headers);
    }
    
    public function put($url, $parameters=array(), $headers=array()){
		return $this->execute($url, 'PUT', $parameters, $headers);
    }
    
    public function delete($url, $parameters=array(), $headers=array()){
        return $this->execute($url, 'DELETE', $parameters, $headers);
    }
    
    public function format_query($parameters, $primary='=', $secondary='&'){
        $query = "";
        foreach($parameters as $key => $value){
            $pair = array(urlencode($key), urlencode($value));
            $query .= implode($primary, $pair) . $secondary;
        }
		
        return rtrim($query, $secondary);
    }
    
    public function parse_response($response){
        $headers = array();
        $http_ver = strtok($response, "\n");
        
        while($line = strtok("\n")){
            if(strlen(trim($line)) == 0) break;
            
            list($key, $value) = explode(':', $line, 2);
            $key = trim(strtolower(str_replace('-', '_', $key)));
            $value = trim($value);
            if(empty($headers[$key])){
                $headers[$key] = $value;
            }
            elseif(is_array($headers[$key])){
                $headers[$key][] = $value;
            }
            else {
                $headers[$key] = array($headers[$key], $value);
            }
        }
        
        $this->headers = (object) $headers;
        $this->response = strtok("");
    }
    
    public function execute($url, $method='GET', $parameters=array(), $headers=array()){
        $client = clone $this;
        $client->url = $url;
        $client->handle = curl_init();
        $curlopt = array(
            CURLOPT_HEADER => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => $client->options['user_agent']
        );
        
        if($client->options['username'] && $client->options['password'])
            $curlopt[CURLOPT_USERPWD] = sprintf("%s:%s",
                $client->options['username'], $client->options['password']);
        
       
	   
		
	   if(count($client->options['headers']) || count($headers)){
            $curlopt[CURLOPT_HTTPHEADER] = array();
            $headers = array_merge($client->options['headers'], $headers);
            foreach($headers as $key => $value){
                $curlopt[CURLOPT_HTTPHEADER][] = sprintf("%s:%s", $key, $value);
            }
        }
        
        if($client->options['format'])
            $client->url .= '.'.$client->options['format'];
        
        if(strtoupper($method) != 'GET'){
			if($method == 'PUT'){
				$curlopt[CURLOPT_CUSTOMREQUEST] = 'PUT';
			}
			elseif($method == 'DELETE'){
				$curlopt[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			}
			else{
				$curlopt[CURLOPT_POST] = TRUE;
			}
			
            //Mike Barcroft
			//BD modification to support json posts
			//17/03/13
			if($this->options['postJson']){
				$curlopt[CURLOPT_POSTFIELDS] = json_encode($parameters);
			}
			elseif($this->options['postArray']){
				$curlopt[CURLOPT_POSTFIELDS] = $parameters;
			}
			else{
				$curlopt[CURLOPT_POSTFIELDS] = $client->format_query($parameters);
			}
        }
        elseif(count($parameters)){
            $client->url .= strpos($client->url, '?')? '&' : '?';
            $client->url .= $client->format_query($parameters);
        }
        
        if($client->options['base_url']){
            if($client->url[0] != '/' || substr($client->options['base_url'], -1) != '/')
                $client->url = '/' . $client->url;
            $client->url = $client->options['base_url'] . $client->url;
        }
        $curlopt[CURLOPT_URL] = $client->url;
        
        if($client->options['curl_options']){
            // array_merge would reset our numeric keys.
            foreach($client->options['curl_options'] as $key => $value){
                $curlopt[$key] = $value;
            }
        }
		
		if($this->options['authKey'] && $this->options['sharedSecret']){
			//sort the headers for processing
			ksort($headers);
			//put the signature string together
			$stringToSign = '';
			$stringToSign .= strtoupper($method) ."\n";
			//loop through the headers
			foreach($headers as $key => $header){
				if(strtolower($key) != 'authorization'){
					$stringToSign .= "\n" . strtolower($header);
				}
			}
			//echo'Headers:<br/>';var_dump($headers);
			//echo'Client String:<br/>'."\n". $stringToSign ."<br/>\n"; 
			$this->stringToSign = $stringToSign;
			//echo'Shared Secret: '. $this->options['sharedSecret'] ."<br/>";
			//create the signature
			$sig = base64_encode(hash_hmac ('sha1',$stringToSign,$this->options['sharedSecret'],true));
			//set the full key
			$fullKey = $this->options['authKey'] .':'. $sig;
			//echo'Full Key: '. $fullKey ."<br/>";
			$curlopt[CURLOPT_HTTPHEADER][] = sprintf("%s:%s",'Authorization',$fullKey);
		}
		
		//grab the curl toption keys
		$constants = get_defined_constants(true);
		$curlOptLookup = preg_grep('/^CURLOPT_/', array_flip($constants['curl']));
		//create a usable debuggable array of curl options
		$curlDebugArr = array();
		foreach($curlopt as $key => $val){
			if(isset($curlOptLookup[$key])){
				//var_dump($val);
				$curlDebugArr[$key] = $curlOptLookup[$key] .' ('. $key .') : '. (is_array($val) ? implode($val) : $val);
			}
		}
		$this->curlDebugArr = $curlDebugArr;
		$this->curlopt = $curlopt;
		
		$this->requestHeaders = $curlopt[10023];
		
        curl_setopt_array($client->handle, $curlopt);
        //var_dump( (object) curl_getinfo($client->handle));
		if($this->forceInsecure){
			curl_setopt($client->handle, CURLOPT_VERBOSE, true);
			curl_setopt($client->handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($client->handle, CURLOPT_SSL_VERIFYPEER, false);
		}
        $client->parse_response(curl_exec($client->handle));
		$client->info = (object) curl_getinfo($client->handle);
        $client->error = curl_error($client->handle);
        
		
		//echo "HTTP Code: '". $client->info->http_code ."'";
		//echo $client->response;
		$fail = false;
		
		//BD modification to support json posts
		//17/03/13
		if($client->info->http_code > 399){
			$this->errorCode = $client->info->http_code;
			$fail = true;
			$this->logReport('BDP API '. ($fail ? 'Fail' : 'Success') .' '. date('d M Y H:i:s'));
			$this->logReport("Request Path: '". $client->url ."'");
			$this->logReport('');
			$this->logReport('Request headers: ');
			$this->logArray($this->requestHeaders);
			$this->logReport('');
			$this->logReport('Response Headers');
			$this->logArray($client->info);
			$this->logReport('Reponse Text: ');
			$this->logReport($client->response);
			$this->logReport('');
			$this->logReport('');
			throw new Exception($client->response,$client->info->http_code);
		}
		if($fail){
			
		}
		if($this->fullLog || $fail){
			$this->logReport('BDP API '. ($fail ? 'Fail' : 'Success') .' '. date('d M Y H:i:s'));
			$this->logReport("Request Path: '". $client->url ."'");
			$this->logReport('');
			$this->logReport('Request headers: ');
			$this->logArray($this->requestHeaders);
			$this->logReport('');
			$this->logReport('Response Headers');
			$this->logArray($client->info);
			$this->logReport('Reponse Text: ');
			$this->logReport($client->response);
			$this->logReport('');
			$this->logReport('');
		}
        curl_close($client->handle);
        
		$this->client = $client;
		
		return $client;
    }
}

?>