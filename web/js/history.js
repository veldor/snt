"use strict";


function handleHistory() {
    $('#bank_set_li a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $('#invoice_set_li a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    let resendMailingBtn = $('button.resend-mailing');
    resendMailingBtn.on('click.resend', function () {
        let newWindow = window.open('/mailing/' + $(this).attr('data-id'));
        newWindow.focus();
    });

    let deleteMailingBtn = $('button.delete-mailing');
    deleteMailingBtn.on('click.clear', function () {
        let anchor = $(this);
        makeInformerModal(
            'Удаление рассылки',
            'Удалить рассылку? Вместе с ней будут удалены все сообщения этой рассылки',
            function () {
                sendAjax('post',
                    '/delete-mailing',
                    simpleAnswerHandler,
                    {'id' : anchor.attr('data-id')});
            },
            function () {

            }
        );
    });
    let infoMailingBtn = $('button.info-mailing');
    infoMailingBtn.on('click.clear', function () {
        sendAjax('get',
            '/mailing-info/' + $(this).attr('data-id'),
            function (data) {
            if(data.status && data.status === 1){
                makeModal('Информация о рассылке', data.html);
            }
        });
    });
    let infoBillBtn = $('button.info-bill');
    infoBillBtn.on('click.clear', function () {
        sendAjax('get',
            '/bill-info/' + $(this).attr('data-id'),
            function (data) {
            if(data.status && data.status === 1){
                makeModal('Информация о счете', data.html);
            }
        });
    });
    let deleteMassBillBtn = $('button.delete-mass-bill');
    deleteMassBillBtn.on('click.clear', function () {
        let anchor = $(this);
        makeInformerModal(
            'Удаление счетов',
            'Удалить счета, выставленные в этом блоке? Вместе с ними будут удалены все неотправленные сообщения об этих счетах',
            function () {
                sendAjax('post',
                    '/delete-bills',
                    simpleAnswerHandler,
                    {'id' : anchor.attr('data-id')});
            },
            function () {

            }
        );
    });
}

$(function () {
    handleHistory();
});