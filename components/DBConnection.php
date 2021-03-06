<?php

namespace app\components;

use yii\web\View;

class DBConnection extends \yii\db\Connection
{
    private static $catchedError = false;

    /**
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function open()
    {
        try {
            parent::open();
        } catch (\yii\db\Exception $e) {
            if (static::$catchedError) {
                echo '<h1>Could not open database - and an endless loop occurred.</h1>';
                echo 'This should not happen under any circumstance. You might consider opening a ';
                echo '<a href="https://github.com/CatoTH/antragsgruen">bugreport</a>.';
            } else {
                $view = new View();
                echo \yii::$app->controller->renderContent(
                    $view->render(
                        '@app/views/errors/error',
                        [
                            'name'       => 'Datenbank-Verbindungsfehler',
                            'message'    => 'Leider ist beim Aufbau der Datenbank-Verbindung ein Fehler aufgetreten.',
                            'httpStatus' => 500,
                        ],
                        \yii::$app->controller
                    )
                );

            }
            \yii::$app->end(500);
        }
    }
}
