<?php
Yii::import('application.modules.reviews.components.*');

class OmDbApi extends ReviewInterface {

	public function getType () {
		return array(
			'movie',
			'tvSeries'
		);
	}

	public function getId () {
		return 'OmDbApi';
	}

	public function getTitle () {
		return Yii::t('reviewsModule.omDbApi', 'OmDbApi - рейтинги фильмов и сериалов с imdb.com');
	}

	public function getDescription () {
		return Yii::t('reviewsModule.omDbApi', 'Рейтинг imdb.com');
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
		$title = (isset($args['t']) ? rawurlencode($args['t']) : '');
		$year = (isset($args['y']) ? (int) $args['y'] : 0);

		try {
			if ( $title && $year ) {
				$contents = $this->makeRequest('http://omdbapi.com/?t=' . $title . '&y=' . $year);

				if ( $contents->Response != 'False' ) {
					return Yii::t('reviewsModule.omDbApi',
						'<a href="http://www.imdb.com/title/{movieId}/" target="_blank">{rating}, голосов: {votes}, metascore: {metascore}</a>',
						array(
							'{movieId}'   => $contents->imdbID,
							'{rating}'    => $contents->imdbRating,
							'{votes}'     => $contents->imdbVotes,
							'{metascore}' => ($contents->Metascore == 'N/A' ? $contents->Metascore : $contents->Metascore . '/100'),
						));
				}
			}
		} catch ( CException $e ) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
		}

		return false;
	}
}