<?php
/**
 * клиент Используемый для работы с http (MULTI curl); 
 * @author mapron
 * @see EHttpClient
 * @see EHttpClientAdapterMultiCurl
 */
class EMultiHttpClient extends EHttpClient
{
    protected $requests = array();
	
    protected $fieldsCopy = array( 'headers', 'method', 'paramsGet', 'paramsPost', 'enctype', 'raw_post_data', 'auth', 
    'files' );	
	
    /**
     * Данная функция завершает текущий запрос и помещает его в очередь.
     * @param unknown_type $id
     */
    public function finishRequest($id = null) 
    {
        $request = array();		
        foreach ($this->fieldsCopy as $field) {
        	$request[$field] = $this->$field;
        }
        foreach (array('uri', 'cookiejar') as $field) {
	        if (!is_null($this->$field)) {
	            $request[$field] = clone $this->$field;
	        }
        }
        
        $config = $this->config;
        unset($config['adapter']); // это, в принципе, не критично, ибо объект мы создаем вручную в request(). 
        // Сделано для совместимости с возомжными изменениями в Zend.
        
        $request['_config'] = $this->config;
        if (is_null($id)){ // можно не передавать $id, будет просто индексированный массив.
            $this->requests[] = $request;
        } else {
            $this->requests[$id] = $request;
        }
    }
	
    /**
     * Метод, который обрабатывает все ранее сделанные запросы
     * @see EHttpClient::request()
     */
    public function request() 
    {
        if (empty($this->requests)) {
            throw new EHttpClientException('Request queue is empty.');
        }
        $this->redirectCounter = 0;
        $response = null;
		
        // Да, выглядит как грязный хак, но так оно и есть. Данный класс просто не поддерживает другие адаптеры.
        $this->adapter = new EHttpClientAdapterMultiCurl();
		
        $requests = array();
        $this->last_request = array();
       
        foreach ($this->requests as $id => &$requestPure){
            //  Здесь и ниже немного измененная копия первоначального кода.
            
        	// Копируем параметры для запроса.
            foreach ($this->fieldsCopy as $field) $this->$field = $requestPure[$field];
            
            // обрабатываем URI, приводя к стандартному виду.
            $uri = $requestPure['uri'];
            if (! empty($requestPure['paramsGet'])){
                $query = $uri->getQuery();
                if (! empty($query)) {
                    $query .= '&';
                }
                $query .= http_build_query($requestPure['paramsGet'], null, '&');
                $requestPure['paramsGet'] = array();
                $uri->setQuery($query);
            }
			
            // тело запроса
            $body = $this->_prepareBody();
            if (! isset($this->headers['host'])) {
                $this->headers['host'] = array('Host', $uri->getHost());		 	
            }
            $headers = $this->_prepareHeaders();
			
            // записываем по кусочкам в массив
            $request = array();
            $request['host'] = $uri->getHost();
            $request['port'] = $uri->getPort();
            $request['scheme'] = ($uri->getScheme() == 'https' ? true : false);
            $request['method'] = $this->method;
            $request['uri'] = $uri;
            $request['httpversion'] = $this->config['httpversion'];
            $request['headers'] = $headers;
            $request['body'] = $body;
            $request['_config'] = $requestPure['_config'];
            $requests[$id] = $request;
        }
        
        // запоминаем запросы, вдруг понадобятся в отладке!
        $this->last_request = $requests;
		
        // выполняем мульти-запрос.
        $this->adapter->execute($requests);
		
        // метод read() на самом деле просто извлекает уже сохраненный результат. 
        $responses = $this->adapter->read();
		
        if (! $responses) {
            throw new EHttpClientException('Unable to read response, or response is empty');
        }
      
        // преобразуем ответы в удобный EHttpResponse для дальнейшей работы.
        foreach ($responses as &$response){			
            $response = EHttpResponse::fromString($response);
        }
		
        if ($this->config['storeresponse']) {
            $this->last_response = $response;
        }
		
        return $responses;
    }

}