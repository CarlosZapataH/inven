var longitud = false,
    minuscula = false,
    numero = false,
    mayuscula = false,
    renew_pass = false, swal;

$(function() {
    "use strict";
    window.onload = function () {
        if (typeof history.pushState === "function") {
            history.pushState("jibberish", null, null);
            window.onpopstate = function () {
                history.pushState('newjibberish', null, null);
                // Handle the back (or forward) buttons here
                // Will NOT handle refresh, use onbeforeunload for this.
            };
        }
        else {
            var ignoreHashChange = true;
            window.onhashchange = function () {
                if (!ignoreHashChange) {
                    ignoreHashChange = true;
                    window.location.hash = Math.random();
                    // Detect and redirect change here
                    // Works in older FF and IE9
                    // * it does mess with your hash symbol (anchor?) pound sign
                    // delimiter on the end of the URL
                }
                else {
                    ignoreHashChange = false;
                }
            };
        }
    };
});

$(document).on('keyup','#clave_new',function () {
    "use strict";
    let btnChangePass = $('#btnChangePass_Modificar');
    let new_pass = $(this);
    let confir_pass = $('#clave_new_confirm');
    if (new_pass.val().length < 8) {
        $('#length').removeClass('text-success').addClass('text-danger');
        $('#length i').removeClass('ti-check text-success').addClass('ti-close text-danger');
        longitud = false;
    }
    else {
        $('#length').removeClass('text-danger').addClass('text-success');
        $('#length i').removeClass('ti-close text-danger').addClass('ti-check text-success');
        longitud = true;
    }
    //validate letter_ch
    if (new_pass.val().match(/[A-z]/)) {
        $('#letter').removeClass('text-danger').addClass('text-success');
        $('#letter i ').removeClass('ti-close text-danger').addClass('ti-check text-success');
        minuscula = true;
    }
    else {
        $('#letter').removeClass('text-success').addClass('text-danger');
        $('#letter i').removeClass('ti-check text-success').addClass('ti-close text-danger');
        minuscula = false;
    }
    //validate capital_ch letter_ch
    if (new_pass.val().match(/[A-Z]/)) {
        $('#capital').removeClass('text-danger').addClass('text-success');
        $('#capital i').removeClass('ti-close text-danger').addClass('ti-check text-success');
        mayuscula = true;
    }
    else {
        $('#capital').removeClass('text-success').addClass('text-danger');
        $('#capital i').removeClass('ti-check text-success').addClass('ti-close text-danger');
        mayuscula = false;
    }
    //validate number_ch
    if (new_pass.val().match(/\d/)) {
        $('#number').removeClass('text-danger').addClass('text-success');
        $('#number i').removeClass('ti-close text-danger').addClass('ti-check text-success');
        numero = true;
    }
    else {
        $('#number').removeClass('text-success').addClass('text-danger');
        $('#number i').removeClass('ti-check text-success').addClass('ti-close text-danger');
        numero = false;
    }

    if(longitud && minuscula && numero && mayuscula){
        if(confir_pass.val() ==="" || confir_pass.val().trim().length === 0){
            new_pass.parent().removeClass("has-danger has-success").addClass("has-success");
        }
        else{
            if(confir_pass.val() !=="" || confir_pass.val().trim().length > 0){
                if(new_pass.val() === confir_pass.val()){
                    new_pass.parent().removeClass("has-danger has-success").addClass("has-success");
                    btnChangePass.prop('disabled',false);
                    btnChangePass.addClass('btn-hover-transform');
                    btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-warningg");
                }
                else if(new_pass.val() !== confir_pass.val()){
                    new_pass.parent().removeClass("has-danger has-success").addClass("has-danger");
                    btnChangePass.prop('disabled',true);
                    btnChangePass.removeClass('btn-hover-transform');
                    btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-default");
                }
            }
        }

    }
    else{
        new_pass.parent().removeClass("has-danger has-success").addClass("has-danger");
        btnChangePass.prop('disabled',true);
        btnChangePass.removeClass('btn-hover-transform');
        btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-default");
    }
});

$(document).on('keyup','#clave_new_confirm',function(){
    "use strict";
    let btnChangePass = $('#btnChangePass_Modificar');
    let new_pass = $('#clave_new')
    let confir_pass = $(this);
    if((new_pass.val()!=="" || new_pass.val().trim().length>0) && (confir_pass.val()!=="" || confir_pass.val().trim().length>0)){
        if(new_pass.val() === confir_pass.val()){
            confir_pass.parent().removeClass("has-danger has-success").addClass("has-success");
            renew_pass = true;
        }
        else if(new_pass.val() !== confir_pass.val()){
            confir_pass.parent().removeClass("has-danger has-success").addClass("has-danger");
            renew_pass = false;
        }
    }
    else if((new_pass.val()==="" || new_pass.val().trim().length===0) && (confir_pass.val()==="" || confir_pass.val().trim().length===0)){
        confir_pass.removeClass("has-danger has-success");
        new_pass.removeClass("has-danger has-success");
        renew_pass = false;
    }

    if(renew_pass && longitud && minuscula && numero && mayuscula){
        btnChangePass.addClass('btn-hover-transform');
        btnChangePass.prop('disabled',false);
        btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-warningg");

    }else{
        btnChangePass.prop('disabled',true);
        btnChangePass.removeClass('btn-hover-transform');
        btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-default");
    }
});

$(document).on('submit', '#guardarDatos_Changepass', function(e){
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    let new_pass = $('#clave_new');
    let clave_actual = $('#clave_actual');
    sga.blockUI.loading_body();
    $.post('../controller/UsuarioController.php?action=Change_Password_Default_Token_JSON',data,
        function(response){
            if(parseInt(response.status)===1){
                Swal.fire({
                    text: "Su sesión se cerrara para validar su nueva clave",
                    type: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                    showLoaderOnConfirm: true,
                    allowEscapeKey: false,
                    preConfirm: function() {
                        return new Promise(function(resolve) {
                            setTimeout(function() {
                                parent.document.location.href	="../controller/CerrarSesionController.php";
                                resolve();
                            }, 2000);
                        });
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            title: "Sesión finalizada!",
                            text: "Cerrando sesión.",
                            showConfirmButton: false,
                            timer: 1800
                        });
                    }
                });
            }
            if(parseInt(response.status)===2){
                clave_actual.val("");
                sga.error.show('danger','id_error_pass',response.message);
                setTimeout(function() { $('#id_error_pass').html(""); }, 9000);
            }
            else if(parseInt(response.status)===0){
                new_pass.val("");
                sga.error.show('danger','id_error_pass',response.message);
                setTimeout(function() { $('#id_error_pass').html(""); }, 9000);
            }
    },"json").fail(function (e) {
        new_pass.val("");
        sga.error.show('danger','id_error_pass','Error al registrar la nueva contraseña.');
        setTimeout(function() { $('#id_error_pass').html(""); }, 9000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btn_logout',function(e){
    "use strict";
    e.preventDefault();
    sga.page.logout();
});