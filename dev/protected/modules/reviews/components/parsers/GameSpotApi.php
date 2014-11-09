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


    public function returnReviewString ( $params ) {
        $ret = '<a href="' . $params['url'] . '" target="_blank">';
        $ret .= ( $params['reviewerScore'] ? 'Reviewer score: ' . $params['reviewerScore'] : '' ) . ($params['word'] ? ' / ' . $params['word'] . '. ' : '');
        $ret .= ( $params['usersScore'] ? 'Average Score: ' . $params['usersScore'] : '' ) . ($params['usersCount'] ? ' / ' . $params['usersCount'] . ' votes' : '');
        $ret .= '</a>';

        return $ret;
    }

    /**
     * @param array $args
     * @return array|bool
     */
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


    /**
     * @param $content
     * @param $url
     * @return array|bool
     */
	private function _parseSearchResults ( $content, $url ) {
		$html = phpQuery::newDocument($content);

		$reviewerScore = trim($html->find('.review-breakdown__inner')->find('.gs-score__cell')->html());
		$usersScore = trim($html->find('.breakdown-avgScore')->find('.gs-score__cell')->html());
		$usersCount = (int) $html->find('.breakdown-avgScore')->find('.breakdown-avgScore__meta')->find('a')->html();
		$word = trim($html->find('.score-word')->html());


		if ( $reviewerScore || $usersScore ) {
            return [
                'url' => $url,
                'reviewerScore' => $reviewerScore,
                'usersScore' => $usersScore,
                'word' => $word, //like great, bad, good etc
                'usersCount' => $usersCount,
            ];
		}

		return false;
	}
}