<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \common\models\ResetPasswordForm */

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'login';
?>
<div class="login-box-body">
    <p class="login-box-msg">Please choose your new password.</p>

    <?php $form = ActiveForm::begin([]) ?>
        <?= $form->field($model, 'password',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{error}"
        ])->passwordInput(['placeholder' => 'Password']) ?>

        <?= $form->field($model, 'retypePassword',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{error}"
        ])->passwordInput(['placeholder' => 'Retype Password']) ?>

        <div class="row">
            <div class="col-xs-offset-8 col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>
</div>
