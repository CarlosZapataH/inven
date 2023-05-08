$(function() {
    "use strict";
    sga.plugins.select2('.selectIclass');
});

$(document).on('change', '#id_perfil', function() {
    "use strict";
    let idperfil = $(this);
    let idmodOp = $('#id_modulo_opcion');
    if(idperfil.val() !== "" &&  idmodOp.val() !== ""){
        Tbl_Ini_Permisos(idperfil.val(), idmodOp.val());
    }
});

$(document).on('change', '#id_modulo_opcion', function(){
    "use strict";
    let idperfil = $('#id_perfil');
    let idmodOp = $(this);
    if(idperfil.val() !== "" &&  idmodOp.val() !== ""){
        Tbl_Ini_Permisos(idperfil.val(), idmodOp.val());
    }
});

$(document).on('change',".chkPermiso_modulo", function() {
    "use strict";
    let $this = $(this);
    let checked = 0;
    if ($this.is(":checked")) { checked = 1; }
    let datos = {
        'idmodulo':$this.attr('data-idmodulo'),
        'tipo':$this.attr('data-tipo'),
        'padre':$this.attr('data-padre'),
        'perfil':$this.attr('data-perfil'),
        'chekedd': checked
    };
    change_permiso_perfil(datos)
});

function change_permiso_perfil(datos) {
    "use strict";
    $.post('../controller/PermisoController.php?action=update_Permiso_Modulo_JSON', datos, function(response){
        if(parseInt(response.status) === 1){
            sga.error.show('success','mensajes_actions',"Permiso Actualizado satisfactoriamente.");
            window.setTimeout(function () { $('#mensajes_actions').html("");}, 6000);
        }
        else if(parseInt(response.status) === 0){
            sga.error.show('danger','mensajes_actions',"Error al actualziar el permiso del módulo.");
            window.setTimeout(function () { $('#mensajes_actions').html("");}, 6000);
        }
    },"json").always(function () {
        sga.blockUI.unblock_body();
    });
}

function Tbl_Ini_Permisos(idperfil, idmodOp){
    "use strict";
    let divResponse = $('#divResponse');
    divResponse.empty();
    sga.blockUI.loading_body();
    $.get('../controller/PermisoController.php?action=listar_Permisos_All_Ajax', {'id_perfil':idperfil,'id_modulo':idmodOp}, function(response){
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}