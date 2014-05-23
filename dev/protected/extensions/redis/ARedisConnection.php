<?php
/**
 * Represents a redis connection.
 *
 * @author Charles Pick
 * @package packages.redis
 */

/**
 * Class ARedisConnection
 * @see https://github.com/nicolasff/phpredis
 *
 * @method mixed get(string $key) Get the value related to the specified key. If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
 * @method bool set(string $key, string $value, mixed $optional=null) Set the string value in argument as value of the key. Parameter $optional: ff you pass an integer, phpredis will redirect to SETEX, and will try to use Redis >= 2.6.12 extended options if you pass an array with valid values. Returns TRUE if the command is successful.
 * @method bool setex(string $key, int $ttl, string $value) Set the string value in argument as value of the key, with a time to live (seconds). Returns TRUE if the command is successful.
 * @method bool psetex(string $key, int $ttl, string $value) Set the string value in argument as value of the key, with a time to live (milliseconds). Returns TRUE if the command is successful.
 * @method bool setnx(string $key, string $value) Set the string value in argument as value of the key if the key doesn't already exist in the database. Returns TRUE in case of success, FALSE in case of failure.
 * @method int del(mixed $keys) Remove specified keys. Returns number of keys deleted.
 * @method int delete(mixed $keys) Remove specified keys. Returns number of keys deleted.
 * @method bool exists(string $key) Verify if the specified key exists. If the key exists, return TRUE, otherwise return FALSE.
 * @method int incr(string $key) Increment the number stored at key by one. Returns the new value.
 * @method int incrBy(string $key, int $value)  Increment the number stored at key by $value. Returns the new value.
 * @method float incrByFloat(string $key, float $value)  Increment the key with floating point precision $value. Returns the new value.
 * @method int decr(string $key) Decrement the number stored at key by one. Returns the new value.
 * @method int decrBy(string $key) Decrement the number stored at key by $value. Returns the new value.
 * @method array mGet(array $keys) Get the values of all the specified keys. Returns array containing the values related to keys in argument. If one or more keys dont exist, the array will contain FALSE at the position of the key.
 * @method array getMultiple(array $keys) Get the values of all the specified keys. Returns array containing the values related to keys in argument. If one or more keys dont exist, the array will contain FALSE at the position of the key.
 * @method string getSet(string $key) Sets a value and returns the previous entry at that key. Returns a string, the previous value located at this key.
 * @method string randomKey() Returns a random key. Returns an existing key in redis.
 * @method bool move(string $key, int $dbIndex) Moves a key to a different database. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool rename(string $srcKey, string $dstKey) Renames a key. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool renameKey(string $srcKey, string $dstKey) Renames a key. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool renameNx(string $srcKey, string $dstKey) Same as rename, but will not replace a key if the destination already exists. This is the same behaviour as setNx. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool expire(string $key, int $ttl) Sets an expiration date (a timeout, seconds) on an item. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool setTimeout(string $key, int $ttl) Sets an expiration date (a timeout, seconds) on an item. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool pexpire(string $key, int $ttl) Sets an expiration date (a timeout, milliseconds) on an item. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool expireAt(string $key, int $timestamp) Sets an expiration date (a timestamp) on an item. Returns TRUE in case of success, FALSE in case of failure.
 * @method bool pexpireAt(string $key, int $timestamp) Sets an expiration date (a timestamp in milliseconds) on an item. Returns TRUE in case of success, FALSE in case of failure.
 * @method array keys(string $pattern) Returns the keys that match a certain pattern. Returns the keys that match a certain pattern.
 * @method array getKeys(string $pattern) Returns the keys that match a certain pattern (using '*' as a wildcard.). Returns the keys that match a certain pattern.
 * @method integer append(string $key, string $value) Append specified string to the string stored in specified key. Returns size of the value after the append
 * @method string getRange(string $key, integer $start, integer $end) Returns a substring of a larger string.
 * @method integer setRange(string $key, integer $offset, string $value) Returns the length of the string after it was modified.
 * @method mixed hGet(string $key, string $hashKey) Gets a value from the hash stored at key. If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
 * @method mixed hSet(string $key, string $hashKey, string $value) Adds a value to the hash stored at key. If this value is already in the hash, FALSE is returned.
 * @method mixed hMGet(string $key, array $hashKey) Retirieve the values associated to the specified fields in the hash. For every field that does not exist in the hash, a nil value is returned. Because a non-existing keys are treated as empty hashes, running HMGET against a non-existing key will return a list of nil values.
 * @method mixed hMSet(string $key, array $hashKey, string $value)Sets the specified fields to their respective values in the hash stored at key. This command overwrites any existing fields in the hash. If key does not exist, a new key holding a hash is created.
 * @method mixed hExists(string $key, string $hashKey) Verify if the specified member exists in a key. If the member exists in the hash table, return TRUE, otherwise return FALSE.
 *
 */
class ARedisConnection extends CApplicationComponent
{
	/**
	 * The redis client
	 * @var Redis
	 */
	protected $_client;

	/**
	 * The redis server name
	 * @var string
	 */
	public $hostname = "localhost";

    /**
     * Redis default prefix
     * @var string
     */
    public $prefix = "Yii.redis.";

	/**
	 * The redis server port
	 * @var integer
	 */
	public $port=6379;

	/**
	 * The database to use, defaults to 1
	 * @var integer
	 */
	public $database=1;

    /**
     * The redis server password
     * @var password
     */
    public $password=null;

    /**
     * Включение/выключение профайлера
     * @var bool
     */
    public $enableProfiling = false;

	/**
	 * Sets the redis client to use with this connection
	 * @param Redis $client the redis client instance
	 */
	public function setClient(Redis $client)
	{
		$this->_client = $client;
	}

	/**
	 * Gets the redis client
	 * @return Redis the redis client
	 */
	public function getClient()
	{
		if ($this->_client === null) {
			$this->_client = new Redis;
			if($this->hostname[0] == '/') {
                $this->_client->connect($this->hostname);
            } else {
                $this->_client->connect($this->hostname, $this->port);
            }
			if (isset($this->password)) {
				if ($this->_client->auth($this->password) === false) {
					throw new CException('Redis authentication failed!');
				}
			}
            $this->_client->setOption(Redis::OPT_PREFIX, $this->prefix);
            $this->_client->select($this->database);
		}
		return $this->_client;
	}

	/**
	 * Returns a property value based on its name.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to read a property
	 * <pre>
	 * $value=$component->propertyName;
	 * </pre>
	 * @param string $name the property name
	 * @return mixed the property value
	 * @throws CException if the property is not defined
	 * @see __set
	 */
	public function __get($name) {
		$getter='get'.$name;
		if (property_exists($this->getClient(),$name)) {
			return $this->getClient()->{$name};
		}
		elseif(method_exists($this->getClient(),$getter)) {
			return $this->$getter();
		}
		return parent::__get($name);
	}

	/**
	 * Sets value of a component property.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to set a property
	 * <pre>
	 * $this->propertyName=$value;
	 * </pre>
	 * @param string $name the property name
	 * @param mixed $value the property value
	 * @return mixed
	 * @throws CException if the property is not defined or the property is read only.
	 * @see __get
	 */
	public function __set($name,$value)
	{
		$setter='set'.$name;
		if (property_exists($this->getClient(),$name)) {
			return $this->getClient()->{$name} = $value;
		}
		elseif(method_exists($this->getClient(),$setter)) {
			return $this->getClient()->{$setter}($value);
		}
		return parent::__set($name,$value);
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $name the property name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$getter='get'.$name;
		if (property_exists($this->getClient(),$name)) {
			return true;
		}
		elseif (method_exists($this->getClient(),$getter)) {
			return true;
		}
		return parent::__isset($name);
	}

	/**
	 * Sets a component property to be null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using unset() to set a component property to be null.
	 * @param string $name the property name or the event name
	 * @throws CException if the property is read only.
	 * @return mixed
	 */
	public function __unset($name)
	{
		$setter='set'.$name;
		if (property_exists($this->getClient(),$name)) {
			$this->getClient()->{$name} = null;
		}
		elseif(method_exists($this,$setter)) {
			$this->$setter(null);
		}
		else {
			parent::__unset($name);
		}
	}
	/**
	 * Calls a method on the redis client with the given name.
	 * Do not call this method. This is a PHP magic method that we override to
	 * allow a facade in front of the redis object.
	 * @param string $name the name of the method to call
	 * @param array $parameters the parameters to pass to the method
	 * @return mixed the response from the redis client
	 */
	public function __call($name, $parameters) {
		$params = is_array($parameters) ? $paramsEncoded = json_encode($parameters, JSON_UNESCAPED_UNICODE) : $parameters;
        Yii::trace('Call Redis command: <strong>' . $name . '</strong> with parameters: '. $params, 'system.Redis');
        if($this->enableProfiling) {
            Yii::beginProfile('redis.command: '.$name.' ' . $params,'redis.command');
        }
        $result = call_user_func_array([$this->getClient(),$name], $parameters);

        if($this->enableProfiling) {
            Yii::endProfile('redis.command: '.$name.' ' . $params,'redis.command');
        }
        return $result;
	}

    /**
   	 * Returns the statistical results of Redis commands executions.
   	 * The results returned include the number of Redis commands executed and
   	 * the total time spent.
   	 * In order to use this method, {@link enableProfiling} has to be set true.
   	 * @return array the first element indicates the number of SQL statements executed,
   	 * and the second element the total time spent in SQL execution.
   	 */
   	public function getStats()
   	{
   		$logger=Yii::getLogger();
   		$timings=$logger->getProfilingResults(null,'redis.command');
   		$count=count($timings);
   		$time=array_sum($timings);
   		return array($count,$time);
   	}
}
