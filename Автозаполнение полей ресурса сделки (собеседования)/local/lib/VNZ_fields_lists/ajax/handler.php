<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

require_once ($_SERVER['DOCUMENT_ROOT'].'/local/lib/VNZ_fields_lists/ajax/SelectListsData.php');


//вузы по ID города/раздела
if($_POST['ACTION'] === 'GIVE_ME_VUZ_BY_CITY_ID'){
    $obj = new SelectListsData;
    //$obj->test();
    $obj->getVuzByCity($_POST['CITY_ID']);
}

//Языки по ID Вуза
//if($_POST['ACTION'] === 'GIVE_ME_LANGUAGES_BY_VUZ_ID'){
//    $obj = new SelectListsData;
//    //$obj->test();
//    $obj->getLanguagesByVuz($_POST['VUZ_ID']);
//}


//Направления обучения по Вузу, Языку, Уровню
if($_POST['ACTION'] === 'GIVE_ME_ACTIVITY_DIRECTION_OPTIONS'){
    $obj = new SelectListsData;

    $obj->getActivityOptions($_POST);
}