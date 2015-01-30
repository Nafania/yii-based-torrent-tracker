<?php
/* @var $form CActiveForm */

$form = $this->beginWidget('CActiveForm',
	array(
	     'id'          => get_class($model) . '-id-form',
	));
?>

	<div class="container-flexible">
		<div class="">
        <div class="column span-24">
            <h3><?php echo Yii::t('staticpagesModule.common', 'Создание страницы') ?></h3>
            <fieldset class="module">
                    <div class="row <?php if ( $model->getError('title') ) {
	                    echo 'errors';
                    } ?>">
                        <div class="column span-4"><?php echo $form->labelEx($model, 'fk_tag'); ?></div>
                        <div class="column span-16 span-flexible">
                            <?php echo $form->textField($model, 'fk_tag'); ?>
	                        <ul class="errorlist">
                                <li><?php echo $form->error($model, 'fk_tag'); ?></li>
                            </ul>
                        </div>
                    </div>

	            <div class="row <?php if ( $model->getError('fk_category') ) {
              echo 'errors';
             } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'fk_category'); ?></div>
                 <div class="column span-16 span-flexible">
                     <?php echo $form->dropDownList($model, 'fk_category', CHtml::listData(Category::model()->findAll(), 'id', 'title')); ?>
                  <ul class="errorlist">
                         <li><?php echo $form->error($model, 'fk_category'); ?></li>
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
                <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить и создать новую запись'); ?>"
                           name="_addanother" />
                </li>
                <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить и редактировать'); ?>"
                           name="_continue" />
                </li>
            </ul>
            <br clear="all">
        </div>

    </div>
</div>
<?php
$this->endWidget();

