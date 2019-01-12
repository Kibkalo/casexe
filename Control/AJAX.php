<?php
/**
 * Переадресация ajax-запросов
 *
 * @author okibkalo
 */

/**
 * Хедеры для ajax-запросов
 */
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// дата в прошлом
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // всегда модифицируется
header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");// HTTP/1.0
header("Content-Type: text/html; charset=UTF-8");
error_reporting(0);

/**
 * Подключение модулей
 */
require_once dirname(__FILE__) . "/../autoload.php";


if (isset($_REQUEST['method']) and ($_REQUEST['method']!='')) {
    $method = $_REQUEST['method'];
    $info = $_REQUEST['info'];
    $class = "Data_Processing";
    if (isset($_REQUEST['Class'])) {
        $class = $_REQUEST['Class'];
    }
} else {
    echo 'destroy';
    exit();
}

$class = "\\Classes\\Info\\$class";

try {
    $result = new $class();
    $infoExplode = explode(",", $info);
    $arrayInfo = array();
    for ($I = 0; $I<count($infoExplode); $I++) {
        $arrayExplodeInfo = explode("=", $infoExplode[$I]);
        for ($i = 0; $i < count($arrayExplodeInfo); $i += 2) {
            $pos = strpos($arrayExplodeInfo[$i+1], "::");
            if ($pos === false) {
                $arrayInfo[$arrayExplodeInfo[$i]] = $arrayExplodeInfo[$i+1];
            } else {
                $ExplodeInfoNewArray = explode("::", $arrayExplodeInfo[$i+1]);
                $arrayInfo[$arrayExplodeInfo[$i]] = $ExplodeInfoNewArray;
            }
        }
    }

    echo $result->handling($method,$arrayInfo);
} catch (Exception $e) {
    echo $e->getMessage();
}