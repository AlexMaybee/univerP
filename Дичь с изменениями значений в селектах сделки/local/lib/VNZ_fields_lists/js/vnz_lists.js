

BX.ready(function() {

   // console.log('Yo, Nigga!');
    let vnzObj = new VnzListsFunctions();

});

class VnzListsFunctions{

    constructor(){
        this.url = window.location.href;

        this.isDealOpened = this.chechUrlIfDealOpened(this.url);
      //  console.log(this.isDealOpened);



        if(this.isDealOpened !== false){
            var other = this;

            console.log(this.isDealOpened[1]);
            $('div[data-cid="UF_CRM_1552650638"]').css({'pointer-events':'none','opacity':'0.3'}); // поле Город ВНЗ

            $(document).on("DOMNodeInserted", function (event) {


                this.city_select = $('select[name="UF_CRM_1552650638"]'); //1й селект с городом;
                this.univer_select = $('select[name="UF_CRM_1552650693"]'); //2й селект с вузом;
                this.other_VUZ = $('input[name="UF_CRM_1552982722"]'); //5й инпут для Другой ВНЗ
                this.language_select = $('select[name="UF_CRM_1552650744"]'); //3й селект с языком изучения
                this.level_select = $('select[name="UF_CRM_1552650798"]'); //4й селект с уровнем обучения (бакалавр, ...)
                this.activity_direction_select = $('select[name="UF_CRM_1552650852"]'); //4й селект с направлением обучения


                //деактивация всех селктов в отображении на странице
                $('div[data-cid="UF_CRM_1552650638"]').css({'pointer-events':'none','opacity':'0.3'});
                $('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'none','opacity':'0.3'});
                $('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'none','opacity':'0.3'});
                $('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'none','opacity':'0.3'});
                $('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'none','opacity':'0.3'});

                $('div[data-cid="UF_CRM_1552982722"]').css({'pointer-events':'none','opacity':'0.3'});
                $('div[data-cid="UF_CRM_1552982722"]').css({'display':'none'});


                //console.log(this.city_select);
                //если
                if(this.city_select.length > 0){

                    //это для работы в дальнейших функциях
                    var self = this;


                   // other.getActivityDirectionOptions(this.univer_select,this.language_select,this.level_select,this.activity_direction_select);


                    //активируем селект с городами (главный)
                    this.city_select.closest('div[data-cid="UF_CRM_1552650638"]').css({'pointer-events':'auto','opacity':'1'});

                    //1. Если город выбран, то активируем селект с универами и вставляем в него только универы == id option'a города
                    if(this.city_select.val() != undefined && this.city_select.val().length > 0){

                        //активируем выбором универов
                        this.univer_select.closest('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'auto','opacity':'1'});

                    }

                    //изменение селекта Городов
                    this.city_select.change(function () {

                        console.log('Изменение города!');
                        if(self.city_select.val() == undefined || self.city_select.val().length <= 0){
                            //ДеАктивируем селекты:
                         //   self.univer_select.empty();
                         //   self.language_select.empty();
                         //   self.level_select.empty();
                         //   self.activity_direction_select.empty();
                            self.univer_select.closest('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'none','opacity':'0.3'}); //ВУЗЫ
                            self.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'none','opacity':'0.3'}); // ЯЗЫКИ
                            self.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'none','opacity':'0.3'}); // УРОВЕНЬ
                            self.activity_direction_select.closest('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'none','opacity':'0.3'}); // НАПРАВЛЕНИЕ
                        }
                        else {
                            //активируем поля и вставляем нужные значения
                            self.univer_select.closest('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'auto','opacity':'1'});

                            BX.ajax({
                                method: "POST",
                                url: '/local/lib/VNZ_fields_lists/ajax/handler.php', // \local\lib\VNZ_fields_lists\ajax\handler.php
                                data: {'CITY_ID':self.city_select.val(),'ACTION':'GIVE_ME_VUZ_BY_CITY_ID'},
                                dataType: "json",
                                onsuccess: function (data) {

                                    console.log(data);
                                    //Если результат пустой, то очищаем селект с Вузами и деактивируем его!
                                    if(data.options === false || data.options == null){
                                        //ДеАктивируем селекты:
                                       // self.univer_select.empty();
                                      //  self.language_select.empty();
                                       // self.level_select.empty();
                                       // self.activity_direction_select.empty();
                                        self.univer_select.closest('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'none','opacity':'0.3'}); //ВУЗЫ
                                        self.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'none','opacity':'0.3'}); // ЯЗЫКИ
                                        self.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'none','opacity':'0.3'}); // УРОВЕНЬ
                                        self.activity_direction_select.closest('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'none','opacity':'0.3'}); // НАПРАВЛЕНИЕ
                                    }
                                    else {
                                        //активируем поля и вставляем нужные значения
                                        self.univer_select.empty();
                                        self.univer_select.append(data.options);
                                        self.univer_select.closest('div[data-cid="UF_CRM_1552650693"]').css({'pointer-events':'auto','opacity':'1'});
                                        self.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'auto','opacity':'1'});
                                    }

                                }
                            });

                        }
                       // console.log(self.city_select.val());
                    });


                    //2. Если ВУЗ выбран, то активируем селект с Языками и вставляем в него только универы == id option'a языков
                    if(this.univer_select.val() != undefined && this.univer_select.val().length > 0){

                        //активируем выбором универов
                        this.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'auto','opacity':'1'});
                        this.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'auto','opacity':'1'});

                    }

                    //изменение селекта Вузов
                    this.univer_select.change(function () {
                     //   console.log(self.univer_select.val());
                        //запуск только если значение > 0
                        console.log('Универа!');
                        //пустое значение - блокируем все
                        if(self.univer_select.val() == undefined || self.univer_select.val().length <= 0){
                            //ДеАктивируем селекты:
                         //   self.language_select.empty();
                         //   self.level_select.empty();
                            self.activity_direction_select.empty();
                            self.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'none','opacity':'0.3'}); // ЯЗЫКИ
                            self.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'none','opacity':'0.3'}); // УРОВЕНЬ
                            self.activity_direction_select.closest('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'none','opacity':'0.3'}); // НАПРАВЛЕНИЕ
                        }
                        //активируем поле ниже Языков, значения НЕ вставляем и запросы НЕ делаем
                        else {
                            console.log(self.univer_select.val());
                            //Если Выбрано значение "Другое", то отображаем поле "ВНЗ другое"
                            if(self.univer_select.val() == '1'){
                                self.other_VUZ.closest('div[data-cid="UF_CRM_1552982722"]').css({'display':'block'}); // ДОП. ВУЗ
                                self.other_VUZ.closest('div[data-cid="UF_CRM_1552982722"]').css({'pointer-events':'auto','opacity':'1'});

                            }
                            else {
                                self.other_VUZ.closest('div[data-cid="UF_CRM_1552982722"]').css({'display':'none'}); // ДОП. ВУЗ
                                self.other_VUZ.closest('div[data-cid="UF_CRM_1552982722"]').css({'pointer-events':'none','opacity':'0.3'});
                            }

                            self.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'auto','opacity':'1'});

                           // getActivityDirectionOptions();
                        }
                    });


                   // 3. Статика. Если Язык выбран, то активируем селект с Уровнем и вставляем в него только универы == id option'a языков
                    if(this.univer_select.val() != undefined && this.univer_select.val().length > 0){

                        //активируем выбором универов
                        this.language_select.closest('div[data-cid="UF_CRM_1552650744"]').css({'pointer-events':'auto','opacity':'1'});
                        this.level_select.closest('div[data-cid="UF_CRM_1552650798"]').css({'pointer-events':'auto','opacity':'1'});

                    }


                }



            });



            /*$('select[name="UF_CRM_1552650638"]').change(function () {
                console.log($('select[name="UF_CRM_1552650638"]').val()); // значение поля Города

            })*/


        }
    }

    //функция сверки url, что он соотв. открытой сделке
    chechUrlIfDealOpened (urlStr){
        var matchMassive;
        if(matchMassive = urlStr.match(/\/crm\/deal\/details\/([\d]+)/i)){


            //console.log(matchMassive);

            return matchMassive[1] > 0 ? matchMassive : false; //в массиве 0 - url, 1 - current user id, 2 - task id
        }
        else return false
    }

    //функция, которая будет проверять значение селекта с городами
    getActivityDirectionOptions(univer,lang,level,activity){

        var self = this;

      /*  this.univer_select = $('select[name="UF_CRM_1552650693"]'); //2й селект с вузом;
        this.other_VUZ = $('input[name="UF_CRM_1552982722"]'); //5й инпут для Другой ВНЗ
        this.language_select = $('select[name="UF_CRM_1552650744"]'); //3й селект с языком изучения
        this.level_select = $('select[name="UF_CRM_1552650798"]'); //4й селект с уровнем обучения (бакалавр, ...)*/

        console.log('Activity Direction Options !!!');
        console.log(univer.val());
        console.log(lang.val());
        console.log(level.val());

        if(
            (univer.val() != undefined && univer.val().length > 0) && //если заполнены Универ, Язык и Уровень
            (lang.val() != undefined && lang.val().length > 0) &&
            (level.val() != undefined && level.val().length > 0)
        ){
            console.log('Универ не пустой!');

            //аякс-запрос для получения направлений обучения
            BX.ajax({
                method: "POST",
                url: '/local/lib/VNZ_fields_lists/ajax/handler.php', // \local\lib\VNZ_fields_lists\ajax\handler.php
                data: {'UNIVER_ID':univer.val(),'LANG_ID':lang.val(),'LEVEL_ID':level.val(),'ACTION':'GIVE_ME_ACTIVITY_DIRECTION_OPTIONS'},
                dataType: "json",
                onsuccess: function (data) {

                    console.log(data);
                    console.log(activity);
                    //Если результат пустой, то очищаем селект с Вузами и деактивируем его!
                    if(data.options === false || data.options == null){
                        //ДеАктивируем селекты:
                        activity.closest('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'none','opacity':'0.3'}); // НАПРАВЛЕНИЕ
                    }
                    else {
                        //активируем поля и вставляем нужные значения
                        activity.empty();
                        activity.append(data.options);
                        activity.closest('div[data-cid="UF_CRM_1552650852"]').css({'pointer-events':'auto','opacity':'1'});
                    }

                }
            });
            //аякс-запрос для получения направлений обучения




        }


    }



}
