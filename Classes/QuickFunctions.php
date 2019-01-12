<?php
/**
 * Сборник наиболее часто используемых функций
 *
 * @author okibkalo
 * Date: 06.01.16
 * Time: 9:50
 */

/**
 * Инициализация пространства имен
 */
namespace Classes;

/**
 * Подключение модулей
 */
use Control\Connections;
use Classes\StorageTemplates;

/**
 * Class QuickFunctions Сборник наиболее часто используемых функций
 * @package Classes Пакет общих классов проекта
 */
class QuickFunctions
{
    public static $Highcharts = "[CHARTS]";

    /**
     * @var array массив шаблонов для подключаемых файлов css и js
     */
    private static $pattern = array(
        "js" => "<script src='[FILE]' language='JavaScript' charset='UTF-8'></script>",
        "css" => "<link type='text/css' href='[FILE]' rel='stylesheet'/>"
    );

    /**
     * @var string терминал конца строки
     */
    private static $endLine = "\n";

    /**
     * @var string css стили для select chosen
     */
    public static $SelectStyle = "text-align: left; width:250px;";

    public static function attachedFiles($files, $replacement = "[ROOT]", $root = true)
    {
        $result = "";
        //echo count($files);
        for ($i = 0; $i < count($files); $i++)
        {
            if(strtoupper($files[$i]) == "[CHARTS]")
            {
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/modules/offline-exporting.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/modules/exporting.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/highcharts.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/lib/default_vfs.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/lib/svg2pdf.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/lib/jspdf.js");
                array_splice($files, $i + 1, 0, "[ROOT]/js/highcharts/lib/jspdf.customfonts.min.js");
            } else {
                $tmp = explode('.', $files[$i]);
                $tmp = end($tmp);
                $files[$i] = QuickFunctions::getPath($files[$i], $replacement, $root);
                $result .= str_replace("[FILE]", $files[$i], QuickFunctions::$pattern[$tmp]).QuickFunctions::$endLine;
            }
        }
        return $result;
    }

    /**
     * Форматирование числа, если целое - разделение разрядов
     * иначе  - 2 знака после запятой + разделение разрядов
     * @param int|double $val - число для форматирования
     * @return int|double - число в формате
     *
     */
    public static function formatValue($val)
    {
        $tmp = 0;
        if(intval($val) == floatval($val)){
            $tmp = number_format($val, 0, '', ' ');
        }else{
            $tmp = number_format($val, 2, '.', ' ');
        }
        return $tmp;
    }

    /**
     * Получение имени сервера или папки ROOT
     * @param bool $http если true - имя сервера, иначе ROOT
     * @return string - имя сервера или ROOT
     */
    public static function getRoot($http = true)
    {
        $root = "";
        if ($http) {
            $root = "//{$_SERVER['HTTP_HOST']}";
        } else {
            $root = $_SERVER['DOCUMENT_ROOT'];
        }
        return $root;
    }

    /**
     * Замена в шаблоне значения $replacement на путь к ROOT
     * @param string $path - шаблон ссылки
     * @param string $replacement - заменяемая строка
     * @return string ссылка
     */
    public static function getPath($path, $replacement = "[ROOT]", $root = true)
    {
        return str_replace($replacement, QuickFunctions::getRoot($root), $path);
    }

    /**
     * Получение расположения файла на сервере, путем замены $replacement
     * @param string $dRoot - шаблон пути к файлу
     * @param string $replacement - заменяемая строка
     * @return string путь к файлу на сервере
     */
    public static function getDocumentRoot($dRoot, $replacement = "[DR]")
    {
        return str_replace($replacement, QuickFunctions::getRoot(false), $dRoot);
    }

    /**
     * Удаление всех файлов из папки
     * @param string $dir - название папки на сервере
     */
    public static function cleanDir($dir)
    {
        $files = glob($dir."/*");
        if (count($files) > 0) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Трансформация двумерного массива по указанному стобцу в строку с разделителем
     * @param array $arr - двумерный массив
     * @param int $index - номер столбца
     * @param string $separator - разделитель в строке
     * @return string строка данных по столбцу с разделителями
     */
    public static function arrayToString($arr, $index, $separator = ", ")
    {
        $tmp = "";
        for ($i = 0; $i < count($arr); $i++) {
            $tmp .= $arr[$i][$index];
            if ($i < count($arr) - 1) {
                $tmp .= $separator;
            }
        }
        return $tmp;
    }

    /**
     * Логирование обновлений графиков
     * @param string $key - идентификатор типа графиков
     * @return array|bool|string - true - успешно
     */
    public static function logRefresh($key)
    {
        $config = new Connections();
        $session = new Universal_session();

        $idKey = $config->Select("tbl_spr_name_refresh as nr","nr.id","nr.key = '$key'");

        if (is_array($idKey)) {
            $idKey = $idKey[0][0];
            $user_id = $session->getSession('user_id');
            if ($user_id == "") $user_id = '0';

            $insertRefresh = $config->Insert("tbl_refresh",
                "refreshNameId, date, loginRefresh",
                "$idKey, NOW(), '$user_id'");

            if ($insertRefresh === true) {
                return true;
            } else {
                return $insertRefresh;
            }
        } else {
            return $idKey;
        }
    }

    /**
     * Формирование цвета для стиля в графике посещений сайта за неделю
     * @param int $percent - процент
     * @return string название класса стилей
     */
    public static function getClassWeekDay($percent){
        $style = "";
        if ($percent == 0) {
            $style = "Zero";
        } elseif ($percent <= 5) {
            $style = "Five";
        } elseif ($percent <= 10) {
            $style = "Ten";
        } elseif ($percent <= 15) {
            $style = "Fifteen";
        } elseif ($percent <= 20) {
            $style = "Twenty";
        } elseif ($percent <= 25) {
            $style = "TwentyFive";
        } elseif ($percent <= 30) {
            $style = "Thirty";
        } elseif ($percent <= 35) {
            $style = "ThirtyFive";
        } else {
            $style = "Forty";
        }
        return $style;
    }

    /**
     * Построение select с помощью библиотеки chosen
     * @param array $arr одномерный массив данных для заполнения
     * @param int $level Значение по умолчанию
     * @param string $text Текст элемента пока не сделан выбор
     * @param string $id Уникальный индентификатор
     * @param string $onchange Добавление события изменения элемента
     * @return string HTML - код select
     */
    public static function buildSelect($arr, $level, $text, $id, $onchange = "")
    {
        $SelectStyle = QuickFunctions::$SelectStyle;
        $result = "<select id='$id'
            data-placeholder='$text'
            class='chosen-select'
            style='$SelectStyle'
            $onchange>";
        $result .= "<option id='0' value=''></option>";
        $result .= "<option id='-1' value='-1'>$text</option>";
        for ($i = 0; $i < count($arr); $i++) {
            $selected = "";
            if ($level == $arr[$i][0]) {
                $selected = "selected";
            }
            $result .= "<option id='$i'
                style='text-align: left;'
                value='{$arr[$i][0]}'
                $selected>{$arr[$i][1]}</option>";
        }
        $result .= "</select>";
        return $result;
    }

    /**
     * Построение select спомошью библиотеки chosen с групировкой
     * @param array $arr одномерный массив данных для заполнения
     * @param int $level Значение по умолчанию
     * @param string $text Текст элемента пока не сделан выбор
     * @param string $id Уникальный индентификатор
     * @param string $onchange Добавление события изменения элемента
     * @return string HTML - код select
     */
    public static function buildSelectGroup($arr, $level, $text, $id, $onchange = "")
    {
        $SelectStyle = QuickFunctions::$SelectStyle;
        $result = "<select id='$id'
            data-placeholder='$text'
            class='chosen-select'
            style='$SelectStyle'
            $onchange>";
        $result .= "<option id='0' value=''></option>";
        $result .= "<option id='-1' value='-1'>$text</option>";
        $group = "";
        $open = 0;
        for ($i = 0; $i < count($arr); $i++) {
            if ($group != $arr[$i][2])
            {
                if ($open > 0)
                {
                    $open = 0;
                    $result .= "</optgroup>";
                }
                $group = $arr[$i][2];
                $result .= "<optgroup label='{$group}'>";
                $open++;
            }
            $selected = "";
            if ($level == $arr[$i][0]) {
                $selected = "selected";
            }
            $result .= "<option id='$i'
                style='text-align: left;'
                value='{$arr[$i][0]}'
                $selected>{$arr[$i][1]}</option>";
        }
        $result .= "</select>";
        return $result;
    }

    /**
     * Построение select без использования плагинов chosen
     * @param array $arr одномерный массив данных для заполнения
     * @param string $id Уникальний индификатор select
     * @param string $onchange Добавление события изменения
     * @param string $width Ширина элемента
     * @return string HTML - код select
     */
    public static function PlainBuildSelect($arr, $id, $onchange = "", $width = "250px")
    {
        $result = "<select id='$id' style='text-align: left; width:{$width};' $onchange>";
        for($i = 0; $i < count($arr); $i++){
            $result .= "<option id='{$i}' style='text-align: left;' value='{$arr[$i][0]}'>{$arr[$i][1]}</option>";
        }
        $result .= "</select>";
        return $result;
    }

    /**
     * Чтения Excel файлов
     * @param string $FilePath полный путь с именем к файлу
     * @param int $SheetIndex Номер листа
     * @return array Содержимое листа
     */
    public static function readExcelFile($FilePath, $SheetIndex)
    {
        $ar = array();

        $inputFileType = \PHPExcel_IOFactory::identify($FilePath);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($FilePath);
        $objPHPExcel->setActiveSheetIndex($SheetIndex);
        //print_r($objPHPExcel->getActiveSheet()->toArray());
        $ar = $objPHPExcel->getActiveSheet()->toArray(null, true);
        return $ar;
    }

    /**
     * Получение имен листов в указанном Excel файле
     * @param string $filePath полный путь с именем к файлу
     * @return array массив названий листов
     */
    public static function getNameExcelFile($filePath){
        $ar = array();
        $inputFileType = \PHPExcel_IOFactory::identify($filePath);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);
        $objPHPExcel->setActiveSheetIndex(0);
        $ar[] = $objPHPExcel->getSheetNames();
        return $ar;
    }

    /**
     * ФОрмирование шифрованной строки по ключу
     * @param string $str строка для шифрования
     * @param string $key ключ шифрования
     * @return string - зашифрованная строка
     */
    public static function StrCode($str, $key="")
    {
        $salt = "Dn8*#2n!9j";
        $len = strlen($str);
        $gamma = '';
        $n = $len > 100 ? 8 : 2;
        while (strlen($gamma) < $len) {
            $gamma .= substr(pack('H*', sha1($key.$gamma.$salt)), 0, $n);
        }
        return $str ^ $gamma;
    }

    /**
     * Создание сессии пользователя
     * @param string $login - логин пользователя
     * @param string $fio - ФИО пользователя для добавления нового
     * @return string 1992- успешное
     */
    public static function FillingSessions($login, $fio = null)
    {
        $session = new Universal_session();
        $config = new Connections();

        $login = str_replace('\'', '\'\'', $login);

        $id = $config->Select('tbl_user u',
            "u.id", "u.login = \"$login\"");

        $session->setSession("login", $login);
        $session->setSession("user_id", $id[0][0]);

        return '1992';
    }

    /**
     * Создание HTML- кода таблицы по двумерному или одомерному массиву
     * @param array $array - массив данных
     * @return string HTML- код таблицы
     */
    public function printArrayIsTable($array)
    {
        $show = "<table>";
        for ($i = 0; $i < count($array); $i++) {
            $show .= "<tr>";
            if (is_array($array[$i])) {
                for ($j = 0; $j < count($array[$i]); $j++) {
                    $show .= "<td class='td_title_status td_pad' style='text-align: center;'>{$array[$i][$j]}</td>";
                }
            } else {
                $show .= "<td class='td_title_status td_pad' style='text-align: center;'>{$array[$i]}</td>";
            }
            $show .= "</tr>";
        }
        $show .= "</table>";
        return $show;
    }

    /**
     * Поворот двумерного массива
     * @param array $array массив данных
     * @return array отображенный массив
     */
    public static function turnArray($array)
    {
        $dateB = array();
        for ($i = 0; $i < count($array[0]); $i++) {
            $tmp = array();
            for ($j = 0; $j < count($array); $j++) {
                $tmp[] = $array[$j][$i];
            }
            $dateB[] = $tmp;
        }
        return $dateB;
    }

    /**
     * Определение количества загруженных файлов (для статистики)
     * @return array 0 елемент - количество загруженных файлов,
     * 1 - сколько должны загрузить файлов
     */
    public static function loadingDateFile()
    {
        $config = new Connections();
        $session = new Universal_session();

        $type_id = $session->getSession('type');

        $file = $config->Select("tbl_load l
            LEFT JOIN tbl_load_row lr on lr.load_id = l.id","table_name",
            "l.topical = 1 and l.type_id = $type_id");
        $countFile = count($file);
        $fromSelect = "";

        $dateW = "DATE_SUB(DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE()) DAY), INTERVAL -1 DAY)";
        $dateM = "LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";

        if($type_id == 1)
        {
            $date = $dateW;
        } else {
            $date = $dateM;
        }

        if ($countFile != 0)
        {
            for ($i = 0; $i < $countFile; $i++) {
                $tmp = str_replace("[TABLE]", $file[$i][0], StorageTemplates::$SELECT_OFFICE_IN_FILE);
                $tmp = str_replace("[TYPE]", $type_id, $tmp);
                $fromSelect .= str_replace("[DATE]", $date, $tmp);
                if ($i < $countFile - 1) {
                    $fromSelect .= " UNION ALL ";
                }
            }
            $loadFile = $config->Select("($fromSelect) as d","SUM(d.sum)");
            $countLoadFile = $loadFile[0][0];
        } else {
            $countLoadFile = 0;
        }

        return array($countLoadFile, $countFile);
    }

    /**
     * Замена в шаблоне поля отчетов COUNDAY на количество дней в указанном периоде
     * @param string $str - строка для измененеия
     * @param string $date - Дата из которой получаем количество месяцов
     * @param int $type тип периода
     * @return string строка со вставленным значением количества дней
     */
    public static function daySelectType($str, $type, $date)
    {
        $day = 7;
        if ($type == 2)
        {
            $day = date('t', strtotime(date('Y', strtotime($date)).'-'.(date('m', strtotime($date))).'-01'));
        }
        $str = str_replace('[COUNDAY]', $day, $str);
        return $str;
    }

    /**
     * Замена символов знаков сравнений на мнемокоды
     * используется для дополнительных фильтров в детализации
     * @param bool $OneLine - true - результат в формате ключ = значение
     * иначе двумерній массив
     * @return array соответствие символов сравнения и мнемоключей
     */
    public static function getArrayConditions($OneLine = false)
    {
        if ($OneLine)
        {
            $conditions = array(
                "less" => ">",
                "more" => "<",
                "equally" => "=",
                "notEqual" => "<>",
                "longerStill" => "<=",
                "lessWell" => ">=");
        } else {
            $conditions = array(
                array("less", ">"),
                array("more", "<"),
                array("equally", "="),
                array("notEqual", "<>"),
                array("longerStill", "<="),
                array("lessWell", ">="));
        }
        return $conditions;
    }

    /**
     * Построение части SQL-запроса по дополнительным фильтрам
     * @param array $arrayEO массив условий по доп\ фильтрам
     * @param string $alias Доплнительний синоним для полей
     * @return string SQL-запрос
     */
    public static function buildWhereEO($arrayEO, $alias = "")
    {
        $ar_conditions = QuickFunctions::getArrayConditions(true);
        $EOWhere = "";
        $EOWhereIn = null;
        if (!strpos($alias, '.'))
        {
            $alias .= ".";
        }

        for ($i = 0; $i < count($arrayEO); $i += 3)
        {
            $field = $arrayEO[$i];
            $conditions = $ar_conditions[$arrayEO[$i + 1]];
            $date = $arrayEO[$i + 2];

            $EOWhereIn[$field][$conditions][] = $date;
        }
        foreach($EOWhereIn as $key => $value)
        {
            foreach($value as $key1 => $value1)
            {
                if ($key1 == '=' && count($value1) > 1)
                {
                    $tmp = implode("', '", $value1);
                    $EOWhere .= " and $alias$key in ('$tmp')";
                } else {
                    if ($value1[0] == 'null')
                    {
                        if ($key1 == '=')
                        {
                            $EOWhere .= " and $alias$key is NULL";
                        } else {
                            $EOWhere .= " and $alias$key is not NULL";
                        }
                    } else {
                        $EOWhere .= " and $alias$key $key1 '{$value1[0]}'";
                    }
                }
            }
        }

        $EOWhere = str_replace("'null'", "NULL", $EOWhere);
        return $EOWhere;
    }

    /**
     * По указазанным дате и типу периода определяет периода, в который входит указанная дата
     * @param int $type тип периода
     * @param string $date - дата, указанная пользователем
     * @param string $page - с какой страницы идет вызов, null - дата последнего периода
     * @return array ассоциативный массив данных периода
     * FROM = начало периода
     * TO = конец периода
     * DAY = дата, на которую построен период
     * START = начало квартала
     * END = конец квартала
     */
    public static function getDateArchive($type, $date, $page = null)
    {
        $from = null;
        $to = null;
        $day = null;

        $period = null;

        if (is_null($date))
        {
            $period = new PeriodCurrent();
            $period = new PeriodCurrent($period->getTo());
        } else {
            $period = new PeriodCurrent($date);
            if (!is_null($page))
            {
                $period = new PeriodCurrent($period->getTo());
            }
        }

        if ($type == 1)
        {
            $day = $period->setStep($period->getLastTo(), 1);
            $from = $period->getLastFrom();
            $to = $period->getLastTo();
        } else if ($type == 2) {
            $day = $period->getFrom();
            $from = $period->getFrom();
            $to = $period->getTo();
        }
        else if ($type == 4) {
            $day = $period->getValue();
            $from = $to = $day;
        }
        
        $kv = $period->getIntervalKv();

        return array("FROM" => $from,
            "TO" => $to,
            "DAY" => $day,
            "START" => $kv['start'],
            "END" => $kv['end']);
    }

    /**
     * Формирование по типу периода Sql - подзапроса с учетом недельных или месячных дат
     * @param int $type_id типу периода
     * @param string $type Тип данных
     * @return string Sql - подзапрос
     */
    public static function getDateSql($type_id, $type = 'CURDATE()')
    {
        $date = "";
        switch($type_id)
        {
            case 1:
                $date = "DATE_SUB(DATE_SUB($type, INTERVAL DAYOFWEEK($type) DAY), INTERVAL -1 DAY)";
                break;
            case 2:
                $date = "LAST_DAY(DATE_SUB($type, INTERVAL 1 MONTH))";
                break;
            case 3:
                $date = "";
                break;
            case 4:
                $date = "$type";
                break;
        }
        return $date;
    }

    public static function getLoadDate($dateTo, $type_id, $inAr = false)
    {
        $config = new Connections();
        $session = new Universal_session();
        $isAdmin = !is_null($session->getSession('id_admin'));
        $root = QuickFunctions::getRoot();

        $result = "";

        if(strlen($dateTo) <= 10)
        {
            $dateTo = "'" . $dateTo . "'";
        }

        $loadDate = $config->Select("(Select 1 id,if(SUM(good) > 1, 1, 1) fact
                from log_view_java lvj
                    LEFT JOIN tbl_view tv on lvj.view_id = tv.id
                where lvj.date_start > $dateTo
                    and tv.topicality = 1
                    and lvj.type_id = $type_id
                    and lvj.good = 1
                GROUP BY lvj.view_id) t1,
                (SELECT 1 id, COUNT(*) fact
                FROM tbl_view
                WHERE topicality = 1
                        and type_id = $type_id) t2",
            "SUM(t1.fact), t2.fact",
            "t1.id = t2.id");

        $loadDate = $loadDate[0];
        $loadFile = QuickFunctions::loadingDateFile();
        $loadPercent = round((($loadDate[0] + $loadFile[0]) / ($loadDate[1] + $loadFile[1])) * 100, 2);

        if ($isAdmin) {
            $title_load = "Загружено; {$loadPercent}%\nView; {$loadDate[0]} / {$loadDate[1]}";
            $title_load .= "\nFile; {$loadFile[0]} / {$loadFile[1]}";
            $result .= "<a href='{$root}/View/Office/LogJava.php'>";
        } else {
            $title_load = "Загружено; {$loadPercent}%";
        }

        $result .= QuickFunctions::getImageLoadDate($loadPercent, $title_load);

        if ($isAdmin) {
            $result .= "</a>";
        }

        if($inAr)
        {
            return array("PERCENT" => $loadPercent,
                "TITLE" => $title_load,
                "HREF" => $result);
        } else {
            return $result;
        }
    }

    public static function getImageLoadDate($loadPercent, $title_load)
    {
        $result = "";
        $root = QuickFunctions::getRoot();

        if ($loadPercent <= 25)
        {
            $result .= "<img src='{$root}/Image/menu/battery/empty.png'
			    width='42' height='42' title='{$title_load}'>";
        } else if ($loadPercent < 50) {
            $result .= "<img src='{$root}/Image/menu/battery/quoter.png'
			    width='42' height='42' title='{$title_load}'>";
        } else if ($loadPercent < 75) {
            $result .= "<img src='{$root}/Image/menu/battery/half.png'
			    width='42' height='42' title='{$title_load}'>";
        } else if ($loadPercent < 100) {
            $result .= "<img src='{$root}/Image/menu/battery/third.png'
			    width='42' height='42' title='{$title_load}'>";
        } else if ($loadPercent >= 100) {
            $result .= "<img src='{$root}/Image/menu/battery/full.png'
			    width='42' height='42' title='{$title_load}'>";
        }
        return $result;
    }

    public static function arrayDoubleToRow($array, $add_start = null)
    {
        $row = array();
        $group = null;

        $tmp = array();
        for($i = 0; $i < count($array); $i++)
        {
            if($group != $array[$i][0])
            {
                if($tmp != array())
                {
                    array_push($row, $tmp);
                }
                $tmp = array();
                $group = $array[$i][0];
                array_push($tmp, $group);
            }
            array_push($tmp, $array[$i][1]);
        }

        if($group != $array[count($array)][0])
        {
            if(!is_null($add_start))
            {
                array_unshift($tmp, $add_start);
            }
            array_push($row, $tmp);
        }

        return $row;
    }

    public static function displayConditions($tech, $coa, $smot, $alias = "t")
    {
        $ar = array();

        if($tech == 1)
        {
            $ar[] = $alias . ".tech";
        }

        if($coa == 1)
        {
            $ar[] = $alias . ".Market";
        }

        if($smot == 1)
        {
            $ar[] = $alias . ".smot";
        }

        return "(" . implode(" + ", $ar) . ") > 0";
    }

    public static function MySQLTypeOracle($type)
    {
        $ar = array(
            "int" => "number",
            "double" => "number",
            "float" => "number",
            "varchar" => "varchar2",
            "date" => "date",
            "datetime" => "date",
            "time" => "date",
            "text" => "long",
            "longtext" => "long"
        );
        return $ar[strtolower($type)];
    }

    public static function nameFieldOracle($field)
    {
        $return = $field;
        $ar = array(
            "date" => 'data',
            "level" => 'lvl',
            "file" => 'file_name',
            "select" => 'select_name',
            "table" => "table_name",
            "type" => "kind",
            "sql" => "query",
            "view" => "view_name",
            "group" => "party"
        );

        if(isset($ar[$return]))
        {
            $return = $ar[$return];
        }

        return $return;
    }
} 