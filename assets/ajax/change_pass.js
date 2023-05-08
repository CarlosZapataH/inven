var longitud = false,
	minuscula = false,
	numero = false,
	mayuscula = false,
	renew_pass = false, swal;

$(document).ready(function() {
	"use strict";
	$('#pass_actual').focus();
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
			new_pass.parent().parent().removeClass("has-danger has-success").addClass("has-success");
		}
		else{
			if(confir_pass.val() !=="" || confir_pass.val().trim().length > 0){
				if(new_pass.val() === confir_pass.val()){
					new_pass.parent().parent().removeClass("has-danger has-success").addClass("has-success");
					btnChangePass.prop('disabled',false);
					btnChangePass.addClass('btn-hover-transform');
					btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-warningg");
				}
				else if(new_pass.val() !== confir_pass.val()){
					new_pass.parent().parent().removeClass("has-danger has-success").addClass("has-danger");
					btnChangePass.prop('disabled',true);
					btnChangePass.removeClass('btn-hover-transform');
					btnChangePass.removeClass("btn-default btn-warningg").addClass("btn-default");
				}
			}
		}

	}
	else{
		new_pass.parent().parent().removeClass("has-danger has-success").addClass("has-danger");
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
			confir_pass.parent().parent().removeClass("has-danger has-success").addClass("has-success");
			renew_pass = true;
		}
		else if(new_pass.val() !== confir_pass.val()){
			confir_pass.parent().parent().removeClass("has-danger has-success").addClass("has-danger");
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

$(document).on('submit','#form_Change_Password',function (e) {
	"use strict";
	e.preventDefault();
	let $data = $(this).serialize();
	let $disableAction = $('.disableAction_pass');
	let $pass_new  = $('#pass_new').val();
	let $pass_conf = $('#pass_new_confir').val();
	if($pass_new === $pass_conf) {
		sga.blockUI.loading_body();
		$.post('../controller/UsuarioController.php?action=Change_Password_Usuario_Ajax', $data,
			function (response) {
				if (parseInt(response.status) === 1) { //se cambio correctamente
					Swal.fire({
						title: 'Contraseña actualizada',
						text: "Su sesión se cerrara para validar su nueva clave.",
						type: 'success',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'Aceptar',
						showLoaderOnConfirm: true,
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
								timer: 1800,
								showLoaderOnConfirm: false,
								showConfirmButton: false,
								showCancelButton: false
							});
						}
					});
				}
				else if (parseInt(response) === 0) {
					sga.error.show('danger', 'mensaje_action_ps', 'Error al actualizar la contraseña.');
					window.setTimeout(function () { $('#mensaje_action_ps').html("");}, 6000);
				}
		},"json").fail(function () {
			sga.error.show('danger', 'mensaje_action_ps', 'Error al actualizar la contraseña, vuelva a intentarlo, si el problema persiste contacte al Administrador.');
			window.setTimeout(function () { $('#mensaje_action_ps').html("");}, 6000);
		}).always(function () {
			sga.blockUI.unblock_body();
		});
	}
	else{
		swal.fire({
			text: "Las contraseñas ingresadas no coinciden",
			type: "error",
			showCancelButton: false,
			showConfirmButton: true,
			allowOutsideClick: false,
			allowEscapeKey: false,
			confirmButtonText: 'Aceptar'
		}).then(function () {
			$disableAction.val("");
		});
	}

});

var typingTimer;                //timer identifier
var doneTypingInterval = 2000;  //time in ms (2 seconds)

$(document).on('keyup','#pass_actual',function () {
	"use strict";
	let pass_actual = $(this).val();
	let $disableAction = $('.disableAction_pass');
	let divMensaje = $('#mensaje_action_ps');
	clearTimeout(typingTimer);
	if($.trim(pass_actual).length > 0){
		let thisIdUS = $('#us_pss').val();
		let data = {'idus':thisIdUS, 'pass_ing':pass_actual};
		if (pass_actual) {
			typingTimer = setTimeout(doneTyping(data), doneTypingInterval);
		}
	}
	else{
		$disableAction.val("");
		$disableAction.prop('disabled',true);
		divMensaje.empty();
	}
});


function doneTyping (datos) {
	"use strict";
	let $disableAction = $('.disableAction_pass');
	let divMensaje = $('#mensaje_action_ps');
	$.get('../controller/UsuarioController.php?action=verifica_password_Usuario_Ajax',datos,function(response){
		if(parseInt(response.status) === 1){
			$disableAction.val("");
			$disableAction.prop('disabled',false);
			divMensaje.empty();
		}
		else if(parseInt(response.status) === 0){
			$disableAction.val("");
			$disableAction.prop('disabled',true);
			sga.error.show('danger','mensaje_action_ps',"La contrasela actual ingresada no coincide con la registrada.");
		}
	},"json");
}