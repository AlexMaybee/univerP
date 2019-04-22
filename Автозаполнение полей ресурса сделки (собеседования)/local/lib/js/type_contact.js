
BX.ready(function(){

    $(document).on("DOMNodeInserted", function (event) {

        var contact_list = document.querySelectorAll('a.crm-entity-widget-client-box-name'),
            contactId, matches;
        //console.log(contact_list);
        if(contact_list.length > 0 ) {
            for (var i = 0; i < contact_list.length; i++) {
                if(contact_list[i]) {
                    //console.log(contact_list[i].href);
                    if (contact_list[i].href.match(/\/contact\/details\/([\d]+)\//i) && !contact_list[i].className.includes('active')) {
                        matches = contact_list[i].href.match(/\/contact\/details\/([\d]+)\//i);
                        contactId = matches[1];
                        contact_list[i].className += " active";
                        contact_list[i].id = "contact_" + contactId;
                        var key = i;
                        if(contactId > 0) {
                            BX.ajax({
                                url: '/local/ajax/getTypeContact.php',
                                data: {'id': contactId, 'action': 'getContactId'},
                                method: 'POST',
                                dataType: 'json',
                                timeout: 30,
                                async: true,
                                processData: true,
                                scriptsRunFirst: true,
                                emulateOnload: true,
                                start: true,
                                cache: false,
                                onsuccess: function(data) {
                                     if(data.id) {
                                         document.getElementById("contact_" + data.id).insertAdjacentHTML("beforeend", " <span style='color: #ed0000'>(" + data.status + ")</span>");
                                    }

                                },
                                onfailure: function(){

                                }
                            });
                        }

                     }
                }
            }
        }

    });

});
//#Печатать задачи