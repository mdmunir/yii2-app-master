<?php
namespace common\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $fullname;
    public $email;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['username', 'default', 'value' => function() {
                    return User::getUniqueUsername(explode('@', $this->email)[0]);
                }],
            ['username', 'trim'],
            ['username', 'string', 'min' => 2, 'max' => 60],
            ['username', 'checkUsername', 'clientValidate' => 'checkUsernameClient'],

            ['fullname', 'trim'],
            ['fullname', 'required'],
            ['fullname', 'string', 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
        ];
    }

    public function checkUsername()
    {
        if (ArrayHelper::isIn(strtolower($this->username), User::$forbidden)) {
            $this->addError('username', 'Username is invalid.');
        }
    }

    public function checkUsernameClient()
    {
        $options = json_encode([
            'range' => User::$forbidden,
            'not' => true,
            'message' => 'Username is invalid.',
            'skipOnEmpty' => 1
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "yii.validation.range(value.toLowerCase(), messages, $options);";
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->fullname = $this->fullname;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }
}
