<?php
/**
 * Главная страница проекта с всеми показателями
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

use Control\Connections;
use Control\Template;
use Classes\Universal_session;
use Classes\core\baseHelper;


$session = new Universal_session();
$config = new Connections();
$html = new Template();

$user_id = $session->getSession("user_id");

$html->ViewStart(array("[ROOT]/js/main.js"));

$date = $config->Select("tbl_user_item ui
	LEFT JOIN tbl_item i on i.id = ui.item_id",
    "i.`name`, ui.amount, i.id, i.type_id, i.piece",
    "ui.user_id = $user_id");

$show = "";
$show_tr = "";
for($i = 0; $i < count($date); $i++)
{
    $show_td = baseHelper::getTableData($date[$i][0]);
    $show_td .= baseHelper::getTableData($date[$i][1]);

    if($date[$i][3] == 3){
        $show_td .= baseHelper::getTableData($date[$i][4]);
        $show_td .= baseHelper::getTableData(
            baseHelper::getLink("Продать",
            array(baseHelper::Id => $date[$i][2],
                baseHelper::Style => "cursor: pointer;",
                baseHelper::OnClick => "sell(this)")));
    } else {
        $show_td .= baseHelper::getTableData("");
        $show_td .= baseHelper::getTableData("");
    }
    $show_tr .= baseHelper::getTableRow($show_td);
}

$show .= baseHelper::getDiv(baseHelper::getTable($show_tr), array(baseHelper::Id => "block"));

$game = $config->Select("tbl_game g
	LEFT JOIN sys_type t on g.type_id = t.id",
    "g.id, g.`name`, t.`name`, g.piece");

for($i = 0; $i < count($game); $i++)
{
    $show .= baseHelper::getLink("{$game[$i][1]} ({$game[$i][2]} - {$game[$i][3]})",
        array(baseHelper::Id => $game[$i][0],
            baseHelper::Style => "cursor: pointer;",
            baseHelper::OnClick => "game(this)"));
}

$show .= baseHelper::getDiv("", array(baseHelper::Id => "showDiv"));

$show .= baseHelper::getDiv("Перевод деняг в бонусы" . baseHelper::getBR() .
    baseHelper::getInput(array("type" => "text", "id" => "2_in", "value" => "0")).
    baseHelper::getLink(" Перевод",
        array(baseHelper::Id => 2,
            baseHelper::Style => "cursor: pointer;",
            baseHelper::OnClick => "trans(this)"))
);


$show .= baseHelper::getDiv("Перевод бонус в денягы " . baseHelper::getBR() .
    baseHelper::getInput(array("type" => "text", "id" => "1_in", "value" => "0")) .
    baseHelper::getLink(" Перевод",
        array(baseHelper::Id => 1,
            baseHelper::Style => "cursor: pointer;",
            baseHelper::OnClick => "trans(this)"))
);

$show .= baseHelper::getDiv("", array(baseHelper::Id => "showTrans"));

$show .= baseHelper::getDiv("Вывод деняг  " . baseHelper::getBR() .
    baseHelper::getInput(array("type" => "text", "id" => "piece", "value" => "0")) .
    baseHelper::getLink(" Вывод",
        array(baseHelper::Id => 1,
            baseHelper::Style => "cursor: pointer;",
            baseHelper::OnClick => "bank(this)"))
);

$show .= baseHelper::getDiv("", array(baseHelper::Id => "showPiece"));

echo $show;

$html->ViewEnd();
