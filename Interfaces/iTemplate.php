<?php
/**
 * Интерфейс построения шаблона сайта
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Interfaces;

/**
 * Interface iTemplate интерфейс построения шаблона сайта
 * @package Interfaces Пакет интерфейсов
 */
interface iTemplate
{
    /**
     * Пролог страницы
     * @param array $attachedFiles перечень подключаемых внешних файлов типа js css
     * @return string HTML-код
     */
    public function ViewStart($attachedFiles = array());

    /**
     * Footer страницы
     * @return string HTML-код
     */
    public function ViewEnd();

}