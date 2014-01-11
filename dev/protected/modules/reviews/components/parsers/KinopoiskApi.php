<?php
Yii::import('application.modules.reviews.components.*');

class KinopoiskApi extends ReviewInterface {

	public function getType () {
		return array(
			'movie',
			'tvSeries'
		);
	}

	public function getId () {
		return 'KinopoiskApi';
	}

	public function getTitle () {
		return Yii::t('reviewsModule.kinopoiskApi', 'KinopoiskApi - рейтинги фильмов и сериалов с www.kinopoisk.ru');
	}

	public function getDescription () {
		return Yii::t('reviewsModule.kinopoiskApi', 'Рейтинг kinopoisk.ru');
	}

	public function getNeededFields () {
		return array(
			't' => Yii::t('reviewsModule.omDbApi', 'Название фильма или сериала'),
			'y' => Yii::t('reviewsModule.omDbApi', 'Год выхода'),
		);
	}

	public function getAdditionalFields () {

	}

	protected function getApiData ( $args ) {
		$title = $args['t'];
		$year = $args['y'];

		try {
			$contents = $this->makeRequest('http://www.kinopoisk.ru/s/type/film/find/' . rawurlencode($title) . '/m_act[year]/' . $year . '/',
				array(
					'useragent' => $this->_generateUserAgent(),
					'headers'   => $this->_generateHeaders(),
					'referer'   => 'http://www.kinopoisk.ru/',
				),
				false);

			return $this->_parseSearchResults($contents);

		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
		}

		return false;
	}

	private function _generateUserAgent () {
		return 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0';
	}

	private function _generateHeaders () {
		return array(
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
			'Referer: http://www.kinopoisk.ru/',
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
			'DNT: 1',
			'Accept-Encoding: gzip, deflate',
			'Connection: keep-alive'
		);
	}

	private function _parseSearchResults ( $content ) {
		$html = phpQuery::newDocument($content);

		$div = $html->find('div.element.most_wanted');

		if ( $div ) {
			$href = $div->find('p.name')->find('a')->attr('href');
			preg_match('/film\/([0-9]+)\//', $href, $matches);

			if ( !empty($matches[1]) ) {
				$movieId = (int) $matches[1];

				try {
					$xml = $this->makeRequest('http://rating.kinopoisk.ru/' . $movieId . '.xml',
						array(
							'useragent' => $this->_generateUserAgent(),
						),
						false);

					$data = simplexml_load_string($xml);

					return '<a href="http://www.kinopoisk.ru' . $href . '" target="_blank">' . $data->kp_rating . '</a>';

				} catch ( CException $e ) {
					Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
				}
			}
		}
		return false;
	}
}