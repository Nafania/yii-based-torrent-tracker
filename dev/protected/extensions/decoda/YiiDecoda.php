<?php

/**
 * Yii Decoda Class
 *
 * @author Vadim Vorotilov <fant.geass@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php Licensed under The MIT License
 * @link    http://milesj.me/code/php/decoda
 * @version 1.1
 */

class YiiDecoda extends CApplicationComponent
{

	/**
	 * @var string the path to the vendor location
	 * Defaults to 'ext.decoda.vendors.decoda'
	 */
	public $vendorPath = 'ext.decoda.vendors.decoda';

	/**
	 * @var array
	 * By default, all tags are wrapped in square brackets.
	 * syntax: array('[',']')
	 */
	public $brackets;

	/**
	 * @var string
	 * Decoda comes with a built-in translation dictionary, which is used to translate words like "mail" and "quote".
	 * To see the list of supported locales, open up the config/messages.json file.
	 * if not set, see in Yii::app()->language
	 */
	public $locale;

	/**
	 * @var bool
	 * If you would like a hyperlink to display its shorthand variant
	 * (displaying the word link or mail, instead of the text/url/email)
	 */
	public $shorthandLinks = false;

	/**
	 * @var bool
	 * By default all strings are parsed as HTML, if you would like to use XHTML set true
	 */
	public $useXHTML = false;

	/**
	 * @var array
	 * To only parse specific tags, pass an array of whitelisted tags
	 */
	public $whitelistTags;

	/**
	 * @var bool
	 * Use it to disable parsing all together
	 */
	public $disableParsing = false;

	/**
	 * @var bool
	 * Add all default filters and hooks
	 */
	public $defaults = false;

	/**
	 * @var array
	 * List of used filters
	 * example: array('Email', 'Url', 'Quote')
	 */
	public $addFilters = array();

	/**
	 * @var array
	 * List of removed filters
	 * example: array('Email', 'Url', 'Quote')
	 */
	public $removeFilters = array();

	/**
	 * @var bool
	 * You can also disable filters by set this property true, which will turn off all tag parsing.
	 * Doing this actually removes all filters, so you can't un-disable them unless you add them again.
	 */
	public $disableFilters = false;

	/**
	 * @var array
	 * List of used hooks
	 * example: array('Emoticon', 'Censor')
	 */
	public $addHooks = array();

	/**
	 * @var array
	 * List of removed hooks
	 * example: array('Emoticon', 'Censor')
	 */
	public $removeHooks = array();

	/**
	 * @var bool
	 * You can also disable hooks by set this property true.
	 * Doing this actually removes all hooks, so you can't un-disable them unless you add them again.
	 */
	public $disableHooks = false;

	/**
	 * Replace ' ' into '&nbsp;'
	 * @var bool
	 */
	public $convertWhitespaces = true;

	/**
	 * @var Decoda
	 */
	private $_decoda;

	/**
	 * @var string
	 */
	private $_assetsUrl;


	/**
	 * Register script and setup Decoda
	 */
	public function init()
	{
		require_once (Yii::getPathOfAlias($this->vendorPath) . '/Decoda.php');
		Yii::registerAutoloader(array('Decoda', 'loadFile'), true);
		$this->_decoda = new Decoda();

		parent::init();
	}

	/**
	 * Get decoda assets url
	 * @return string
	 */
	public function getAssetsUrl()
	{
		if (empty($this->_assetsUrl)) {
			//$this->_assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/assets');
		}
		return $this->_assetsUrl;
	}

	/**
	 *  Get Decoda object
	 * @return Decoda
	 */
	public function getDecoda()
	{
		return $this->_decoda;
	}

	/**
	 * Reset => setup => parse
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function parse($string)
	{
		if ($this->convertWhitespaces) {
			$string = str_replace(' ', "&nbsp;", $string);
		}

		$this->_decoda->reset($string, true);
		$this->setup();
		return $this->_decoda->parse();
	}

	/**
	 * Return the parsing errors.
	 *
	 * @param $type int
	 *
	 * @return array
	 */
	public function getErrors($type = Decoda::ERROR_ALL)
	{
		return $this->_decoda->getErrors($type);
	}

	/**
	 * Setup all settings, include filters and hooks
	 */
	public function setup()
	{
		if ($this->defaults == true) {
			$this->_decoda->defaults();
		}

		$this->_setupFilters();
		$this->_setupHooks();

		if (is_array($this->brackets)) {
			$this->_decoda->setBrackets($this->brackets[0], $this->brackets[1]);
		}

		if ($this->locale) {
			$this->_decoda->setLocale($this->locale);
		} elseif (Yii::app()->language != null) {
			$this->_decoda->setLocale(Yii::app()->language);
		}

		if ($this->shorthandLinks == true) {
			$this->_decoda->setShorthand(true);
		}

		if ($this->useXHTML == true) {
			$this->_decoda->setXhtml(true);
		}

		if (is_array($this->whitelistTags)) {
			$this->_decoda->whitelist($this->whitelistTags);
		}

		if ($this->disableParsing == true) {
			$this->_decoda->disable(true);
		}
	}

	/**
	 * Setup filters
	 */
	protected function _setupFilters()
	{
		if (is_array($this->addFilters) && !empty($this->addFilters)) {
			foreach ($this->addFilters as $filter) {
				$filterClass = $filter . 'Filter';
				$this->_decoda->addFilter(new $filterClass());
			}
		}

		if (is_array($this->removeFilters) && !empty($this->removeFilters)) {
			foreach ($this->removeFilters as $filter) {
				$this->_decoda->removeFilter($filter);
			}
		}

		if ($this->disableFilters == true) {
			$this->_decoda->disableFilters();
		}
	}

	/**
	 * Setup hooks
	 */
	protected function _setupHooks()
	{
		if (is_array($this->addHooks) && !empty($this->addHooks)) {
			foreach ($this->addHooks as $hook) {
				$hookClass = $hook . 'Hook';
				$this->_decoda->addHook(new $hookClass());
			}
		}

		if (is_array($this->removeHooks) && !empty($this->removeHooks)) {
			foreach ($this->removeHooks as $hook) {
				$this->_decoda->removeHook($hook);
			}
		}

		if ($this->disableHooks == true) {
			$this->_decoda->disableHooks();
		}
	}

}
