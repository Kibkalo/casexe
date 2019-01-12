<?php
/**
 * Получение данных для шаблона сайта
 *
 * @author      okibkalo
 * @version     v.1 (02/11/16)
 * @copyright   Copyright (c) 2017
 */

/**
 * Инициализация пространства имен
 */
namespace Classes\core;

/**
 * Подключение модулей
 */
use Control\Connections;
use Classes\Universal_session;
use Classes\QuickFunctions;
use Classes\core\baseHelper;

/**
 * Class baseModel получение данных для шаблона сайта
 * @package Classes\core базовый пакет для хелпера и логирования
 */
class baseModel
{
    /**
     * @var \Control\Connections|null класс для работы с БД
     */
    protected $CONFIG = null;
    /**
     * @var string логин текущего пользователя
     */
    protected $login = null;
    /**
     * @var string id текущего пользователя
     */
    protected $userId = null;
    /**
     * @var string id текущего типа периода (неделя/месяц)
     */
    protected $typeId = null;
    /**
     * @var string полное имя пользователя
     */
    protected $fio = null;
    /**
     * @var bool|null|string дата конца прошлого периода
     */
    protected $dateTo = null;
    /**
     * @var bool true - пользователь администратор
     */
    protected $isAdmin = false;
    /**
     * @var bool true - у пользователя есть доступ к соданию заявок по маппингу
     */
    protected $isMapping = false;

    /**
     * @var string путь к root проекта
     */
    protected $root = false;
    /**
     * @var string заголовок страницы
     */
    protected $title = null;

    public static $title_set = null;

    /**
     * Конструктор для инициализации переменных класса
     */
    public function __construct()
    {
        $this->CONFIG = new Connections();
        $session = new Universal_session();

        $this->login = $session->getSession('login');
        $this->userId = $session->getSession('user_id');
        $this->typeId = $session->getSession('type');
        $this->fio = $session->getSession('Fio');
        $this->isAdmin = !is_null($session->getSession('id_admin'));

        $this->root = QuickFunctions::getRoot();

        $isMapping = $this->CONFIG->Select("tbl_wu_role", "IFNULL(id, 0)",
            "users_id = " . $this->userId . " and topicality = 1");
        if (isset($isMapping[0][0]) && $isMapping[0][0] > 0) {
            $this->isMapping = true;
        }
    }

    /**
     * Получение пути меню вернего уровня (для выделения цветом)
     * @return array одномерный массив, ключ=0 , значение -  путь меню вернего уровня
     */
    protected function getPathFile()
    {
        $files = array();
        array_push($files, $_SERVER['SCRIPT_NAME']);

        $requestUrl = explode('&', $_SERVER['REQUEST_URI']);
        $requestUrl = $requestUrl[0];

        $arrayHref = $this->CONFIG->Select("tbl_top_menu as ttm", "ttm.Page",
            "ttm.sort_order = (SELECT FLOOR(dttm.sort_order)
                FROM tbl_top_menu as dttm
                where dttm.Page = '{$_SERVER['SCRIPT_NAME']}' or dttm.Page = '$requestUrl')
            or ttm.id = (SELECT dtum.top_menu_id
                FROM tbl_under_menu as dtum
                where dtum.Page = '{$_SERVER['SCRIPT_NAME']}' or dtum.Page = '$requestUrl')");

        if (isset($arrayHref[0][0]))
        {
            $arrayHref = $arrayHref[0][0];
        }

        if (!in_array($arrayHref, $files))
        {
            array_push($files, $arrayHref);
        }

        return $files;
    }

    /**
     * Из массива перечня подкючаемых модулей js и css формирует строку HTML-кода подключения
     * @param array $files одномерный массив подкючаемых модулей, ключ не используется
     * @return string HTML-код подкючения внешних модулей js и css
     */
    protected function attachedFiles($files)
    {
        return QuickFunctions::attachedFiles($files);
    }

    /**
     * Получение данных для построения меню сайта
     * таблица tbl_top_menu
     * @return array данные для меню
     * Title_name - название пункта меню
     * Page - ссылка на страницу
     * id  - идентификатор страницы в меню
     * image - ссылка на картинку, вместо текта пункта меню (если картинка указана в sys_spt_image)
     */
    protected function getArrayMenu()
    {
        $result = array();
        if ($this->isAdmin)
        {
            $result = $this->CONFIG->Select("tbl_top_menu ttm
	                LEFT JOIN sys_spt_image ssi on ssi.id = ttm.image_id
	                LEFT JOIN sys_spt_image_holidays ssih on ssih.image_id = ssi.id and ssih.coming = CURDATE()",
                "ttm.Title_name, ttm.Page, ttm.id, IFNULL(ssih.path, ssi.path)",
                "ttm.`Show` = 1 or ttm.show_admin = 1 ORDER BY ttm.sort_order");
        } else {
            $result = $this->CONFIG->Select("tbl_top_menu ttm
	                LEFT JOIN sys_spt_image ssi on ssi.id = ttm.image_id
	                LEFT JOIN sys_spt_image_holidays ssih on ssih.image_id = ssi.id and ssih.coming = CURDATE()",
                "ttm.Title_name, ttm.Page, ttm.id, IFNULL(ssih.path, ssi.path)",
                "ttm.`Show` = 1 or (ttm.id = 72 and (SELECT COUNT(uit.user_id)
                    FROM tbl_load_user as uit where uit.user_id =  {$this->userId}) > 0)
                ORDER BY ttm.sort_order");
        }
        return $result;
    }

    /**
     * Получение массива данных для построения подменю
     * таблица tbl_under_menu
     * @return array трехмерный массив данных меню,
     * где ключ id родительского пункта меню
     * значение - массив подменю,
     * ключ - порядковый номер,
     * значение - массив (название пункта меню, ссылка на страницу перехода)
     *
     */
    protected function getArrayMenuUnder()
    {
        $result = array();
        if ($this->isAdmin) {
            $result = $this->CONFIG->Select("tbl_under_menu",
                "top_menu_id, Title_name, Page",
                " (`Show` = 1 or show_admin = 1) ORDER BY sort_order");
        } else {
            $result = $this->CONFIG->Select("tbl_under_menu",
                "top_menu_id, Title_name, Page",
                " `Show` = 1 ORDER BY sort_order");
        }

        $tmp = array();
        for ($i = 0; $i < count($result); $i++)
        {
            $id = $result[$i][0];
            unset($result[$i][0]);
            if (!isset($tmp[$id]))
            {
                $tmp[$id] = array();
            }
            array_push($tmp[$id], $result[$i]);
        }
        $result = $tmp;

        return $result;
    }

    /**
     * Получение подменю типов периодов
     * таблица sys_spt_type_date
     * @return array 0=> HTML-код подменю периодов,
     * 1=> текст меню верхнего уровня (меняется в зависимости от выбранного типа данных)
     */
    protected function displayType()
    {
        $selectImage = "<img src='{$this->root}/Image/ok.png'
						    style='float:right;padding-top: 2px;' width='30' height='30'/>";
        $dateTypes = $this->CONFIG->Select("sys_spt_type_date","id, name, display_name", "topicality = 1");
        $resultName = "";
        $result = "";

        $result .= "<ul>";
        for ($i = 0; $i < count($dateTypes); $i++) {
            $result .= "<li>";
            $result .= "<a href='javascript:void(0)' id='{$dateTypes[$i][0]}' onclick='setType(this)'>";
            $result .= "{$dateTypes[$i][2]}";
            if ($dateTypes[$i][0] == $this->typeId) {
                $resultName = $dateTypes[$i][2];
                $result .= $selectImage;
            }
            $result .= "</a>";
            $result .= "</li>";
        }
        $result .= "</ul>";

        return array($result, $resultName);
    }

    /**
     * Формирование HTML-кода батарейки состояния загрузки данных
     * таблица log_view_java
     * @return string HTML-код батарейки
     */
    protected function loadingData()
    {
        return QuickFunctions::getLoadDate($this->dateTo, $this->typeId);
    }

    /**
     * Формирование HTML-кода фотографии пользователя (+, если ссылка на просмотр заявок маппинга,
     * + наличие открытых заявок маппинга)
     * @return string HTML-код
     */
    protected function imageUser()
    {
        $documentRoot = QuickFunctions::getRoot(false);
        $result = "";
        $messageUser = "and r.user_id = $this->userId";
        $onClick = "";
        $cursor = "";
        if ($this->isAdmin)
        {
            $messageUser = "";
        }
        if ($this->isMapping)
        {
            $onClick = "onclick='createApplication()'";
            $cursor = "cursor: pointer;";
        }

        $numMessage = $this->CONFIG->Select("tbl_wu_request r",
            "COUNT(*)",
            "r.status_id <> 3 $messageUser");
        $numMessage = $numMessage[0][0];
        $result .= "<div class='imageBlock' style='display:inline-block;$cursor' $onClick>";

        if (filesize("{$documentRoot}/Image/users/{$this->login}.jpg") > 0) {
            $result .= "<img src='{$this->root}/Image/users/{$this->login}.jpg'
			    width='42' height='42' title='{$this->fio}' class='imageBlock'>";
        } else {
            $result .= "<img src='{$this->root}/Image/icon-user.png'
			    width='42' height='42' title='{$this->fio}' class='imageBlock'>";
        }

        $result .= "<h2 class='zoneText'>";
        if ($numMessage > 0)
        {
            $result .= "<span class='textValue'>$numMessage</span>";
        }
        $result .= "</h2>";
        $result .= "</div>";

        return $result;
    }

    protected function DisplayMessage()
    {
        $requestUrlInsert = explode('&', $_SERVER['REQUEST_URI']);
        $requestUrlInsert = $requestUrlInsert[0];

        $date = $this->CONFIG->Select("tbl_alerts a
            LEFT JOIN tbl_top_menu tm on tm.id = a.top_menu_id
            LEFT JOIN tbl_under_menu um on um.id = a.under_menu_id
            LEFT JOIN tbl_spr_alerts sa on sa.id = a.status_alerts_id",
            "IFNULL(tm.Page, um.Page), a.message, sa.style",
            "a.date_from < NOW()
                AND a.date_to > NOW()
                and a.topicality = 1");

        $ret = "";

        for($i = 0; $i < count($date); $i++)
        {
            if ($requestUrlInsert == $date[$i][0] or $date[$i][0] == null)
            {
                $ret .= baseHelper::getDiv($date[$i][1], array("class" => $date[$i][2]));
            }
        }

        return $ret;
    }
} 