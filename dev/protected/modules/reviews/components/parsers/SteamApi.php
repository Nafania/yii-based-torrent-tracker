<?php
/**
 * Created by PhpStorm.
 * User: ikazdym
 * Date: 27.01.2015
 * Time: 11:01
 */

Yii::import('application.modules.reviews.components.*');

class SteamApi extends ReviewInterface {
    public function getType () {
   		return array(
   			'games',
   		);
   	}

   	public function getId () {
   		return 'SteamApi';
   	}

   	public function getTitle () {
   		return Yii::t('reviewsModule.steamApi', 'SteamApi - магазин игр');
   	}

   	public function getDescription () {
   		return Yii::t('reviewsModule.steamApi', 'Рейтинг steam');
   	}

   	public function getNeededFields () {
   		return array(
   			't' => Yii::t('reviewsModule.steamApi', 'Оригинальное название игры'),
   		);
   	}

   	public function getAdditionalFields () {

   	}

       /**
        * @return array
        */
       public function getReturnParams()
       {
           return [
               'url' => Yii::t('reviewsModule.steamApi', 'Url игры'),
               'status' => Yii::t('reviewsModule.steamApi', 'status'),
               'percent' => Yii::t('reviewsModule.steamApi', 'percent'),
               'total_users' => Yii::t('reviewsModule.steamApi', 'totalUsers'),
           ];
       }


       public function returnReviewString ( $params ) {
           $ret = '<a href="' . $params['url'] . '" target="_blank">';
           $ret .= Yii::t('reviewsModule.steamApi', $params['status']) . ' (' . Yii::t('reviewsModule.steamApi', '{percent}% из {total_users} обзоров этой игры положительны', ['{percent}' => $params['percent'], '{total_users}' => $params['total_users']]) . ')';
           $ret .= '</a>';

           return $ret;
       }

       /**
        * @param array $args
        * @return array|bool
        */
   	protected function getApiData ( $args ) {
   		$title = (isset($args['t']) ? $args['t'] : '');

   		try {
   			if ( $title ) {
   				$contents = $this->makeRequest('http://store.steampowered.com/search/?term=' . rawurlencode($title), [], false);

                return $this->_getReviewScore($contents, $title);
   			}
   		} catch ( CException $e ) {
   			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
   		}
   	}


    private function _getReviewScore($content, $title)
    {
        $html = phpQuery::newDocument($content);

        $links = $html->find('.search_result_row.ds_collapse_flag');

        foreach ( $links AS $link ) {
            $link = pq($link);

            $_title = $link->find('.title')->html();

            if (levenshtein($title, $_title) > 5) {
                continue;
            }

            $url = $link->attr('href');

            return array_merge($this->parseReview($link->find('.search_review_summary')->attr('data-store-tooltip')), ['url' => $url]);
        }

        return null;
    }

    protected function parseReview ($str) {
        //Positive<br>100% of the 9 user reviews for this game are positive.
        //Very Positive<br>83% of the 318 user reviews for this game are positive.
        //Mostly Negative<br>34% of the 318 user reviews for this game are positive.
        //Negative<br>0% of the 1 user reviews for this game are positive.
        //Mixed<br>57% of the 26 user reviews for this game are positive.
        preg_match('/([a-z ]+)<br>([0-9]+)% of the ([0-9,]+)/si', $str, $matches);

        return [
            'status' => $matches[1],
            'percent' => $matches[2],
            'total_users' => (int) str_replace([',', '.'], '', $matches[3]),
        ];
    }
}