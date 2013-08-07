<?php
$form = $this->beginWidget('CActiveForm',
	array(
	     'id'                     => 'category-attributes-form',
	     'enableAjaxValidation'   => true,
	     'enableClientValidation' => true,
	     'focus'                  => array(
		     $model,
		     'title'
	     ),

	));
?>
	<div class="container-flexible">
	<?php if ( $model->hasErrors() ): ?>
			<p class="errornote"><?php echo YiiadminModule::t('Пожалуйста, исправьте ошибки, указанные ниже.'); ?></p>
		<?php endif; ?>

		<div class="">
        <div class="column span-16">
            <h3><?php echo Yii::t(get_class($model), get_class($model)) ?></h3>
            <fieldset class="module">

	            <div class="row <?php if ( $model->getError('cId') ) {
		            echo 'errors';
	            } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'cId'); ?></div>
                 <div class="column span-flexible">
			<?php echo $form->dropDownList($model, 'cId', CHtml::listData(Category::model()->findAll(), 'id', 'title'), array('empty' => '')); ?>
	                 <ul class="errorlist">
                         <li><?php echo $form->error($model, 'cId'); ?></li>
                     </ul>
                 </div>
             </div>

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

                <div class="row <?php if ( $model->getError('type') ) {
	                echo 'errors';
                } ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'type'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->dropDownList($model, 'type', $model->typeLabels()); ?>
	                    <ul class="errorlist">
                            <li><?php echo $form->error($model, 'type'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="row <?php if ( $model->getError('required') ) {
	                echo 'errors';
                } ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'required'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->checkBox($model, 'required'); ?>
	                    <ul class="errorlist">
                            <li><?php echo $form->error($model, 'required'); ?></li>
                        </ul>
                    </div>
                </div>

	            <div class="row <?php if ( $model->getError('common') ) {
		            echo 'errors';
	            } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'common'); ?></div>
                 <div class="column span-flexible">
			<?php echo $form->checkBox($model, 'common'); ?>
	                 <ul class="errorlist">
                         <li><?php echo $form->error($model, 'common'); ?></li>
                     </ul>
                 </div>
             </div>


                <div class="row <?php if ( $model->getError('description') ) {
	                echo 'errors';
                } ?>">
                    <div class="column span-4"><?php echo $form->labelEx($model, 'description'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textField($model, 'description'); ?>
	                    <ul class="errorlist">
                            <li><?php echo $form->error($model, 'description'); ?></li>
                        </ul>
                    </div>
                </div>

	            <div class="row <?php if ( $model->getError('separate') ) {
              echo 'errors';
             } ?>">
                 <div class="column span-4"><?php echo $form->labelEx($model, 'separate'); ?></div>
                 <div class="column span-flexible">
			<?php echo $form->checkBox($model, 'separate'); ?>
                  <ul class="errorlist">
                         <li><?php echo $form->error($model, 'separate'); ?></li>
                     </ul>
                 </div>
             </div>
            </fieldset>

        </div>


        <div class="column span-8 characteristics"<?php echo ($model->isCharacteristicsNeeded() ? '' : ' style="display: none;"') ?>>
            <h3><?php echo Yii::t(get_class($char), 'Характеристики') ?></h3>

	        <?php
	        $i = 0;
	        foreach ( $chars AS $char ) {
		        ?>

		        <fieldset class="module">
	            <div class="row">
                 <ul class="actions tools">
                     <?php
	                 echo '<li>' . CHtml::link(YiiadminModule::t('Добавить'),
		                 '#',
		                 array('class' => 'add-handler icon')) . '</li>';
	                 echo '<li>' . CHtml::link(YiiadminModule::t('Удалить'),
		                 '#',
		                 array('class' => 'delete-handler delete icon')) . '</li>';
	                 ?>
                 </ul>
             </div>
                <div class="row <?php if ( $char->getError('[' . $i . ']title') ) {
	                echo 'errors';
                } ?>">
                    <div class="column span-4"><?php echo $form->labelEx($char, '[' . $i . ']title'); ?></div>
                    <div class="column span-flexible">
						<?php echo $form->textField($char, '[' . $i . ']title'); ?>
	                    <ul class="errorlist">
                            <li><?php echo $form->error($char, '[' . $i . ']title'); ?></li>
                        </ul>
                    </div>
                </div>
            </fieldset>

		        <?php ++$i;
	        } ?>

        </div>


        <div class="module footer">
            <ul class="submit-row">
				<?php if ( !$model->isNewRecord ): ?>
		            <li class="left delete-link-container">
					<?php echo CHtml::link(YiiadminModule::t('Удалить'),
				            Yii::app()->createUrl('categoryattributes/categoryAttributesBackend/delete',
					            array(
					                 'source' => $model->primaryKey,
					            )),
				            array(
				                 'class'   => 'delete-link',
				                 'confirm' => YiiadminModule::t('Удалить запись ID ') . $model->primaryKey . '?',
				            )); ?>
                </li>
	            <?php endif; ?>
	            <li class="submit-button-container">
					<?php echo CHtml::submitButton(Yii::t('CategoryAttributesModule', 'Сохранить'),
			            array(
			                 'id' => 'categoryFormSubmit'
			            )); ?>
                </li>
            </ul>
            <br clear="all">
        </div>
    </div>
</div>
<?php $this->endWidget();