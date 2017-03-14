<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use mdm\upload\CropImage;

/* @var $this View */
/* @var $model common\models\User */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = $this->title;

$email = $model->email;
$isEditable = Yii::$app->user->id == $model->id;
?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-solid">
            <div class="box-body box-profile">
                <a data-toggle="modal" data-target="#ChangePhotoProfile" id="btn-change-photo">
                <?=
                Html::img($model->avatarUrl ?: '@web/img/default.jpg', [
                    'class' => 'profile-user-img img-responsive img-circle',
                ])
                ?>
                </a>
                <h3 class="profile-username text-center"><?= Html::encode($model->fullname) ?></h3>
                <p class="text-muted text-center"><?= Html::encode($model->bio) ?></p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Email</b> <a class="pull-right" href="mailto:<?= $model->email ?>"><?= $model->email ?></a>
                    </li>
                </ul>
                <?=
                $isEditable ? Html::a('<b>Edit Profile</b>', ['update-profile'], ['class' => 'btn btn-primary btn-block'])
                        : ''
                ?>
            </div>
            <!-- /.box-body -->
        </div>
    </div><!-- col-sm-3 -->

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Profile</a></li>

                <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="table-responsive">
                        <table class="table about-table">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td><?= Html::encode($model->fullname) ?></td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td><?= Html::encode($model->username) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= Html::mailto($model->email, $model->email, ['target' => '_blank']) ?>
                                        <?php
                                        if ($isEditable):
                                            ?>
                                            <span class="badge badge-primary">
                                                <a href="<?= Url::to(['update-user-email']) ?>" style="color: #fff">
                                                    <i class="fa fa-edit"></i> edit email
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Birth Date</th>
                                    <td> <?= Yii::$app->formatter->asDate($model->birth_day) ?></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td><?= $model->gender ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?= Html::encode($model->address) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>

    </div><!-- col-sm-8 -->
</div>

<?php if ($isEditable): ?>
    <div id="ChangePhotoProfile" class="modal fade in" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <?=
            Html::beginForm(['upload-photo'], 'post', [
                'class' => 'form-horizontal',
                'enctype' => 'multipart/form-data'
            ]);
            ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2 class="modal-title">Change photo</h2>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                    <?=
                    CropImage::widget([
                        'id' => 'dcrop',
                        'name' => 'image',
                        'options' => ['class' => 'col-md-12', 'style' => 'min-height: 300px;'],
                        'imgOptions' => ['class' => 'img-responsive', 'style' => 'max-height: 350px;'],
                        'clientOptions' => [
                            //'aspectRatio' => 4 / 3,
                            'minWidth' => 200,
                            'button' => '#select-file',
                        ]
                    ])
                    ?>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="select-file">Select File</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-raised btn-primary" id="btn-submit" style="display: none;">
                        Upload<div class="ripple-container"></div></button>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
<?php endif; ?>
<?php
$js = <<<JS
    $('#dcrop').on('afterLoadFile', function(){
        $('#btn-submit').show();
    });
JS;
$this->registerJs($js);
