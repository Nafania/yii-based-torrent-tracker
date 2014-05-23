<?php

class DefaultController extends components\Controller {

	/**
	 * @return array action filters
	 */
	public function filters () {
	}

	public function actionSitemap () {
		// It is a list of AppStructureComponent
		$applicationStructure = Yii::app()->sitemap->getApplicationStructure();

		// Now lets watch what we got - it is always better to see it once:
		function displayStructure ( $structure ) {
			foreach ( $structure as $structureNode ) {
				echo '<ul>';
				echo "<li>Name: {$structureNode->getName()}</li>";
				echo "<li>Description: {$structureNode->getDescription()}</li>";
				echo "<li>Route: {$structureNode->getRoute()}</li>";
				if ( method_exists($structureNode->getInstance(), 'getPagesForSitemap') ) {
					echo '<li>Pages: ';
					Yii::app()->getController()->widget('zii.widgets.grid.CGridView',
						array('dataProvider' => $structureNode->getInstance()->getPagesForSitemap()));
					echo '</li>';
				}
				/*else {
					echo '<li>Children: ';
					displayStructure($structureNode->getChildren());
					echo '</li>';
				}*/
				echo '<li>Children: ';
				displayStructure($structureNode->getChildren());
				echo '</li>';
				echo '</ul>';
			}
		}

		//CVarDumper::dump($applicationStructure, 5,true);
		displayStructure($applicationStructure);
	}

	public function actionSitemapXml () {
		$cacheFile = get_class($this) . 'sitemap.tmp';
		$cacheDirectories = array(
			@realpath(sys_get_temp_dir()),
			DIRECTORY_SEPARATOR . 'tmp',
			DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'tmp',
			@realpath(dirname(__FILE__)),
		);
		$cacheTime = 3600;

		$cacheDir = '.';
		foreach ( $cacheDirectories as $dir ) {
			if ( @is_writeable($dir) ) {
				$cacheDir = $dir;
				break;
			}
		}
		$cacheFile = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $cacheFile;

		if ( @file_exists($cacheFile) ) {
			$tmp = @file_get_contents($cacheFile);
			$mTime = @filemtime($cacheFile);
			if ( $mTime !== false && (time() - $mTime) < $cacheTime ) {
				echo $tmp;
				Yii::app()->end();
			}

		}

		$applicationStructure = $this->_getData();

		$file = $this->renderPartial('sitemapXml',
			array(
				'data' => $applicationStructure,
			),
			true);

		file_put_contents($cacheFile, $file);

		echo $file;
	}

	public function actionRss ( $type ) {
		$applicationStructure = Yii::app()->sitemap->getApplicationStructure();

		foreach ( $applicationStructure AS $structureNode ) {
			if ( $structureNode->getInstance()->getId() == $type ) {

				if ( method_exists($structureNode->getInstance(), 'getRss') ) {

					$models = $structureNode->getInstance()->getRss()->findAll(array(
						'order' => 't.ctime DESC',
						'limit' => 100
					));

					$this->renderPartial('application.modules.' . $type . '.views.default.rss',
						array(
							'models' => $models
						));
					Yii::app()->end();
				}
			}
		}

		throw new CHttpException(404);
	}

	private function _getData () {
		$applicationStructure = Yii::app()->sitemap->getApplicationStructure();

		// Now lets watch what we got - it is always better to see it once:
		function getStructure ( $structure ) {
			static $data = array();

			foreach ( $structure as $structureNode ) {
				if ( method_exists($structureNode->getInstance(), 'getPagesForSitemap') ) {

                    $model = $structureNode->getInstance()->getPagesForSitemap();
                    $db = $model->getDbConnection();
                    $comm = $db->createCommand('SELECT * FROM ' . $model->getTableSchema()->name . ' ORDER BY ctime DESC');
                    $dataReader = $comm->query();

                    foreach ( $dataReader AS $row ) {
                        $model = $model->populateRecord($row, false);
						$url = $model->getUrl();
						$data[] = array(
							'url'        => Yii::app()->createAbsoluteUrl($url[0], array_splice($url, 1)),
							'changefreq' => 'daily',
							'priority'   => '0.8'
						);
					}
				}
				getStructure($structureNode->getChildren());
			}
			return $data;
		}

		$data = getStructure($applicationStructure);

		//TODO: move this to modules
		$data = CMap::mergeArray($data,
			array(
				array(
					'url'        => Yii::app()->createAbsoluteUrl('site/index'),
					'changefreq' => 'always',
					'priority'   => 1,
				),
				array(
					'url'        => Yii::app()->createAbsoluteUrl('/torrents/default/index'),
					'changefreq' => 'always',
					'priority'   => 1,
				),
				array(
					'url'        => Yii::app()->createAbsoluteUrl('/blogs/default/index'),
					'changefreq' => 'always',
					'priority'   => 1,
				),
				array(
					'url'        => Yii::app()->createAbsoluteUrl('/groups/default/index'),
					'changefreq' => 'always',
					'priority'   => 1,
				),
			));
		//CVarDumper::dump($applicationStructure, 5,true);
		return $data;
	}
}
