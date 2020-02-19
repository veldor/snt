"use strict";
const cottageNumber = location.pathname.split('/')[2];

function handleMe() {

    // обработка действий по платежу
    $('.tooltip-enabled').tooltip();

    let savePdfBtn = $('.save-pdf');
    savePdfBtn.on('click.savePdf', function () {
        sendAjax('post', '/invoice-saved', function () {
        }, {'billNumber': $(this).attr('data-bill-id')});
    });

    let printBillBtn = $('.print-bill');
    printBillBtn.on('click.print', function () {
        sendAjax('post', '/invoice-printed', function () {
        }, {'billNumber': $(this).attr('data-bill-id')});
    });

    let sendBillBtn = $('.send-bill');
    sendBillBtn.on('click.send', function () {
        sendAjax('post',
            '/send-notifications',
            function (data) {
                if (data.status === 1) {
                    // открою в новом окне очередь печати
                    let newWindow = window.open('/mailing-schedule');
                    newWindow.focus();
                } else if (data.message) {
                    makeInformer('danger', "Ошибка", data.message);
                }
            },
            {'billNumber': $(this).attr('data-bill-id')});
    });

    // счета
    // выставление счёта
    let addBillButton = $('button#addBill');
    addBillButton.on('click.addBill', function () {
        sendAjax('get', '/form/bill-add/' + cottageNumber, handleModalForm);
    });
    // выставление счёта за электроэнергию
    let addPowerBillButton = $('button#addPowerBill');
    addPowerBillButton.on('click.addBill', function () {
        sendAjax('get', '/form/power-bill-add/' + cottageNumber, handleModalForm);
    });


    let billDelete = $('.bill-delete');
    billDelete.on('click.delete', function () {
        let anchor = $(this);
        makeInformerModal('Удаление счёта',
            "Удалить выбранный счёт?",
            function () {
                let id = anchor.attr('data-id');
                sendAjax('post',
                    '/bill-delete',
                    ajaxFormAnswerHandler,
                    {'id': id}
                );
            },
            function () {
            });
    });


    // изменение данных о участке
    let changeCottageBtn = $('button#changeCottageBtn');
    changeCottageBtn.on('click.getForm', function () {
        sendAjax('get', '/form/cottage-change/' + cottageNumber, handleModalForm);
    });
    // изменение данных о плательщике
    let changeBillBtn = $('button.bill-change');
    changeBillBtn.on('click.getForm', function () {
        sendAjax('get', '/form/bill-change/' + $(this).attr('data-id'), handleModalForm);
    });

    // ПЛАТЕЛЬЩИК
    let addPayerBtn = $('button#addPayer');
    addPayerBtn.on('click.addPayer', function () {
        sendAjax('get', '/form/payer-add/' + cottageNumber, handleModalForm);
    });

    // изменение данных о плательщике
    let changePayerInfoBtn = $('button.payer-change');
    changePayerInfoBtn.on('click.getForm', function () {
        sendAjax('get', '/form/payer-change/' + $(this).attr('data-id'), handleModalForm);
    });


    let payerDelete = $('.payer-delete');
    payerDelete.on('click.delete', function () {
        let anchor = $(this);
        makeInformerModal('Удаление плательщика',
            "Удалить плательщика?",
            function () {
                let id = anchor.attr('data-id');
                sendAjax('post',
                    '/payer-delete',
                    ajaxFormAnswerHandler,
                    {'id': id}
                );
            },
            function () {
            });
    });

    // ЭЛЕКТРОННАЯ ПОЧТА ==================================================================================
    let addMailBtn = $('button#addMailBtn');
    addMailBtn.on('click.addMail', function () {
        sendAjax('get', '/form/mail-add/' + cottageNumber, handleModalForm);
    });

    let changeMailBtn = $('button.mail-change');
    changeMailBtn.on('click.changeMail', function () {
        sendAjax('get', '/form/mail-change/' + $(this).attr('data-id'), handleModalForm);
    });

    let mailDelete = $('.mail-delete');
    mailDelete.on('click.delete', function () {
        let anchor = $(this);
        makeInformerModal('Удаление электронной почты',
            "Удалить адрес электронной почты?",
            function () {
                let id = anchor.attr('data-id');
                sendAjax('post',
                    '/mail-delete',
                    ajaxFormAnswerHandler,
                    {'id': id}
                );
            },
            function () {
            });
    });

    // НОМЕРА ТЕЛЕФОНОВ ==================================================================================
    let addPhoneBtn = $('button#addPhoneBtn');
    addPhoneBtn.on('click.addPhone', function () {
        sendAjax('get', '/form/phone-add/' + cottageNumber, handleModalForm);
    });

    let changePhoneBtn = $('button.phone-change');
    changePhoneBtn.on('click.changePhone', function () {
        sendAjax('get', '/form/phone-change/' + $(this).attr('data-id'), handleModalForm);
    });

    let phoneDelete = $('.phone-delete');
    phoneDelete.on('click.delete', function () {
        let anchor = $(this);
        makeInformerModal('Удаление номера телефона',
            "Удалить номер телефона?",
            function () {
                let id = anchor.attr('data-id');
                sendAjax('post',
                    '/phone-delete',
                    ajaxFormAnswerHandler,
                    {'id': id}
                );
            },
            function () {
            });
    });

    let deleteCottageBtn = $('button#deleteCottageBtn');
    deleteCottageBtn.on('click.delete', function () {
        makeInformerModal("Удаление участка",
            "Будут удалены все данные об участке, включая плательщиков, адреса электронной почты и выставленные счета",
            function () {
                sendAjax(
                    'post',
                    '/cottage-delete',
                    function (data) {
                        if (data) {
                            if (data.status === 1) {
                                normalReload();
                                location.replace("/");
                            } else {
                                if (data.message) {
                                    makeInformer('warning', 'Ошибка', data.message);
                                }
                            }
                        }
                    },
                    {'cottageNumber': cottageNumber});
            },
            function () {
            });
    });

}

$(function () {
    handleMe();
});