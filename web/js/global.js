'use strict';


function stringify(data) {
    if (typeof data === 'string') {
        return data;
    } else if (typeof data === 'object') {
        let answer = '';
        for (let i in data) {
            answer += data[i] + '<br/>';
        }
        return answer;
    }
}

function deleteWaiter() {
    $('div.wrap, div.flyingSumm, div.modal').removeClass('blured');
    $('body').css({'overflow': ''});
    let shader = $('div.shader');
    if (shader.length > 0)
        shader.hideLoading().remove();
}

// ТИПИЧНАЯ ОБРАБОТКА ОТВЕТА AJAX
function simpleAnswerHandler(data) {
    if (data['status']) {
        if (data['status'] === 1) {
            let message = data['message'] ? data['message'] : 'Операция успешно завершена';
            makeInformerModal("Успешно", message);
        } else {
            makeInformer('info', 'Ошибка, статус: ' + data['status'], stringify(data['message']));
        }
    } else {
        makeInformer('alert', 'Ошибка', stringify(data));
    }
}

// ========================================================== ИНФОРМЕР


// СКРЫВАЮ ИНФОРМЕР
function closeAlert(alertDiv) {
    const elemWidth = alertDiv[0].offsetWidth;
    alertDiv.animate({
        left: elemWidth
    }, 500, function () {
        alertDiv.animate({
            height: 0,
            opacity: 0
        }, 300, function () {
            alertDiv.remove();
        });
    });
}

// ПОКАЗЫВАЮ ИНФОРМЕР
function showAlert(alertDiv) {
    // считаю расстояние от верха страницы до места, где располагается информер
    const topShift = alertDiv[0].offsetTop;
    const elemHeight = alertDiv[0].offsetHeight;
    let shift = topShift + elemHeight;
    alertDiv.css({'top': -shift + 'px', 'opacity': '0.1'});
    // анимирую появление информера
    alertDiv.animate({
        top: 0,
        opacity: 1
    }, 500, function () {
        // запускаю таймер самоуничтожения через 5 секунд
        setTimeout(function () {
            closeAlert(alertDiv)
        }, 50000);
    });

}

// СОЗДАЮ ИНФОРМЕР
function makeInformer(type, header, body) {
    if (!body)
        body = '';
    const container = $('div#alertsContentDiv');
    const informer = $('<div class="alert-wrapper"><div class="alert alert-' + type + ' alert-dismissable my-alert"><div class="panel panel-' + type + '"><div class="panel-heading">' + header + '<button type="button" class="close">&times;</button></div><div class="panel-body">' + body + '</div></div></div></div>');
    informer.find('button.close').on('click.hide', function (e) {
        e.preventDefault();
        closeAlert(informer);
    });
    container.append(informer);
    showAlert(informer);
}


function dangerReload() {
    $(window).on('beforeunload.message', function () {
        return "Необходимо заполнить все поля на странице!";
    });
}

function normalReload() {
    $(window).off('beforeunload');
}

function serialize(obj) {
    const str = [];
    for (let p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    return str.join("&");
}

function showWaiter() {
    let shader = $('<div class="shader"></div>');
    $('body').append(shader).css({'overflow': 'hidden'});

    $('div.wrap, div.flyingSumm, div.modal').addClass('blured');
    shader.showLoading();
}


function ajaxDangerReload() {
    $(window).on('beforeunload.ajax', function () {
        return "Необходимо заполнить все поля на странице!";
    });
}

function ajaxNormalReload() {
    $(window).off('beforeunload.ajax');
}

function sendAjax(method, url, callback, attributes, isForm, silent) {
    if (!silent){
        showWaiter();
        ajaxDangerReload();
    }
    // проверю, не является ли ссылка на арртибуты ссылкой на форму
    if (attributes && attributes instanceof jQuery && attributes.is('form')) {
        attributes = attributes.serialize();
    } else if (isForm) {
        attributes = $(attributes).serialize();
    } else {
        attributes = serialize(attributes);
    }
    if (method === 'get') {
        $.ajax({
            method: method,
            data: attributes,
            url: url
        }).done(function (e) {
            deleteWaiter();
            ajaxNormalReload();
            callback(e);
        }).fail(function (e) {// noinspection JSUnresolvedVariable
            ajaxNormalReload();
            deleteWaiter();
            if (e.responseJSON) {// noinspection JSUnresolvedVariable
                makeInformer('danger', 'Системная ошибка', e.responseJSON['message']);
            } else {
                makeInformer('info', 'Ответ системы', e.responseText);
                console.log(e);
            }
            //callback(false)
        });
    } else if (method === 'post') {
        $.ajax({
            data: attributes,
            method: method,
            url: url
        }).done(function (e) {
            deleteWaiter();
            normalReload();
            callback(e);
        }).fail(function (e) {// noinspection JSUnresolvedVariable
            deleteWaiter();
            normalReload();
            if (e['responseJSON']) {// noinspection JSUnresolvedVariable
                makeInformer('danger', 'Системная ошибка', e.responseJSON.message);
            } else {
                makeInformer('info', 'Ответ системы', e.responseText);
            }
            //callback(false)
        });
    }
}

function makeInformerModal(header, text, acceptAction, declineAction) {
    if (!text)
        text = '';
    let modal = $('<div class="modal fade mode-choose"><div class="modal-dialog text-center"><div class="modal-content"><div class="modal-header"><h3>' + header + '</h3></div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-success" type="button" id="acceptActionBtn">Ок</button></div></div></div>');
    $('body').append(modal);
    let acceptButton = modal.find('button#acceptActionBtn');
    if (declineAction) {
        let declineBtn = $('<button class="btn btn-warning" role="button">Отмена</button>');
        declineBtn.insertAfter(acceptButton);
        declineBtn.on('click.custom', function () {
            normalReload();
            modal.modal('hide');
            declineAction();
        });
    }
    dangerReload();
    modal.modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    modal.on('hidden.bs.modal', function () {
        normalReload();
        modal.remove();
        $('div.wrap div.container, div.wrap nav').removeClass('blured');
    });
    $('div.wrap div.container, div.wrap nav').addClass('blured');

    acceptButton.on('click', function () {
        normalReload();
        modal.modal('hide');
        if (acceptAction) {
            acceptAction();
        } else {
            location.reload();
        }
    });

    return modal;
}


// Функция вызова пустого модального окна
function makeModal(header, text, delayed) {
    if (delayed) {
        // открытие модали поверх другой модали
        let modal = $("#myModal");
        if (modal.length == 1) {
            modal.modal('hide');
            let newModal = $('<div id="myModal" class="modal fade mode-choose"><div class="modal-dialog  modal-lg"><div class="modal-content"><div class="modal-header">' + header + '</div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-danger"  data-dismiss="modal" type="button" id="cancelActionButton">Отмена</button></div></div></div>');
            modal.on('hidden.bs.modal', function () {
                modal.remove();
                if (!text)
                    text = '';
                $('body').append(newModal);
                dangerReload();
                newModal.modal({
                    keyboard: true,
                    show: true
                });
                newModal.on('hidden.bs.modal', function () {
                    normalReload();
                    newModal.remove();
                    $('div.wrap div.container, div.wrap nav').removeClass('blured');
                });
                $('div.wrap div.container, div.wrap nav').addClass('blured');
            });
            return newModal;
        }
    }
    if (!text)
        text = '';
    let modal = $('<div id="myModal" class="modal fade mode-choose"><div class="modal-dialog  modal-lg"><div class="modal-content"><div class="modal-header">' + header + '</div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-danger"  data-dismiss="modal" type="button" id="cancelActionButton">Отмена</button></div></div></div>');
    $('body').append(modal);
    dangerReload();
    modal.modal({
        keyboard: true,
        show: true
    });
    modal.on('hidden.bs.modal', function () {
        normalReload();
        modal.remove();
        $('div.wrap div.container, div.wrap nav').removeClass('blured');
    });
    $('div.wrap div.container, div.wrap nav').addClass('blured');
    return modal;
}

function ajaxFormAnswerHandler(data) {
    if (data.status === 1) {
        normalReload();
        location.reload();
    } else if (data.message) {
        makeInformer('danger', "Ошибка", data.message);
    }
}

function handleModalForm(data) {
    if (data.status && data.status === 1) {
        let modal = makeModal(data.header, data.data);
        let form = modal.find('form');
        form.on('submit.sendByAjax', function (e) {
            e.preventDefault();
            sendAjax('post',
                form.attr('action'),
                ajaxFormAnswerHandler,
                form,
                true);
        });
    }
}


function handleGlobalOptions() {
    // активирую переход к участку по ссылке
    $('#goToCottageActivator').on('click.go', function () {
        let cottageValue = $('#goToCottageInput').val();
        if(cottageValue){
            location.replace('/show/' + cottageValue);
        }
    });
    $('#goToCottageInput').on('keypress.go', function (e) {
        if(e.charCode === 13){
            let cottageValue = $('#goToCottageInput').val();
            if(cottageValue){
                location.replace('/show/' + cottageValue);
            }
        }
    });

    let unsendedMessagesBage = $('span#unsendedMessagesBadge');
    setInterval(function () {
        sendAjax('get',
            '/get-unsended-messages-count',
            function (data) {
                if (data.status && data.status === 1) {
                    unsendedMessagesBage.text(data.count);
                    if (data.count > 0)
                        unsendedMessagesBage.addClass('badge-danger');
                    else{
                        unsendedMessagesBage.removeClass('badge-danger');
                    }
                }
            },
            false,
            false,
            true);
    }, 1000);
}

$(function () {
    //handleGlobalOptions();
});

function roundRubles(num) {
    return Math.round(num * 100) / 100;
}

