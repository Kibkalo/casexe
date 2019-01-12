<?php
/**
 * Для построения HTML - кода
 *
 * @author      okibkalo
 * @version     v.1 (27/12/16)
 * @copyright   Copyright (c) 2017
 */

/**
 * Инициализация пространства имен
 */
namespace Classes\core;

/**
 * Class baseHelper Для построения HTML - кода
 * @package Classes\core базовый пакет для хелпера и логирования
 */
class baseHelper
{
    const Style = "style";
    const Classes = "class";
    const Id = "id";
    const Colspan = "colspan";
    const OnClick = "onclick";
    const Type = "type";
    const Value = "value";
    const Href = "href";
    const Rowspan = "rowspan";
    const Title = "title";
    const Src = "src";
    const Height = "height";
    const Name = "name";
    const Checkbox = "checkbox";
    const Radio = "radio";

    const DisplayNone = "display: none;";

    public static function split()
    {
        return baseHelper::getObject('div', '', baseHelper::getAttributeArray(array("id" => "split")));
    }

    public static function getBody($body, $attribute = array())
    {
        return baseHelper::getObject('body', $body, baseHelper::getAttributeArray($attribute));
    }

    public static function getScript($body, $attribute = array())
    {
        return baseHelper::getObject('script', $body, baseHelper::getAttributeArray($attribute));
    }

    public static function getLinkHead($attribute = array())
    {
        return baseHelper::getObjectOneLink('link', baseHelper::getAttributeArray($attribute));
    }

    public static function getMeta($attribute = array())
    {
        return baseHelper::getObjectOneLink('meta', baseHelper::getAttributeArray($attribute));
    }

    public static function getTitle($body)
    {
        return baseHelper::getObject('title', $body, '');
    }

    public static function getHead($body)
    {
        return baseHelper::getObject('head', $body, '');
    }

    public static function getDoctypeHtml($body)
    {
        $result = "<!DOCTYPE html>";
        $result .= $body;
        $result .= "</html>";
        return $result;
    }

    public static function getSelect($arr, $select = null, $attribute = array())
    {
        $result = "";
        for($i = 0; $i < count($arr); $i++){
            $selected = null;
            if ($select == $arr[$i][0]) {
                $selected = "selected";
            }

            $result .= baseHelper::getObject("option", $arr[$i][1],
                baseHelper::getAttributeArray(array("id" => $i, "value" => $arr[$i][0],
                    "style" => "text-align: left;", "selected" => $selected)));
        }
        $obj = baseHelper::getObject("select", $result, baseHelper::getAttributeArray($attribute));
        return $obj;
    }

    /**
     * Формирование HTML-кода объекта ChosenSelect
     * для библиотеки jquery Chosen
     * @param array $arr данные для заполнения Select
     * @param int $level номер элемента по-умолчанию
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getChosenSelect($arr, $level, $attribute = array())
    {
        $text = "Вибір";
        if (isset($attribute['text']))
        {
            $text = $attribute['text'];
            unset($attribute['text']);
        }

        $obj = baseHelper::getObject("option", "", baseHelper::getAttributeArray(array("id" => 0, "value" => "")));
        $obj .= baseHelper::getObject("option", $text, baseHelper::getAttributeArray(array("id" => -1, "value" => -1)));
        for ($i = 0; $i < count($arr); $i++)
        {
            $selected = null;
            if ($level == $arr[$i][0]) {
                $selected = "selected";
            }
            $obj .= baseHelper::getObject("option", $arr[$i][1], baseHelper::getAttributeArray(array("id" => $i,
                "value" => $arr[$i][0], "selected" => $selected, "style" => "text-align: left;")));
        }
        $obj = baseHelper::getObject("select", $obj, baseHelper::getAttributeArray($attribute));
        return $obj;
    }

    /**
     * Формирование HTML-кода объекта Label
     * @param string $body содержимое контейнера
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getLabel($body, $attribute = array())
    {
        return baseHelper::getObject('label', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта BR
     * @return string HTML-код
     */
    public static function getBR()
    {
        return baseHelper::getObjectOneLink('br', "");
    }

    /**
     * Формирование HTML-кода объекта Button
     * @param string $body текст кнопки
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getButton($body, $attribute = array())
    {
        return baseHelper::getObject('button', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта img
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getImage($attribute = array())
    {
        return baseHelper::getObjectOneLink('img', baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта input
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getInput($attribute = array())
    {
        return baseHelper::getObjectOneLink('input', baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта textarea
     * @param string $body текст объекта
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getTextArea($body, $attribute = array())
    {
        return baseHelper::getObject('textarea', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта center
     * @param string $body текст объекта center
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getCenter($body, $attribute = array())
    {
        return baseHelper::getObject('center', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода table
     * (обрамление готового кода строк таблицы тегами table)
     * @param string $body содержимое таблицы в виде <tr> и <td>
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @param bool $center true -центрирование таблицы
     * @return string HTML-код
     */
    public static function getTable($body, $attribute = array(), $center = false)
    {
        if ($center)
        {
            return baseHelper::getCenter(baseHelper::getObject('table', $body,
                baseHelper::getAttributeArray($attribute)));
        } else {
            return baseHelper::getObject('table', $body, baseHelper::getAttributeArray($attribute));
        }
    }

    /**
     * Формирование HTML-кода объекта div
     * @param string $body содержимое контейнера
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getDiv($body, $attribute = array())
    {
        return baseHelper::getObject('div', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта span
     * @param string $body содержимое контейнера
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getSpan($body, $attribute = array())
    {
        return baseHelper::getObject('span', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта tr
     * @param string $body содержимое строки в виде <th> и <td>
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getTableRow($body, $attribute = array())
    {
        return baseHelper::getObject('tr', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта td
     * @param string $body содержимое ячейки
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getTableData($body, $attribute = array())
    {
        return baseHelper::getObject('td', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * Формирование HTML-кода объекта гиперссылки
     * @param string $body отображаемое содержимое ссылки
     * @param array $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    public static function getLink($body, $attribute = array())
    {
        return baseHelper::getObject('a', $body, baseHelper::getAttributeArray($attribute));
    }

    /**
     * формирования строки атрибутов из переданного массива
     * @param array $ar атрибуты объекта в виде ассоциативного массива, где ключ - это название атрибута
     * @return string строка атрибутов для HTML-кода
     */
    private static function getAttributeArray($ar)
    {
        $obj = "";
        if (is_array($ar)){
            foreach($ar as $key => $value)
            {
                $obj .= baseHelper::getAttribute($key, $value);
            }
        }
        return $obj;
    }

    /**
     * Слияние переменных в одну строку для атрибутов HTML-кода
     * @param string $name название атрибута
     * @param string $value значение атрибута
     * @return string 1 елемент строки атрибутов
     */
    private static function getAttribute($name, $value)
    {
        $obj = "";
        if (isset($value) && !is_array($value) && $value != "")
        {
            $obj .= "{$name}='{$value}'";
        }
        return $obj;
    }

    /**
     * Формирование HTML-кода для вложенных объектов
     * @param string $name название тега объекта
     * @param string $body содержимое объекта
     * @param string $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    private static function getObject($name, $body, $attribute)
    {
        $obj = "";
        $obj .= "<{$name} {$attribute}>";
        $obj .= $body;
        $obj .= "</{$name}>";
        return $obj;
    }

    /**
     * Формирование HTML-кода для самозакрывающихся тегов
     * @param string $name название тега объекта
     * @param string $attribute атрибуты HTML-кода объекта (для стилей и идентификации объекта)
     * @return string HTML-код
     */
    private static function getObjectOneLink($name, $attribute)
    {
        $obj = "";
        $obj .= "<{$name} {$attribute}/>";
        return $obj;
    }
}