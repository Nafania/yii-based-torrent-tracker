<?php
/**
 * @var $dataProvider CActiveDataProvider
 */
?>

	<h1><?php echo Yii::t('pmsModule.common', 'Личные сообщения'); ?></h1>
<?php

$this->widget('bootstrap.widgets.TbGridView',
	array(
	     'id'             => 'pms-grid',
	     'dataProvider'   => $dataProvider,
	     //'itemView'           => '_view',
	     'template'       => "{items}\n{pager}",
	     'enableHistory'  => true,
	     'type'           => 'striped',
	     'selectableRows' => 2,
	     'filter'         => $model,
	     'columns'        => array(
		     array(
			     //'header' => '',
			     'name'        => 'readed',
			     'filter'      => array(
				     0 => Yii::t('common', 'Нет'),
				     1 => Yii::t('common', 'Да')
			     ),
			     'type'        => 'html',
			     'value'       => function ( $data ) {
				     $icon = ($data->readed == PrivateMessage::READED ? 'open' : 'close');
				     $text = Yii::t('pmsModule.common',
					     ($data->readed == PrivateMessage::READED ? 'Все прочитано' : 'Есть непрочитанные сообщения'));
				     echo '<i class="icon-eye-' . $icon . '" title="' . $text . '" data-toggle="tooltip"  data-placement="top"></i>';
			     },
			     'htmlOptions' => array(
				     'style' => 'width:50px'
			     ),
		     ),
		     array(
			     'name'   => 'senderUid',
			     'value'  => function ( $data ) {
				     if ( $data->sender ) {
					     $str = CHtml::image($data->sender->profile->getImageUrl(30, 30),
						     $data->sender->getName(),
						     array(
						          'class' => 'img-polaroid',
						          'style' => 'width:30px;height:30px;',
						     ));
					     $str .= '&nbsp;&nbsp;' . CHtml::link($data->sender->getName(), $data->sender->getUrl());
					     return $str;
				     }
				     else {
					     $img = '/images/no_photo.png';
					     $alt = '';
					     $str = CHtml::image($img,
						     $alt,
						     array(
						          'class' => 'img-polaroid',
						          'style' => 'width:30px;height:30px;',
						     ));
					     $str .= '&nbsp;&nbsp;<i>' . Yii::t('userModule.common', 'Аккаунт удален') . '</i>';
					     return $str;
				     }
			     },
			     'type'   => 'html',
			     'filter' => PrivateMessage::getAllSenders(),
		     ),
		     array(
			     'name'   => 'receiverUid',
			     'value'  => function ( $data ) {
				     if ( $data->receiver ) {
					     $str = CHtml::image($data->receiver->profile->getImageUrl(30, 30),
						     $data->receiver->getName(),
						     array(
						          'class'  => 'img-polaroid',
						          'width'  => '30',
						          'height' => '30'
						     ));
					     $str .= '&nbsp;&nbsp;' . CHtml::link($data->receiver->getName(), $data->receiver->getUrl());
				     }
				     else {
					     $img = '/images/no_photo.png';
					     $alt = '';
					     $str = CHtml::image($img,
						     $alt,
						     array(
						          'class' => 'img-polaroid',
						          'style' => 'width:30px;height:30px;',
						     ));
					     $str .= '&nbsp;&nbsp;<i>' . Yii::t('userModule.common', 'Аккаунт удален') . '</i>';
					     return $str;
				     }
				     return $str;
			     },
			     'type'   => 'html',
			     'filter' => PrivateMessage::getAllReceivers(),
		     ),
		     array(
			     'name'  => 'subject',
			     'type'  => 'html',
			     'value' => function ( $data ) {
				     return CHtml::link($data->getTitle(), $data->getUrl());
			     }
		     ),
		     array(
			     'filter' => false,
			     'header' => Yii::t('pmsModule.common', 'Время последнего сообщения'),
			     'name'   => 'ctime',
			     'type'   => 'html',
			     'value'  => function ( $data ) {
				     return '<abbr title="' . $data->getCtime('d.m.Y H:i:s') . '">' . TimeHelper::timeAgoInWords($data->getCtime()) . '</abbr>';
			     }
		     ),
	     ),
	));


if ( Yii::app()->user->checkAccess('pms.default.create') ) {
	?>
	<div class="form-actions">
		<div class="pull-left">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'link',
			     'type'       => 'primary',
			     'label'      => Yii::t('pmsModule.common', 'Создать сообщение'),
			     'url'        => Yii::app()->createUrl('/pms/default/create'),

			));
		?>
		</div>

		<div class="pull-right">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType'  => 'ajaxLink',
			     'type'        => 'danger',
			     'label'       => Yii::t('pmsModule.common', 'Удалить выбранное'),
			     'url'         => Yii::app()->createUrl('/pms/default/delete'),
			     'ajaxOptions' => array(
				     'dataType' => 'json',
				     'type'     => 'post',
				     'data'     => 'js:{id: $("#pms-grid").yiiGridView("getSelection")}',
				     'success'  => 'js:function(data){
				        $("#pms-grid").yiiGridView("update");
				     		                                    $(".top-right").notify({
		                                        message: { html: data.message },
		                                        fadeOut: {
		                                            enabled: true,
		                                            delay: 9000
		                                        },
		                                        type: "success"
		                                    }).show();}'
			     ),

			));
		?>
		</div>
</div>
<?php } ?>