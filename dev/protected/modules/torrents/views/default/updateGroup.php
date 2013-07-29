<?php
/**
 * @var TbActiveForm $form
 * @var TorrentGroup $torrentGroup
 * @var Torrent      $torrent
 */
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id' => 'torrent-form',
	     'enableClientValidation' => true,
	     'htmlOptions' => array(
		     'enctype' => 'multipart/form-data'
	     )
	)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>


<?php
echo $form->fileFieldRow($torrentGroup, 'picture');

$this->renderPartial('_attributes',
	array(
	     'model' => $torrentGroup,
	     'attributes' => $attributes,
	));
?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => 'Update',
			)); ?>
	</div>

<?php $this->endWidget(); ?>
