<?php

namespace backend\controllers;

use Yii;
use common\models\LoginForm;
use common\models\PasswordResetRequestForm;
use common\models\ResetPasswordForm;
use common\models\SignupForm;
use common\models\ChangePasswordForm;
use common\models\ChangeUserEmailForm;
use common\models\UserProfile;
use common\models\User;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\classes\AuthFilter;
use common\classes\GuestFilter;
use common\classes\AuthHandler;
use common\classes\UploadImage;
use yii\web\NotFoundHttpException;

/**
 * User controller
 */
class UserController extends Controller
{
    public $defaultAction = 'profile';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'only' => ['logout', 'change-password', 'update-profile', 'update-user-email'],
            ],
            'guest' => [
                'class' => GuestFilter::className(),
                'only' => ['signup', 'reset-password', 'login', 'request-password-reset'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'upload-photo' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        list($success, $message) = (new AuthHandler($client))->handle();
        if ($message) {
            Yii::$app->session->setFlash($success ? 'success' : 'error', [$message]);
        }
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                    'model' => $model,
            ]);
        }
    }

    public function actionProfile($id = null)
    {
        $model = $id ? User::findOne($id) : Yii::$app->user->identity;
        if ($model) {
            return $this->render('profile', [
                    'model' => $model,
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUploadPhoto()
    {
        $model = UserProfile::findOne(Yii::$app->user->id);
        $photo_id = UploadImage::store('image', [
                'crop' => Yii::$app->getRequest()->post('crop'),
                'rules' => ['minWidth' => 400]
        ]);
        if ($photo_id !== false) {
            $model->photo_id = $photo_id;
            $model->save();
        }
        return $this->redirect(['profile']);
    }

    public function actionUpdateProfile()
    {
        $model = Yii::$app->user->identity;
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    $transaction->commit();
                    return $this->redirect(['profile']);
                }
            } catch (\Exception $exc) {
                $model->addError('', $exc->getMessage());
            }
            $transaction->rollBack();
        }
        return $this->render('update-profile', [
                'model' => $model,
        ]);
    }

    public function actionUpdateUserEmail()
    {
        $user = Yii::$app->user->identity;
        $model = new ChangeUserEmailForm([
            'username' => $user->username,
            'email' => $user->email,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            return $this->redirect(['profile']);
        }
        return $this->render('update-user-email', [
                'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if ($user->status == User::STATUS_ACTIVE) {
                    Yii::$app->getUser()->login($user);
                }
                return $this->goHome();
            }
        }

        return $this->render('signup', [
                'model' => $model,
        ]);
    }

    public function actionActivate($token, $action)
    {
        $userId = Yii::$app->tokenManager->getTokenData($token, 'activate.account');
        if ($userId !== false) {
            $user = User::findOne($userId);
            if ($action == 'r') {
                $user->delete();
            } else {
                Yii::$app->user->login($user);
            }
            Yii::$app->tokenManager->deleteToken($token);
            return $this->goHome();
        }
        throw new \yii\base\UserException('Invalid link');
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                'model' => $model,
        ]);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            return $this->goHome();
        }

        return $this->render('change-password', [
                'model' => $model,
        ]);
    }
}
