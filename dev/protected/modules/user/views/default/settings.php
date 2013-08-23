<?php
/**
 * @var $user           User
 * @var $profile        UserProfile
 * @var $socialServices array
 */
?>

<h1><?php echo $user->getName() ?></h1>
<div class="row-fluid">
	<div class="span2">
	<?php echo CHtml::image($profile->getImageUrl(150, 150),
			$user->getName(),
			array(
			     'class' => 'img-polaroid',
			     'style' => 'width:150px'
			)); ?>
	</div>

	<div class="span10">
		<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
			array(
			     'id'                     => 'settings-form',
			     'enableClientValidation' => true,
			     'enableAjaxValidation'   => true,
			     'type'                   => 'horizontal',
			     'htmlOptions'            => array(
				     'enctype' => 'multipart/form-data'
			     )
			)); ?>

		<?php
		echo $form->fileFieldRow($profile, 'picture');
		echo $form->textFieldRow($user, 'name');
		echo $form->textFieldRow($user, 'email');
		echo $form->passwordFieldRow($user,
			'password',
			array(
			     'value'        => '',
			     'autocomplete' => 'off'
			));

		?>

		<hr />

		<?php
		if ( $user->socialAccounts ) {
			?>
			<h4><?php echo Yii::t('userModule.common', 'Добавленные аккаунты социальных сетей') ?></h4>
			<?php

			?>

			<ul class="auth-services clear">
		  <?php
				foreach ( $user->socialAccounts AS $account ) {
					$service = $socialServices[$account->service];
					echo '<li class="auth-service ' . $service->id . '">';
					$html = '<span class="auth-link"><span class="auth-icon ' . $service->id . '"><i></i></span>';
					$html .= '<span class="auth-title">' . $account->name . '</span></span>';
					echo $html;
					echo '</li>';
					unset($socialServices[$account->service]);
				}
				?>
		  </ul>


		<?php
		}

		if ( $socialServices ) {
			?>
			<h4><?php echo Yii::t('userModule.common', 'Добавить аккаунт социальных сетей') ?></h4>

			<?php $this->widget('application.modules.user.extensions.eauth.EAuthWidget',
				array(
				     'action'   => '/user/default/socialAdd',
				     'services' => $socialServices
				));
		}
		?>

		<div class="form-actions">
				<?php $this->widget('bootstrap.widgets.TbButton',
				array(
				     'buttonType' => 'submit',
				     'type'       => 'primary',
				     'label'      => Yii::t('userModule.common', 'Submit settings'),
				));

			if ( !$user->emailConfirmed ) {

				$this->widget('bootstrap.widgets.TbButton',
					array(
					     'label' => Yii::t('userModule.common', 'Confirm email'),
					     'type'  => 'link',
					     'url'   => array('/user/default/confirmEmail'),
					));
			}
			?>
		</div>

		<?php $this->endWidget(); ?>
	</div>
</div>