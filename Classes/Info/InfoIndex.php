<?php
/**
 * Авторизация пользователя и тип данных для отображений
 *
 * @author okibkalo
 * Date: 21.08.14
 * Time: 13:36
 */

/**
 * Инициализация пространства имен
 */
namespace Classes\Info;

/**
 * Подключение модулей
 */
use Interfaces\iAnswer;
use Classes\core\baseInfo;
use Control\Connections;
use Classes\QuickFunctions;
use Classes\Universal_session;
use Classes\core\baseHelper;

/**
 * Class InfoIndex Авторизация пользователя и тип данных для отображений
 * @package Classes\Info Пакет для ответов ajax-запросов
 */
class InfoIndex extends baseInfo implements iAnswer
{
    /**
     * Вызов метода класса
     * в случае отсутствия метода - строка с ошибкой - "Отсутствие метода в классе"
     * @param string $method название функции
     * @param array $info параметры, вызываемой функции
     * @return mixed результат выполнения функции
     */
    public function Handling($method, $info)
    {
        if (method_exists($this, $method)) {
            return $this->$method($info);
        } else {
            return "Необходимо реализовать метод [$method]";
        }
    }

    /**
     * Проверка авторизации пользователя
     * @param array $info входные параметры
     * login - Логин пользователя
     * password - Пароль пользователя
     * @return string Ощибка авторизаций или 1992 - код подтверждения
     */
    private function Login($info)
    {
        $info['password'] = str_replace("[KEPA]", ",", $info['password']);
        $info['password'] = str_replace("[POW]", "=", $info['password']);
        $info['password'] = str_replace("[TOP]", "'", $info['password']);

        $config = new Connections();

        $info['password'] = md5($info['password']);

        $user = $config->Select("tbl_user", "login, password",
            " login = \"{$info['login']}\" and password = \"{$info['password']}\"");

        if(count($user) == 0)
        {
            $i = $config->Insert("tbl_user", "login, password", "\"{$info['login']}\", \"{$info['password']}\"");
            if($i !== true)
            {
                return $i;
            } else {
                $id = $config->GetLastIdInsertRow();
                $i = $config->Insert("tbl_user_item", "user_id, item_id, amount", "$id, 2, 500");
                if($i !== true)
                {
                    return $i;
                }
            }
        }

        return QuickFunctions::FillingSessions($info['login']);
    }

    private function setGame($info)
    {
        $config = new Connections();
        $session = new Universal_session();
        $user_id = $session->getSession("user_id");

        $date = $config->Select("tbl_user_item i
                LEFT JOIN tbl_item ti on ti.id = i.item_id
                LEFT JOIN tbl_game g on g.type_id = ti.type_id",
            "i.amount, g.piece, if(i.amount >= g.piece, 1, 0), i.item_id",
            "i.user_id =  $user_id and g.id = {$info['id']}");

        $piece = $date[0][1];
        $type_id = $date[0][3];
        if($date[0][2] == 0)
        {
            return "Нету Бонусы";
        }

        $date = $config->Select("tbl_game_drop d,
            sys_item_amount a
            LEFT JOIN tbl_item i on i.id = a.item_id",
            "i.id, i.`name`, d.min, d.max",
            "d.game_id = {$info['id']} and a.item_id = d.item_id and a.amount > 0");

        $rand = rand(0, count($date) - 1);

        $date = $date[$rand];

        $rand = rand($date[2], $date[3]);

        $u = $config->Update("tbl_user_item", "amount = amount - $piece", "item_id = $type_id");

        if($u === true)
        {
            $u = $config->Update("sys_item_amount", "amount = amount - $rand", "item_id = {$date[0]}");

            if($u === true)
            {
                $amount = $config->Select("tbl_user_item i",
                    "i.amount",
                    "i.user_id = $user_id and i.item_id = {$date[0]}");
                $amount = $amount[0][0];

                $amount += $rand;
                $i = $config->InsertKeyUpdate("tbl_user_item", "user_id, item_id, amount",
                    "$user_id, {$date[0]}, $amount",
                array('amount'));
            }
        }

        echo $date[1] . " = " . $rand;
    }

    private function getBlock($info)
    {
        $session = new Universal_session();
        $config = new Connections();
        $user_id = $session->getSession("user_id");

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

        return $show;
    }

    private function trans($info)
    {
        $session = new Universal_session();
        $config = new Connections();
        $user_id = $session->getSession("user_id");

        $date = $config->Select("tbl_user_item ui
	        LEFT JOIN tbl_transfer t on t.in_type_id = ui.item_id",
            "ui.amount, if(ui.amount >= {$info['in']}, 1, 0), {$info['in']} * t.amount, ui.amount - {$info['in']}, t.to_type_id, t.in_type_id",
            "ui.user_id = $user_id and ui.item_id = {$info['id']}");

        $piece = $date[0][2];
        $piece_add = $date[0][3];
        $type_id_to = $date[0][4];
        $type_id_in = $date[0][5];
        $good = $date[0][1];
        if($good == 0)
        {
            return "Нету средств";
        }

        $u = $config->Update("tbl_user_item", "amount = $piece_add",
            "user_id = $user_id and item_id = $type_id_in");

        if($u === true)
        {
            $u = $config->Update("tbl_user_item", "amount = amount + $piece",
                "user_id = $user_id and item_id = $type_id_to");
            if($u === true)
            {
                return 1;
            }
        }
    }

    private function sell($info)
    {
        $session = new Universal_session();
        $config = new Connections();
        $user_id = $session->getSession("user_id");

        $u = $config->Update("sys_item_amount", "amount = amount + 1",
            "item_id = {$info['id']}");

        if($u === true)
        {
            $u = $config->Update("tbl_user_item", "amount = amount - 1",
                "user_id = $user_id and item_id = {$info['id']}");
            if($u === true)
            {
                $date = $config->Select("tbl_item",
                    "piece",
                    "id = {$info['id']}");

                $date = $date[0][0];

                $u = $config->Update("tbl_user_item", "amount = amount + $date",
                    "user_id = $user_id and item_id = 2");

                if($u === true)
                {
                    return 1;
                }
            }
        }
    }

    private function bank($info)
    {
        $session = new Universal_session();
        $config = new Connections();
        $user_id = $session->getSession("user_id");

        $date = $config->Select("tbl_user_item", "if(amount >= {$info['piece']}, 1, 0)", "user_id = $user_id and item_id = 2");

        if($date[0][0] == 0)
        {
            return "Нету средств";
        }

        $u = $config->Update("tbl_user_item", "amount = amount - {$info['piece']}",
            "user_id = $user_id and item_id = 2");

        if($u === true)
        {
            $i = $config->Insert("tbl_bank", "user_id, piece, comit_user",
                "$user_id, {$info['piece']}, NOW()");

            if($i === true)
            {
                return 1;
            }
        }
    }
} 