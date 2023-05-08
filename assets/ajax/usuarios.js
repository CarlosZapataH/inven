var $Tbl_Usuario, inputFIle, tblLoad, $Nregistros = 0, $count = 0;

inputFIle ='<div class="row">';
inputFIle+='  <div class="col-12 mb-20 mt-20" id="div_FileInput" style="display:block">';
inputFIle+='    <h5 class="">Importar Usuarios</h5>';
inputFIle+='    <p>Adjunte el archivo con los datos contemplados en la plantilla.</p>';
inputFIle+='    <div class="card">';
inputFIle+='      <div class="card-body">';
inputFIle+='        <h6 class="card-subtitle mb-10">Seleccione un tipo de tabla a actualizar y/o cargar.</h6>';
inputFIle+='        <form id="form_Viewtable_listDatos" enctype="multipart/form-data">';
inputFIle+='          <div class="row">';
inputFIle+='            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">';
inputFIle+='              <select id="perfil_im" name="perfil_im" class="form-control selectPClass" data-placeholder="Perfil..." required>';
inputFIle+='                <option></option>';
inputFIle+='              </select>';
inputFIle+='            </div>';
inputFIle+='            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
inputFIle+='              <input type="file" class="file" id="filedata_import" name="filedata_import" required';
inputFIle+='                     data-show-preview="false" data-show-upload="true"';
inputFIle+='                     data-show-caption="true" data-show-remove="true"';
inputFIle+='                     data-show-cancel="false"';
inputFIle+='                     data-browse-Label="Examinar"';
inputFIle+='                     data-remove-Label="Eliminar"';
inputFIle+='                     data-upload-Label="Visualizar"';
inputFIle+='                     data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"';
inputFIle+='                     data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"';
inputFIle+='                     data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">';
inputFIle+='                <span class="help-block">';
inputFIle+='                    <small>Formatos permitidos [<code>xls, xlsx</code>].</small>';
inputFIle+='                </span>';
inputFIle+='            </div>';
inputFIle+='          </div>';
inputFIle+='        </form>';
inputFIle+='      </div>';
inputFIle+='      <hr class="no-margin">';
inputFIle+='      <div class="card-body text-right">';
inputFIle+='        <button type="button" id="btnCancel_Tab" class="btn btn-light mr-20">Cancelar</button>';
inputFIle+='        <a class="btn bg-success-0 btn-hover-transform" href="../assets/formato//Plantilla-Usuarios.xlsx" ';
inputFIle+='           download="Plantilla-Usuarios.xlsx">Descargar Plantilla</a>';
inputFIle+='      </div>';
inputFIle+='    </div>';
inputFIle+='  </div>';
inputFIle+='  <div class="col-12 mb-20 mt-20" id="divResponse_All" style="display:none"></div>';
inputFIle+='</div>';

tblLoad ='<table id="TblStandar_Load" class="table table-bordered" cellpadding="0" cellspacing="0" width="100%">';
tblLoad+='  <thead>';
tblLoad+='    <tr>';
tblLoad+='      <th class="text-center">#</th>';
tblLoad+='      <th class="text-center">Área/Servicio</th>';
tblLoad+='      <th class="text-center">Apellido Paterno</th>';
tblLoad+='      <th class="text-center">Apellido Materno</th>';
tblLoad+='      <th class="text-center">Nombres</th>';
tblLoad+='      <th class="text-center">Nro.documento</th>';
tblLoad+='      <th class="text-center d-none">Email</th>';
tblLoad+='      <th class="text-center d-none">Cargo/Puesto</th>';
tblLoad+='    </tr>';
tblLoad+='  </thead>';
tblLoad+='  <tbody></tbody>';
tblLoad+='</table>';

$.extend($.fn.dataTableExt.oStdClasses, {
    "sFilterInput": "form-control",
    "sLengthSelect": "form-control"
});

$.extend( $.fn.dataTable.defaults, {
    autoWidth: false,
    responsive: true,
    dom: '<"datatable-header"flB><"datatable"t><"datatable-footer"ip>',
    language: {
        search: '_INPUT_',
        lengthMenu: '_MENU_',
        searchPlaceholder: "Escribir para filtrar...",
        paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
        "sProcessing": "Procesando...",
        "sZeroRecords": 'Ningún dato disponible en esta tabla',
        "sEmptyTable": 'Ningún dato disponible en esta tabla',
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sLoadingRecords": "Cargando..."
    },
    drawCallback: function () {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
    },
    preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
    }
});

$(function() {
    "use strict";
    load_Ini_Tbl_Usuario_All();
});

function load_Ini_Tbl_Usuario_All(){
    "use strict";
    sga.blockUI.loading_body();
    $Tbl_Usuario = $('#Tbl_Usuario').DataTable({
        buttons: {
            buttons: [ ]
        },
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        lengthMenu: [ [7, 10, 25, 50, -1], [7, 10, 25, 50, "Todos"] ],
        Columns:[
            { "mDataProp": "0"},
            { "mDataProp": "1"},
            { "mDataProp": "2"},
            { "mDataProp": "3"},
            { "mDataProp": "4"},
            { "mDataProp": "5"},
            { "mDataProp": "6"},
            { "mDataProp": "7"}
        ],
        columnDefs: [
            {
                className: 'control',
                orderable: false,
                targets: [0]
            },
            {
                'checkboxes': {
                    'selectRow': false
                },
                orderable: false,
                width: "50px",
                targets: [1]
            },
            {
                className: 'text-left',
                targets: [2]
            },
            {
                className: 'text-left',
                targets: [3]
            },
            {
                className: 'text-center',
                targets: [4]
            },
            {
                className: 'text-center',
                targets: [5]
            },
            {
                className: 'text-left',
                targets: [6]
            },
            {
                className: 'text-center',
                targets: [7]
            }
        ],
        'select': {
            'style': 'multi'
        },
        order:[2 , 'asc'],
        ajax:{
            url: '../controller/UsuarioController.php?action=lst_Usuarios_All_JSON',
            type : "get",
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            if(parseInt(json.data.length) > 0) {
                $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
                $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
                let actions = '<div class="btn-group">';
                actions += '  <button type="button" class="btn btn-sm btn-outline-secundary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acciones</button>';
                actions += '  <div class="dropdown-menu">';
                actions += '    <a class="dropdown-item cursor-pointer" id="create_UsuarioBtn"><i class="fa fa-plus mr-5"></i>Nuevo</a>';
                actions += '    <a class="dropdown-item cursor-pointer" id="edit_UsuarioBtn"><i class="fa fa-edit mr-5"></i>Editar</a>';
                actions += '    <a class="dropdown-item cursor-pointer" id="enable_UsuarioBtn"><i class="fa fa-play mr-5"></i>Habilitar</a>';
                actions += '    <a class="dropdown-item cursor-pointer" id="suspend_UsuarioBtn"><i class="fa fa-pause mr-5"></i>Suspender </a>';
                actions += '    <div class="dropdown-divider"></div>';
                actions += '    <a class="dropdown-item cursor-pointer" id="import_UsuarioBtn" data-status="4"><i class="fa fa-cloud-upload mr-5"></i>Importar</a>';
                actions += '  </div>';
                actions += '</div>';

                $('#Tbl_Usuario_wrapper > div.datatable-header > div.dt-buttons').prepend(actions);
                $('#Tbl_Usuario_wrapper > div.datatable-header > div.dt-buttons').addClass('p-r-20');
            }
        },
        "drawCallback": function( settings ) {
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
        }
    });
}

$(document).on('click','#create_UsuarioBtn',function(){
    "use strict";
    let $divTitle = $('#divTitle_Tab');
    let $divTabla = $('#divTabla_Tab');
    let $divResponse = $('#divResponse_Tab');
    $divTitle.show();
    $divTabla.show();
    $divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/UsuarioController.php?action=loadCampos_NuevaUsuario_Ajax', function (response) {
        $divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        $divTitle.hide();
        $divTabla.hide();
        $divResponse.show();
        sga.plugins.select2('.selectClass');
        sga.plugins.select2_search('.selectSearchClass');
        sga.plugins.selectpicker('#almacenSelect');
    });
});

$(document).on('change','#servicioSelect',function(){
    "use strict";
    let id = $(this).val();
    let selectID = $('#almacenSelect');
    selectID.empty();
    //selectID.prop('disabled',true);
    if(id !== "") {
        sga.blockUI.loading_body();
        $.get('../controller/AlmacenController.php?action=lista_Almacenes_Activos_JSON',{'idserv':id}, function (lista) {
            if (lista !== null && parseInt(lista.length) > 0) {
                for (let i = 0; i < parseInt(lista.length); i++) {
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    selectID.append($option);
                }
            }
        }, "json").always(function () {
            sga.blockUI.unblock_body();
            sga.plugins.selectpicker_refresh('#almacenSelect');
            //selectID.prop('disabled',false);
        });
    }
});

$(document).on('click', '#btnCancel_Tab', function(){
    "use strict";
    resetIni_Campos();
});

function resetIni_Campos() {
    "use strict";
    let $divTitle = $('#divTitle_Tab');
    let $divTabla = $('#divTabla_Tab');
    let $divResponse = $('#divResponse_Tab');
    $divTitle.show();
    $divTabla.show();
    $divResponse.hide();
    $divResponse.empty();
    sga.table.refreshDatatable_chk('#Tbl_Usuario');
}

$(document).on('submit','#formNewUsuario', function(e){
    "use strict";
    e.preventDefault();
    let arrayAlmacen = $('#almacenSelect').val();
    let data = $(this).serialize()+"&arrayAlmacen="+arrayAlmacen.join();
    sga.blockUI.loading_body();
    $.post('../controller/UsuarioController.php?action=registrar_Usuario_JSON', data, function (response) {
        if (parseInt(response.status) === 1) {
            swal({
                text: "Registro realizado satisfactoriamente.",
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                resetIni_Campos();
            });
        }
        else if (parseInt(response.status) === 2) {
            swal({
                html: "No es posible realizar el registro, debido a que el usuario ya fue registrado previamente.",
                type: "warning",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            });
        }
        else if (parseInt(response.status) === 0) {
            sga.error.show('danger', 'mensajes_actions_add', 'Error al realizar el registro');
            window.setTimeout(function () {$('#mensajes_actions_add').html("");}, 6000);
        }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensajes_actions_add', 'Error al realizar el registro');
        window.setTimeout(function () { $('#mensajes_actions_add').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#enable_UsuarioBtn', function() {
    "use strict";
    let rows_selected = $Tbl_Usuario.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(parseInt(arrayChk.length)>0) {
        update_EstadoUsuario(1, arrayChk);
    }
    else{
        swal({
            text: "Debe seleccionar al menos un Registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('click','#suspend_UsuarioBtn', function() {
    "use strict";
    let rows_selected = $Tbl_Usuario.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(parseInt(arrayChk.length)>0) {
        update_EstadoUsuario(0, arrayChk);
    }
    else{
        swal({
            text: "Debe seleccionar al menos un Registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('keyup','#ndoc',function(){
    "use strict";
    let thisValue = $(this).val();
    let inputUsuario = $('#txt_usuario_us');
    inputUsuario.val("");
    if($.trim(thisValue).length > 0){
        inputUsuario.val(thisValue);
    }
});

function update_EstadoUsuario($estd, $arrayID){
    "use strict";
    sga.blockUI.loading_body();
    $.post('../controller/UsuarioController.php?action=Update_Estado_Usuario_JSON', {'id':$arrayID, 'estd':$estd}, function(response){
        if(parseInt(response.status)===1 || parseInt(response.status)===2){
            swal({
                type: "success",
                text: "Estado(s) actualizado(s) satisfactoriamente",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK'
            }).then(function () {
                sga.table.refreshDatatable_chk('#Tbl_Usuario');
            });
        }
        else if(parseInt(response.status)===0){
            sga.error.show('danger','mensajes_actions_tab','Error al actualizar estado');
            window.setTimeout(function () { $('#mensajes_actions_tab').html(""); }, 5600);
        }
    },"json").fail(function () {
        sga.error.show('danger','mensajes_actions_tab',"Error al actualizar el estado del Registro, intentelo nuevamente si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensajes_actions_tab').html(""); }, 5600);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}

$(document).on('click','#edit_UsuarioBtn',function(){
    "use strict";
    let rows_selected = $Tbl_Usuario.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(parseInt(arrayChk.length) === 1) {
        let $divTitle = $('#divTitle_Tab');
        let $divTabla = $('#divTabla_Tab');
        let $divResponse = $('#divResponse_Tab');
        $divTitle.show();
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/UsuarioController.php?action=loadCampos_EditarUsuario_Ajax', {'id':arrayChk[0]}, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTitle.hide();
            $divTabla.hide();
            $divResponse.show();
            sga.plugins.select2('.selectClass');
            sga.plugins.select2_search('#edit_Servicio');
            sga.plugins.select2_search('#edit_ServicioAlm');
            sga.plugins.select2_inClear('#almacenSelect');
        });
    }
    else if(parseInt(arrayChk.length) === 0){
        swal.fire({
            text: "Debe seleccionar al menos un Registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
    else if(parseInt(arrayChk.length) >1){
        swal.fire({
            text: "Debe seleccionar solo un Registro para proceder a editarlo.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('submit','#formEditUsuario', function(e) {
    "use strict";
    e.preventDefault();
    let $data = $(this).serialize();
    Swal.fire({
        html: 'Se va a modificar las datos del usaurio.<br>Desea grabar los cambios...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/UsuarioController.php?action=update_Usuario_JSON', $data, function(response){
                if(parseInt(response.status)===1){
                    swal({
                        type: "success",
                        text: "Registro actualizado satisfactoriamente",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    });
                }
                else if(parseInt(response.status)===0){
                    sga.error.show('danger','mensajes_actions_add','Error al realizar la actualización del registro');
                    window.setTimeout(function () { $('#mensajes_actions_add').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensajes_actions_add',"Error al realizar la actualización de los datos, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensajes_actions_add').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('submit','#formEditCredenciales', function(e) {
    "use strict";
    e.preventDefault();
    let $data = $(this).serialize();
    Swal.fire({
        html: 'Se va a modificar las credenciales del Usuario.<br>Desea grabar los cambios...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/UsuarioController.php?action=update_Credenciales_JSON', $data, function(response){
                if(parseInt(response.status)===1){
                    swal({
                        type: "success",
                        text: "Registro actualizado satisfactoriamente",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    });
                }
                else if(parseInt(response.status)===0){
                    sga.error.show('danger','mensajes_actions_cred','Error al realizar la actualización del registro');
                    window.setTimeout(function () { $('#mensajes_actions_cred').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensajes_actions_cred',"Error al realizar la actualización de los datos, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensajes_actions_cred').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#import_UsuarioBtn',function(){
    "use strict";
    let $divTitle = $('#divTitle_Tab');
    let $divTabla = $('#divTabla_Tab');
    let $divResponse = $('#divResponse_Tab');
    $divTitle.hide();
    $divTabla.hide();
    $divResponse.append(inputFIle);
    $divResponse.show();
    sga.plugins.inputFile('#filedata_import','excel');
    lista_perfiles_Activos();
});

function lista_perfiles_Activos() {
    "use strict";
    let $selectID = $('#perfil_im');
    $selectID.empty();
    $.get('../controller/PerfilController.php?action=lista_Perfiles_Activos_JSON',function (lista) {
        $selectID.append('<option></option>');
        if(lista!== null && parseInt(lista.length)>0){
            for(let i=0; i<parseInt(lista.length); i++){
                let $option = $("<option></option>");
                $option.val(lista[i].id);
                $option.text(lista[i].texto);
                $selectID.append($option);
            }
        }
    },"json").always(function (){
        sga.plugins.select2('.selectPClass');
    });
}

$(document).on('submit','#form_Viewtable_listDatos',function(e){
    "use strict";
    e.preventDefault();
    let $divFileInput = $('#div_FileInput');
    let divRespondeAll = $('#divResponse_All');
    let perfil = $('#perfil_im');
    let titulo = "Lista de usuarios a cargar.";
    $divFileInput.show();
    divRespondeAll.hide();
    let formdata = new FormData($(this)[0]);
    formdata.append('perfil',perfil.val());
    $.ajax({
        url: '../controller/UsuarioController.php?action=list_View_Rows_File',
        type: "POST",
        data: formdata,
        dataType: "json",
        cache: false,
        contentType: false, //No especificamos ningún tipo de dato
        enctype: 'multipart/form-data',
        processData:false, //Evitamos que JQuery procese los datos, daría error
        beforeSend: function(){
            sga.blockUI.loading_body();
        },
        success: function (response) {
            if(parseInt(response.status)===0 || parseInt(response.status)===2){
                reset_campos_InputFile();
                sga.blockUI.unblock_body();
                let $tituloMsje;
                if(parseInt(response.status)===0){
                    $tituloMsje = "Se encontro problemas al cargar el archivo adjunto, verifique que el archivo contenga los campos contemplados en la plantilla, caso contrario contactese con Soporte-IMC.";
                }
                else if(parseInt(response.status)===2){
                    $tituloMsje = "El archivo adjunto cuenta con varias hojas asociadas, solo debe cargar el archivo que contenga unicamente tipo dato a cargar.";
                }
                swal.fire({
                    text: $tituloMsje,
                    type: "error",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Ok'
                });
            }
            else if(parseInt(response.status)===1){
                let lista = response.data;
                if (lista !== null) {
                    $divFileInput.hide();
                    divRespondeAll.show();
                    contendResponse( titulo, 1, perfil);
                    sga.plugins.inputFile_clear('#filedata_import');
                    sga.plugins.inputFile_enable('#filedata_import');
                    $Nregistros = parseInt(lista.length);
                    for (let t = 0; t < parseInt(lista.length); t++) {
                        setDatos_xTipo(lista[t], t, 1);
                        if(t === parseInt(lista.length)-1){
                            $('.btnDisabled_stand').prop('disabled',false);
                        }
                    }
                }
                else{
                    reset_campos_InputFile();
                    sga.blockUI.unblock_body();
                    swal.fire({
                        text: "Error a intentar visualizar los registros a cargar.",
                        type: "error",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Ok'
                    });
                }
            }
        }
    }).fail(function (e) {
        reset_campos_InputFile();
        sga.blockUI.unblock_body();
        swal.fire({
            text: "Error a intentar visualizar los registros a cargar.",
            type: "error",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'Ok'
        });
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

function setDatos_xTipo(lista, i, tipo) {
    "use strict";
    let lineTbl;
    if(parseInt(tipo) === 1){
        lineTbl  = '<tr id="us_tr_' + i + '">';
        lineTbl += '  <td class="text-center text-center">' + (parseInt(i)+1) + '</td>';
        lineTbl += '  <td class="text-center n_area">' + lista[0] + '</td>';
        lineTbl += '  <td class="text-left n_apepa">' + lista[1] + '</td>';
        lineTbl += '  <td class="text-left n_apema">' + lista[2] + '</td>';
        lineTbl += '  <td class="text-left n_nombres">' + lista[3] + '</td>';
        lineTbl += '  <td class="text-left n_ndoc">' + lista[4] + '</td>';
        lineTbl += '  <td class="text-left n_email d-none">' + lista[5] + '</td>';
        lineTbl += '  <td class="text-left n_puesto d-none">' + lista[6] + '</td>';
        lineTbl += '</tr>';
    }
    $('#TblStandar_Load > tbody').append(lineTbl);
}

$(document).on('click', '#btn_LoadDatos', function(){
    "use strict";
    let $option = $(this).attr('data-opcion');
    let perfil = $(this).attr('data-perfil');
    swal.fire({
        html: "Se va a proceder a realizar la carga de los datos.<br>desea continuar..!!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            if (parseInt($Nregistros) > 0) {
                $('.btnDisabled_stand').prop('disabled', true);
                if (parseInt($option) === 1) {
                    importarDatos_Usuario($('#us_tr_' + $count), perfil);
                }

            }
        }
    });
});

$(document).on('click', '#btnCancel_Load', function(){
    "use strict";
    reset_campos_InputFile();
});

function reset_campos_InputFile() {
    "use strict";
    let $divFileInput = $('#div_FileInput');
    let divRespondeAll = $('#divResponse_All');
    $divFileInput.show();
    divRespondeAll.hide()
    divRespondeAll.empty();
    sga.plugins.inputFile_clear('#filedata_import');
    $Nregistros = 0;
    $count = 0;
}

function contendResponse(titulo, tipo, perfil) {
    "use strict";
    let tblDefault = "";
    if(parseInt(tipo) === 1){ tblDefault = tblLoad;}

    let $contenedor = $('#divResponse_All');
    let row ='<div class="card">';
    row+='  <div class="card-body">';
    row+='    <h6 class="card-subtitle">'+titulo+'</h6>';
    row+='    <div class="row">';
    row+='      <div class="col-12 text-right mb-20">';
    row+='        <button type="button" class="btn btn-outline-secondary m-r-10 btnDisabled_stand" id="btn_LoadDatos" disabled data-opcion="'+tipo+'" data-perfil="'+perfil+'">';
    row+='          <i class="icon-share-alt position-left"></i>Cargar Datos';
    row+='        </button>';
    row+='        <button type="button" class="btn btn-outline-danger btnDisabled_stand" id="btnCancel_Load" disabled>';
    row+='          <i class="icon-ban position-left"></i>Cancelar';
    row+='        </button>';
    row+='      </div>';
    row+='    </div>';
    row+='    <div class="row">';
    row+='      <div class="col-12">';
    row+='        <div class="table-responsive"> ' + tblDefault +'</div>';
    row+='      </div>';
    row+='    </div>';
    row+='  </div>';
    row+='  <hr class="no-margin">';
    row+='  <div class="card-body text-right">';
    row+='    <label class="col-lg-2 col-sm-12 text-center pull-right text-danger font-weight-bold"> Error';
    row+='      <button type="button" class="btn btn-danger btn-circle"><i class="fa fa-times"></i> </button>';
    row+='    </label>';
    row+='    <label class="col-lg-2 col-sm-12 text-right pull-right text-warning font-weight-bold"> Actualizado';
    row+='      <button type="button" class="btn btn-warning btn-circle"><i class="fas fa-history"></i> </button>';
    row+='    </label>';
    row+='    <label class="col-lg-2 col-sm-12 text-right pull-right text-success font-weight-bold"> Correcto';
    row+='      <button type="button" class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>';
    row+='    </label>';
    row+='  </div>';
    row+='</div>';
    $contenedor.append(row);
}

function importarDatos_Usuario(fila,perfil) {
    "use strict";
    let data = {
        'area': fila.find('.n_area').text(),
        'apepa': fila.find('.n_apepa').text(),
        'apema': fila.find('.n_apema').text(),
        'nombres': fila.find('.n_nombres').text(),
        'ndoc': fila.find('.n_ndoc').text(),
        'email': fila.find('.n_email').text(),
        'puesto': fila.find('.n_puesto').text(),
        'perfil': perfil
    };
    $.post('../controller/UsuarioController.php?action=registrar_usuario_Import_JSON', data, function(response){
        if(parseInt(response.status) === 2){ // ya existe registro
            $('#us_tr_' + $count).addClass('table-warning').removeClass('table-success').removeClass('table-danger');
        }
        else if(parseInt(response.status) === 1){//registro satisfactorio
            $('#us_tr_' + $count).addClass('table-success').removeClass('table-danger').removeClass('table-warning');
        }
        else if(parseInt(response.status) === 0) {//error en registro
            $('#us_tr_' + $count).addClass('table-danger').removeClass('table-warning').removeClass('table-success');
        }
        $count++;
        if(parseInt($count) < parseInt($Nregistros)){
            importarDatos_Usuario($('#us_tr_' + $count),perfil);
        }

        if(parseInt($count) === parseInt($Nregistros)){
            swal({
                text: "Registros cargados satisfactoriamente.",
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                resetIni_Campos();
                $count = 0;
                $Nregistros = 0;
            });
        }
    },"json");
}

$(document).on('click','#btnBajaAlta_userServicio', function() {
    "use strict";
    let id = $(this).attr('data-id');
    let estado = $(this).attr('data-estd');
    sga.blockUI.loading_body();
    $.post('../controller/UsuarioController.php?action=altaBaja_UsuarioServicio_JSON', {'id':id,'estado':estado}, function (response) {
        if (parseInt(response.status) === 1) {
            swal({
                text: "Registro actualizado satisfactoriamente.",
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                load_INI_UsuarioServicios(response.id);
            });
        }
        else if (parseInt(response.status) === 0) {
            sga.error.show('danger', 'mensaje_actions_userService', 'Error al actualizar el estado del servicio seleccionado.');
            window.setTimeout(function () { $('#mensaje_actions_userService').html(""); }, 6000);
        }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensaje_actions_userService', "Se produjo un error al intentar actualizar el estado del servicio, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensaje_actions_userService').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btnDelete_userServicio', function() {
    "use strict";
    let id = $(this).attr('data-id');
    swal.fire({
        html: 'Se va a eliminar el servicio asignado.<br>Una vez realizada esta acción no podra ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/UsuarioController.php?action=delete_UsuarioServicio_JSON', {'id':id,}, function (response) {
                if (parseInt(response.status) === 1) {
                    swal({
                        text: "Servicio eliminado satisfactoriamente.",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        load_INI_UsuarioServicios(response.id);
                        resetSelectBox(response.id);
                        resetServiciosAsign(response.id);
                        load_INI_AlmacenesAsigandos(response.id);
                    });
                }
                else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensaje_actions_userService', 'Error al eliminar el servicio seleccionado.');
                    window.setTimeout(function () { $('#mensaje_actions_userService').html(""); }, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger', 'mensaje_actions_userService', "Se produjo un error al intentar eliminar el servicio, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
                window.setTimeout(function () { $('#mensaje_actions_userService').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('submit','#addService_Usuario', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    Swal.fire({
        html: 'Se va agregar el servicio seleccionado.<br>Desea continuar...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/UsuarioController.php?action=add_UsuarioServicio_JSON', data, function(response){
                if(parseInt(response.status)===1){
                    swal({
                        type: "success",
                        text: "Servicio agregado satisfactoriamente",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        load_INI_UsuarioServicios(response.id);
                        resetSelectBox(response.id);
                        resetServiciosAsign(response.id);
                    });
                }
                else if(parseInt(response.status)===0){
                    sga.error.show('danger','mensaje_servicio','Error al agregar el servicio.');
                    window.setTimeout(function () { $('#mensaje_servicio').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensaje_servicio',"Error al agregar el servicio, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensaje_servicio').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

function load_INI_UsuarioServicios(id) {
    "use strict";
    let setTable = $('#servicioUsuario > tbody');
    setTable.empty();
    sga.blockUI.loading_body();
    $.get('../controller/UsuarioController.php?action=lista_Servicios_Asignados_xUsuario_JSON',{'id':id}, function(lista){
        console.log(lista);
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let estadoRow = '<span class="label label-block text-danger">Baja</span>'
                let btnActionEstado =
                    '<button class="btn btn-icon btn-primary btn-icon-style-1 mt-0 btn-hover-transform mr-10" title="Dar alta" id="btnBajaAlta_userServicio" data-id="' + lista[j].id + '" data-estd="1">' +
                    '  <span class="btn-icon-wrap">' +
                    '    <span class="material-icons">thumb_up</span>' +
                    '  </span>' +
                    '</button>'
                if(parseInt(lista[j].estado) === 1){
                    estadoRow ='<span class="label label-block text-success ">Activo</span>';
                    btnActionEstado =
                        '<button class="btn btn-icon btn-warning btn-icon-style-1 mt-0 btn-hover-transform mr-10" title="Dar baja" id="btnBajaAlta_userServicio" data-id="' + lista[j].id + '" data-estd="0">' +
                        '  <span class="btn-icon-wrap">' +
                        '    <span class="material-icons">thumb_down</span>' +
                        '  </span>' +
                        '</button>'
                }

                let btnActionDelete =
                    '<button class="btn btn-icon btn-danger btn-icon-style-1 mt-0 btn-hover-transform" title="Eliminar" id="btnDelete_userServicio" data-id="' + lista[j].id + '">' +
                    '  <span class="btn-icon-wrap">' +
                    '    <span class="material-icons">delete</span>' +
                    '  </span>' +
                    '</button>'



                let row =
                    '<tr>'+
                    '    <td class="text-center">' + (j+1) + '</td>'+
                    '    <td class="text-left">' + lista[j].servicio + '</td>'+
                    '    <td class="text-center">' + lista[j].fecha + '</td>'+
                    '    <td class="text-center">' + estadoRow + '</td>'+
                    '    <td class="text-center">'+
                    '      <div class="button-list">'+btnActionEstado + btnActionDelete +'</div>'+
                    '    </td>'+
                    '</tr>';
                setTable.append(row);
            }
        }
        else{
            setTable.append('<tr><td colspan="4" class="text-center">No se encontraron servicios asociados al usuario.</td></tr>');
        }
    },"json").always(function () {
        sga.blockUI.unblock_body();
    });
}

function resetSelectBox(id){
    "use strict";
    let selectID = $('#edit_Servicio');
    selectID.empty();
    sga.blockUI.loading_body();
    $.get('../controller/UsuarioController.php?action=lista_Servicios_All_Activas_xUsuario_JSON',{'id':id}, function(lista){
        selectID.append('<option></option>');
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let option = $("<option></option>");
                option.val(lista[j].id);
                option.text(lista[j].texto);
                selectID.append(option);
            }
        }
    },"json").always(function () {
        sga.plugins.select2_search('#edit_Servicio');
        sga.blockUI.unblock_body();
    });
}

$(document).on('change','#edit_ServicioAlm',function(){
    "use strict";
    let id = $(this).val();
    let selectID = $('#almacenSelect');
    selectID.empty();
    selectID.append("<option></option>");
    if(id !== "") {
        sga.blockUI.loading_body();
        $.get('../controller/AlmacenController.php?action=lista_Almacenes_Activos_xuserServicio_JSON',{'idsu':id}, function (lista) {
            if (lista !== null && parseInt(lista.length) > 0) {
                for (let i = 0; i < parseInt(lista.length); i++) {
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    selectID.append($option);
                }
            }
        }, "json").always(function () {
            sga.blockUI.unblock_body();
            sga.plugins.select2_inClear('#almacenSelect');
        });
    }
});

function resetServiciosAsign(id){
    "use strict";
    let selectID = $('#edit_ServicioAlm');
    let selectIDAlm = $('#almacenSelect');
    selectID.empty();
    selectIDAlm.empty();
    selectIDAlm.append('<option></option>');
    sga.plugins.select2_inClear('#almacenSelect');
    sga.blockUI.loading_body();
    $.get('../controller/UsuarioController.php?action=lista_Servicios_Asinados_Activas_xUsuario_JSON',{'id':id}, function(lista){
        selectID.append('<option></option>');
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let option = $("<option></option>");
                option.val(lista[j].id);
                option.text(lista[j].texto);
                selectID.append(option);
            }
        }
    },"json").always(function () {
        sga.plugins.select2_search('#edit_ServicioAlm');
        sga.blockUI.unblock_body();

    });
}

$(document).on('click','#btnDelete_userServicioAlm', function() {
    "use strict";
    let idual = $(this).attr('data-idual');
    let idus = $(this).attr('data-idus');
    swal.fire({
        html: 'Se va a eliminar el almacén asignado.<br>Una vez realizada esta acción no podra ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/AlmacenController.php?action=delete_UsuarioAlmacen_JSON', {'idual':idual,'idus':idus,}, function (response) {
                if (parseInt(response.status) === 1) {
                    swal({
                        text: "Servicio eliminado satisfactoriamente.",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        load_INI_AlmacenesAsigandos(response.id);
                    });
                }
                else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensaje_actions_userAlmacen', 'Error al eliminar el almacén seleccionado.');
                    window.setTimeout(function () { $('#mensaje_actions_userAlmacen').html(""); }, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger', 'mensaje_actions_userAlmacen', "Se produjo un error al intentar eliminar el almacén, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
                window.setTimeout(function () { $('#mensaje_actions_userAlmacen').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

function load_INI_AlmacenesAsigandos(id) {
    "use strict";
    let setTable = $('#usuarioAlmacen > tbody');
    setTable.empty();
    sga.blockUI.loading_body();
    $.get('../controller/AlmacenController.php?action=lista_Almacen_xUsuario_JSON',{'id':id}, function(lista){
        console.log(lista);
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let estadoRow = '<span class="label label-block text-danger">Baja</span>'
                if(parseInt(lista[j].estado) === 1){
                    estadoRow ='<span class="label label-block text-success ">Activo</span>';
                }

                let btnActionDelete =
                    '<button class="btn btn-icon btn-danger btn-icon-style-1 mt-0 btn-hover-transform" title="Eliminar" id="btnDelete_userServicioAlm" data-idual="' + lista[j].idual + '" data-idus="' + lista[j].idus + '">' +
                    '  <span class="btn-icon-wrap">' +
                    '    <span class="material-icons">delete</span>' +
                    '  </span>' +
                    '</button>';

                let row =
                    '<tr>'+
                    '    <td class="text-center">' + (j+1) + '</td>'+
                    '    <td class="text-left">' + lista[j].servicio + '</td>'+
                    '    <td class="text-left">' + lista[j].titulo + '</td>'+
                    '    <td class="text-center">' + estadoRow + '</td>'+
                    '    <td class="text-center">'+
                    '      <div class="button-list">'+ btnActionDelete +'</div>'+
                    '    </td>'+
                    '</tr>';
                setTable.append(row);
            }
        }
        else{
            setTable.append('<tr><td colspan="4" class="text-center">No se encontraron servicios asociados al usuario.</td></tr>');
        }
    },"json").always(function () {
        sga.blockUI.unblock_body();
    });
}

$(document).on('submit','#addServiceUsuario_Almacen', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    Swal.fire({
        html: 'Se va agregar el almacén seleccionado.<br>Desea continuar...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/AlmacenController.php?action=add_AlmacenUsuario_JSON', data, function(response){
                if(parseInt(response.status)===1){
                    swal({
                        type: "success",
                        text: "Almacén agregado satisfactoriamente",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        load_INI_AlmacenesAsigandos(response.id);
                    });
                }
                else if(parseInt(response.status)===2){
                    swal({
                        type: "warning",
                        text: "El almacén seleccionado ya se encuentra asignado.",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        load_INI_AlmacenesAsigandos(response.id);
                    });
                }
                else if(parseInt(response.status)===0){
                    sga.error.show('danger','mensaje_actions_userAlmacen','Error al agregar el almacén.');
                    window.setTimeout(function () { $('#mensaje_actions_userAlmacen').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensaje_actions_userAlmacen',"Error al agregar el almacén, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensaje_actions_userAlmacen').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

