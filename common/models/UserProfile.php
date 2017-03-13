<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property integer $company_id
 * @property string $username
 * @property string $email
 * @property string $fullname
 * @property integer $photo_id
 * @property string $avatar
 * @property string $gender
 * @property string $address
 * @property string $birth_day
 * @property string $bio
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $avatarUrl
 *
 * @property User $user
 *
 */
class UserProfile extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{%user_profile}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'fullname'], 'required'],
            [['user_id'], 'integer'],
            [['birth_day'], 'safe'],
            [['bio'], 'string'],
            [['file'], 'file', 'extensions' => ['jpg', 'jpeg', 'png']],
            [['fullname', 'avatar', 'gender', 'address'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'fullname' => 'Fullname',
            'photo_id' => 'Photo ID',
            'avatar' => 'Avatar',
            'gender' => 'Gender',
            'address' => 'Address',
            'birth_day' => 'Birth Day',
            'bio' => 'Bio',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //$this->synchronToFirebase(array_keys($changedAttributes));
    }

    public function synchronToFirebase($attrs = null)
    {
        $map = [
            'fullname' => 'name',
            'gender' => 'gender',
            'address' => 'address',
            'birth_day' => 'birth_day',
            'bio' => 'bio',
        ];
        $data = [];
        if ($attrs === null) {
            $attrs = $this->attributes();
        }
        foreach ($attrs as $attr) {
            if (isset($map[$attr])) {
                $data[$map[$attr]] = $this->$attr;
            } elseif ($attr == 'photo_id' || $attr == 'avatar') {
                $data['profilPicUrl'] = $this->avatarUrl;
            }
        }
        Yii::$app->firebase->update("users/{$this->user_id}",$data);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->photo_id) {
            return Url::to(['@backend/image', 'id' => $this->photo_id], true);
        }
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->user->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->user->email;
    }

    public function behaviors()
    {
        return[
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => 'mdm\upload\UploadBehavior',
                'savedAttribute' => 'photo_id',
            ],
        ];
    }
}
