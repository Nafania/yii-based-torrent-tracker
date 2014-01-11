<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $model AuthItemForm */
/* @var $item CAuthItem */
/* @var $form TbActiveForm */
?>



<?php $form = $this->beginWidget('CActiveForm',
	array( //'type'=>'horizontal',
	)); ?>

	<div class="container-flexible">
       <div class="column span-24">
	       <h3>
	      	<?php echo CHtml::encode($item->description); ?>
		       <small><?php echo $this->getTypeText(); ?></small>
	      </h3>
	       <fieldset class="module">
				<?php echo $form->hiddenField($model, 'type'); ?>
		       <div class="row">
		       <div class="column span-4"><?php echo $form->label($model, 'name'); ?></div>
		       <div class="column span-16 span-flexible"><?php echo $form->textField($model,
				       'name',
				       array(
					       'disabled' => true,
					       'title'    => Yii::t('AuthModule.main', 'System name cannot be changed after creation.'),
				       )); ?>          </div>
	            </div>

		       <div class="row">
		            <div class="column span-4"><?php echo $form->label($model, 'description'); ?></div>
		       		<div class="column span-16 span-flexible"><?php echo $form->textField($model,
					        'description'); ?></div>
				</div>
		       <div class="row">
		            <div class="column span-4"><?php echo $form->label($model, 'bizrule'); ?></div>
					<div class="column span-16 span-flexible"><?php echo $form->textField($model,
							'bizrule'); ?></div>
				</div>

		       <div class="row">
		            <div class="column span-4"><?php echo $form->label($model, 'data'); ?></div>
		            		       		<div class="column span-16 span-flexible"><?php echo $form->textField($model,
							                    'data'); ?></div>
				</div>

		       </fieldset>
</div></div>
	<div class="module footer">
     <ul class="submit-row"><li class="submit-button-container">
		<?php echo CHtml::submitButton(Yii::t('AuthModule.main', 'Save')) ?></li>
	     <li class="submit-button-container">
		<?php echo CHtml::button(Yii::t('AuthModule.main', 'Cancel'),
			array(
				'view',
				'name' => $item->name
			)) ?></li>
	</div>
<?php $this->endWidget(); ?>