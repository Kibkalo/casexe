<?php
/**
 * Работа с БД
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Control;

/**
 * Подключение модулей
 */
use Interfaces\iDateBase;
use Classes\Settings;

/**
 * Class Connections работа с БД
 * @package Control Пакет паттерных классов (типа мост)
 */
class Connections implements iDateBase
{
    /**
     * @var object класс работы с БД
     */
    public $Class_Data_Base = null;
    /**
     * @var null|string название БД
     */
    private $nameDateBase = null;
    /**
     * @var null|string тип БД (Mysqli/Oracle)
     */
    private $db = null;

    /**
     * Создание объекта класса соединения с БД
     * @param string $db тип БД (Mysqli/Oracle)
     */
    public function __construct($db = "Mysqli")
    {
        $this->db = $db;
        $Sett = new Settings($db);
        $Connect = $Sett->getConnectByDefault();
		$classes = $Sett->getNameServer();
        $this->nameDateBase = $Sett->getNameDateBase();
		$this->Class_Data_Base = new $classes($Connect);
    }


    public function GroupConcatMaxLen()
    {
        return $this->Class_Data_Base->GroupConcatMaxLen();
    }

    public function CreateIndex($table, $field)
    {
        return $this->Class_Data_Base->CreateIndex($table, $field);
    }

    /**
     * Получение название активной БД
     * @return null|string название активной БД
     */
    public function getNameDateBase()
    {
        return $this->nameDateBase;
    }

    /**
     * Вызывает хранимую процедуру в БД
     * @param string $name название процедуры
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function CallingFunctions($name){
        return $this->Class_Data_Base->CallingFunctions($name);
    }

    /**
     * Формирование и выполнение sql- запроса update
     * @param string $Name_table название таблицы
     * @param string $set строка для изменения данных, разделитель ","
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Update($Name_table, $set, $where){
        return $this->Class_Data_Base->Update($Name_table, $set, $where);
    }

    /**
     * Формирование и выполнение sql- запроса Insert
     * @param string $Name_table название таблицы
     * @param string $name - перечень полей, разделитель ","
     * @param string $value - перечень значений, разделитель ","
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Insert($Name_table, $name, $value){
        return $this->Class_Data_Base->Insert($Name_table, $name, $value);
    }

    /**
     * Формирование и выполнение sql- запроса Insert с вложенным Select
     * @param string $Name_table название таблицы
     * @param string $select - строка вложенного селекта
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertSelect($Name_table, $select){
        return $this->Class_Data_Base->InsertSelect($Name_table, $select);
    }

    /**
     * Формирование и выполнение sql- запроса Delete
     * @param string $Name_table название таблицы
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Delete($Name_table, $where){
        return $this->Class_Data_Base->Delete($Name_table, $where);
    }

    /**
     * Формирование и выполнение sql- запроса Select
     * @param string $Name_table название таблицы
     * @param string $select перечень полей, разделитель ","
     * @param string $where условие
     * @param string $order_by перечень полей для сортировки, разделитель ","
     * @return array|string array - выполнена без ошибок с результатом выполнения,
     * string - сообщение об ошибке
     */
    public function Select($Name_table, $select, $where = "", $order_by = ""){
        return $this->Class_Data_Base->Select($Name_table, $select, $where, $order_by);
    }

    /**
     * Получение sql последнего выполненного запроса
     * @return string последний запущенный sql- запрос к БД
     */
    public function GetLastSQL(){
        return $this->Class_Data_Base->GetLastSQL();
    }

    /**
     * Формирование и выполнение sql- запроса Insert типа ON DUPLICATE KEY UPDATE
     * @param string $Name_table название таблицы
     * @param string $name перечень полей, разделитель ","
     * @param string $value перечень значений, разделитель ","
     * @param string|array $update строка или массив полей для ON DUPLICATE KEY UPDATE
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertKeyUpdate($Name_table, $name, $value, $update){
        return $this->Class_Data_Base->InsertKeyUpdate($Name_table, $name, $value, $update);
    }

    /**
     * Получение перечня названий полей последнего выполненного sql- запроса
     * @return array|null перечень названий полей последнего выполненного sql- запроса
     */
    public function GetFetchField(){
        return $this->Class_Data_Base->GetFetchField();
    }

    /**
     * Получение id последней измененной или вставленной строки
     * @return int|null id строки в таблице
     */
    public function GetLastIdInsertRow()
    {
        return $this->Class_Data_Base->GetLastIdInsertRow();
    }

    /**
     * Получение количества измененніх строк в последнем sql- запрос на изменение, добавлени и удаление
     * @return int|null количество измененных строк
     */
    public function GetAffectedRows()
    {
        return $this->Class_Data_Base->GetAffectedRows();
    }

    public function Query($sql)
    {
        return $this->Class_Data_Base->Query($sql);
    }

}