<?php

class SelectListsData{

    public function test(){
        $this->sentAnswer(['Hello' => 'MAN!']);
    }


    public function getVuzByCity($cityId){
        $result = [
            'options' => false,
            'message' => 'Start message of getVuzByCity method '.$cityId,
        ];


        //получаем по фильтру универы, у которых радел = городу
        $vuz_elements_filter = [
            'IBLOCK_ID' => 29,
            'IBLOCK_SECTION_ID' => $cityId, // ID раздела =  ID города
        ];
        $vuz_elements_select = ['ID', 'NAME','IBLOCK_ID'];
        $vuz_elements_res = $this->getListElementsByFilter($vuz_elements_filter,$vuz_elements_select);
        if(count($vuz_elements_res) > 0){
            $result['options'] .= '<option value="">Не выбрано</option>';
            foreach ($vuz_elements_res as $elem){
                $result['options'] .= '<option value="'.$elem['ID'].'">'.$elem['NAME'].'</option>';
            }
            $result['options'] .= '<option value="1">Інший</option>';
            $result['message'] = 'Данные для поля Вузов вроед заполнены, проверь!';
        }
        else $result['message'] = 'Нема вузів в поточному місті!';

        $this->sentAnswer($result);
    }

    //Activity options
    public function getActivityOptions($data){
        $result = [
            'options' => false,
            'message' => 'Start message of getActivityOptions method',
        ];

        //запрос options для селекта направления обучения
        $activity_elements_filter = [
            'IBLOCK_ID' => 31,
            'PROPERTY_113' => $data['UNIVER_ID'], // ID универа
            'PROPERTY_118' => $data['LANG_ID'], // ID Языка
            'PROPERTY_117' => $data['LEVEL_ID'], // ID Уровень обучения
        ];
        $activity_elements_select = ['ID', 'NAME','IBLOCK_ID'];
        $activity_elements_res = $this->getListElementsByFilter($activity_elements_filter,$activity_elements_select);
        if(count($activity_elements_res) > 0 ){
            $result['options'] .= '<option value="">Не выбрано</option>';
            foreach ($activity_elements_res as $elem){
                $result['options'] .= '<option value="'.$elem['ID'].'">'.$elem['NAME'].'</option>';
            }
        }
        else $result['message'] = 'Нема напрямків навчання згідно поточних фільтрів (універ + мова + рівень)!';
        $result['activities'] = $activity_elements_res;

        $this->sentAnswer($result);
    }


    //ответ в консоль
    private function sentAnswer($answ){
        echo json_encode($answ);
    }

    //получение данных элементов
    private function getListElementsByFilter($arFilter,$arSelect){
        $result = array();
        $resultList = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
        while ($list = $resultList->Fetch()) {
            $result[] = $list;
        }
        return $result;
    }



}