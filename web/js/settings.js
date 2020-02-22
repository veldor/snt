"use strict";
function handleSettings() {
    let updateBtn = $('button#doUpdate');
    updateBtn.on('click.getUpdate',
        function () {
            sendAjax('get', '/update', simpleAnswerHandler);
        });
    let backupDbBtn = $('button#backupDb');
    backupDbBtn.on('click.getUpdate',
        function () {
            sendAjax('get', '/backup-db', function () {
                // сохраню файл
                let newWindow = window.open('/download-db-backup');
                newWindow.focus();
            });
        });

    let form = $('form#bank-settings-form');
    form.on('submit', function (e) {
       e.preventDefault();
       sendAjax('post',
                    '/bank-settings-edit',
                function () {
                            location.reload();
                         },
                        form,
                true);
    });
    let mailSettingsForm = $('form#mail-settings-form');
    mailSettingsForm.on('submit', function (e) {
       e.preventDefault();
       sendAjax('post',
                    '/mail-settings-edit',
                function () {
                            location.reload();
                         },
                        mailSettingsForm,
                true);
    });
    let powerSettingsForm = $('form#power-settings-form');
    console.log(powerSettingsForm);
    powerSettingsForm.on('submit', function (e) {
       e.preventDefault();
       sendAjax('post',
                    '/power-settings-edit',
                function () {
                            location.reload();
                         },
           powerSettingsForm,
                true);
    });

    $('#bank_set_li a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    let saveMailTemplateBtn = $('button#saveMailTemplateBtn');
    saveMailTemplateBtn.on('click.saveTemplate',function () {
        let templateData = CKEDITOR.instances.mailingBody.getData();
        if(templateData){
            sendAjax('post',
                '/save-mail-template',
                function (data) {
                    if(data.status){
                        makeInformer('success', 'Успешно', 'Шаблон письма сохранён');
                    }
                    else if(data.message){
                        makeInformer('danger', 'Ошибка отправки', data.message);
                    }
                },
                {'template' :  encodeURIComponent(templateData)}
            );
        }
        else{
            makeInformer("danger",
                'Ошибка!',
                "Необходимо заполнить шаблон рассылки");
        }
    });
    let saveBillMailTemplateBtn = $('button#saveBillMailTemplateBtn');
    saveBillMailTemplateBtn.on('click.saveTemplate',function () {
        let templateData =CKEDITOR.instances.billMailTemplateBody.getData();
        if(templateData){
            sendAjax('post',
                '/save-bill-mail-template',
                function (data) {
                    if(data.status){
                        makeInformer('success', 'Успешно', 'Шаблон письма сохранён');
                    }
                    else if(data.message){
                        makeInformer('danger', 'Ошибка отправки', data.message);
                    }
                },
                {'template' :  encodeURIComponent(templateData)}
            );
        }
        else{
            makeInformer("danger",
                'Ошибка!',
                "Необходимо заполнить шаблон рассылки");
        }
    });

    // при выборе файлов регистра- отправлю форму
    let registryInput = $('#restoreDbInput');
    registryInput.on('change.send', function () {
        if($(this).val()){
            $(this).parents('form').trigger('submit');
        }
    });
}

$(function () {
    handleSettings();
});