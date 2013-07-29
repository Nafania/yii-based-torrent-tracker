<?php
/**
 * Аналог  EHttpClient_Adapter_Curl, только использует curl_multi для параллельных запросов.
 * @author mapron
 * @see EHttpClient_Adapter_Curl
 */
class EHttpClientAdapterMultiCurl extends EHttpClientAdapterCurl
{
    protected $_mcurl = null; // handle мультикурла;
    protected $_handles = array();	// массив с handle curl-запросов.

	/**
	 * Выполнение запросов с помощью curl_multi
	 * @param array $requests массив с запросами
	 * @throws EHttpClientException
	 */
    public function execute(array $requests) 
    {
		
        $this->_mcurl = curl_multi_init();
		
        foreach ($requests as $id => $request) {		
            $this->setConfig($request['_config']);	
            // Do the actual connection
            $_curl = curl_init();
            if ($request['port'] != 80) {
                curl_setopt($_curl, CURLOPT_PORT, intval($request['port']));
            }
			
            // Set timeout
            curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $this->_config['timeout']);
			
            // Set Max redirects
            curl_setopt($_curl, CURLOPT_MAXREDIRS, $this->_config['maxredirects']);
			
            if (!$_curl){			
                throw new EHttpClientException('Unable to Connect to ' . $request['host'] . ':' . 
                	$request['port']);
            }
			
            if ($request['scheme'] !== false) 
            {
                // Behave the same like Zend_Http_Adapter_Socket on SSL options.
                if (isset($this->_config['sslcert'])) 
                {
                    curl_setopt($_curl, CURLOPT_SSLCERT, $this->_config['sslcert']);
                }
                if (isset($this->_config['sslpassphrase'])) {
                    curl_setopt($_curl, CURLOPT_SSLCERTPASSWD, $this->_config['sslpassphrase']);
                }
            }
			
            // Update connected_to
            $this->_connected_to = array($request['host'], $request['port']);
			
            if (!$_curl) {
                throw new EHttpClientException('Trying to write but we are not connected');
            }
			
            if ($this->_connected_to[0] != $request['uri']->getHost() || $this->_connected_to[1] != $request['uri']->
            	getPort()) {
                throw new EHttpClientException('Trying to write but we are connected to the wrong host');
            }
			
            // set URL
            curl_setopt($_curl, CURLOPT_URL, $request['uri']->__toString());
			
            // ensure correct curl call
            $curlValue = true;
            switch ($request['method']) 
            {
                case EHttpClient::GET:
                    $curlMethod = CURLOPT_HTTPGET;
                    break;
				
                case EHttpClient::POST:
                    $curlMethod = CURLOPT_POST;
                    break;
				
                case EHttpClient::PUT:
                    // There are two different types of PUT request, either a Raw Data string has been set
                    // or CURLOPT_INFILE and CURLOPT_INFILESIZE are used.
                    if (isset($this->_config['curloptions'][CURLOPT_INFILE])) 
                    {
                        if (!isset($this->_config['curloptions'][CURLOPT_INFILESIZE])) {
                            //require_once 'Zend/Http/Client/Adapter/Exception.php';
                            throw new EHttpClientException(
                            'Cannot set a file-handle for cURL option CURLOPT_INFILE without also setting its size in CURLOPT_INFILESIZE.'
                            );
                        }
						
                        // Now we will probably already have Content-Length set, so that we have to delete it
                        // from $headers at this point:
                        foreach ($request['headers'] AS $k => $header) 
                        {
                            if (stristr($header, 'Content-Length:') !== false) {
                                unset($request['headers'][$k]);
                            }
                        }
						
                        $curlMethod = CURLOPT_PUT;
                    } else {
                        $curlMethod = CURLOPT_CUSTOMREQUEST;
                        $curlValue = 'PUT';
                    }
                    break;
				
                case EHttpClient::DELETE:
                    $curlMethod = CURLOPT_CUSTOMREQUEST;
                    $curlValue = 'DELETE';
                    break;
				
                case EHttpClient::OPTIONS:
                    $curlMethod = CURLOPT_CUSTOMREQUEST;
                    $curlValue = 'OPTIONS';
                    break;
				
                case EHttpClient::TRACE:
                    $curlMethod = CURLOPT_CUSTOMREQUEST;
                    $curlValue = 'TRACE';
                    break;
				
                default:
                    // For now, through an exception for unsupported request methods
                    //require_once 'Zend/Http/Client/Adapter/Exception.php';
                    throw new EHttpClientException('Method currently not supported');
            }
			
            // get http version to use
            $curlHttp = ($request['httpversion'] = 1.1) ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0;
			
            // mark as HTTP request and set HTTP method
            curl_setopt($_curl, $curlHttp, true);
            curl_setopt($_curl, $curlMethod, $curlValue);
			
            // ensure headers are also returned
            curl_setopt($_curl, CURLOPT_HEADER, true);
			
            // ensure actual response is returned
            curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
			
            // set additional headers
            $request['headers']['Accept'] = '';
            curl_setopt($_curl, CURLOPT_HTTPHEADER, $request['headers']);
			
            /**
			 * Make sure POSTFIELDS is set after $curlMethod is set:
			 * @link http://de2.php.net/manual/en/function.curl-setopt.php#81161
			 */
            if ($request['method'] == EHttpClient::POST) {
                curl_setopt($_curl, CURLOPT_POSTFIELDS, $request['body']);
            }elseif ($curlMethod == CURLOPT_PUT) {
                // this covers a PUT by file-handle:
                // Make the setting of this options explicit (rather than setting it through the loop following a bit 
                // lower) 
                // to group common functionality together.
                curl_setopt($_curl, CURLOPT_INFILE, $this->_config['curloptions'][CURLOPT_INFILE]);
                curl_setopt($_curl, CURLOPT_INFILESIZE, $this->_config['curloptions'][CURLOPT_INFILESIZE]);
                unset($this->_config['curloptions'][CURLOPT_INFILE]);
                unset($this->_config['curloptions'][CURLOPT_INFILESIZE]);
            }elseif ($request['method'] == EHttpClient::PUT) {
                // This is a PUT by a setRawData string, not by file-handle
                curl_setopt($_curl, CURLOPT_POSTFIELDS, $request['body']);
            }
			
            // set additional curl options
            if (isset($this->_config['curloptions'])) {
                foreach ((array)$this->_config['curloptions'] as $k => $v) {
                    if (!in_array($k, $this->_invalidOverwritableCurlOptions)) {
                        //echo $k.'=>'.$v."\r\n";
                        if (curl_setopt($_curl, $k, $v) == false) {
                            //require_once 'Zend/Http/Client/Exception.php';
                            throw new EHttpClientException(sprintf("Unknown or erroreous cURL option '%s' set", 
                            $k));
                        }
                    }
                }
            }			
            curl_multi_add_handle($this->_mcurl, $_curl);
            $this->_handles[$id] = $_curl;
        }
		
        $running = null;
        do{
            curl_multi_exec($this->_mcurl, $running);
            // added a usleep for 0.10 seconds to reduce load
            usleep (100000);
        }
        while ($running > 0);
		
        // get the content of the urls (if there is any)
        $this->_response = array();
        $requestsTexts = array();
        foreach ($this->_handles as $id => $ch) {
            // get the content of the handle
            $resp = curl_multi_getcontent($ch);
			
            // remove the handle from the multi handle
            curl_multi_remove_handle($this->_mcurl, $ch);		       
				
			
            $request = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            $request .= $requests[$id]['body'];
            $requestsTexts[$id] = $request;
			
            if (empty($resp))  {
                // require_once 'Zend/Http/Client/Exception.php';
                throw new EHttpClientException('Error in cURL request: ' . curl_error($ch));
            }
			
            // cURL automatically decodes chunked-messages, this means we have to disallow the Zend_Http_Response to do 
            // it again 
            if (stripos($resp, "Transfer-Encoding: chunked\r\n")){
                $resp = str_ireplace("Transfer-Encoding: chunked\r\n", '', $resp);
            }
			
            // Eliminate multiple HTTP responses.
            do {
                $parts = preg_split('|(?:\r?\n){2}|m', $resp, 2);
                $again = false;
				
                if (isset($parts[1]) && preg_match("|^HTTP/1\.[01](.*?)\r\n|mi", $parts[1])) {
                    $resp = $parts[1];
                    $again = true;
                }
            }
            while ($again);
			
            // cURL automatically handles Proxy rewrites, remove the "HTTP/1.0 200 Connection established" string:
            if (stripos($resp, "HTTP/1.0 200 Connection established\r\n\r\n") !== false)  {
                $resp = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $resp);
            }
			
            $this->_response[$id] = $resp;
		 	
            if (is_resource($ch)) {
                curl_close($ch);
            }
        }
        curl_multi_close($this->_mcurl);

        return $requestsTexts;
    }
}