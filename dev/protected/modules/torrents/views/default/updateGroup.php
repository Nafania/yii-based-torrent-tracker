<?php
/**
 * @var TbActiveForm $form
 * @var TorrentGroup $torrentGroup
 * @var Torrent      $torrent
 */
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'torrent-form',
	     'enableClientValidation' => true,
	     'htmlOptions'            => array(
		     'enctype' => 'multipart/form-data'
	     )
	)); ?>

<?php
	echo $form->fileFieldRow($torrentGroup, 'picture', array('class' => 'span5'));

	$this->renderPartial('_attributes',
		array(
		     'model'      => $torrentGroup,
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
