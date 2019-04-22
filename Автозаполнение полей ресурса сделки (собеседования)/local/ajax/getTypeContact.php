<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

CModule::IncludeModule("crm");

$return = [];

if($_REQUEST['action']) {
    switch ($_REQUEST['action']) {
        case 'getContactId':
            $arFilter = Array('ID' => $_REQUEST['id']);
            $arSelect = Array('ID', 'UF_CRM_5CA325B4E045E');
            $db_list = CCrmContact::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
            if($ar_result = $db_list->GetNext())
                if($ar_result['UF_CRM_5CA325B4E045E']) {
                    $return = ['id'=>$ar_result['ID'], 'status'=> getStatusName($ar_result['UF_CRM_5CA325B4E045E'])];
                }
            //$return = $arFilter;
            break;
    }
}


function getStatusName($ID)
{
    $db_list = CCrmStatus::GetList(array(), array('ENTITY_ID' => 'CONTACT_TYPE', 'STATUS_ID' => $ID));
    if ($ar_result = $db_list->GetNext())
        return $ar_result['NAME'];
}

echo json_encode($return);

?>