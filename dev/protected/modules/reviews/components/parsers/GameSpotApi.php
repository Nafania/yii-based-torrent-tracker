<?php
Yii::import('application.modules.reviews.components.*');

class GameSpotApi extends ReviewInterface {

	public function getType () {
		return array(
			'games',
		);
	}

	public function getId () {
		return 'GameSpotApi';
	}

	public function getTitle () {
		return Yii::t('reviewsModule.gameSpotApi', 'GameSpot - рейтинги игр');
	}

	public function getDescription () {
		return Yii::t('reviewsModule.gameSpotApi', 'Рейтинг gamespot');
	}

	public function getNeededFields () {
		return array(
			't' => Yii::t('reviewsModule.gameSpotApi', 'Оригинальное название игры'),
		);
	}

	public function getAdditionalFields () {

	}

	protected function getApiData ( $args ) {
		$title = (isset($args['t']) ? rawurlencode($args['t']) : '');

		try {
			if ( $title ) {
				$contents = $this->makeRequest('http://www.gamespot.com/jsonsearch/?indices[0]=game&page=1&q=' . $title);

				if ( !empty($contents->results[0]->url) ) {
					$url = 'http://www.gamespot.com' . $contents->results[0]->url . '/';
					return $this->_parseSearchResults($this->makeRequest($url, array(), false), $url);
				}
				return false;
			}
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
		}

		return false;
	}


	private function _parseSearchResults ( $content, $url ) {
		$html = phpQuery::newDocument($content);

		$reviewerScore = trim($html->find('.review-breakdown__inner')->find('.gs-score__cell')->html());
		$usersScore = trim($html->find('.breakdown-avgScore')->find('.gs-score__cell')->html());
		$usersCount = (int) $html->find('.breakdown-avgScore')->find('.breakdown-avgScore__meta')->find('a')->html();
		$word = trim($html->find('.score-word')->html());


		if ( $reviewerScore || $usersScore ) {
			$ret = '<a href="' . $url . '" target="_blank">';
			$ret .= ( $reviewerScore ? 'Reviewer score: ' . $reviewerScore : '' ) . ($word ? ' / ' . $word . '. ' : '');
			$ret .= ( $usersScore ? 'Average Score: ' . $usersScore : '' ) . ($usersCount ? ' / ' . $usersCount . ' votes' : '');
			$ret .= '</a>';

			return $ret;
		}

		return false;
	}
}