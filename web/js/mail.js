"use strict";

function handleMailing() {
    // назначу действия кнопкам управления адресами
    let selectAllActivator = $('#selectAllActivator');
    let selectNoneActivator = $('#selectNoneActivator');
    let selectInvertActivator = $('#selectInvertActivator');
    let destination = $('input.mail-target[type="checkbox"]');
    console.log(destination);

    selectAllActivator.on('click.change', function (e) {
        e.preventDefault();
        destination.prop('checked', true);
    });
    selectNoneActivator.on('click.change', function (e) {
        e.preventDefault();
        destination.prop('checked', false);
    });
    selectInvertActivator.on('click.change', function (e) {
        e.preventDefault();
        destination.each(function () {
            $(this).prop('checked', !$(this).prop('checked'));
        });
    });

    let createMailingActivatorBtn = $('button#createMailingActivator');
    let titleInput = $('input#mailingSubject');

    createMailingActivatorBtn.on('click.createMailing', function () {
        let mailData;
        for (let i in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(i)) {
                mailData = CKEDITOR.instances[i].getData();
                break;
            }
        }
        if (!mailData) {
            makeInformer("danger",
                'Ошибка!',
                "Необходимо заполнить текст рассылки");
            return;
        }
        if (!titleInput.val()) {
            makeInformer("danger",
                'Ошибка!',
                "Необходимо заполнить тему рассылки");
            titleInput.focus();
            return;
        }
        // найду адреса почты, отмеченные для рассылки
        // если они не найдены- укажу, что они должны быть указаны
        let active = destination.filter(':checked');
        if (active.length === 0) {
            makeInformer("danger",
                'Ошибка!',
                "Выберите хотя бы один адрес для рассылки");
            return;
        }

        let addressesList = [];
        active.each(function () {
           addressesList.push($(this).attr('data-mail-id'));
        });

        // отправлю запрос на создание рассылки
        sendAjax('post',
            '/mailing-create',
            function (data) {
                if (data) {
                    if (data.status === 1) {
                        // открою окно с очередью рассылки писем
                        let win = window.open('/mailing-schedule', '_blank');
                        win.focus();
                    } else {
                        if (data.message) {
                            makeInformer('warning', 'Ошибка', data.message);
                        }
                    }
                }
                     },
            {'title' : encodeURIComponent(titleInput.val()), 'body' : encodeURIComponent(mailData), 'addresses' : addressesList}
            );

    });
}

$(function () {
    handleMailing();
});