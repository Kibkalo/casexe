<?php
/**
 * Мост формирования шаблона сайта
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
use Interfaces\iTemplate;

/**
 * Class Template мост формирования шаблона сайта
 * @package Control Пакет паттерных классов (типа мост)
 */
class Template implements iTemplate
{
    /**
     * @var object объект шаблона сайта
     */
    private $view = null;

    /**
     * Создание объекта шаблона сайта
     * @param string $style название модуля с шаблоном сайта
     */
    public function __construct($style = "\\Model\\modelUser"){
        if($style == null)
        {
            $style = "\\Model\\modelUser";
        }

		$this->view = new $style();
    }

    /**
     * Пролог страницы
     * @param array $attachedFiles перечень подключаемых внешних файлов типа js css
     * @return string HTML-код
     */
    public function ViewStart($attachedFiles = array()){
        return $this->view->ViewStart($attachedFiles);
    }

    /**
     * Footer страницы
     * @return string HTML-код
     */
    public function ViewEnd(){
        return $this->view->ViewEnd();
    }

    /**
     * Получение HTML-кода меню страницы
     * @return string HTML-код
     */
    public function ViewMenu(){
        return $this->view->ViewMenu();
    }
} 