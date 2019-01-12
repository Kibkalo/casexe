<?php
/**
 * Страница авторизаций
 *
 * @author okibkalo
 */

/**
 * Подключение модулей
 */
require_once dirname(__FILE__) . "/autoload.php";

use Classes\Universal_session;
use Classes\core\baseHelper;

$session = new Universal_session();
$login = $session->getSession("login");

if(isset($login))
{
    header("location: View/main.php");
}

$show = baseHelper::getDoctypeHtml(
    baseHelper::getHead(
        baseHelper::getTitle("Casexe") .
        baseHelper::getMeta(array("charset" => "utf-8")) .
        baseHelper::getMeta(array("http-equiv" => "X-UA-Compatible", "content" => "IE=edge,chrome=1")) .
        baseHelper::getLinkHead(array("rel" => "stylesheet", "href" => "css/reset_menu.css")) .
        baseHelper::getLinkHead(array("rel" => "stylesheet", "href" => "css/animate.css")) .
        baseHelper::getScript("", array("src" => "js/Bridgework.js", "language" => "JavaScript")) .
        baseHelper::getScript("", array("src" => "js/index.js", "language" => "JavaScript"))
    ) .
    baseHelper::getBody(
        baseHelper::getDiv("", array("class" => "vladmaxi-top", "id" => "message", "style" => "text-align: center")) .
        baseHelper::getDiv("Casexe", array("class" => "dash", "style" => "font-size: 32px;")) .
        baseHelper::getDiv(
            baseHelper::getDiv(
                baseHelper::getLabel("Логин:", array("for" => "name")) .
                baseHelper::getInput(array("type" => "name", "id" => "login", "value" => "")) .
                baseHelper::getLabel("Пароль:", array("for" => "username")) .
                baseHelper::getInput(array("type" => "password", "id" => "password", "onkeyup" => "enter()",
                    "value" => "")) .
                baseHelper::getDiv(
                    baseHelper::getInput(array("type" => "button", "onclick" => "Login();", "value" => "Войти")),
                    array("id" => "lower")
                ),
                array("class" => "form")),
            array("id" => "container"))
    )
);
echo $show;
?>