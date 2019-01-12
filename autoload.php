<?php
/**
 * <table>
 * <tr>
 * <td>1</td><td>2</td>
 * </tr>
 * <tr>
 * <td>-</td><td>+</td>
 * </tr>
 * </table
 * Автрозагрузчик классов с использованием пространства имен
 *
 * @package     Main
 * @author      okibkalo
 * @version     v.1.1 21/11/2017 Модернизация PHPDoc комментариев
 * @copyright   Copyright (c) 2017, Ukrtelecom UA
 */

/**
 * Регистрация автозагрузчика классов
 */
DashboardAutoLoader::Register();

/**
 * Работа с пространством имен
 * Class DashboardAutoLoader Для загрузки классов
 *
 * @package     Main
 * @author      okibkalo
 * @version     v.1.1 21/11/2017 Модернизация PHPDoc комментариев
 * @copyright   Copyright (c) 2017, Ukrtelecom UA
 */
class DashboardAutoLoader
{
    /**
     * Для регистраций нового подключения классов
     *
     * @uses DashboardAutoLoader::loader Регистратор классов
     *
     * @version v.1.1 21/11/2017 Модернизация PHPDoc комментариев
     *
     * @return bool Успешно / Не успешно
     */
    public static function Register()
    {
        return spl_autoload_register(array('DashboardAutoLoader', 'loader'));
    }

    /**
     * Подключения файла класса
     *
     * @throws InvalidArgumentException Если передаваемый аргумент не является пространством имен
     *
     * @version v.1.1 21/11/2017 Модернизация PHPDoc комментариев
     *
     * @param string $pObjectName Объект для подключения
     *
     * @return void
     */
    public static function loader($pObjectName)
    {
        $pObjectName = str_replace('\\', '/', $pObjectName);
        $file = dirname(__FILE__) . "/" . $pObjectName . ".php";
        require_once $file;
    }
}