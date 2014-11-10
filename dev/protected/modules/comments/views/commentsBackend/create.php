<?php
/* @var $form CActiveForm */

$form = $this->beginWidget('CActiveForm',
	array(
	     'id'          => get_class($model) . '-id-form',
	     'htmlOptions' => array(
		     'enctype' => 'multipart/form-data'
	     )
	));
?>

	<div class="container-flexible">
		<div class="">
        <div class="column span-24">
            <h3><?php echo Yii::t('commentsModule.common', 'Создание комментария') ?></h3>
            <fieldset class="module">
	            <div class="row <?php if ( $model->getError('text') ) {
              echo 'errors';
             } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'text'); ?></div>
                 <div class="column span-16 span-flexible">
	                 <?php
	                 $this->widget('application.extensions.imperavi-redactor-widget.ImperaviRedactorWidget', array(
	                     'model' => $model,
	                     'attribute' => 'text',
	                     'options' => array(
		                     'minHeight' => 200
	                     ),
	                 ));?>
                  <ul class="errorlist">
                         <li><?php echo $form->error($model, 'text'); ?></li>
                     </ul>
                 </div>

		            <div class="row <?php if ( $model->getError('status') ) {
	              echo 'errors';
	             } ?>">
	                 <div class="column span-4"><?php echo $form->labelEx($model, 'status'); ?></div>
	                 <div class="column span-16 span-flexible"><?php echo $form->dropDownList($model, 'status', $model->statusLabels()) ?>
	                  <ul class="errorlist">
	                         <li><?php echo $form->error($model, 'status'); ?></li>
	                     </ul>
	                 </div>
             </div>

            </fieldset>

        </div>

        <div class="module footer">
            <ul class="submit-row">
                <?php if ( !$model->isNewRecord ): ?>
		            <li class="left delete-link-container">
                    <?php echo CHtml::link(YiiadminModule::t('Удалить'),
				            $this->createUrl('manageModel/delete',
					            array(
					                 'model_name' => get_class($model),
					                 'pk'         => $model->primaryKey,
					            )),
				            array(
				                 'class'   => 'delete-link',
				                 'confirm' => YiiadminModule::t('Удалить запись ID ') . $model->primaryKey . '?',
				            )); ?>
                </li>
	            <?php endif; ?>
	            <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить'); ?>" class="default"
                           name="_save" />
                </li>
            </ul>
            <br clear="all">
        </div>

    </div>
</div>
<?php
$this->endWidget();

