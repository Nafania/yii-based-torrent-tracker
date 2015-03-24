<?php

abstract class ReviewInterface
{

    /**
     * Method to get type of reviews (video, audio, games etc)
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @return array
     */
    abstract public function getNeededFields();

    /**
     * @return string
     */
    abstract public function getId();

    /**
     * Must return [] if we have data, null if something wrong with data and false if we have errors with getting data
     *
     * @param array $args
     *
     * @return array|bool|null
     */
    abstract protected function getApiData($args);

    /**
     * @param array $params
     * @return string
     */
    abstract public function returnReviewString( $params );

    /**
     * Метод должен возвращать массив параметров параметр => описание
     * которые получает по апи
     *
     * @return array
     */
    abstract public function getReturnParams();

    /**
     * @param $attrs
     * @param $className
     * @param $primaryKey
     * @return mixed
     */
    public function getReviewData($attrs, $className, $primaryKey)
    {
        $row = Yii::app()->db->createCommand('SELECT params FROM reviews WHERE modelId = :pk AND modelName = :name AND apiName = :apiName AND mtime > :mtime')->queryRow(true, [
                ':pk' => $primaryKey,
                ':name' => $className,
                ':apiName' => $this->getId(),
                ':mtime' => (time() - 1 * 24 * 60 * 60)]
        );

        if ($row) {
            return CJSON::decode($row['params']);
        }

        return $this->sendData($this->getApiData($attrs), $className, $primaryKey);
    }

    /**
     * @param $data
     * @param $className
     * @param $primaryKey
     * @return mixed
     */
    public function sendData($data, $className, $primaryKey)
    {
        /**
         * @var $review Review
         */
        $review = Review::model()->findByPk(array(
            'modelId' => $primaryKey,
            'modelName' => $className,
            'apiName' => $this->getId()
        ));

        if ( !$review ) {
            $review = new Review();
            $review->modelId = $primaryKey;
            $review->modelName = $className;
            $review->apiName = $this->getId();
            $review->mtime = time();
        }

        $review->mtime = time();

        /**
         * if $data !== false then we have answer from api, it may be null if not correct answer, may be data not present or something else.
         * If $data === false, there are no answer from api, so we not update params
         */
        if ($data !== false) {
            $review->params = ($data === null) ? new CDbExpression('NULL') : CJSON::encode($data);
        }

        //must update mtime anyway
        $review->save();

        return $data;
    }

    /**
     * @param       $url
     * @param array $options
     * @param bool $parseJson
     *
     * @return mixed|object
     * @throws CException
     */
    public function makeRequest($url, $options = array(), $parseJson = true)
    {
        $ch = $this->initRequest($url, $options);

        $result = curl_exec($ch);
        $headers = curl_getinfo($ch);

        if (curl_errno($ch) > 0) {
            throw new CException(curl_error($ch));
        }

        if ($headers['http_code'] != 200) {
            Yii::log('Invalid response http code: ' . $headers['http_code'] . '.' . PHP_EOL . 'URL: ' . $url . PHP_EOL . 'Result: ' . $result,
                CLogger::LEVEL_ERROR);
            throw new CException(Yii::t('reviewsModule.common',
                'Invalid response http code: {code}.',
                array('{code}' => $headers['http_code'])), $headers['http_code']);
        }

        curl_close($ch);

        if ($parseJson) {
            $result = $this->parseJson($result);
        }

        return $result;
    }

    /**
     * Initializes a new session and return a cURL handle.
     *
     * @param string $url url to request.
     * @param array $options HTTP request options. Keys: query, data, referer.
     *
     * @return cURL handle.
     */
    protected function initRequest($url, $options = array())
    {
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // error with open_basedir or safe mode
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if (isset($options['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
        }

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['useragent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $options['useragent']);
        }
        if (isset($options['proxy']) && is_array($options['proxy'])) {
            $key = array_rand($options['proxy'], 1);
            curl_setopt($ch, CURLOPT_PROXY, $options['proxy'][$key]);
        }

        if (isset($options['query'])) {
            $url_parts = parse_url($url);
            if (isset($url_parts['query'])) {
                $query = $url_parts['query'];
                if (strlen($query) > 0) {
                    $query .= '&';
                }
                $query .= http_build_query($options['query']);
                $url = str_replace($url_parts['query'], $query, $url);
            } else {
                $url_parts['query'] = $options['query'];
                $new_query = http_build_query($url_parts['query']);
                $url .= '?' . $new_query;
            }
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        return $ch;
    }

    /**
     * Parse response from {@link makeRequest} in json format and check OAuth errors.
     *
     * @param string $response Json string.
     *
     * @return object result.
     */
    protected function parseJson($response)
    {
        try {
            $result = json_decode($response);
            $error = $this->fetchJsonError($result);
            if (!isset($result)) {
                throw new CException(Yii::t('reviewsModule.common',
                    'Invalid response format.' . PHP_EOL . 'Response: {response}',
                    array('{response}' => var_export($response, true))), 500);
            } else {
                if (isset($error) && !empty($error['message'])) {
                    throw new CException($error['message'], $error['code']);
                } else {
                    return $result;
                }
            }
        } catch (Exception $e) {
            throw new CException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the error info from json.
     *
     * @param stdClass $json the json response.
     *
     * @return array the error array with 2 keys: code and message. Should be null if no errors.
     */
    protected function fetchJsonError($json)
    {
        if (isset($json->error)) {
            return array(
                'code' => 500,
                'message' => 'Unknown error occurred.',
            );
        } else {
            return null;
        }
    }
}