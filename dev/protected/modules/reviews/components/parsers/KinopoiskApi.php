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

    protected function getApiData($args)
    {
        $title = $args['t'];
        $year = $args['y'];
        $type = $args['mt'];

        //        http://www.kinopoisk.ru/index.php?level=7&from=forma&result=adv&m_act%5Bfrom%5D=forma&m_act%5Bwhat%5D=content&m_act%5Bfind%5D=%D1%F2%F0%E5%EB%E0&m_act%5Bfrom_year%5D=2012&m_act%5Bto_year%5D=2013&m_act%5Bcontent_find%5D=serial

        $title = str_replace('/', '', $title);

        if ($type == 'S') {
            $url = 'http://www.kinopoisk.ru/index.php?level=7&from=forma&result=adv&m_act%5Bfrom%5D=forma&m_act%5Bwhat%5D=content&m_act%5Bfind%5D=' . rawurlencode($title) . '&m_act%5Bcontent_find%5D=serial';
        } else {
            $url = 'http://www.kinopoisk.ru/s/type/film/find/' . rawurlencode($title) . '/m_act[year]/' . $year . '/';
        }

        try {
            $contents = $this->makeRequest($url,
                array(
                    'useragent' => $this->_generateUserAgent(),
                    'headers' => $this->_generateHeaders(),
                    'referer' => 'http://www.kinopoisk.ru/',
                    'proxy' => array(
                        '119.31.123.207:8000',
                        '77.120.99.249:3128',
                        '222.124.198.136:3129'
                    )
                ),
                false);

            return $this->_parseSearchResults($contents);

        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        return false;
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

    private function _parseSearchResults($content)
    {
        $html = phpQuery::newDocument($content);

        $div = $html->find('div.element.most_wanted');
        if ($div->length) {
            $href = $div->find('p.name')->find('a')->attr('href');
            preg_match('/film\/([0-9]+)\//', $href, $matches);

            if (!empty($matches[1])) {
                $movieId = (int)$matches[1];
                return $this->parseXml($movieId);
            }
        } else {
            preg_match('/id_film = ([0-9]+);/', $content, $matches);
            if (!empty($matches[1])) {
                $movieId = (int)$matches[1];
                return $this->parseXml($movieId);
            }
        }
        return false;
    }

    protected function parseXml($movieId)
    {
        try {
            $xml = $this->makeRequest('http://rating.kinopoisk.ru/' . $movieId . '.xml',
                array(
                    'useragent' => $this->_generateUserAgent(),
                ),
                false);

            $data = simplexml_load_string($xml);
            $votes = $data->kp_rating['num_vote'];

            if ($data->kp_rating == 0 || $votes == 0) {
                return false;
            } else {
                return Yii::t('reviewsModule.kinopoiskApi', '<a href="http://www.kinopoisk.ru/film/' . $movieId . '/" target="_blank">{rating}, голосов: {votes}</a>', array('{rating}' => $data->kp_rating, '{votes}' => Yii::app()->getNumberFormatter()->formatDecimal($votes)));
            }

        } catch (CException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }
}