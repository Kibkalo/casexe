<?php
/**
 * Разружения сессий
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace View;

/**
 * Подключение модулей
 */
require_once dirname(__FILE__) . "/../autoload.php";

use Classes\Universal_session;

setcookie('user', '', time() - 30, "/");
setcookie('agent', '', time() - 30, "/");
$session = new Universal_session();
$session->DestroySession();

header("Location: ../");