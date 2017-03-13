<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//use yii\helpers\Html;

/* @var $this View */
$this->title = 'Login';
$this->context->layout = 'login';
?>
<div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php $form = ActiveForm::begin([]) ?>
        <?= $form->field($model, 'username',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>\n{error}"
        ])->textInput(['placeholder' => 'Username or email']) ?>
        <?= $form->field($model, 'password',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{error}"
        ])->passwordInput(['placeholder' => 'Password']) ?>

        <div class="row">
            <div class="col-xs-8">
                <div class="col-sm-12 checkbox icheck">
                    <label>
                        <input type="checkbox"> Remember Me
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div>
        </div>
    <?php ActiveForm::end(); ?>

    <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="<?= Url::to(['/user/auth', 'authclient' => 'facebook'])?>" class="btn btn-block btn-social btn-facebook btn-flat">
            <i class="fa fa-facebook"></i> Sign in using Facebook
        </a>
        <a href="<?= Url::to(['/user/auth', 'authclient' => 'google'])?>" class="btn btn-block btn-social btn-google btn-flat">
            <i class="fa fa-google-plus"></i> Sign in using Google+
        </a>
    </div>
    <!-- /.social-auth-links -->

    <a href="<?= Url::to(['/user/request-password-reset'])?>">I forgot my password</a><br>
    <a href="<?= Url::to(['/user/signup'])?>" class="text-center">Register a new membership</a>

</div>