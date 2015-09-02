<?php

namespace app\models\forms;

use app\models\exceptions\Internal;
use app\models\settings\AntragsgruenApp;
use yii\base\Model;

class AntragsgruenInitForm extends Model
{
    /** @var string */
    public $configFile;

    public $siteUrl;

    public $sqlType = 'mysql';
    public $sqlHost;
    public $sqlUsername;
    public $sqlPassword;
    public $sqlDB;
    public $sqlFile;

    public $adminUsername;
    public $adminPassword;

    /** @var int[] */
    public $adminIds;

    /** @var boolean */
    public $sqlCreateTables = true;

    /**
     * @param string $configFile
     */
    public function __construct($configFile)
    {
        parent::__construct();
        $this->configFile = $configFile;

        if (file_exists($configFile)) {
            $configJson = file_get_contents($configFile);
            try {
                $config        = new AntragsgruenApp($configJson);
                $this->siteUrl = $config->domainPlain;
                $this->setDatabaseFromParams($config->dbConnection);
                $this->adminIds = $config->adminUserIds;
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @param array $params
     */
    private function setDatabaseFromParams($params)
    {
        if (!is_array($params) || !isset($params['dsn'])) {
            return;
        }
        if (isset($params['username'])) {
            $this->sqlUsername = $params['username'];
        }
        if (isset($params['password'])) {
            $this->sqlPassword = $params['password'];
        }
        $parts = explode(':', $params['dsn']);
        if (count($parts) != 2) {
            return;
        }
        $this->sqlType = $parts[0];
        $params        = explode(';', $parts[1]);
        for ($i = 0; $i < count($params); $i++) {
            $parts = explode('=', $params[$i]);
            if (count($parts) == 2) {
                if ($parts[0] == 'dbname') {
                    $this->sqlDB = $parts[1];
                }
                if ($parts[0] == 'host') {
                    $this->sqlHost = $parts[1];
                }
            }
        }
    }


    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['siteUrl', 'sqlType', 'adminUsername', 'adminPassword'], 'required'],
            [['sqlType', 'sqlHost', 'sqlFile', 'sqlUsername', 'sqlPassword', 'sqlDB', 'sqlCreateTables'], 'safe'],
            [['siteUrl'], 'safe'],
            [['adminUsername', 'adminPassword'], 'safe'],
        ];
    }

    /**
     * @return array
     * @throws Internal
     */
    public function getDBConfig()
    {
        if ($this->sqlType == 'mysql') {
            return [
                'dsn'            => 'mysql:host=' . $this->sqlHost . ';dbname=' . $this->sqlDB,
                'emulatePrepare' => true,
                'username'       => $this->sqlUsername,
                'password'       => $this->sqlPassword,
                'charset'        => 'utf8mb4',
            ];
        }
        throw new Internal('Unknown SQL Type');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function verifyDBConnection()
    {
        try {
            $connConfig = $this->getDBConfig();
            $connection = new \yii\db\Connection($connConfig);
            $tables     = $connection->createCommand('SHOW TABLES')->queryAll();
            return (count($tables) > 0);
        } catch (\yii\db\Exception $e) {
            switch ($e->getCode()) {
                case 1044:
                    $message = 'The database login is correct, however I could not connect to the actual database.
                    Maybe a permission problem?';
                    break;
                case 1045:
                    $message = 'Invalid database username or password';
                    break;
                case 1046:
                    $message = 'Invalid database name entered';
                    break;
                case 2002:
                    if (mb_stripos($e->getMessage(), 'Connection refused')) {
                        $message = 'Database: Connection refused';
                    } elseif (mb_stripos($e->getMessage(), 'getaddrinfo failed')) {
                        $message = 'Database hostname not found';
                    } else {
                        $message = 'Could not connect to database: ' . $e->getMessage();
                    }
                    break;
                default:
                    if (mb_stripos($e->getMessage(), 'No database selected') !== false) {
                        $message = 'Invalid or no database name entered';
                    } else {
                        $message = 'Unknown error when trying to connect to database: ' . $e->getMessage();
                    }
            }
            throw new \Exception($message);
        }
    }

    /**
     * @return string[]
     */
    public function verifyConfiguration()
    {
        $errors = [];
        try {
            $this->verifyDBConnection();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        // @TODO
        return $errors;
    }

    /**
     * @return bool
     */
    public function hasAdminAccount()
    {
        return (count($this->adminIds) > 0);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return file_exists($this->configFile);
    }
}
