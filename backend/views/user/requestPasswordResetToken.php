<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \common\models\PasswordResetRequestForm */

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'login';
?>

<div class="login-box-body">
    <p class="login-box-msg">Please fill out your email. A link to reset password will be sent there.</p>

    <?php $form = ActiveForm::begin([]) ?>
        <?= $form->field($model, 'email',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>\n{error}"
        ])->textInput(['placeholder' => 'Email']) ?>

        <div class="row">
            <div class="col-xs-offset-8 col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>
</div>
