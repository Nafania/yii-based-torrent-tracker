<?php
/**
 * @var CActiveForm $form
 */
$form = $this->beginWidget('CActiveForm',
	array(
	     'id'                    => 'news-form',
	     'enableAjaxValidation'  => true,
	     'enableClientValidation'=> true,
	     'htmlOptions'           => array(
		     'enctype' => 'multipart/form-data'
	     )

	)); ?>
<div class="container-flexible">
	<?php if ( $model->hasErrors() ): ?>
    <p class="errornote"><?php echo YiiadminModule::t('Пожалуйста, исправьте ошибки, указанные ниже.'); ?></p>
	<?php endif; ?>

        <div class="column span-16">
            <h3><?php echo Yii::t(get_class($model), get_class($model)) ?></h3>
            <fieldset class="module">


                <div class="row <?php if ( $model->getError('title') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'title'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textField($model, 'title'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'title'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="row <?php if ( $model->getError('text') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'text'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textArea($model, 'text'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'text'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="row <?php if ( $model->getError('pinned') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'pinned'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->checkBox($model, 'pinned'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'pinned'); ?></li>
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
					Yii::app()->createUrl('news/newsBackend/delete',
						array(
						     'source' => $model->primaryKey,
						)),
					array(
					     'class'   => 'delete-link',
					)); ?>
                </li>
				<?php endif; ?>
                <li class="submit-button-container">
					<?php echo CHtml::submitButton(Yii::t('newsModule.common', 'Сохранить'),
					array(
					     'id' => 'newsFormSubmit'
					)); ?>
                </li>
            </ul>
            <br clear="all">
        </div>

</div>
<?php $this->endWidget(); ?>