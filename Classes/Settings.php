<?php
/**
 * Соединение с БД
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Classes;

/**
 * Class Settings Соединение с БД
 * @package Classes Пакет общих классов проекта
 */
class Settings
{
    /**
     * @var string Хост базы данных
     */
    private $host = "";
    /**
     * @var int Порт базы данных
     */
    private $port = 0;
    /**
     * @var string Название базы данных
     */
    private $bd = "";
    /**
     * @var string Имя пользователя
     */
    private $login_bd = "";
    /**
     * @var string Пароль пользователя
     */
    private $password_bd = "";
    /**
     * @var string Название сервера Базы данных
     */
    private $name_server = "";
    /**
     * @var string Название Базы данных
     * @example MYSQLI, MYSQL, ORACLE
     */
    private $nameDB = "";

    /**
     * Конструктор с параметрами и паролями подключения к бД
     * @param string $db База данных
     */
    public function __construct($db = "Mysqli")
    {
        $this->nameDB = $db;
        switch(strtoupper($db)) {
            case "MYSQLI":
            case "MYSQL":
                /*$this->host = "10.36.9.125";
                $this->bd = "dashboard_test";
                $this->port = 3306;
                $this->login_bd = "portal";
                $this->password_bd = "portal123";
                $this->nameDB = "MYSQL";*/
                $this->host = "10.36.39.127";
                $this->bd = "dashboard_test";
                //$this->bd = "dashboard";
                $this->port = 3306;
                $this->login_bd = "webodb_ruser";
                $this->password_bd = "GKdFGMB4";
                $this->nameDB = "MYSQL";
            break;
            default:
                return "Нету параметров для соединения с базой данных";
        }
    }

    /**
     * Функция для получения названия базы данных
     * @return string Название базы данных
     */
    public function getNameDateBase()
    {
        return $this->nameDB;
    }


    /**
     * Создает соединения с базой данных mysqli
     * @return \mysqli Возврашает соединения с базой данных
     */
    public function getConnectMySqli()
    {
        $Connect = new \mysqli($this->host, $this->login_bd, $this->password_bd, $this->bd, $this->port);
        if ($Connect->connect_error) {
            die('Connect Error (' . $Connect->connect_errno . ') ' . $Connect->connect_error);
        } else {
            if (!$Connect->set_charset("UTF8")) {
                printf("Ошибка при загрузке набора символов UTF-8: %s\n", $Connect->error);
            }
        }
        $this->name_server = "Classes\JobDatabase\JobMySqli";
        return $Connect;
    }

    /**
     * Создает соеденение с базой данных Oracle
     * @return resource Возвращает соединения с базой данных
     */
    public function getConnectOracle()
    {
        $Connect = \oci_connect($this->login_bd,
            $this->password_bd,
            $this->host . ":" . $this->port . "/" . $this->bd,
            "UTF8");
        if (!$Connect) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $this->name_server = "Classes\JobDatabase\JobOracle";
        return $Connect;
    }

    /**
     * Метод для получения имени сервера
     * @return string Возвращает название сервера
     */
    public function getNameServer()
    {
        return $this->name_server;
    }

    /**
     * Метод для получения соединения с базой данных по умолчанию
     * @return object|string Возвращает соединение с базой данных
     */
    public function getConnectByDefault()
    {
        switch(strtoupper($this->nameDB)) {
            case "MYSQLI":
            case "MYSQL":
                return $this->getConnectMySqli();
                break;
            case "ORACLE":
            case "ORACLE_DEV":
                return $this->getConnectOracle();
                break;
            default:
                return "Нету параметров для соединения с базой данных";
        }
    }
}