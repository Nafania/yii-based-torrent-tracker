<?php
/**
 * @var $user           User
 * @var $profile        UserProfile
 * @var $socialServices array
 * @var $form TbActiveForm
 */
?>
<?php
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->getModule('user')->getAssetsUrl() . '/js/users.js');
$url = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.user.extensions.eauth.assets'));
$cs->registerCssFile($url . '/css/auth.css');
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
                'id' => 'settings-form',
                'enableClientValidation' => true,
                'enableAjaxValidation' => true,
                'type' => 'horizontal',
                'htmlOptions' => array(
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
                'value' => '',
                'autocomplete' => 'off'
            ));

        echo $form->dropDownListRow($profile, 'theme', $profile->getThemes());

        echo $form->checkBoxRow($profile, 'disabledNotifies');
        ?>

        <hr/>

        <?php
        if ($user->socialAccounts) {
            ?>
            <h4><?php echo Yii::t('userModule.common', 'Добавленные аккаунты социальных сетей') ?></h4>
            <?php

            ?>

            <ul class="auth-services clear">
                <?php
                foreach ($user->socialAccounts AS $account) {
                    $service = $socialServices[$account->service];
                    echo '<li class="auth-service ' . $service->id . '" title="' . Yii::t('userModule.common',
                            'Кликните, чтобы удалить') . '" data-toggle="tooltip">';
                    $html = '<span class="auth-link"><span class="auth-icon ' . $service->id . '"><i></i></span>';
                    $html .= '<span class="auth-title">' . $account->name . '</span></span>';
                    echo CHtml::link($html,
                        array(
                            '/user/default/socialDelete',
                            'service' => $service->id
                        ),
                        array(
                            'data-action' => 'social-delete',
                        ));
                    echo '</li>';
                    unset($socialServices[$service->id]);
                }
                ?>
            </ul>


        <?php
        }

        if ($socialServices) {
            ?>
            <h4><?php echo Yii::t('userModule.common', 'Добавить аккаунт социальных сетей') ?></h4>

            <?php $this->widget('application.modules.user.extensions.eauth.EAuthWidget',
                array(
                    'action' => '/user/default/socialAdd',
                    'services' => $socialServices
                ));
        }
        ?>

        <div class="form-actions">
            <?php $this->widget('bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'submit',
                    'type' => 'primary',
                    'label' => Yii::t('userModule.common', 'Сохранить'),
                ));
            ?>

            <?php
            if (Yii::app()->getUser()->checkAccess('user.default.delete')) {
                $this->widget('bootstrap.widgets.TbButton',
                    array(
                        'buttonType' => 'link',
                        'type' => 'danger',
                        'label' => Yii::t('userModule.common', 'Удалить свой аккаунт'),
                        'htmlOptions' => array(
                            'submit' => array('/user/default/delete'),
                            'class' => 'pull-right',
                            'csrf' => true,
                            'confirm' => Yii::t('userModule.common',
                                    'Вы уверены? Ваш аккаунт и все данные, связанные с ним, будут удалены без возможности восстановления.')
                        )
                    ));
            }

            if (!$user->emailConfirmed && $user->getEmail()) {

                $this->widget('bootstrap.widgets.TbButton',
                    array(
                        'label' => Yii::t('userModule.common', 'Подтвердить email'),
                        'type' => 'link',
                        'url' => array('/user/default/confirmEmail'),
                    ));
            }
            ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>