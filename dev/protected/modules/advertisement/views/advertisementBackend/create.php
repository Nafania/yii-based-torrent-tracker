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
            <h3><?php echo Yii::t('advertisementModule.common', 'Создание рекламного блока') ?></h3>
            <fieldset class="module">
                    <div class="row <?php if ( $model->getError('title') ) {
	                    echo 'errors';
                    } ?>">
                        <div class="column span-4"><?php echo $form->labelEx($model, 'systemName'); ?></div>
                        <div class="column span-16 span-flexible">
                            <?php echo $form->textField($model, 'systemName', array('disabled' => ( $model->getIsNewRecord() ? '' : 'disabled'))); ?>
	                        <ul class="errorlist">
                                <li><?php echo $form->error($model, 'systemName'); ?></li>
                            </ul>
                        </div>
                    </div>
	            <div class="row <?php if ( $model->getError('description') ) {
              echo 'errors';
             } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'description'); ?></div>
                 <div class="column span-16 span-flexible">
	                 <?php echo $form->textArea($model, 'description'); ?>
                  <ul class="errorlist">
                         <li><?php echo $form->error($model, 'description'); ?></li>
                     </ul>
                 </div>
             </div>

	            <div class="row <?php if ( $model->getError('code') ) {
              echo 'errors';
             } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'code'); ?></div>
                 <div class="column span-16 span-flexible">
	                 <?php echo $form->textArea($model, 'code'); ?>
                  <ul class="errorlist">
                         <li><?php echo $form->error($model, 'code'); ?></li>
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

