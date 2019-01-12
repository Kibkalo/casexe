<?php
/**
 * Оповишения пользователя mail сообщенмям
 *
 * @author okibkalo
 */

/**
 * Инициализация пространства имен
 */
namespace View\system;

/**
 * Подключение модулей
 */
require_once dirname(__FILE__) . "/../../autoload.php";

use Control\Connections;

$config = new Connections();

$limit = 10;

if(isset($_GET['limit']))
{
    $limit = $_GET['limit'];
}

$date = $config->Select("tbl_bank b
	LEFT JOIN tbl_user u on u.id = b.user_id",
    "b.id, u.login, piece",
    "comit_all is NULL LIMIT $limit");

for($i = 0; $i < count($date); $i++)
{
    echo $date[$i][1] . " - " . $date[$i][2] . "<br/>";
    $in = $config->Update("tbl_bank", "comit_all = NOW()", "id = {$date[$i][0]}");
    if($in !== true)
    {
        echo $in;
    }
}
