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
    public function AfterEventCl($arFields)
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
                    self::updateCalendarSection($arFields);
                }
            }
        }

        //при бронировании ресурса переносим встречу в календарь "собеседования" (SECTIONS = [48]) //цифра - шв
        if($arFields['ID'] > 0 && $arFields['EVENT_TYPE'] == '#resourcebooking#' && $GLOBALS['_POST']['ACTION'] == 'SAVE' && $GLOBALS['_POST']['ACTION_ENTITY_TYPE'] == 'D'){
            $updFields = array(
                'SECTIONS' => array(48),
                'ID' => $arFields['ID'],
            );
            $search = 'Бронювання';
            if(preg_match('/'.$search.'/',$arFields['NAME'])){

            //UPD 14.05 - делаем название события календаря по габлону:
            // $updFields['NAME'] = 'півбесіда: + название сделки, + ФИО Абитуриента, + ФИО родителей, + скайп/вайбер абитуриента, + примечание из сделки'
                $newActivityTitle = self::formNewActivityTitle($arFields['ID']);


           //     echo '<br>СОВПАДЕНИЕ!<br>';
                $updFields['NAME'] = preg_replace('/'.$search.'/','Співбесіда',$arFields['NAME']).' - '.$newActivityTitle;
            }
            $upd = self::updateCalendarSection($updFields);
        }
       // df([$arFields,$newActivityTitle,$upd,$GLOBALS['_POST']]);
    }

    private function updateCalendarSection($arFields)
    {
        global $USER;
        $Params['arFields'] = $arFields;
        $Params['userId'] = $USER->GetID();
        $res = CCalendarEvent::Edit($Params);
        //df($res);
    }

    // $updFields['NAME'] = 'півбесіда: + название сделки, + ФИО Абитуриента, + ФИО родителей, + скайп/вайбер абитуриента, + примечание из сделки'
    private function formNewActivityTitle($activityId){
        $activityTitleString = false;
        $abiturientMassengers = false;

        $resEvents = CCalendarEvent::GetById($activityId);
        if($resEvents['UF_CRM_CAL_EVENT']){
            $dealID = false;

            foreach($resEvents['UF_CRM_CAL_EVENT'] as $entityTypeId){
                if(preg_match('/D_[\d]+/', $entityTypeId)) $dealID = substr($entityTypeId,2);
            }

            if($dealID){
                $dealsMassive = self::getDealByFilter1(['ID' => $dealID],['ID','TITLE','COMPANY_ID','UF_CRM_1557824219221']);
                if($dealsMassive){
                    //Запрос массива контактов, которые привязаны к семье (компании) по ID
                    $contacts = self::getContactsByFilter1(['COMPANY_ID' => $dealsMassive[0]['COMPANY_ID']],['ID','NAME','LAST_NAME','UF_CRM_5CA325B4E045E','COMPANY_ID']);

                    if(count($contacts) > 0){
                        foreach($contacts as $contact){
//                            $newContactMassive[] = [
//                                'NAME' => $contact['LAST_NAME'].' '.$contact['NAME'],
//                                'ROLE' => getStatusName12($contact['UF_CRM_5CA325B4E045E']),
//                                'ROLE_ID' => $contact['UF_CRM_5CA325B4E045E'],
//                            ];

                            if($contact['UF_CRM_5CA325B4E045E'] === 'PARTNER'){
                                $activityTitleString .= $contact['LAST_NAME'].' '.$contact['NAME'].'('.self::getStatusName1($contact['UF_CRM_5CA325B4E045E']).'), ';
                                //Получаем мессенджеры абитуриента - getIMcontact
                                $massengersFilter = [
                                    'ENTITY_ID'  => 'CONTACT',
                                    'ELEMENT_ID' => $contact['ID'],
                                    'TYPE_ID'    => 'IM', //PHONE,EMAIL
                                    //'VALUE_TYPE' => 'WORK',
                                ];
                                $massengersSelect = ['VALUE_TYPE','VALUE'];
                                $abiturientMassengers = self::getIMcontact($massengersFilter,$massengersSelect);
                            }

                            else $activityTitleString .= $contact['LAST_NAME'].' '.$contact['NAME'].'('.self::getStatusName1($contact['UF_CRM_5CA325B4E045E']).'), ';
                        }
                    }

                    //Добавление мессенджеров абитуриента в строку
                    if ($abiturientMassengers){
                        $activityTitleString .= 'Зв\'язок: ';
                        foreach ($abiturientMassengers as $massenger){
                            $activityTitleString .= $massenger['VALUE'].' ('.$massenger['VALUE_TYPE'].'), ';
                        }
                    }

                    //Ну и добавляем примечание в конец строки
                    if($activityTitleString && $dealsMassive[0]['UF_CRM_1557824219221'] != '')
                        $activityTitleString .= 'Примітка: '.$dealsMassive[0]['UF_CRM_1557824219221'];

                }
            }

        }

        return $activityTitleString;
    }

    private function getDealByFilter1($filter,$select){
        $db_list = CCrmDeal::GetListEx(['ID' => 'ASC'], $filter, false, false, $select, array()); //получение пользовательских полей сделки по ID

        $result = array();
        while ($dealsList = $db_list->GetNext()) {
            $result[] = $dealsList;
        }

        if($result) return $result;
        return false;
    }

    private function getContactsByFilter1($filter,$select){
        $contactData = CCrmContact::GetList(array('ID' => 'ASC'), $filter, $select, false);
        $result = [];
        while ($ar_result = $contactData->GetNext()){
            $result[] = $ar_result;
        }
        return $result;
    }

    private function getStatusName1($ID){
        $db_list = CCrmStatus::GetList(array(), array('ENTITY_ID' => 'CONTACT_TYPE', 'STATUS_ID' => $ID));
        if ($ar_result = $db_list->GetNext())
            return $ar_result['NAME'];
    }

    //получение IM (контакт- фейсбук, вайбер, телеграмм...)
    private function getIMcontact($filter,$select){
        $dbRes = \CCrmFieldMulti::GetListEx(['ID' => 'asc'], $filter, false, false, $select);
        $allIm = false;
        while ($multiFields = $dbRes->Fetch()) {
            $allIm[] = $multiFields;
        }
        return $allIm;
    }

}


//событие встречи в сделке - автозаполнение полей ресурса - 23.04 отменено клиентом
//include $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/client_interview_meeting.php';


?>