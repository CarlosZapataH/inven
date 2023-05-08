
$.cargando = "Cargando...";

$(document).ready(function() {
    "use strict";
    $('#txtNroDoc').focus();

    reload_captcha();

    let refreshId =  setInterval( function(){
        reload_captcha();
    }, 120000);
});


function reload_captcha() {
    "use strict";
    var captcha = $('#captcha');
    captcha.addClass('d-none');
    sga.spinner.captcha('#spiner_CaptchaContend');
    $.get('../assets/plugins/captcha/reLoad_captcha.php', function(response){
        var resul = JSON.parse(response);
        captcha.prop('src', resul.image_src);
    }).always(function() {
        $('#spiner_CaptchaContend').html("");
        captcha.removeClass('d-none');
    });
}
