<?php

class RatingsBackendController extends YAdminController {
	public $defaultAction = 'create';

	public function actionCreate () {
		$ratings = Yii::app()->config->get('ratingsModule.ratings');

		if ( !$ratings = @unserialize($ratings) ) {
			$ratings = array();
			for ( $i = 0; $i < 18; ++$i ) {
				$ratings[] = 0;
			}
		}

		if ( isset($_POST['Rating']) ) {
			$_ratings = (array) $_POST['Rating'];

			$ratings = array();
			for ( $i = 0; $i < 18; ++$i ) {
				$rating = str_replace(',','.',(isset($_ratings[$i]) ? (float) $_ratings[$i] : 0));
				$ratings[] = $rating;
			}
			$ratings[0] = ( $ratings[0] == 0 ? 1 : $ratings[0] );

			Yii::app()->config->set('ratingsModule.ratings', serialize($ratings));

			Yii::app()->getUser()->setFlash('flashMessage',
				Yii::t('ratingsModule.common',
					'Коэффициенты рейтингов успешно сохранены'));
		}

		$this->render('create',
			array(
			     'ratings' => $ratings
			));
	}
}