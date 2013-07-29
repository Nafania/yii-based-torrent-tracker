<?php
/**
 * @var CActiveForm $form
 */
$form = $this->beginWidget('CActiveForm',
	array(
	     'id'                    => 'category-form',
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


                <div class="row <?php if ( $model->getError('name') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'name'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textField($model, 'name'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'name'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="row <?php if ( $model->getError('image') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'image'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->fileField($model, 'image'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'image'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="row <?php if ( $model->getError('description') ) {
					echo 'errors';
				} ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'description'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textArea($model, 'description'); ?>
                        <ul class="errorlist">
                            <li><?php echo $form->error($model, 'description'); ?></li>
                        </ul>
                    </div>
                </div>
            </fieldset>

        </div>
		<?php
		if ( Yii::app()->getModule('categoryattributes') ) {
			Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getModule('categoryattributes')->getAssetsUrl() . '/js/categoryAttributesList.js', CClientScript::POS_END);

			?>
            <div class="container">
                <div class="column span-16">
                    <h3><?php echo Yii::t('CategoryAttributesModule', 'Атрибуты') ?></h3>
                    <fieldset class="module">

                        <table class="categoryAttributes">
                            <thead>
                            <tr>
                                <td><?php echo Yii::t('CategoryAttributesModule', 'Атрибуты категории') ?></td>
                                <td><?php echo Yii::t('CategoryAttributesModule', 'Доступные атрибуты') ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr valign="top">
                                <td>
	                                <?php echo $form->dropDownList($model, 'categoryAttributes', CHtml::listData(Attribute::model()->includeIds($model->categoryAttributes)->findAll(), 'id', 'title'), array(
	                                                                                                                                                               'multiple' => 'multiple',
	                                                                                                                                                               'id' => 'categoryAttributes'
	                                                                                                                                                          )) ?>

                                </td>
                                <td>
	                                <?php echo CHtml::dropDownList('allAttributes[]', 0, CHtml::listData(Attribute::model()->forCat($model->getId())->excludeIds($model->categoryAttributes)->findAll(), 'id', 'title'), array(	                                                                                                                                                               'multiple' => 'multiple',
	                                                                                                                                                               'id' => 'allAttributes'
	                                                                                                                                                          )) ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                </div>
            </div>
			<?php
		}
		?>

        <div class="module footer">
            <ul class="submit-row">
				<?php if ( !$model->isNewRecord ): ?>
                <li class="left delete-link-container">
					<?php echo CHtml::link(YiiadminModule::t('Удалить'),
					Yii::app()->createUrl('category/categoryBackend/delete',
						array(
						     'source' => $model->primaryKey,
						)),
					array(
					     'class'   => 'delete-link',
					)); ?>
                </li>
				<?php endif; ?>
                <li class="submit-button-container">
					<?php echo CHtml::submitButton(Yii::t('CategoryModule', 'Сохранить'),
					array(
					     'id' => 'categoryFormSubmit'
					)); ?>
                </li>
            </ul>
            <br clear="all">
        </div>

</div>
<?php
if ( !empty($parentId) ) {
	echo CHtml::hiddenField('parentId', $parentId);
}
?>
<?php $this->endWidget(); ?>