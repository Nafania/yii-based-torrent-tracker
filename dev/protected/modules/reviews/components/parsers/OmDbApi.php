<?php
Yii::import('application.modules.reviews.components.*');

class OmDbApi extends ReviewInterface
{

    public function getType()
    {
        return array(
            'movie',
            'tvSeries'
        );
    }

    public function getId()
    {
        return 'OmDbApi';
    }

    public function getTitle()
    {
        return Yii::t('reviewsModule.omDbApi', 'OmDbApi - рейтинги фильмов и сериалов с imdb.com');
    }

    public function getDescription()
    {
        return Yii::t('reviewsModule.omDbApi', 'Рейтинг imdb.com');
    }

    public function getNeededFields()
    {
        return array(
            't' => Yii::t('reviewsModule.omDbApi', 'Название фильма или сериала'),
            'y' => Yii::t('reviewsModule.omDbApi', 'Год выхода'),
        );
    }

    public function getAdditionalFields()
    {

    }

    protected function getApiData($args)
    {
        $title = (isset($args['t']) ? rawurlencode($args['t']) : '');
        $year = (isset($args['y']) ? (int)$args['y'] : 0);
        $type = (isset($args['mt']) ? $args['mt'] : '');

        if ( !$title ) {
            return false;
        }

        try {
            if ($type == 'S') {
                $url = 'http://omdbapi.com/?s=' . $title;
            } else {
                $url = 'http://omdbapi.com/?t=' . $title . '&y=' . $year;
            }

            $contents = $this->makeRequest($url);

            if ($type == 'M' && $contents->Response != 'False') {
                return Yii::t('ReviewsModule.omDbApi',
                    '<a href="http://www.imdb.com/title/{movieId}/" target="_blank">{rating}, голосов: {votes}, metascore: {metascore}</a>',
                    array(
                        '{movieId}' => $contents->imdbID,
                        '{rating}' => $contents->imdbRating,
                        '{votes}' => $contents->imdbVotes,
                        '{metascore}' => ($contents->Metascore == 'N/A' ? $contents->Metascore : $contents->Metascore . '/100'),
                    ));

            } elseif ($type == 'S' && $contents->Response != 'False') {
                foreach ($contents->Search AS $movie) {
                    if ($movie->Type == 'series') {
                        $data = $this->makeRequest('http://omdbapi.com/?i=' . $movie->imdbID);
                        if ($data->Response != 'False') {
                            return Yii::t('ReviewsModule.omDbApi',
                                '<a href="http://www.imdb.com/title/{movieId}/" target="_blank">{rating}, голосов: {votes}, metascore: {metascore}</a>',
                                array(
                                    '{movieId}' => $data->imdbID,
                                    '{rating}' => $data->imdbRating,
                                    '{votes}' => $data->imdbVotes,
                                    '{metascore}' => ($data->Metascore == 'N/A' ? $data->Metascore : $data->Metascore . '/100'),
                                ));
                        }
                    }
                }
            } else {
                return false;
            }
        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        return false;
    }
}