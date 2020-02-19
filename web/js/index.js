"use strict";

function handle() {
    let addNewBtn = $('button#addNewBtn');
    addNewBtn.on('click.add', function () {
        sendAjax('get', '/form/cottage-add', handleModalForm);
    });

}

$(function () {
    handle();
});
