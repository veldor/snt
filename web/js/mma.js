'use strict';

function handleMma(){
    // покажу шейдер после отправки формы
    let form = $('form');
    form.on('submit.showLoader', function () {
        showWaiter();
    });
}

$(function () {
    handleMma();
});