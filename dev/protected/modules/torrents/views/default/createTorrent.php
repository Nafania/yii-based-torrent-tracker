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

<h1><?php echo $torrentGroup->getTitle() ?></h1>
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>


<?php
echo $form->fileFieldRow($torrent, 'info_hash');

$this->renderPartial('_attributes',
	array(
	     'model' => $torrent,
	     'attributes' => $attributes
	));

echo CHtml::label('Tags', 'tags');
echo CHtml::textField('tags', $torrent->tags->toString());
?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType' => 'submit',
			     'type'       => 'primary',
			     'label'      => 'Upload',
			)); ?>
	</div>

<?php $this->endWidget(); ?>
