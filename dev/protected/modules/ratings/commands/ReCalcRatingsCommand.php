<?php

class ReCalcRatingsCommand extends CConsoleCommand {
	public function run ( $args ) {
		$type = (isset($args[0]) ? $args[0] : '');
		$limit = (isset($args[1]) ? (int) $args[1] : 1000);

		switch ( $type ) {
			case 'BlogPost':
				Yii::import('application.modules.blogs.models.*');
				$modelName = 'modules\blogs\models\BlogPost';
				$tableName = '{{blogPosts}}';
				break;

			case 'Blog':
				Yii::import('application.modules.blogs.models.*');
				$modelName = 'modules\blogs\models\Blog';
				$tableName = '{{blogs}}';
				break;

			case 'Group':
				Yii::import('application.modules.groups.models.*');
				$modelName = 'Group';
				$tableName = '{{groups}}';
				break;

			case 'User':
				Yii::import('application.modules.user.models.*');
				$modelName = 'User';
				$tableName = '{{users}}';
				break;

			case 'TorrentGroup':
				Yii::import('application.modules.torrents.models.*');
				$modelName = 'modules\torrents\models\TorrentGroup';
				$tableName = '{{torrentGroups}}';
				break;

			case 'Comment':
				Yii::import('application.modules.comments.models.*');
				$modelName = 'Comment';
				$tableName = '{{comments}}';
				break;

			default:
				echo 'Invalid argument type ' . $type . '. Must be one of this: Comment, Blog, BlogPost, Group, User, TorrentGroup.' . "\n";
				return 1;
				break;
		}

		$limit = ($limit <= 0 ? 1000 : $limit);

		ini_set('memory_limit', '256M');

		Yii::getLogger()->autoFlush = 0;

		$startTime = microtime(true);

		/**
		 * @var CDbConnection $db
		 */
		$db = $modelName::model()->getDbConnection();
		$comm = $db->createCommand('SELECT COUNT(*) AS count FROM ' . $tableName);
		//$comm->bindValue(':tableName', $tableName);
		$count = ($row = $comm->queryRow()) ? $row['count'] : 0;

		try {
			$j = ceil($count / $limit);

			//echo "calc start at " . $startTime . " and going in " . $j . " steps \n";
			for ( $i = 0; $i < $j; ++$i ) {
				$offset = ($i * $limit);
				//echo SizeHelper::formatSize(memory_get_usage()) . "\tbefore find\n";

				$comm = $db->createCommand('SELECT * FROM ' . $tableName . '  ORDER BY ctime ASC LIMIT :offset, :limit');
				$comm->bindValue(':limit', $limit);
				$comm->bindValue(':offset', $offset);
				$dataReader = $comm->query();

				foreach ( $dataReader AS $data ) {
					/**
					 * @var EActiveRecord $model
					 */
					$model = EActiveRecord::model($modelName)->populateRecord($data, false);
					$model->calculateRating();
				}

				/*$models = $modelName::model()->findAll(array(
				                                            'order'  => 'ctime DESC',
				                                            'limit'  => $limit,
				                                            'offset' => $offset
				                                       ));

				echo SizeHelper::formatSize(memory_get_usage()) . "\tafter find\n";

				foreach ( $models AS $_model ) {
					$_model->calculateRating();
					unset($_model);
				}
				unset($models);*/
				gc_collect_cycles();

				//echo SizeHelper::formatSize(memory_get_usage()) . "\tafter destruct\n";
				Yii::getLogger()->flush(true);

				//echo "step " . ( $i + 1 ) . ' (' . $offset . " from " . $count . ") done with " . (microtime(true) - $startTime) . "ms\n";
			}
		} catch ( CException $e ) {
			echo $e->getMessage();
			return 1;
		}

		$endTime = microtime(true);
		//echo "calc end at " . $endTime . ". Time spent: " . ($endTime - $startTime) . " \n";

		return 0;
	}
}