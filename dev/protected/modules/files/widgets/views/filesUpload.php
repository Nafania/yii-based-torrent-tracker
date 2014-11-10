<?php
/**
 * @var EActiveRecord $model
 * @var string        $modelName
 */
?>

<?php
$this->widget('application.modules.files.extensions.EFineUploader.EFineUploader',
	array(
		'id'     => 'FineUploader',
		'config' => array(
			'autoUpload' => true,
			'multiple'   => true,
			'request'    => array(
				'endpoint'  => Yii::app()->createUrl('/files/default/upload',
						array(
							'modelName' => $modelName,
							'modelId'   => $model->getId()
						)),
				// OR $this->createUrl('files/upload'),
				'params'    => array(Yii::app()->getRequest()->csrfTokenName => Yii::app()->getRequest()->csrfToken),
				'inputName' => 'file',
				'uuidName'  => 'id',
			),
			'deleteFile' => array(
				'enabled'      => true,
				'forceConfirm' => true,
				'method'       => 'POST',
				'endpoint'     => Yii::app()->createUrl('/files/default/delete'),
				'params'       => array(Yii::app()->getRequest()->csrfTokenName => Yii::app()->getRequest()->csrfToken),
			),
			'retry'      => array(
				'enableAuto'                   => true,
				'preventRetryResponseProperty' => true
			),
			'text'       => array(
				'uploadButton' => Yii::t('filesModule.common', 'Добавить документ'),
				'deleteButton' => Yii::t('filesModule.common', 'Delete'),
			),
			'callbacks'  => array(
				'onComplete'     => 'js:function(id, name, responseJSON, maybeXhr){this.setUuid(id, responseJSON.id);}',
				'onSubmitDelete' => 'js:function(id){console.log(id);}',
			),
			/*'chunking'   => array(
				 'enable'   => true,
				 'partSize' => 100
			 ),*/
			//bytes
			//'callbacks'  => array(//'onComplete'=>"js:function(id, name, response){  }",
			//'onError'=>"js:function(id, name, errorReason){ }",
			//),
			'validation' => array(
				'allowedExtensions' => $model->image->types,
				'sizeLimit'         => $model->image->maxSize,
				//maximum file size in bytes
				//'minSizeLimit'      => 2 * 1024 * 1024,
				// minimum file size in bytes
			),
		)
	));

if ( $model->files ) {
	$fileStr = '';
	foreach ( $model->files AS $file ) {
		$fileStr .= '<li class="qq-upload-success">
		                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale src="' . $file->getFileUrl() . '" style="width:100px">
		                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
		                <span class="qq-upload-file-selector qq-upload-file">' . $file->getOriginalTitle() . '</span>
		                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
		                <span class="qq-upload-size-selector qq-upload-size"></span>
		                <a class="qq-upload-delete-selector upload-delete" href="#" data-id="' . $file->getId() . '">' . Yii::t('filesModule.common',
				'Удалить') . '</a>
		                <span class="qq-upload-status-text-selector qq-upload-status-text"></span></li>';
		/*$fileStr .= '<li class="qq-upload-success">
	 <span class="qq-upload-finished"></span>
	 <span class="qq-upload-file">' . $file->getOriginalTitle() . '</span>
	 <span class="qq-upload-size" style="display: inline;">' . '' . '</span>
	 <a class="upload-delete" href="#" style="display: inline;" data-id="' . $file->getId() . '">' . Yii::t('filesModule.common',
				'Delete') . '</a>
	 <span class="qq-upload-status-text"></span>
	 </li>';*/

	}
	Yii::app()->getClientScript()->registerScript(__FILE__ . 'list',
		"$('.qq-upload-list').html(" . CJavaScript::encode($fileStr) . ");
		$(document).on('click', '.upload-delete', function(e) {
			e.preventDefault();
			var elem = $(this);
			if (confirm(" . CJavaScript::encode(Yii::t('filesModule.common',
			'Вы уверены, что хотите удалить этот файл?')) . ")) {
				$.ajax({
					url: " . CJavaScript::encode(Yii::app()->createUrl('/files/default/delete')) . ",
					type: 'post',
					dataType: 'json',
					data: {id: elem.data('id')},
					success: function( data ) {
						elem.parents('li').remove();
					}
				});
			}

		});",
		CClientScript::POS_LOAD);
} ?>
<script type="text/template" id="qq-template">
    <div class="qq-uploader-selector qq-uploader">
        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
            <span><?php echo Yii::t('filesModule.common', 'Перетащите файлы сюда'); ?></span>
        </div>
        <div class="qq-upload-button-selector qq-upload-button btn btn-primary">
            <div><?php echo $buttonTitle; ?></div>
        </div>
        <span class="qq-drop-processing-selector qq-drop-processing">
            <span>Processing dropped files...</span>
            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
        <ul class="qq-upload-list-selector qq-upload-list">
            <li>
                <div class="qq-progress-bar-container-selector">
                    <div class="qq-progress-bar-selector qq-progress-bar"></div>
                </div>
                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
                <span class="qq-upload-file-selector qq-upload-file"></span>
                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                <span class="qq-upload-size-selector qq-upload-size"></span>
                <a class="qq-upload-cancel-selector qq-upload-cancel" href="#"><?php echo Yii::t('filesModule.common', 'Отмена'); ?></a>
                <a class="qq-upload-retry-selector qq-upload-retry" href="#"><?php echo Yii::t('filesModule.common', 'Повторить'); ?></a>
                <a class="qq-upload-delete-selector qq-upload-delete" href="#"><?php echo Yii::t('filesModule.common', 'Удалить'); ?></a>
                <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
            </li>
        </ul>
    </div>
</script>