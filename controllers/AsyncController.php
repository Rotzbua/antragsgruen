<?php

namespace app\controllers;

use app\async\models\Userdata;
use app\models\db\User;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class AsyncController extends Base
{
    /**
     * @return string
     * @throws UnauthorizedHttpException
     */
    public function actionUser()
    {
        if (\Yii::$app->request->remoteIP !== '127.0.0.1' && \Yii::$app->request->remoteIP !== '::1') {
            throw new UnauthorizedHttpException('This IP is not whitelisted');
        }
        $user = User::getCurrentUser();
        if (!$user) {
            throw new UnauthorizedHttpException('not logged in', 401);
        }
        \yii::$app->response->format = Response::FORMAT_RAW;
        \yii::$app->response->headers->add('Content-Type', 'application/json');
        return Userdata::createFromDbObject($user)->toJSON();
    }

    /**
     * @return string
     */
    public function actionClient()
    {
        return $this->render('client');
    }
}
