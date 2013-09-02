<?php

/**
 * Translating language with Google API
 * @author gabe@fijiwebdesign.com
 * @version $Id: google.translator.php 7 2009-08-20 09:30:43Z bucabay $
 * @license - Share-Alike 3.0 (http://creativecommons.org/licenses/by-sa/3.0/)
 * 
 * Google requires attribution for their Language API, please see: http://code.google.com/apis/ajaxlanguage/documentation/#Branding
 * 
 */
class Google_Translate_API {

	/**
	 * Translate a piece of text with the Google Translate API
	 * @return String
	 * @param $text String
	 * @param $from String[optional] Original language of $text. An empty String will let google decide the language of origin
	 * @param $to String[optional] Language to translate $text to
	 */
	function translate($text, $from = '', $to = 'en') {
		$url = 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q='.rawurlencode($text).'&langpair='.rawurlencode($from.'|'.$to);
		$response = file_get_contents(
			$url,
			null,
			stream_context_create(
				array(
					'http'=>array(
						'method'=>"GET",
						'header'=>"Referer: http://".$_SERVER['HTTP_HOST']."/\r\n"
					)
				)
			)
		);
		if (preg_match("/{\"translatedText\":\"([^\"]+)\"/i", $response, $matches)) {
			return self::_unescapeUTF8EscapeSeq($matches[1]);
		}
		return false;
	}
	
	/**
	 * Convert UTF-8 Escape sequences in a string to UTF-8 Bytes. Old version.
	 * @return UTF-8 String
	 * @param $str String
	 */
	function __unescapeUTF8EscapeSeq($str) {
		return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return html_entity_decode(\'&#x\'.$matches[1].\';\', ENT_NOQUOTES, \'UTF-8\');'), $str);
	}
	
	/**
	 * Convert UTF-8 Escape sequences in a string to UTF-8 Bytes
	 * @return UTF-8 String
	 * @param $str String
	 */
	function _unescapeUTF8EscapeSeq($str) {
		return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return Google_Translate_API::_bin2utf8(hexdec($matches[1]));'), $str);
	}
	
	/**
	 * Convert binary character code to UTF-8 byte sequence
	 * @return String
	 * @param $bin Mixed Interger or Hex code of character
	 */
	function _bin2utf8($bin) {
		if ($bin <= 0x7F) {
			return chr($bin);
		} else if ($bin >= 0x80 && $bin <= 0x7FF) {
			return pack("C*", 0xC0 | $bin >> 6, 0x80 | $bin & 0x3F);
		} else if ($bin >= 0x800 && $bin <= 0xFFF) {
			return pack("C*", 0xE0 | $bin >> 11, 0x80 | $bin >> 6 & 0x3F, 0x80 | $bin & 0x3F);
		} else if ($bin >= 0x10000 && $bin <= 0x10FFFF) {
			return pack("C*", 0xE0 | $bin >> 17, 0x80 | $bin >> 12 & 0x3F, 0x80 | $bin >> 6& 0x3F, 0x80 | $bin & 0x3F);
		}
	}
	
}


?>


