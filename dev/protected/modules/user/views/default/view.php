<?php
/**
 * @var $model User
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerCssFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.css');
$cs->registerScriptFile(Yii::app()->getBaseUrl() . '/js/fancyapps-fancyBox/source/jquery.fancybox.js');
Yii::app()->getComponent('bootstrap')->registerPackage('font-awesome');
Yii::app()->getComponent('bootstrap')->registerPackage('social-buttons');
?>

	<h1><?php echo Yii::t('userModule.common',
			'Просмотр профиля "{name}"',
			array('{name}' => $model->getName())) ?><span class="userRating <?php echo $model->getRatingClass() ?>"><?php echo $model->getRating() ?></span></h1>

	<div class="row-fluid">

     <div class="span2">
	     <?php
	     $img = CHtml::image($model->profile->getImageUrl(200, 200),
		     $model->getName(),
		     array(
			     'class' => 'fancybox img-polaroid',
			     'rel'   => 'group',
			     'style' => 'width:200px;height:200px;'
		     ));
	     echo CHtml::link($img,
		     $model->profile->getImageUrl());
	     ?>
     </div>

     <div class="span10">
	     <dl class="dl-horizontal">
		     <dt><?php echo Yii::t('userModule.common', 'Дата регистрации') ?></dt>
		     <dd><?php echo $model->getCtime('d.m.Y'); ?></dd>

		     <dt><?php echo Yii::t('userModule.common', 'Оставил') ?></dt>
		     <dd><?php echo Yii::t('userModule.common',
				     '{n} комментарий|{n} комментария|{n} комментариев',
				     $model->commentsCount) ?></dd>

		     <dt><?php echo Yii::t('userModule.common', 'Ведет') ?></dt>
		     <dd><?php echo Yii::t('userModule.common',
				     '{n} блог|{n} блога|{n} блогов',
				     $model->blogsCount) ?></dd>

		     <dt><?php echo Yii::t('userModule.common', 'Написал') ?></dt>
		     <dd><?php echo Yii::t('userModule.common',
				     '{n} запись в блогах|{n} записи в блогах|{n} записей в блогах',
				     $model->blogPostsCount) ?></dd>

		     <dt><?php echo Yii::t('userModule.common', 'Создал') ?></dt>
		     <dd><?php echo Yii::t('userModule.common',
				     '{n} группу|{n} группы|{n} групп',
				     $model->groupsOwnerCount) ?></dd>

		     <dt><?php echo Yii::t('userModule.common', 'Состоит в') ?></dt>
		     <dd><?php echo Yii::t('userModule.common',
				     '{n} группе|{n} группах|{n} группах',
				     $model->groupsMemberCount) ?></dd>

		     <?php
		     foreach ( $model->socialAccounts AS $account ) {
			     //echo '<dt class="auth-service ' . $account->service . '">';
			     $html = '<dt>' . CHtml::link('<i class="fa fa-' . $account->service . '"></i>',
					     $account->url,
					     array(
						     'target' => '_blank',
						     'class'  => 'social-button btn btn-mini btn-' . $account->service
					     )) . '</dt>';
			     $html .= '<dd>' . CHtml::link($account->name,
					     $account->url,
					     array('target' => '_blank')) . '</dd>';
			     echo $html;
			     //echo CHtml::link($html, $account->url, array('target' => '_blank'));
			     //echo '</li>';
		     }
		     ?>
	     </dl>

	     <!--<ul class="auth-services clear">-->

	  		  <!--</ul>-->
	</div>


 </div>

<?php
if ( Yii::app()->user->checkAccess('pms.default.create') ) {
	?>
	<div class="form-actions">
	<div class="pull-left">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
				'buttonType' => 'link',
				'type'       => 'primary',
				'label'      => Yii::t('pmsModule.common', 'Отправить личное сообщение'),
				'url'        => Yii::app()->createUrl('/pms/default/create', array('uId' => $model->getId())),

			));
		?>
		</div>
<?php } ?>