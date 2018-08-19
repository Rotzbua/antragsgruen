<?php

namespace app\models\db;

use app\async\models\UserPrivilege;
use app\components\mail\Tools as MailTools;
use app\components\UrlHelper;
use app\models\exceptions\AlreadyExists;
use app\models\exceptions\FormError;
use app\models\exceptions\MailNotSent;
use yii\db\ActiveRecord;

/**
 * @package app\models\db
 *
 * @property int $userId
 * @property int $consultationId
 * @property string $privilegeView
 * @property string $privilegeCreate
 * @property string $adminSuper
 * @property string $adminContentEdit
 * @property string $adminScreen
 * @property string $adminProposals
 *
 * @property User $user
 * @property Consultation $consultation
 */
class ConsultationUserPrivilege extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        /** @var \app\models\settings\AntragsgruenApp $app */
        $app = \Yii::$app->params;
        return $app->tablePrefix . 'consultationUserPrivilege';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsultation()
    {
        return $this->hasOne(Consultation::class, ['id' => 'consultationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId'])
            ->andWhere(User::tableName() . '.status != ' . User::STATUS_DELETED);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['consultationId', 'userId'], 'required'],
            [['consultationId', 'userId'], 'number'],
            [['privilegeView', 'privilegeCreate'], 'number'],
            [['adminSuper', 'adminContentEdit', 'adminScreen', 'adminProposals'], 'number'],
        ];
    }

    /**
     * @param Consultation $consultation
     * @param string $email
     * @param string $name
     * @param string $emailText
     * @param string|null $setPwd
     * @throws AlreadyExists
     * @throws \yii\base\Exception
     */
    public static function createWithUserEmail(Consultation $consultation, $email, $name, $emailText, $setPwd = null)
    {
        $email = mb_strtolower($email);
        $auth  = 'email:' . $email;

        /** @var User $user */
        $user = User::find()->where(['auth' => $auth])->andWhere('status != ' . User::STATUS_DELETED)->one();
        if (!$user) {
            if ($setPwd) {
                $password = $setPwd;
            } else {
                $password = User::createPassword();
            }

            $user                  = new User();
            $user->auth            = 'email:' . $email;
            $user->email           = $email;
            $user->name            = $name;
            $user->pwdEnc          = password_hash($password, PASSWORD_DEFAULT);
            $user->status          = User::STATUS_CONFIRMED;
            $user->emailConfirmed  = 1;
            $user->organizationIds = '';
            $user->save();

            $accountText = str_replace(
                ['%EMAIL%', '%PASSWORD%'],
                [$email, $password],
                \Yii::t('user', 'acc_grant_email_userdata')
            );
        } else {
            $accountText = '';
        }

        /** @var ConsultationUserPrivilege $privilege */
        $privilege = static::findOne(['userId' => $user->id, 'consultationId' => $consultation->id]);
        if ($privilege) {
            throw new AlreadyExists();
        } else {
            $privilege                   = new ConsultationUserPrivilege();
            $privilege->consultationId   = $consultation->id;
            $privilege->userId           = $user->id;
            $privilege->adminContentEdit = 0;
            $privilege->adminScreen      = 0;
            $privilege->adminSuper       = 0;
            $privilege->adminProposals   = 0;
            $privilege->privilegeCreate  = 1;
            $privilege->privilegeView    = 1;
            $privilege->save();
        }

        $consUrl   = UrlHelper::createUrl('consultation/index');
        $consUrl   = UrlHelper::absolutizeLink($consUrl);
        $emailText = str_replace('%LINK%', $consUrl, $emailText);

        try {
            MailTools::sendWithLog(
                EMailLog::TYPE_ACCESS_GRANTED,
                $consultation,
                $email,
                $user->id,
                \Yii::t('user', 'acc_grant_email_title'),
                $emailText,
                '',
                ['%ACCOUNT%' => $accountText]
            );
        } catch (MailNotSent $e) {
            \yii::$app->session->setFlash('error', \Yii::t('base', 'err_email_not_sent') . ': ' . $e->getMessage());
        }
    }

    /**
     * @param Consultation $consultation
     * @param string $username
     * @throws AlreadyExists
     * @throws FormError
     */
    public static function createWithUserSamlWW(Consultation $consultation, $username)
    {
        if (preg_match('/[^\w]/siu', $username)) {
            throw new FormError('Invalid username');
        }
        $auth = 'openid:https://service.gruene.de/openid/' . $username;

        /** @var User $user */
        $user = User::find()->where(['auth' => $auth])->andWhere('status != ' . User::STATUS_DELETED)->one();
        if (!$user) {
            $user                  = new User();
            $user->auth            = $auth;
            $user->email           = '';
            $user->name            = '';
            $user->emailConfirmed  = 0;
            $user->pwdEnc          = null;
            $user->status          = User::STATUS_CONFIRMED;
            $user->organizationIds = '';
            $user->save();
        }

        /** @var ConsultationUserPrivilege $privilege */
        $privilege = static::findOne(['userId' => $user->id, 'consultationId' => $consultation->id]);
        if ($privilege) {
            throw new AlreadyExists();
        } else {
            $privilege                   = new ConsultationUserPrivilege();
            $privilege->consultationId   = $consultation->id;
            $privilege->userId           = $user->id;
            $privilege->adminContentEdit = 0;
            $privilege->adminScreen      = 0;
            $privilege->adminSuper       = 0;
            $privilege->adminProposals   = 0;
            $privilege->privilegeCreate  = 1;
            $privilege->privilegeView    = 1;
            $privilege->save();
        }
    }

    /**
     * @return UserPrivilege
     * @throws \Exception
     */
    public function getAsyncableObject()
    {
        return UserPrivilege::createFromDbObject($this);
    }
}
