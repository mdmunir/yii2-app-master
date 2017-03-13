<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model common\models\ChangeUserEmailForm */

$this->title = 'Change Username Email';
$this->params['breadcrumbs'][] = $this->title;
$this->context->layout = 'login';
?>
<div class="login-box-body">
    <p class="login-box-msg">Please choose your email or username.</p>

    <?php $form = ActiveForm::begin([]) ?>
        <?= $form->field($model, 'email',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>"
        ])->textInput(['placeholder' => 'Email']) ?>
        <?= $form->field($model, 'username',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-user form-control-feedback\"></span>"
        ])->textInput(['placeholder' => 'Username']) ?>
        <?= $form->field($model, 'password',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>"
        ])->passwordInput(['placeholder' => 'Password']) ?>

        <div class="row">
            <div class="col-xs-offset-8 col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>
</div>
