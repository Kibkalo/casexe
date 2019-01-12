<?php
/**
 * Работа с сессией
 *
 * @author okibkalo
 * Date: 04.04.14
 * Time: 9:38
 */

/**
 * Инициализация пространства имен
 */
namespace Classes;

/**
 * Class Universal_session Работа с сессией
 * @package Classes Пакет общих классов проекта
 */
class Universal_session
{
    /**
     * @var array Все ключи сессий
     */
    private $key = array();

    /**
     * Конструктор
     * Открытие сессий
     * @param string $id Уникальний индификатор уже открытой сессии
     * @filesource
     */
    public function __construct($id = null)
    {
        if(isset($id)){
            session_id($id);
        }
        if(!isset($_SESSION)) {
           session_start();
        }
        if($this->getIsSetSession()){
            $this->key = $_SESSION['Key'];
        }
    }

    /**
     * Проверка на открытую сессию
     * @return bool true - Открыта, false- Закрыта
     */
    public function getIsSetSession()
    {
        if(isset($_SESSION['number'])){
            return true;
        }
        return false;
    }

    /**
     * Добавить в сессию новое значение
     * @param string $key Ключ
     * @param string $value Значения
     */
    public function setSession($key, $value)
    {
		if (!isset($_SESSION["number"])) 
		{
			$_SESSION["number"] = 0;
		}
        $_SESSION["number"] = $_SESSION["number"]+1;
        array_push($this->key,$key);
        $_SESSION['Key'] = $this->key;
        $_SESSION[$key] = $value;
    }

    /**
     * Получить значения из сессии
     * @param string $key Ключ
     * @return string Значение
     */
    public function getSession($key)
    {
        if (isset($_SESSION[$key])){
        	return $_SESSION[$key];
        }else{
        	return null;
        }
    }

    /**
     * Получить все ключи из сессии
     * @return array Ключи
     */
    public function getAllSession()
    {
        return $this->key;
    }

    /**
     * Печать всей сессии в виде
     * Ключ = Значение
     */
    public function ShowAllSession()
    {
        for($i=0;$i<count($this->key);$i++){
            echo $this->key[$i]." = ".$_SESSION[$this->key[$i]]."<br/>";
        }
    }

    /**
     * Удаление открытой сессии
     */
    public function DestroySession()
    {
        unset($_SESSION['number']);
        unset($_SESSION['Key']);
        for($i=0;$i<count($this->key);$i++){
            unset($_SESSION[$this->key[$i]]);
        }
    }

    /**
     * Переадресация, если сессия открыта
     * @param string $indexPage Ссылка для перехода в случае закрытой сессии
     */
    public function AutoUserCheck($indexPage)
    {
        if (count($this->key) < 1)
        {
            header("Location: $indexPage");
        }
    }
}