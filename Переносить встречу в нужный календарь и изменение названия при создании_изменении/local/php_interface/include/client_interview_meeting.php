<?php

//Подключение кода для автозаполнения поля с ресурсом (сотрудники,офисы, дата и время)

AddEventHandler("crm", "OnBeforeCrmDealUpdate", ["CustomDealsWorks", "fillResourceFields"]);//в массиве только измененные поля


class CustomDealsWorks{

    public function fillResourceFields(&$arFields){


        $filter = ['ID' => $arFields['ID']];
        $select = [/*'ID','TITLE','UF_CRM_1554893912865'*/'UF_*','*'];
        $res = self::getDealData($filter,$select);

        if($res){
            if(isset($arFields['STAGE_ID'])){

                if($res['CATEGORY_ID'] == 0){
                    //Переход с первой стадии на Спивбесиду
                    if($res['STAGE_ID'] === 'NEW' && $arFields['STAGE_ID'] === 'PREPAYMENT_INVOICE'){
                        $cityId = '';
                        $meetingDateTime = ''; //дата и время встречи
                        $neededTime = 3600;//сколько времени нужно на встречу
                        $meetingType = 'Співбесіда';
                        if($arFields['UF_CRM_1553181252'] > 0){ //адрес и город

                            $cityId = self::fromCityAddrToResource($arFields['UF_CRM_1553181252']);

                            //если выбран Skype
                            if($arFields['UF_CRM_1553181252'] == 754){
                                $neededTime = 1800; //30 мин
                                $meetingType = 'Співбесіда Skype';
                            }
                        }
                        if(!empty($arFields['UF_CRM_1553180538663'])) $meetingDateTime = $arFields['UF_CRM_1553180538663']; //дата и время собеседования
                        //вставляем автоматически для rosource
                        $arFields['UF_CRM_1554893912865'] = [
                            'resource|'.$cityId.'|'.$meetingDateTime.'|'.$neededTime.'|'.$meetingType,
                        ];

                        //Если выбран офис Ровно, то добавляем сотрудника С. Фичук
                        if($arFields['UF_CRM_1553181252'] == 731)
                            $str = 'user|486|'.$meetingDateTime.'|'.$neededTime.'|'.$meetingType;
                            array_push($arFields['UF_CRM_1554893912865'],$str);
                    }
                }
            }
        }

//        $file = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/test.log';;
//        file_put_contents($file, print_r([$arFields,$res], true), FILE_APPEND | LOCK_EX);
    }

    function getDealData($arFilter,$arSelect){
        $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
        if($ar_result = $db_list->GetNext()) return $ar_result;
        return false;
    }

    //For UF_CRM_1553181252 convert to resource
    function fromCityAddrToResource($cityAddrId){
        $cityId = false;
        switch ($cityAddrId){
            case 749: //Винница
                $cityId = 53;
                break;
            case 745: //Днепр
                $cityId = 54;
                break;
            case 738: //Запорожье
                $cityId = 55;
                break;
            case 741: //Івано-Франківськ
                $cityId = 57;
                break;
            case 730: //Київ
                $cityId = 58;
                break;
            case 751: //Костянтинівка
                $cityId = 59;
                break;
            case 752: //Краматорськ
                $cityId = 60;
                break;
            case 736: //Кривий Ріг
                $cityId = 61;
                break;
            case 748: //Кропивницький
                $cityId = 62;
                break;
            case 747: //Луцьк
                $cityId = 63;
                break;
            case 735: //Львів
                $cityId = 64;
                break;
            case 753: //Люботин
                $cityId = 65;
                break;
            case 742: //Маріуполь
                $cityId = 66;
                break;
            case 750: //Нікополь
                $cityId = 67;
                break;
            case 733: //Нова Каховка
                $cityId = 68;
                break;
            case 737: //Одеса
                $cityId = 69;
                break;
            case 743: //Олександрія
                $cityId = 70;
                break;
            case 739: //Охтирка
                $cityId = 71;
                break;
            case 746: //Павлоград
                $cityId = 72;
                break;
            case 731: //Рівне
                $cityId = 73;
                break;
            case 732: //Суми
                $cityId = 74;
                break;
            case 744: //Харків
                $cityId = 75;
                break;
            case 740: //Черкаси
                $cityId = 76;
                break;
            case 734: //Чернігів
                $cityId = 77;
                break;

            case 754: //Skype
                $cityId = 78;
                break;
        }
        return $cityId;
    }

}