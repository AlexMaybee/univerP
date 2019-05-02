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

AddEventHandler("calendar", "OnAfterCalendarEventEdit", Array("ActionCalendar", "AfterEventCl"));
class ActionCalendar
{
    function AfterEventCl($arFields)
    {
        $res_activity = [];
        if($arFields['ID'] > 0 && $GLOBALS['_POST']['ajax_action'] == 'ACTIVITY_SAVE') {
            $res_activity = $GLOBALS['_POST']['data'];

            //$res_activity = CCrmActivity::GetByCalendarEventId($arFields['ID']);
            if(!empty($res_activity)) {
                if($res_activity['providerId'] == 'CRM_MEETING' && $res_activity['ownerType'] == 'DEAL') {
                    $arFields = array(
                        'SECTIONS' => array(48),
                        'ID' => $arFields['ID'],
                    );
                    updateCalendarSection($arFields);
                }
            }
        }

        //при бронировании ресурса переносим встречу в календарь "собеседования" (SECTIONS = [48]) //цифра - шв
        if($arFields['ID'] > 0 && $arFields['EVENT_TYPE'] == '#resourcebooking#' && $GLOBALS['_POST']['ACTION'] == 'SAVE' && $GLOBALS['_POST']['ACTION_ENTITY_TYPE'] == 'D'){
            $updFields = array(
                'SECTIONS' => array(48),
                'ID' => $arFields['ID'],
            );
            $upd = updateCalendarSection($updFields);
        }
        // df([$arFields,$upd,$GLOBALS['_POST']]);
    }
}


function updateCalendarSection($arFields)
{
    global $USER;
    $Params['arFields'] = $arFields;
    $Params['userId'] = $USER->GetID();
    $res = CCalendarEvent::Edit($Params);
    //df($res);
}


//событие встречи в сделке - автозаполнение полей ресурса - 23.04 отменено клиентом
//include $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/client_interview_meeting.php';


?>