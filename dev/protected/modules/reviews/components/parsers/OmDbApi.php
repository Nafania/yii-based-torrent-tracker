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

    /**
     * @return array
     */
    public function getReturnParams()
    {
        return [
            'imdbID' => Yii::t('reviewsModule.omDbApi', 'imdbID'),
            'imdbRating' => Yii::t('reviewsModule.omDbApi', 'imdbRating'),
            'imdbVotes' => Yii::t('reviewsModule.omDbApi', 'imdbVotes'),
            'Metascore' => Yii::t('reviewsModule.omDbApi', 'Metascore'),
        ];
    }

    /**
     * @param array $params
     * @return string
     */
    public function returnReviewString ( $params ) {
        return Yii::t('reviewsModule.omDbApi',
            '<a href="http://www.imdb.com/title/{movieId}/" target="_blank">{rating}, голосов: {votes}, metascore: {metascore}</a>',
            [
                '{movieId}' => $params['imdbID'],
                '{rating}' => Yii::app()->getNumberFormatter()->formatDecimal($params['imdbRating']),
                '{votes}' => $params['imdbVotes'],
                '{metascore}' => ($params['Metascore'] == 'N/A' ? $params['Metascore'] : $params['Metascore'] . '/100'),
            ]);
    }

    /**
     * @param array $args
     * @return array|bool
     */
    protected function getApiData($args)
    {
        $title = (isset($args['t']) ? rawurlencode(str_replace('"', '', $args['t'])) : '');
        $year = (isset($args['y']) ? (int)$args['y'] : 0);
        $type = (isset($args['mt']) ? $args['mt'] : '');

        if ( !$title ) {
            return false;
        }

        $origYear = $year;

        try {
            if ($type === 'S') {
                $url = 'http://omdbapi.com/?s=' . $title;

                return $this->doRequest($url, $type);
            } else {
                $url = 'http://omdbapi.com/?t=%s&y=%d';

                //we do 3 iterations for movies only, for current year, if nothing found for prev year and if again nothing found then for next year
                for ($i = 0; $i < 3; ++$i) {
                    $searchUrl = sprintf($url, $title, $year);

                    $result = $this->doRequest($searchUrl, $type);

                    if ($result === null) {
                        if ($i === 0) {
                            $year = $origYear - 1;
                        } else {
                            $year = $origYear + 1;
                        }
                    } else {
                        return $result;
                    }
                }
            }

            return null;

        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

            return false;
        }
    }

    /**
     * @param string $url
     * @param string $type
     * @return array|bool|null
     */
    protected function doRequest($url, $type)
    {
        try {
            $contents = $this->makeRequest($url);

            return $this->parseResponse($type, $contents);

        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_INFO);
            return false;
        }
    }

    /**
     * @param $type
     * @param $contents
     * @return array|null|false
     * @throws CException
     */
    protected function parseResponse($type, $contents)
    {
        if ($contents->Response !== 'False') {
            switch ($type) {
                //series
                case 'S':
                    foreach ($contents->Search AS $movie) {
                        if ($movie->Type === 'series') {
                            try {
                                $data = $this->makeRequest('http://omdbapi.com/?i=' . $movie->imdbID);
                                if ($data->Response !== 'False' && $contents->imdbID !== null) {
                                    return [
                                        'imdbID' => $contents->imdbID,
                                        'imdbRating' => (float)$contents->imdbRating,
                                        'imdbVotes' => $contents->imdbVotes,
                                        'Metascore' => $contents->Metascore,
                                    ];
                                }
                            }
                            catch (CException $e) {
                                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                                return false;
                            }
                        }
                    }
                    break;

                //movie
                case 'M':
                    if ($contents->imdbID !== null) {
                        return [
                            'imdbID' => $contents->imdbID,
                            'imdbRating' => (float)$contents->imdbRating,
                            'imdbVotes' => $contents->imdbVotes,
                            'Metascore' => $contents->Metascore,
                        ];
                    }
                    break;
            }
        }

        return null;
    }
}