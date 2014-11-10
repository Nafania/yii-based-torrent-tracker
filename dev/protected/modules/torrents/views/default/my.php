<?php
/**
 * Created by PhpStorm.
 * User: Nafania
 * Date: 10.11.2014
 * Time: 17:03
 */
/**
 * @var DefaultController $this
 * @var CActiveDataProvider $dataProvider
 * @var integer $pageSize
 */

$this->renderPartial('indexGrid', [
        'dataProvider' => $dataProvider,
    ]);