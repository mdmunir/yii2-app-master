<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//use yii\helpers\Html;

/* @var $this View */
$this->title = 'Signup';
$this->context->layout = 'login';
?>
<div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php $form = ActiveForm::begin([]) ?>
        <?= $form->field($model, 'fullname',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-user form-control-feedback\"></span>\n{error}"
        ])->textInput(['placeholder' => 'Full name']) ?>
        <?= $form->field($model, 'email',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>\n{error}"
        ])->textInput(['placeholder' => 'Email']) ?>
        <?= $form->field($model, 'username',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-user form-control-feedback\"></span>\n{error}"
        ])->textInput(['placeholder' => 'Username']) ?>
        <?= $form->field($model, 'password',[
            'options' => ['class' => 'form-group has-feedback'],
            'template' => "{input} <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>\n{error}"
        ])->passwordInput(['placeholder' => 'Password']) ?>

        <div class="row">
            <div class="col-xs-offset-8 col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign Up</button>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>
    <a href="<?= Url::to(['/user/login'])?>" class="text-center">Already has account</a>
</div>
<?php
$js = <<<JS
$('#signupform-email').on('blur', function(){
    if($('#signupform-username').val() === '' && $('#signupform-email').val() !== ''){
        var es = $('#signupform-email').val().split('@');
        $('#signupform-username').val(es[0]);
    }
});
JS;
$this->registerJs($js);
