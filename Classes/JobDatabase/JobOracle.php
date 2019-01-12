<?php
/**
 * Работа с Oracle
 *
 * @author okibkalo
 */
namespace Classes\JobDatabase;

use Interfaces\iDateBase;

/**
 * Class JobOracle Работа с Oracle
 * @package Classes\JobDatabase Пакет для работы с БД
 */
class JobOracle implements iDateBase
{
    /**
     * @var object Соеденения с БД
     */
    private $connect = null;
    /**
     * @var string последний запущенный sql- запрос к БД
     */
    private $lastSql = null;
    /**
     * @var array|null перечень названий полей последнего выполненного sql- запроса
     */
    private $fetchField = null;
    /**
     * @var int|null id строки в таблице
     */
    private $lastInsertId = null;
    /**
     * @var int|null количество измененных строк
     */
    private $affectedRows = null;

    /**
     * Конструктор
     * @param object $Connect Соеденения с БД
     */
    public function __construct($Connect)
    {
        $this->connect = $Connect;
    }

    /**
     * Вызывает хранимую процедуру в БД
     * @param string $name название процедуры
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function CallingFunctions($name)
    {

        $sql = "BEGIN $name; END;";

        $query = oci_parse($this->connect, $sql);
        oci_execute($query);

        $this->lastSql = $sql;
        if (!$query) {
            $error = oci_error();
            return "Error: " . $error;
        }
        return TRUE;
    }

    public function GroupConcatMaxLen()
    {
        return false;
    }

    public function CreateIndex($table, $field)
    {
        return false;
    }

    /**
     * Формирование и выполнение sql- запроса update
     * @param string $Name_table название таблицы
     * @param string $set строка для изменения данных, разделитель ","
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Update($Name_table, $set, $where)
    {
        $sql = "UPDATE $Name_table SET $set WHERE $where;";
        $this->lastSql = $sql;
        $query = $this->connect->query($sql);
        $this->affectedRows = $this->connect->affected_rows;
        if (!$query) {
            return "Error: " . $this->connect->error;
        }
        return TRUE;
    }

    /**
     * Формирование и выполнение sql- запроса Insert с вложенным Select
     * @param string $Name_table название таблицы
     * @param string $select - строка вложенного селекта
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertSelect($Name_table, $select)
    {
        $sql = "INSERT INTO $Name_table $select;";
        $this->lastSql = $sql;
        $query = $this->connect->query($sql);
        $this->affectedRows = $this->connect->affected_rows;
        if (!$query) {
            return "Error: " . $this->connect->error;
        }
        return TRUE;
    }

    /**
     * Формирование и выполнение sql- запроса Insert
     * @param string $Name_table название таблицы
     * @param string $name - перечень полей, разделитель ","
     * @param string $value - перечень значений, разделитель ","
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Insert($Name_table, $name, $value)
    {
        $sql = "INSERT INTO $Name_table ($name) values($value)";

        $query = oci_parse($this->connect, $sql);
        oci_execute($query);

        $this->lastSql = $sql;
        if (!$query) {
            $error = oci_error();
            return "Error: " . $error;
        }
        return TRUE;
        /*
        $sql = "INSERT INTO $Name_table ($name) values($value);";
        $this->lastSql = $sql;
        $query = $this->connect->query($sql);
        $this->lastInsertId = $this->connect->insert_id;
        if (!$query) {
            return "Error: " . $this->connect->error;
        }
        return TRUE;*/
    }

    /**
     * Формирование и выполнение sql- запроса Insert типа ON DUPLICATE KEY UPDATE
     * @param string $Name_table название таблицы
     * @param string $name перечень полей, разделитель ","
     * @param string $value перечень значений, разделитель ","
     * @param string|array $update строка или массив полей для ON DUPLICATE KEY UPDATE
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function InsertKeyUpdate($Name_table, $name, $value, $update)
    {
        $sql = "";
        if (is_array($update)) {
            $sql .= "INSERT INTO $Name_table ($name) values($value) ON DUPLICATE KEY UPDATE ";
            for ($i = 0;$i < count($update); $i++) {
                $sql .= "{$update[$i]} = VALUES({$update[$i]})";
                if (count($update)-1 > $i) {
                    $sql .= ",";
                } else {
                    $sql .= ";";
                }
            }
        } else {
            $sql .= "INSERT INTO $Name_table ($name) values($value) ON DUPLICATE KEY UPDATE $update;";
        }

        $this->lastSql = $sql;
        $query = $this->connect->query($sql);
        $this->lastInsertId = $this->connect->insert_id;
        $this->affectedRows = $this->connect->affected_rows;
        if (!$query) {
            return "Error: " . $this->connect->error;
        }
        return TRUE;
    }

    /**
     * Формирование и выполнение sql- запроса Delete
     * @param string $Name_table название таблицы
     * @param string $where условие
     * @return bool|string true - выполнена без ошибок, string - сообщение об ошибке
     */
    public function Delete($Name_table, $where)
    {
        $sql = "DELETE FROM $Name_table WHERE $where";

        $query = oci_parse($this->connect, $sql);
        oci_execute($query);

        $this->lastSql = $sql;
        if (!$query) {
            $error = oci_error();
            return "Error: " . $error;
        }
        return TRUE;/*
        $sql = "DELETE FROM $Name_table WHERE $where;";
        $this->lastSql = $sql;
        $query = $this->connect->query($sql);
        $this->affectedRows = $this->connect->affected_rows;
        if (!$query) {
            return "Error: " . $this->connect->error;
        }
        return TRUE;*/
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
    public function Select($Name_table, $select, $where = "", $order_by = "")
    {
        $this->fetchField = null;
        $return_array = array();

        if ($where!="") {
            $sql = "SELECT $select FROM $Name_table WHERE $where $order_by";
        } else {
            $sql = "SELECT $select FROM $Name_table $order_by";
        }
        $this->lastSql = $sql;

        $sql = oci_parse($this->connect, $sql);
        oci_execute($sql);

        if (!$sql) {
            $error = oci_error();
            return "Error: " . $error['message'];
        } else {
            $num_fields = oci_num_fields($sql);
            for ($i = 1; $i <= $num_fields; $i++) {
                $this->fetchField[]  = oci_field_name($sql, $i);
            }

            while ($row = oci_fetch_array($sql, OCI_NUM)) {
                array_push($return_array, $row);
            }
        }
        /*
        if ($result = $this->connect->query($sql)) {
            $I = 0;
            $return_array = array();
            while ($call = $result->fetch_field()) {
                $this->fetchField[] = $call->name;
            }
            while ($call = $result->fetch_array(MYSQL_NUM)) {
                $tmp_array = array();
                for ($i = 0;$i < count($call); $i++) {
                    array_push($tmp_array, $call[$i]);
                }
                $return_array[$I] = $tmp_array;
                $I++;
            }
        } else {
            return "Error: " . $this->connect->error;
        }*/

        return $return_array;
    }

    /**
     * Получение sql последнего выполненного запроса
     * @return string последний запущенный sql- запрос к БД
     */
    public function GetLastSQL()
    {
        return $this->lastSql;
    }

    /**
     * Получение перечня названий полей последнего выполненного sql- запроса
     * @return array|null перечень названий полей последнего выполненного sql- запроса
     */
    public function GetFetchField()
    {
        return $this->fetchField;
    }

    /**
     * Получение id последней измененной или вставленной строки
     * @return int|null id строки в таблице
     */
    public function GetLastIdInsertRow()
    {
        return $this->lastInsertId;
    }

    /**
     * Получение количества измененніх строк в последнем sql- запрос на изменение, добавлени и удаление
     * @return int|null количество измененных строк
     */
    public function GetAffectedRows()
    {
        return $this->affectedRows;
    }

    public function Query($sql)
    {
        $this->lastSql = $sql;

        $sql = oci_parse($this->connect, $sql);
        oci_execute($sql);

        if (!$sql) {
            $error = oci_error();
            return "Error: " . $error['message'];
        }
        return TRUE;
    }
}