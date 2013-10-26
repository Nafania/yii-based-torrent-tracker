<?php
/**
 * @var $form       TbActiveForm
 * @var $groupUsers GroupUser[]
 */
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
	array(
	     'id'                     => 'group-form',
	     'enableClientValidation' => false,
	     'enableAjaxValidation'   => true,
	     //'action'                 => Yii::app()->createUrl('/groups/default/invite'),
	     'clientOptions'          => array(
		     'validateOnSubmit' => true,
		     'afterValidate'    => 'js:function(form,data,hasError){
		        var button = form.find("button[type=submit]");
		        $.fn.EupdateSummary(form, data);
                       if(!hasError){
                               $.ajax({
                                       type:"POST",
                                       url: $(form).attr("action"),
                                       data:form.serialize(),
                                       dataType: "json",
                                       success:function(data) {
                                       $("#inviteModal").modal("hide");
                                                   $(".top-right").notify({
                message: { html: data.message },
                fadeOut: {
                    enabled: true,
                    delay: 3000
                },
                type: "success"
            }).show();
                                       },
                                       complete: function() {
                                        button.button("reset");
                                       }
                                       });
                               }
                               else {
                                button.button("reset");
                               }

                       }',
	     )
	)); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('groupsModule.common', 'Пригласить пользователей в группу'); ?></h4>
	</div>
	<div class="modal-body">
<?php
echo $form->errorSummary($groupUsers);

$data = array();
foreach ( $groupUsers AS $groupUser ) {
	if ( $groupUser->user ) {
		$data[$groupUser->user->getId()] = array(
			'text' => $groupUser->user->getName()
		);
	}
}

$this->widget('bootstrap.widgets.TbSelect2',
	array(
	     'asDropDownList' => false,
	     'name'           => 'inviteUsers',
	     'htmlOptions'    => array(
		     'class'       => 'span4',
		     'multiple'    => 'multiple',
		     'placeholder' => Yii::t('groupsModule.common',
			     'Имена пользователей'),
	     ),
	     'options'        => array(
		     //'containerCssClass' => 'span5',

		     'minimumInputLength' => 2,
		     'multiple'           => true,
		     'tags'               => true,
		     'tokenSeparators'    => array(
			     ',',
		     ),
		     'initSelection'      => 'js:function (element, callback) {
				         var data = [];
				         var usersData = ' . CJavaScript::encode($data) . ';
				         $.each(usersData, function (key,options) {
				            if ( !key ) {
				                return true;
				            }
				             data.push({id:key, text: options.text, locked:options.locked});
				         });
				         callback(data);
				     }',
		     'ajax'               => 'js:{
				url: ' . CJavaScript::encode(Yii::app()->createUrl('/user/default/suggest')) . ',
                dataType: "json",
                cache: true,
                quietMillis: 100,
                data: function ( term ) {
				return {
					term: term
                    };
                },
                results: function ( data ) {
					return {
						results: data.data.users};
                }}',
	     ),
	));
/**
 * Эти поля нужны для работы yiiActiveFrom.js
 */
foreach ( $groupUsers AS $i => $groupUser ) {
	echo $form->hiddenField($groupUser, '[' . $i . ']idUser');
	echo $form->error($groupUser, '[' . $i . ']idUser');
}
?>
</div>
	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array(
			     'buttonType'  => 'submit',
			     'type'        => 'primary',
			     'label'       => Yii::t('groupsModule.common', 'Пригласить в группу'),
			     'loadingText' => Yii::t('groupsModule.common', 'Высылаем приглашения...'),
			)); ?>
</div>
<?php $this->endWidget(); ?>