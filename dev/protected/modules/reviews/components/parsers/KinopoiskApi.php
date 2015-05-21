<?php
Yii::import('application.modules.reviews.components.*');

class KinopoiskApi extends ReviewInterface
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
        return 'KinopoiskApi';
    }

    public function getTitle()
    {
        return Yii::t('reviewsModule.kinopoiskApi', 'KinopoiskApi - рейтинги фильмов и сериалов с www.kinopoisk.ru');
    }

    public function getDescription()
    {
        return Yii::t('reviewsModule.kinopoiskApi', 'Рейтинг kinopoisk.ru');
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
            'imdbID' => Yii::t('reviewsModule.kinopoiskApi', 'movieId'),
            'imdbRating' => Yii::t('reviewsModule.kinopoiskApi', 'rating'),
            'imdbVotes' => Yii::t('reviewsModule.kinopoiskApi', 'votes'),
        ];
    }

    public function returnReviewString($params)
    {
        return Yii::t('reviewsModule.kinopoiskApi', '<a href="http://www.kinopoisk.ru/film/{movieId}/" target="_blank">{rating}, голосов: {votes}</a>',
            [
                '{movieId}' => $params['movieId'],
                '{rating}' => $params['rating'],
                '{votes}' => Yii::app()->getNumberFormatter()->formatDecimal($params['votes'])
            ]
        );
    }

    /**
     * @param $args
     * @return array|bool|null
     */
    protected function getApiData($args)
    {
        $title = $args['t'];
        $year = (int) $args['y'];
        $type = $args['mt'];

        //        http://www.kinopoisk.ru/index.php?level=7&from=forma&result=adv&m_act%5Bfrom%5D=forma&m_act%5Bwhat%5D=content&m_act%5Bfind%5D=%D1%F2%F0%E5%EB%E0&m_act%5Bfrom_year%5D=2012&m_act%5Bto_year%5D=2013&m_act%5Bcontent_find%5D=serial

        $title = str_replace('/', '', $title);
        $origYear = $year;

        if ($type == 'S') {
            $url = 'http://www.kinopoisk.ru/index.php?level=7&from=forma&result=adv&m_act%5Bfrom%5D=forma&m_act%5Bwhat%5D=content&m_act%5Bfind%5D=' . rawurlencode($title) . '&m_act%5Bcontent_find%5D=serial';
        } else {
            $url = 'http://www.kinopoisk.ru/s/type/film/find/%s/m_act[year]/%d/';
        }

        $proxies = Yii::app()->config->get('reviewsModule.proxies');

        shuffle($proxies);

        foreach ($proxies AS $proxy) {
            //we do 3 iterations for movies only, for current year, if nothing found for prev year and if again nothing found then for next year
            if ($type != 'S') {
                for ($i = 0; $i < 3; ++$i) {
                    $searchUrl = sprintf($url, rawurlencode($title), $year);

                    $result = $this->doRequest($searchUrl, $title, $proxy);

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
            } else {
                return $this->doRequest($url, $title, $proxy);
            }
            return null;
        }

        Yii::log('All kinopoisk proxies fail or kinopoisk not responding', CLogger::LEVEL_ERROR);
        return false;
    }

    /**
     * @param string $url
     * @param string $title
     * @param string $proxy
     * @return array|bool
     */
    protected function doRequest($url, $title, $proxy)
    {
        try {
            $contents = $this->makeRequest(
                $url,
                [
                    'useragent' => $this->_generateUserAgent(),
                    'headers' => $this->_generateHeaders(),
                    'referer' => 'http://www.kinopoisk.ru/',
                    'timeout' => 3,
                    'proxy' => $proxy,
                ],
                false
            );
            return $this->_parseSearchResults($contents, $title);

        } catch (CException $e) {
            Yii::log($e->getMessage() . ' with proxy ' . $proxy, CLogger::LEVEL_INFO);
            return false;
        }
    }

    private function _generateUserAgent()
    {
        return 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0';
    }

    private function _generateHeaders()
    {
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

    /**
     * @param string $content
     * @param string $title
     * @return array|bool
     */
    private function _parseSearchResults($content, $title)
    {
        $html = phpQuery::newDocumentHTML($content, $charset = 'utf-8');

        $div = $html->find('div.element.most_wanted');
        if ($div->length) {
            $link = $div->find('p.name')->find('a');
            $href = $link->attr('href');
            $foundedTitle = $link->html();
            preg_match('/film\/([0-9]+)\//', $href, $matches);
        } else {
            preg_match('/id_film = ([0-9]+);/', $content, $matches);
            $foundedTitle = $html->find('h1.moviename-big')->html();
        }

        if (empty($matches[1]) || levenshtein($title, $foundedTitle) > 5) {
            return null;
        }
        else {
            $movieId = (int)$matches[1];
            return $this->parseXml($movieId);
        }
    }

    /**
     * @param $movieId
     * @return array|null
     */
    protected function parseXml($movieId)
    {
        try {
            $xml = $this->makeRequest('http://rating.kinopoisk.ru/' . $movieId . '.xml',
                [
                    'useragent' => $this->_generateUserAgent(),
                ],
                false);

            $data = simplexml_load_string($xml);

            return [
                'movieId' => $movieId,
                'rating' => (float) $data->kp_rating,
                'votes' => (int) $data->kp_rating['num_vote'],
            ];


        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
        }
    }
}