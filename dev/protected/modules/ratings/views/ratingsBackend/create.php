<?php
/** @var $ratings array */
?>
<h1><?php echo Yii::t('ratingsModule.common', 'Управление коэффициентами рейтингов'); ?></h1>
<ul class="tools">
    <li>
    </li>
</ul>

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
            <fieldset class="module">
	            <?php
	            foreach ( $ratings AS $key => $value ) {
		            echo '<div class="row">';
		            echo '<div class="column span-4">K' . ( $key === 0 ? '' : $key ) . '</div>';
		            echo '<div class="column span-16 span-flexible">' . CHtml::telField('Rating[' . $key . ']', $value) . '</div>';
		            echo '</div>';
	            }
	            ?>
            </fieldset>

        </div>

        <div class="module footer">
            <ul class="submit-row">
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


