<?php
class TextHelper {

	static function makeClickable ( $text ) {
		$text = preg_replace("#(^|[\n\s>])([\w]+?://[^\s\"\n\r\t<]*)#is", "\\1<a href=\"\\2\">\\2</a>", $text);

		return $text;
	}

	static function youtubeToPicture ( $text ) {
		$text = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be\.com|\.be)\/(watch\?v=)?([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
			'<a href="http://youtube.com/watch?v=$3" class="fancybox youtube"><img src="http://img.youtube.com/vi/$3/0.jpg" class="img-polaroid" /></a>',
			$text);
		return $text;
	}

	static function imagesToFancybox ($text) {
		//<img alt="cb3779f3ff72.jpg" src="http://s017.radikal.ru/i413/1208/cb/cb3779f3ff72.jpg">
		//<a href="http://s017.radikal.ru/i413/1208/cb/cb3779f3ff72.jpg" class="fancybox"><img src="http://s017.radikal.ru/i413/1208/cb/cb3779f3ff72.jpg" class="img-polaroid"/></a>
		$text = preg_replace('/<img(.*?)src="(.*?)"(.*?)>/i', '<a href="$2" class="fancybox"><img src="$2" class="img-polaroid" /></a>', $text);
		return $text;
	}

	static function parseText ( $text ) {
		$text = self::imagesToFancybox($text);
		$text = self::youtubeToPicture($text);
		$text = self::makeClickable($text);
		//                <a href="http://www.youtube.com/watch?v=d4okxow8bNo&feature=player_embedded" class="fancybox youtube"><img src="http://img.youtube.com/vi/d4okxow8bNo/1.jpg"/></a>

		return $text;
	}
}