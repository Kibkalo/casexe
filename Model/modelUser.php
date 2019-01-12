<?php
/**
 * Шаблон сайта для пользователей
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Model;

/**
 * Подключение модулей
 */
use Classes\core\baseModel;
use Classes\Universal_session;
use Interfaces\iTemplate;
use Classes\QuickFunctions;

/**
 * Class modelUser шаблон сайта для пользователей
 * @package Model Пакет для шаблонов сайта
 */
class modelUser extends baseModel implements iTemplate
{
    /**
     * Пролог страницы
     * @param array $attachedFiles перечень подключаемых внешних файлов типа js css
     * @return string HTML-код
     */
    public function ViewStart($attachedFiles = array())
    {
        $show = "<!DOCTYPE>\n<html>\n<head>\n";
        $show .= "<title>Casexe</title>\n";
		$show .= "<meta http-equiv='content-type' content='text/html; charset=utf-8' />\n";
        $show .= "<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>\n";
        $show .= "<meta http-equiv='Cache-Control' content='no-cache'>\n";

        $attachedFiles[] = "[ROOT]/js/Bridgework.js";
        $show .= parent::attachedFiles($attachedFiles);

        $show .= "</head>\n<body>\n";
        echo $show;
    }

    /**
     * Footer страницы
     * @return string HTML-код
     */
    public function ViewEnd()
    {
        $show = "</body>\n</html>\n\n";
        echo $show;
    }
}