<?php
/**
 * Перечень обязательных методов для работы с БД
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Interfaces;

/**
 * Interface iDateBase перечень обязательных методов для работы с БД
 * @package Interfaces Пакет интерфейсов
 */
interface iDateBase
{
    /**
     * Вызывает хранимую процедуру в БД
     * @param string $name название процедуры
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function CallingFunctions($name);

    /**
     * Формирование и выполнение sql- запроса update
     * @param string $Name_table название таблицы
     * @param string $set строка для изменения данных, разделитель ","
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Update($Name_table, $set, $where);

    /**
     * Формирование и выполнение sql- запроса Insert
     * @param string $Name_table название таблицы
     * @param string $name - перечень полей, разделитель ","
     * @param string $value - перечень значений, разделитель ","
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Insert($Name_table, $name, $value);

    /**
     * Формирование и выполнение sql- запроса Insert с вложенным Select
     * @param string $Name_table название таблицы
     * @param string $select - строка вложенного селекта
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertSelect($Name_table, $select);

    /**
     * Формирование и выполнение sql- запроса Insert типа ON DUPLICATE KEY UPDATE
     * @param string $Name_table название таблицы
     * @param string $name перечень полей, разделитель ","
     * @param string $value перечень значений, разделитель ","
     * @param string|array $update строка или массив полей для ON DUPLICATE KEY UPDATE
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertKeyUpdate($Name_table, $name, $value, $update);

    /**
     * Формирование и выполнение sql- запроса Delete
     * @param string $Name_table название таблицы
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Delete($Name_table, $where);

    /**
     * Формирование и выполнение sql- запроса Select
     * @param string $Name_table название таблицы
     * @param string $select перечень полей, разделитель ","
     * @param string $where условие
     * @param string $order_by перечень полей для сортировки, разделитель ","
     * @return array|string array - выполнена без ошибок с результатом выполнения,
     * string - сообщение об ошибке
     */
    public function Select($Name_table, $select, $where = "", $order_by = "");

    /**
     * Получение sql последнего выполненного запроса
     * @return string последний запущенный sql- запрос к БД
     */
    public function GetLastSQL();

    /**
     * Получение перечня названий полей последнего выполненного sql- запроса
     * @return array|null перечень названий полей последнего выполненного sql- запроса
     */
    public function GetFetchField();

    /**
     * Получение id последней измененной или вставленной строки
     * @return int|null id строки в таблице
     */
    public function GetLastIdInsertRow();

    /**
     * Получение количества измененніх строк в последнем sql- запрос на изменение, добавлени и удаление
     * @return int|null количество измененных строк
     */
    public function GetAffectedRows();

    public function GroupConcatMaxLen();

    public function CreateIndex($table, $field);

    public function Query($sql);
}
