<?php
CJSCore::Init(array("jquery"));
CModule::IncludeModule('calendar');
CModule::IncludeModule('crm');
global $APPLICATION;
$APPLICATION->AddHeadScript('/local/lib/js/calendar-controls.js');
$APPLICATION->AddHeadScript('/local/lib/js/type_contact.js');
//запись в файл
function df($arr){
    global $USER;
    if ($USER->IsAdmin()){
        $file = $_SERVER['DOCUMENT_ROOT'].'/log.log';
        file_put_contents($file, print_r($arr, true), FILE_APPEND | LOCK_EX);
    }
}





//подключение файла js для работы с выпадающими списками ВНЗ
$arJsConfig = array(
    'addVnzFieldsListFunction' => array(
        'js' => '/local/lib/VNZ_fields_lists/js/vnz_lists.js',
    ),
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

//Вызов библиотеки
CUtil::InitJSCore(array('addVnzFieldsListFunction'));
//подключение файла js для работы с выпадающими списками ВНЗ


//подключение файла с кастомной работой ресурса в сделке
include $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/deal_resource_calendar_title_custom.php';


//событие встречи в сделке - автозаполнение полей ресурса - 23.04 отменено клиентом
//include $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/client_interview_meeting.php';


?>