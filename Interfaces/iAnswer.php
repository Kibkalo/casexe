<?php
/**
 * Ответы для ajax-запросов
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace Interfaces;

/**
 * Interface iAnswer ответы для ajax-запросов
 * @package Interfaces Пакет интерфейсов
 */
interface iAnswer
{
    /**
     * Вызов метода класса
     * в случае отсутствия метода - строка с ошибкой - "Отсутствие метода в классе"
     * @param string $method название функции
     * @param array $info параметры, вызываемой функции
     * @return mixed результат выполнения функции
     */
    public function handling($method, $info);
}