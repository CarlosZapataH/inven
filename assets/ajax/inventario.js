var $Tbl_Inventario, swal, arrayVAL_exp = [], elementosP = [],$Nregistros = 0, $count = 0, successFileLoad = 0;
var objinit = new init(), columVisi, columVisiMdl, $Tbl_ItemStock, optImport,optTable,optImport_notPermise;
var totalItemsPagTable = 0;

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

inputFIle ='<div class="row">';
inputFIle+='  <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12 mb-20 mt-20 offset-xl-3 offset-lg-3 offset-md-2" id="div_FileInput">';
inputFIle+='    <h5 class="display-6 text-center">Importar Inventario Inicial</h5>';
inputFIle+='    <p class="text-center">Adjunte el archivo con los datos contemplados en la plantilla.</p>';
inputFIle+='    <div class="card card-shadow">';
inputFIle+='      <div class="card-body">';
inputFIle+='        <form id="form_Viewtable_listDatos" enctype="multipart/form-data">';
inputFIle+='          <input type="hidden" name="tipoload" id="tipoload" value="1">';
inputFIle+='          <div class="row">';
inputFIle+='            <div class="col-12">';
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
inputFIle+='      <div class="card-footer text-right">';
inputFIle+='        <button type="button" id="btnCancel" class="btn btn-light mr-20">Cancelar</button>';
inputFIle+='        <a class="btn bg-success-0 btn-hover-transform" href="../assets/formato/Plantilla-Datos.xlsx" download="Plantilla-Datos.xlsx"><i class="ti-download position-left"></i>Descargar Plantilla</a>';
inputFIle+='      </div>';
inputFIle+='    </div>';
inputFIle+='  </div>';
inputFIle+='  <div class="col-12 mb-20 mt-20" id="divResponse_All" style="display:none"></div>';
inputFIle+='</div>';

var tblLoad ='<table id="TblStandar_Load" class="table table-bordered mb-0" cellpadding="0" cellspacing="0" width="100%">';
tblLoad+='  <thead>';
tblLoad+='    <tr>';
tblLoad+='      <th class="text-center">#</th>';
tblLoad+='      <th class="text-center">Unidad/Equipo</th>';
tblLoad+='      <th class="text-center">Código</th>';
tblLoad+='      <th class="text-center">Cant.</th>';
tblLoad+='      <th class="text-center">Descripción</th>';
tblLoad+='      <th class="text-center">U.Medida</th>';
tblLoad+='      <th class="text-center">Ubicación</th>';
tblLoad+='      <th class="text-center">Mro.Parte/Serie</th>';
tblLoad+='      <th class="text-center">Reserva/Cesta</th>';
tblLoad+='      <th class="text-center d-none">Orden Mantto.</th>';
tblLoad+='      <th class="text-center d-none">Fecha Ped.</th>';
tblLoad+='      <th class="text-center d-none">Fecha Rec.</th>';
tblLoad+='      <th class="text-center d-none">Marca</th>';
tblLoad+='      <th class="text-center d-none">C.Unit</th>';
tblLoad+='      <th class="text-center d-none">Total</th>';
tblLoad+='      <th class="text-center d-none">Fecha Ins.</th>';
tblLoad+='      <th class="text-center d-none">Mecánico</th>';
tblLoad+='      <th class="text-center d-none">Observaciones</th>';
tblLoad+='      <th class="text-center d-none">O.Compra</th>';
tblLoad+='    </tr>';
tblLoad+='  </thead>';
tblLoad+='  <tbody></tbody>';
tblLoad+='</table>';

var tblLoadM ='<table id="TblStandar_Load" class="table table-bordered mb-0" cellpadding="0" cellspacing="0" width="100%">';
tblLoadM+='  <thead>';
tblLoadM+='    <tr>';
tblLoadM+='      <th class="text-center">#</th>';
tblLoadM+='      <th class="text-center">Código material</th>';
tblLoadM+='      <th class="text-center">Cant.</th>';
tblLoadM+='      <th class="text-center">Descripción</th>';
tblLoadM+='      <th class="text-center">U.Medida</th>';
tblLoadM+='      <th class="text-center">Mro.Parte/Serie</th>';
tblLoadM+='      <th class="text-center d-none">Marca</th>';
tblLoadM+='      <th class="text-center d-none">Observaciones</th>';
tblLoadM+='      <th class="text-center d-none">Fecha recepción</th>';
tblLoadM+='      <th class="text-center d-none">Clasificación</th>';
tblLoadM+='      <th class="text-center d-none">Nro Guia</th>';
tblLoadM+='      <th class="text-center d-none">Fecha ultima Calibración</th>';
tblLoadM+='      <th class="text-center d-none">Frecuencia Calibración</th>';
tblLoadM+='      <th class="text-center d-none">Código Activo</th>';
tblLoadM+='      <th class="text-center d-none">Código Inventario</th>';
tblLoadM+='      <th class="text-center d-none">MAPEL</th>';
tblLoadM+='      <th class="text-center d-none">ONU</th>';
tblLoadM+='      <th class="text-center d-none">Fecha Depreciación</th>';
tblLoadM+='      <th class="text-center d-none">Costo Activo</th>';
tblLoadM+='      <th class="text-center d-none">Frecuencia Depreciación</th>';
tblLoadM+='      <th class="text-center d-none">Valor mensual Depreciación</th>';
tblLoadM+='    </tr>';
tblLoadM+='  </thead>';
tblLoadM+='  <tbody></tbody>';
tblLoadM+='</table>';

let modalInv =
    '<div class="modal-dialog" style="max-width:75%;">' +
    '   <div class="modal-content">' +
    '       <div class="modal-header bg-secondary" style="padding: 5px 20px;">' +
    '           <h4 class="modal-title text-white mt-0">' +
    '               Ítem Almacén' +
    '           </h4>' +
    '           <button type="button" class="close text-white" data-dismiss="modal" id="btnclose_Modal">×</button>' +
    '       </div>' +
    '       <div class="modal-body">' +
    '           <div class="row"><div class="col-12" id="mensaje_action_mdl"></div></div>' +
    '           <table id="Tbl_ItemStock" class="display table table-bordered table-striped">' +
    '               <thead>' +
    '                   <tr>' +
    '                       <th class="text-center fz-11"></th>' +
    '                       <th class="text-center fz-11">CLASIFICACIÓN</th>' +
    '                       <th class="text-center fz-11">CODIGO</th>' +
    '                       <th class="text-center fz-11">DESCRIPCIÓN</th>' +
    '                       <th class="text-center fz-11">MARCA</th>' +
    '                       <th class="text-center fz-11">U.MEDIDA</th>' +
    '                       <th class="text-center fz-11">NRO.PARTE/SERIE</th>' +
    '                       <th class="text-center fz-11">COD.INVENTARIO</th>' +
    '                       <th class="text-center fz-11">COD.ACTIVO</th>' +
    '                       <th class="text-center fz-11">COD.MAPEL</th>' +
    '                       <th class="text-center fz-11">COD.ONU</th>' +
    '                       <th class="text-center fz-11">STOCK</th>' +
    '                       <th class="text-center"></th>' +
    '                   </tr>' +
    '               </thead>' +
    '           </table>' +
    '       </div>' +
    '   </div>' +
    '</div>';

columVisi =
    '<strong class="mr-3">Columnas visibles:</strong>' +
    '<br>' +
    '<nav class="navbar navbar-expand-lg no-padding bg-light">' +
    '   <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">' +
    '       <span class="navbar-toggler-icon"></span>' +
    '   </button>' +
    '   <div class="collapse navbar-collapse" id="navbarNavAltMarkup">' +
    '       <div class="navbar-nav nav-fill w-100">' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="2" data-action="0">CLASIFICACIÓN</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="5" data-action="0">MARCA</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="8" data-action="0">COD.INVENTARIO</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="9" data-action="0">COD.ACTIVO</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="10" data-action="0">COD.MAPEL</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="11" data-action="0">COD.ONU.</a>' +
    '       </div>' +
    '   </div>' +
    '</nav>';

columVisiMdl =
    '<div class="collapse " id="collapseColumnas">' +
    '<div class="card card-body border p-2">' +
    '<strong class="mr-3">Columnas visibles:</strong>' +
    '<br>' +
    '<nav class="navbar navbar-expand-lg no-padding bg-light">' +
    '   <div class="collapse navbar-collapse" id="navbarNavAltM">' +
    '       <div class="navbar-nav nav-fill w-100">' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis bg-warning" data-column="1" data-action="1">CLASIFICACIÓN</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="4" data-action="0">MARCA</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="7" data-action="0">COD.INVENTARIO</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="8" data-action="0">COD.ACTIVO</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="9" data-action="0">COD.MAPEL</a>' +
    '           <a class="cursor-pointer nav-item nav-link toggle-vis" data-column="10" data-action="0">COD.ONU.</a>' +
    '       </div>' +
    '   </div>' +
    '</nav>'+
    '</div>'+
    '</div>';


optImport =
    '<div class="row">\n' +
    '        <div class="col-12 text-center p-90">\n' +
    '            <div class="auth-form-wrap pt-xl-0 pt-70">\n' +
    '                <div class="w-xl-25 w-sm-50 w-100 margin-auto" style="">\n' +
    '                    <div class="flex flex-col items-center justify-center">\n' +
    '                        <span class="material-icons" style="font-size: 100px;color: #acb0b1;">\n' +
    '                            add_task\n' +
    '                        </span>\n' +
    '                    </div>\n' +
    '                    <div class="mt-2">\n' +
    '                        <label class="font-medium">¡Aún no hay registros!</label>\n' +
    '                    </div>\n' +
    '                    <div class="mt-2">\n' +
    '                        <label class="mt-1 text-base font-extralight text-gray-500 text-center flex">\n' +
    '                            Esta sección contendrá los registros del inventario del almacén seleccionado.\n' +
    '                        </label>\n' +
    '                    </div>\n' +
    '                    <div class="mt-8">\n' +
    '                        <button type="button" class="btn btn-outline-primary" id="importItem_Btn" style="text-transform: none">\n' +
    '                            <i class="fa fa-plus position-left" aria-hidden="true"></i>\n' +
    '                            Importar inventario inicial\n' +
    '                        </button>\n' +
    '                    </div>\n' +
    '                </div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>';

optImport_notPermise =
    '<div class="row">\n' +
    '        <div class="col-12 text-center p-90">\n' +
    '            <div class="auth-form-wrap pt-xl-0 pt-70">\n' +
    '                <div class="w-xl-25 w-sm-50 w-100 margin-auto" style="">\n' +
    '                    <div class="flex flex-col items-center justify-center">\n' +
    '                        <span class="material-icons" style="font-size: 100px;color: #acb0b1;">\n' +
    '                            add_task\n' +
    '                        </span>\n' +
    '                    </div>\n' +
    '                    <div class="mt-2">\n' +
    '                        <label class="mt-1 text-base font-extralight text-gray-500 text-center flex">\n' +
    '                            Esta sección contendrá los registros del inventario del almacén seleccionado.<br>Actualmente no dispone del permiso para realizar la carga de datos inicial,<br>contactese con el Administrador del sistema.' +
    '                        </label>\n' +
    '                    </div>\n' +
    '                </div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>';

optTable = '<div class="card shadow">\n' +
    '        <div class="card-header">\n' +
    '            <div class="mb-4 float-right" id="divSelectView"></div>\n' +
    '            <h4 class="card-title mb-10 text-blue-800">Detalle Inventario según Almacén</h4>\n' +
    '            <h6 class="card-subtitle text-muted fz-12" id="infodetail"></h6>\n' +
    '        </div>\n' +
    '        <div class="text-center" id="mensajes_actions_inv"></div>\n' +
    '        <div class="table-responsive">\n' +
    '            <table id="Tbl_Inventario" class="display table table-bordered table-striped">\n' +
    '                 <thead>\n' +
    '                    <tr>\n' +
    '                        <th></th>\n' +
    '                        <th class="text-center">CODIGO</th>\n' +
    '                        <th class="text-left">DESCRIPCIÓN</th>\n' +
    '                        <th class="text-center">U.M.</th>\n' +
    '                        <th class="text-center">NRO. PARTE/SERIE</th>\n' +
    '                        <th class="text-center">C.ACTIVO</th>\n' +
    '                        <th class="text-center">C.INV</th>\n' +
    '                        <th class="text-center">MAPEL</th>\n' +
    '                        <th class="text-center">ONU</th>\n' +
    '                        <th class="text-center">STOCK</th>\n' +
    '                        <th class="text-center">CLASIFICACIÓN</th>\n' +
    '                        <th class="text-center"></th>\n' +
    '                    </tr>\n' +
    '                </thead>\n' +
    '            </table>\n' +
    '        </div>\n' +
    '    </div>';

$(function() {
    "use strict";
    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();

        if(parseInt(countAlmacen)===1){
            let divContend = $('#divContend');
            divContend.append(optTable);
            let datos = {
                'almacen': $('#IdAlmacen').val(),
                'idustk': $('#idustk').val(),
                'elementSelect' : $('#textAlmacen').val(),
            };
            loadTbl_Inventario_INI(datos);
        }
    }
    sga.plugins.select2_search('.selectClass');
    init;
});

function init() {
    "use strict";

    function consultarP() {
        return JSON.stringify(elementosP);
    }

    this.consultarP = function(){
        return JSON.stringify(elementosP);
    };

    this.eliminarP = function(pos){
        pos > -1 && elementosP.splice(parseInt(pos),1);
    };
}

function viewOption_Inventary(){
    "use strict";
    let containerOptions = $('#divOption_btns');
    containerOptions.empty();
    let optNew = $('#inv_new').val();
    let optTransfer = $('#inv_trans').val();
    let optRetiro = $('#inv_reti').val();
    let optDevolver = $('#inv_devol').val();

    let viewBtn_New = ' <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-10">' +
        '                    <a class="cursor-pointer list-group-item flex-column align-items-start btn-hover-transform" id="ingresarItem_Btn">' +
        '                        <div class="d-flex w-100 justify-content-between">' +
        '                            <div class="stats">' +
        '                                <h3 class="mb-5">Ingreso</h3>' +
        '                                <h6 class="text-muted fz-12 text-light mb-0">Ítems a almacén</h6>' +
        '                            </div>' +
        '                            <div class="stats-icon text-right ml-auto">' +
        '                                <i class="fa fa-archive display-5 op-3 text-dark"></i>' +
        '                            </div>' +
        '                        </div>' +
        '                    </a>' +
        '                </div>';
    let viewBtn_Trans = '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-10">' +
        '                    <a class="cursor-pointer list-group-item flex-column align-items-start bg-info btn-hover-transform btn-hover-primaryy" id="transferirItem_Btn">' +
        '                        <div class="d-flex w-100 justify-content-between">' +
        '                            <div class="stats">' +
        '                                <h3 class="mb-5 text-light">Transferencia</h3>' +
        '                                <h6 class="text-muted fz-12 text-white-50 mb-0">Ítems a otro almacén</h6>' +
        '                            </div>' +
        '                            <div class="stats-icon text-right ml-auto">' +
        '                                <i class="fas fa-random display-5 op-3 text-dark"></i>' +
        '                            </div>' +
        '                        </div>' +
        '                    </a>' +
        '                </div>';

    let viewBtn_Retiro = ' <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-10">' +
        '                    <a class="cursor-pointer list-group-item flex-column align-items-start bg-warning btn-hover-transform" id="retirarItem_Btn">' +
        '                        <div class="d-flex w-100 justify-content-between">' +
        '                            <div class="stats">' +
        '                                <h3 class="mb-5 text-light">Retiro</h3>' +
        '                                <h6 class="text-muted fz-12 text-light mb-0">Ítems del almacén</h6>' +
        '                            </div>' +
        '                            <div class="stats-icon text-right ml-auto">' +
        '                                <i class="fas fa-dolly display-5 op-3 text-dark"></i>' +
        '                            </div>' +
        '                        </div>' +
        '                    </a>' +
        '                </div>';
    let viewBtn_Devol = '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-10">' +
        '                    <a class="cursor-pointer list-group-item flex-column align-items-start bg-danger btn-hover-transform" id="devolverItem_Btn">' +
        '                        <div class="d-flex w-100 justify-content-between">' +
        '                            <div class="stats">' +
        '                                <h3 class="mb-5 text-light">Devolución</h3>' +
        '                                <h6 class="text-muted fz-12 text-white-50 mb-0">Ítems a un almacén</h6>' +
        '                            </div>' +
        '                            <div class="stats-icon text-right ml-auto">' +
        '                                <i class="fas fa-dolly display-5 op-3 fa-flip-horizontal"></i>' +
        '                            </div>' +
        '                        </div>' +
        '                    </a>' +
        '                </div>';

    let $htmlview = "";
    if(parseInt(optNew) === 1){ $htmlview += viewBtn_New;}
    if(parseInt(optTransfer) === 1){ $htmlview += viewBtn_Trans;}
    if(parseInt(optRetiro) === 1){ $htmlview += viewBtn_Retiro;}
    if(parseInt(optDevolver) === 1){ $htmlview += viewBtn_Devol;}

    containerOptions.append($htmlview);
}

$(document).on('change', '#IdServicioUsuario', function() {
    "use strict";
    let IdSerUsuario = $(this).val();
    load_Almacenes_xServicioUsuario(IdSerUsuario);
});

function load_Almacenes_xServicioUsuario(id) {
    "use strict";
    let $selectID = $('#IdAlmacen');
    $selectID.empty();
    $selectID.prop('disabled', true);
    if(id !== "") {
        $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_ServicioUsuario_JSON', {'idsu': id}, function (lista) {
            $selectID.empty();
            $selectID.append('<option></option>');
            if (lista !== null && parseInt(lista.length) > 0) {
                for (let i = 0; i < parseInt(lista.length); i++) {
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    $selectID.append($option);
                }
            }
        }, "json").always(function () {
            $selectID.prop('disabled', false);
            sga.plugins.select2('.selectClass');
        });
    }
}

$(document).on('change', '#IdAlmacen', function() {
    "use strict";
    let IdAlmacen = $(this).val();
    let elementSelect = $('#IdAlmacen option:selected')
    let IdTextAlmacen = $('#txtAlmacen');
    let divOption = $('#divOption_btns');
    let opImport = $('#acc_import').val();
    divOption.empty();
    IdTextAlmacen.empty();
    IdTextAlmacen.text(elementSelect.text());
    sga.blockUI.loading_body();
    if(IdAlmacen !== ""){
        valida_existenciaInvetario(IdAlmacen,opImport);
    }
});

function valida_existenciaInvetario(IdAlmacen,opImport) {
    "use strict";
    totalItemsPagTable = 0;
    let divImport = $('#divLoad_import');
    let divContend = $('#divContend');
    divImport.empty();
    divContend.empty();
    $.get('../controller/InventarioController.php?action=validar_existenciaInventario_JSON', {'IdAlmacen':IdAlmacen},function (response) {
        let data = JSON.parse(response);
        totalItemsPagTable = data.valor;
        if(parseInt(data.valor) > 0) {
            divContend.append(optTable);
            let datos = {
                'almacen': IdAlmacen,
                'idustk': $('#idustk').val(),
                'elementSelect' : $('#IdAlmacen option:selected').text()
            };
            loadTbl_Inventario_INI(datos);
        }
        else if(parseInt(data.valor) === 0 && parseInt(opImport) === 1) {
            divImport.append(optImport);
        }
        else if(parseInt(data.valor) === 0 && parseInt(opImport) === 0) {
            divImport.append(optImport_notPermise);
        }
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}

function loadTbl_Inventario_INI(datos){
    "use strict";
    let infodetail = $('#infodetail');
    infodetail.empty();
    viewOption_Inventary();
    sga.blockUI.loading_body();
    $Tbl_Inventario = $('#Tbl_Inventario').DataTable({
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm mr-15',
                title: 'LISTA DE INVENTARIO : '+datos.elementSelect,
                text: '<span class="fa fa-file-excel-o position-left"></span> Exportar',
                filename: function(){
                    let f = new Date();
                    return 'Export-Inventario-' + f.getDate() + (f.getMonth() +1) + f.getFullYear();
                },
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    }
                }
            },
            {
                extend: 'colvis',
                className: 'btn btn-primary btn-sm mr-15',
                text: 'Columnas',
            }
        ],
        colReorder: true,
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        lengthMenu: [ [25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"] ],
        Columns:[
            { "mDataProp": "0"},
            { "mDataProp": "1"},
            { "mDataProp": "2"},
            { "mDataProp": "3"},
            { "mDataProp": "4"},
            { "mDataProp": "5"},
            { "mDataProp": "6"},
            { "mDataProp": "7"},
            { "mDataProp": "8"},
            { "mDataProp": "9"},
            { "mDataProp": "10"},
            { "mDataProp": "11"}
        ],
        columnDefs: [
            {
                className: 'control align-middle',
                orderable: false,
                searchable: false,
                targets: [0]
            },
            {
                className: 'text-center align-middle',
                width: "80px",
                visible: true,
                searchable: true,
                targets: [1]
            },
            {
                className: 'text-left align-middle',
                visible: true,
                searchable: true,
                targets: [2]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [3]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [4]
            },
            {
                className: 'text-center align-middle',
                visible: false,
                searchable: true,
                targets: [5]
            },
            {
                className: 'text-center align-middle',
                visible: false,
                searchable: true,
                targets: [6]
            },
            {
                className: 'text-center align-middle',
                visible: false,
                searchable: true,
                targets: [7]
            },
            {
                className: 'text-center align-middle',
                visible: false,
                searchable: true,
                targets: [8]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [9]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [10]
            },
            {
                className: 'text-left align-middle',
                visible: true,
                searchable: false,
                width: "80px",
                targets: [11]
            }
        ],
        scrollCollapse: false,
        processing: true,
        serverSide: true,
        ajax:{
            url: '../controller/InventarioController.php?action=lst_Inventario_xServicio_All_JSON',
            type : "get",
            data : datos,
            dataType : "json",
            error: function(e){
                // console.log(e);
            },complete:function (datos){
                // console.log(datos);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
            if(parseInt(json.data.length) > 0) {
                infodetail.append('Puede visualizar el detalle de cada ítem, haciendo click en el icono: <i class="ti-info-alt"></i>');
            }

            $('#Tbl_Inventario_wrapper > div.datatable-header > div.dataTables_filter > label > input').attr('id','idsearchInvent');
            $('#Tbl_Inventario_wrapper > div.datatable-header > div.dataTables_filter > label > input').attr('style','margin-left: 15px !important');
            $('#Tbl_Inventario_wrapper > div.datatable-header > div.dataTables_length > label > select').addClass('mr-15');
        },
        "drawCallback": function( settings ) {
            console.log(settings)
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
        }
    });
}

$(document).on('click', '.chkInactiveInput', function() {
    "use strict";
    let $this = $(this);
    let idInput = $this.attr('data-id');
    let selectorID = $('#'+idInput);
    if( $this.is(':checked') ) {
        selectorID.prop('disabled',false);
    }
    else{
        selectorID.val("");
        selectorID.prop('disabled',true);
    }
});

$(document).on('click','#Tbl_Inventario tbody td', function(){
    "use strict";
    let rowIdx = $Tbl_Inventario.cell(this).index().column;
    if(parseInt(rowIdx)>=1  && parseInt(rowIdx)<=7){
        let cunit = $('#text_cunit');
        let reserva = $('#text_reserva');
        let pedido = $('#text_fpedido');
        let masDetails = $('#btnMas_detail');

        let data = $Tbl_Inventario.row( this ).data();
        masDetails.attr('data-id',data[1]);
        cunit.text(data[9]);
        reserva.text(data[10]);
        pedido.text(data[11]);
    }
});

$(document).on('click','#btnDelete_exp',function(){
    "use strict";
    let $tblPaste_ord = $('#tblPaste_codExport');
    let $tblDefault;
    $tblDefault  ='<table id="TblMasCOD_Export" class="table table-bordered" cellpadding="0" cellspacing="0" width="100%">';
    $tblDefault +='  <thead>';
    $tblDefault +='    <tr>';
    $tblDefault +='      <th class="text-center" width="30"></th>';
    $tblDefault +='      <th class="text-left">Código</th>';
    $tblDefault +='    </tr>';
    $tblDefault +='  </thead>';
    $tblDefault +='  <tbody>';
    $tblDefault +='<tr>';
    $tblDefault +='  <td class="text-center">1</td><td class="text-center p-0"><input type="text" class="form-control inputmOrd"></td>';
    $tblDefault +='</tr>';
    $tblDefault +='<tr>';
    $tblDefault +='  <td class="text-center">2</td><td class="text-center p-0"><input type="text" class="form-control inputmOrd"></td>';
    $tblDefault +='</tr>';
    $tblDefault +='<tr>';
    $tblDefault +='  <td class="text-center">3</td><td class="text-center p-0"><input type="text" class="form-control inputmOrd"></td>';
    $tblDefault +='</tr>';
    $tblDefault +='<tr>';
    $tblDefault +='  <td class="text-center">4</td><td class="text-center p-0"><input type="text" class="form-control inputmOrd"></td>';
    $tblDefault +='</tr>';
    $tblDefault +='<tr>';
    $tblDefault +='  <td class="text-center">5</td><td class="text-center p-0"><input type="text" class="form-control inputmOrd"></td>';
    $tblDefault +='</tr>';
    $tblDefault +='  </tbody>';
    $tblDefault +='</table>';
    $tblPaste_ord.html($tblDefault);
    $('#masCodigo_bexp').val("");
    arrayVAL_exp.length = 0;
    $('#btnMas_codBus').removeClass('btn-warning').addClass('btn-info');
    $('#codigo_ajt').val("");
});

$(document).on('click','#btnAceptar_exp',function(){
    "use strict";
    let $inputVal = $('#masCodigo_bexp');
    let modalDefault = $('#ModalAction_ContainerForm');
    $inputVal.val("");
    let $textValor = "";
    let arrayVAL_temp = [];
    $('input[name="masCodigo_exp[]"]').each(function(i){
        if($.trim($(this).val()) !== "") {
            arrayVAL_temp.push($(this).val());
        }
    });

    if(arrayVAL_temp.length >0){
        for(let $i=0; $i<arrayVAL_temp.length; $i++){
            if($i === parseInt(arrayVAL_temp.length) - 1){
                $textValor += arrayVAL_temp[$i];
            }
            else{
                $textValor += arrayVAL_temp[$i] + ",";
            }
        }
        $inputVal.val($textValor);
        $('#btnMas_codBus').removeClass('btn-info').addClass('btn-warning');
        $('#codigo_ajt').val(arrayVAL_temp[0]);
    }
    else{
        $('#btnMas_codBus').removeClass('btn-warning').addClass('btn-info');
    }
    modalDefault.modal('hide');
    modalDefault.empty();
});

$(document).on('click','#btnCloseModal_exp',function(){
    "use strict";
    let $inputVal = $('#masCodigo_bexp');
    if($.trim($inputVal.val()).length === 0){
        $inputVal.val("");
        $('#codigo_ajt').val("");
    }
    arrayVAL_exp.length = 0;
});

$(document).on('click','#ingresarItem_Btn',function(){
    "use strict";
    let idus = $('#idustk').val();
    let idsu = $('#IdServicioUsuario').val();
    let idalm = $('#IdAlmacen').val();
    if(idalm !== "") {
        let $divTabla = $('#divTabla');
        let $divResponse = $('#divResponse');
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/InventarioController.php?action=loadCampos_NuevoItem_Ajax', {
            'idus': idus,
            'idsu': idsu,
            'idalm': idalm
        }, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
            sga.plugins.flatpickr_all('.inputFecha');
            sga.plugins.formatter_date('.inputFecha', '/');
            sga.plugins.inputFile('#filedata_import','excel');
            sga.plugins.formatter_nguia_ie('#guia_itm');
            sga.funcion.pageTop();
        });
    }
    else{
        swal.fire({
            html: "Debe seleccionar un Almacen para realizar un <br><code class='fz-18 font-weight-bold'>Ingreso</code>",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('click', '#btnCancel', function(){
    "use strict";
    resetIni_Campos();
});

$(document).on('submit','#formNewItem', function(e){
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    sga.blockUI.loading_body();
    $.post('../controller/InventarioController.php?action=registrar_Item_JSON', data,
        function (response) {
            if (parseInt(response.status) === 1) {
                swal({
                    text: response.message,
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
                sga.error.show('warning', 'mensajes_actions_act', response.message);
                window.setTimeout(function () {$('#mensajes_actions_act').html("");}, 6000);
            }
            else if (parseInt(response.status) === 0) {
                sga.error.show('danger', 'mensajes_actions_act', response.message);
                window.setTimeout(function () {$('#mensajes_actions_act').html("");}, 6000);
            }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensajes_actions_act', 'Error al realizar el registro');
        window.setTimeout(function () { $('#mensajes_actions_act').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

function resetIni_Campos() {
    "use strict";
    let $divTabla = $('#divTabla');
    let $divResponse = $('#divResponse');
    $divTabla.show();
    $divResponse.hide();
    $divResponse.empty();
    sga.table.refreshDatatable_chk('#Tbl_Inventario');
    sga.funcion.pageTop();
    elementosP.length = 0;
}

$(document).on('click','#editarItem_Btn',function(){
    "use strict";
    let thisIdInventario = $(this).attr('data-id');
    let $divTabla = $('#divTabla');
    let $divResponse = $('#divResponse');
    let idustk = $('#idustk');
    $divTabla.show();
    $divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/InventarioController.php?action=loadCampos_EditarItem_Ajax', {'idinv':thisIdInventario, 'idus':idustk.val()}, function (response) {
        $divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        $divTabla.hide();
        $divResponse.show();
        sga.plugins.flatpickr_all('.inputFecha');
        sga.plugins.formatter_date('.inputFecha','/');
        sga.plugins.formatter_nguia_ie('#guia_itm');
        sga.funcion.pageTop();
    });
});

$(document).on('submit','#formEditItem', function(e) {
    "use strict";
    e.preventDefault();
    let $data = $(this).serialize();
    Swal.fire({
        html: 'Se va a modificar las datos ingresados.<br>Desea grabar los cambios...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/InventarioController.php?action=actualizar_Item_JSON', $data, function(response){
                if (parseInt(response.status) === 1) {
                    swal({
                        text: response.message,
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
                else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensajes_actions_act', response.message);
                    window.setTimeout(function () {$('#mensajes_actions_act').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensajes_actions_act',"Error al realizar la actualización de los datos, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensajes_actions_act').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#deleteItem_Btn', function(e) {
    "use strict";
    e.preventDefault();
    let thisIdInventario = $(this).attr('data-id');
    swal.fire({
        html: 'Se va a eliminar el registro seleccionado.<br>Una vez realizada esta acción no podra ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            let idustk = $('#idustk');
            $.post('../controller/InventarioController.php?action=delete_Item_JSON', {'id':thisIdInventario, 'idus':idustk.val()}, function (response) {
                if (parseInt(response.status) === 1 || parseInt(response.status) === 2) {
                    swal({
                        text: "Registro(s) eliminado(s) satisfactoriamente.",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        sga.table.refreshDatatable_chk('#Tbl_Inventario');
                    });
                }
                else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensajes_actions_inv', 'Error al eliminar los registros seleccionados.');
                    window.setTimeout(function () { $('#mensajes_actions_inv').html(""); }, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger', 'mensajes_actions_inv', "Se produjo un error al intentar eliminar los registros, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
                window.setTimeout(function () { $('#mensajes_actions_inv').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#transferirItem_Btn',function(){
    "use strict";
    let idus = $('#idustk').val();
    let idsu = $('#IdServicioUsuario').val();
    let idalm = $('#IdAlmacen').val();
    if(idalm !== "") {
        let $divTabla = $('#divTabla');
        let $divResponse = $('#divResponse');
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/InventarioController.php?action=loadCampos_TranferirItem_Ajax', {
            'idus': idus,
            'idsu': idsu,
            'idalm': idalm
        }, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
            sga.plugins.select2('.selectedClass');
            sga.plugins.flatpickr_all('.inputFecha');
            sga.plugins.formatter_date('.inputFecha', '/');
            sga.funcion.pageTop();
        });
    }
    else{
        swal.fire({
            html: "Debe seleccionar un Almacen para realizar una <br><code class='fz-18 font-weight-bold'>Tranferencia</code>",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('submit','#formTransferirItem', function(e) {
    "use strict";
    e.preventDefault();
    let tipoTransfer = $('input:radio[name=rdbTipoTransfer]:checked').val();
    let data = {
        'idusitm_tk': $('#idusitm_tk').val(),
        'IdAlmacen_itm': $('#IdAlmacen_itm').val(),
        'recibido_itm': $('#recibido_itm').val(),
        'autorizado_itm': $('#autorizado_itm').val(),
        'obs_itm': $('#obs_itm').val(),
        'idalm_i': $('#idalm_i').val(),
        'fechareg_i': $('#fechareg_i').val(),
        'idalc': $('#idalc').val(),
        'valalc': $('#valalc').val(),
        'nrotransf_i': $('#nrotransf_i').val(),
        'tipoTransfer': tipoTransfer,
        'detalle' : JSON.parse(objinit.consultarP())
    };

    if(parseInt(tipoTransfer) ===2){
        data.motivo_itm = $('#motivo_itm').val();
        data.fguia_itm = $('#fguia_itm').val();
        data.nguia_itm = $('#nguia_itm').val();
        data.ndias_itm = $('#ndias_itm').val();
        data.aper1_itm = $('#aper1_itm').val();
        data.adoc1_itm = $('#adoc1_itm').val();
        data.aper2_itm = $('#aper2_itm').val();
        data.adoc2_itm = $('#adoc2_itm').val();
    }

    if(elementosP.length > 0) {
        Swal.fire({
            html: 'Se va realizar la Tranferencia de los Ítems seleccionados.<br>Desea continuar...!.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.value) {
                sga.blockUI.loading_body();
                $.post('../controller/InventarioController.php?action=tranferir_Item_JSON', data, function (response) {
                    if (parseInt(response.status) === 1) {
                        if(parseInt(response.tipotransfer) === 2){
                            swal.fire({
                                html: response.message,
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
                        else{
                            swal({
                                text: response.message,
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
                    } else if (parseInt(response.status) === 0) {
                        sga.error.show('danger', 'mensajes_actions_act', response.message);
                        window.setTimeout(function () {
                            $('#mensajes_actions_act').html("");
                        }, 6000);
                    }
                },"json").fail(function (e) {
                    sga.error.show('danger', 'mensajes_actions_act', "Error al realizar la transferencia de los ítems, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                    window.setTimeout(function () {
                        $('#mensajes_actions_act').html("");
                    }, 6000);
                }).always(function () {
                    sga.blockUI.unblock_body();
                });
            }
        });
    }
    else{
        swal.fire({
            html: "Debe Agregar al menos 01 item a transferir.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('click','#btnOptionsGuia_Export',function(){
    "use strict";
    let idTransfer = $(this).attr('data-id');
    swal.close();
    let modalLoading = $('#ModalProgressBar_Load');
    modalLoading.empty();
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.empty();
    $.get('../controller/InventarioController.php?action=loadview_optionGuia', {'id':idTransfer}, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('click','#btnGenerated_exp',function () {
    "use strict";
    let idTransfer = $('#idtransf').val();
    let optionTransfer = $('input:radio[name=rdbOption_g]:checked').val();
    generarExport_Guia(idTransfer,optionTransfer);
});

function generarExport_Guia(idTransfer,optionTransfer) {
    "use strict";
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal('hide');
    modalDefault.empty();
    let page = '../app/Export-Guia-PDF-Item.php?idTransfer='+idTransfer+'&option='+optionTransfer;
    $.ajax({
        url: page,
        type: 'POST',
        beforeSend: function() {
            sga.blockUI.loading_body();
        },
        success: function(){
            window.location = page;// you can use window.open also
        },
        error: function(xhr) { // if error occured
            console.log("Error occured.please try again");
            console.log(xhr.statusText + " - " +xhr.responseText);
        },
        complete: function() {
            window.setTimeout(function () {
                sga.blockUI.unblock_body();
                swal.fire({
                    html: 'Guía generada satisfactoriamente',
                    type: "success",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    resetIni_Campos();
                });
            }, 5000);
        }
    });
}

$(document).on('click','#btnCancel_exp',function(){
    "use strict";
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal('hide');
    modalDefault.empty();
    resetIni_Campos();
});

$(document).on('click','#btnLoad_ItemAlmacen',function(){
    "use strict";
    let contModalLoading = $('#ModalProgressBar_Load');
    let idalm = $(this).attr('data-idalm');
    sga.wait.modal('ModalProgressBar_Load');
    contModalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.html(modalInv);
    $Tbl_ItemStock = $('#Tbl_ItemStock').DataTable({
        dom: '<"datatable-header"flB><"datatable-Columnas"><"datatable"t><"datatable-footer"ip>',
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
            { "mDataProp": "7"},
            { "mDataProp": "8"},
            { "mDataProp": "9"},
            { "mDataProp": "10"},
            { "mDataProp": "11"},
            { "mDataProp": "12"}
        ],
        columnDefs: [
            {
                className: 'control',
                orderable: false,
                searchable: false,
                targets: [0]
            },
            {
                className: 'text-center',
                visible: true,
                searchable: true,
                targets: [1]
            },
            {
                className: 'text-center',
                visible: true,
                searchable: true,
                targets: [2]
            },
            {
                className: 'text-left',
                visible: true,
                searchable: true,
                targets: [3]
            },
            {
                className: 'text-center',
                visible: false,
                searchable: true,
                targets: [4]
            },
            {
                className: 'text-center',
                visible: true,
                searchable: true,
                targets: [5]
            },
            {
                className: 'text-center',
                visible: true,
                searchable: true,
                targets: [6]
            },
            {
                className: 'text-center',
                visible: false,
                searchable: true,
                targets: [7]
            },
            {
                className: 'text-center',
                visible: false,
                searchable: true,
                targets: [8]
            },
            {
                className: 'text-center',
                visible: false,
                searchable: true,
                targets: [9]
            },
            {
                className: 'text-center',
                visible: false,
                searchable: true,
                targets: [10]
            },
            {
                className: 'text-center',
                visible: true,
                searchable: true,
                targets: [11]
            },
            {
                className: 'text-center',
                orderable: false,
                visible: true,
                searchable: false,
                width: "120px",
                targets: [12]
            }
        ],
        order:[3,'desd'],
        ajax:{
            url: '../controller/InventarioController.php?action=lista_existencias_Inventario_xAlmacen_JSON',
            type : "get",
            dataType : "json",
            data : {'id': idalm},
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            $('#Tbl_ItemStock_info').addClass('mb-0');
            contModalLoading.modal("hide");
            contModalLoading.empty();
            contModalLoading.hide();
            modalDefault.modal("show");
            if(parseInt(json.data.length) > 0) {
                let divNavColumnas = $('.datatable-Columnas');
                divNavColumnas.empty();
                let btnColumnas = '<button type="button" class="btn btn-outline-secondary float-right collapsed" data-toggle="collapse" href="#collapseColumnas" aria-expanded="true" aria-controls="collapseColumnas">Columnas</button>';
                $('#Tbl_ItemStock_wrapper > div.datatable-header > div.dt-buttons').prepend(btnColumnas);
                $('#Tbl_ItemStock_wrapper > div.datatable-header > div.dt-buttons').addClass('p-r-20');
                divNavColumnas.append(columVisiMdl);
            }
        }
    });
});

$(document).on('click','#btnclose_Modal',function(){
    "use strict";
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal("hide");
    modalDefault.empty();
});

$(document).on('click','#btnItem_AddCart',function() {
    "use strict";
    let idinv = $(this).attr('data-id');
    let codigo = $(this).attr('data-cod');
    let des = $(this).attr('data-des');
    let nparte = $(this).attr('data-nparte');
    let marca = $(this).attr('data-marca');
    let stock = $(this).attr('data-stock');
    let cactivo = $(this).attr('data-cactivo');
    let cinventario = $(this).attr('data-cinventario');
    let cmapel = $(this).attr('data-cmapel');
    let conu = $(this).attr('data-conu');
    let tipo = $(this).attr('data-tipo');
    let umed = $(this).attr('data-um');
    let nStock = parseFloat(stock);
    if(parseInt(tipo) === 2){
        nStock = parseInt(stock);
    }
    AgregarItem_Carrito(idinv,codigo,des,nparte,cactivo,cinventario,cmapel,conu,1,nStock,marca,umed);
});

function AgregarItem_Carrito(id_inv, codigo, des, nparte, cactivo, cinventario, cmapel, conu, cantidad, stock, marca,umed){
    "use strict";
    let detalleArticulo = [parseInt(id_inv), $.trim(codigo), $.trim(des), $.trim(nparte), $.trim(cactivo), $.trim(cinventario), $.trim(cmapel), $.trim(conu), parseInt(cantidad), parseInt(stock), $.trim(marca), $.trim(umed) ];
    let coindice = 0;
    if(elementosP.length > 0) {
        let data = JSON.parse(objinit.consultarP());
        if(data.length > 0) {
            for (let pos in data) {
                if( ($.trim(data[pos][1]) === $.trim(codigo)) && ($.trim(data[pos][3]) === $.trim(nparte))  &&
                    ($.trim(data[pos][4]) === $.trim(cactivo))
                ){
                    coindice++;
                }
            }
        }
    }

    if(coindice === 0){
        elementosP.push(detalleArticulo);
        ConsultarDetalles_Item();
    }
    else{
        sga.error.show('warning', 'mensaje_action_mdl', "El ítem seleccionado ya se encuentra agregado.");
        window.setTimeout(function () {$('#mensaje_action_mdl').html("");}, 8000);

    }
}

function ConsultarDetalles_Item() {
    "use strict";
    let TblDetalleItem = $("#Tbl_DetalleItem > tbody");
    TblDetalleItem.empty();
    let data = JSON.parse(objinit.consultarP());
    let cantidad, stock;
    if(parseInt(data.length)>0) {
        let fila = 1;
        let campoInfo = "";
        for (let pos in data) {
            cantidad = '<input type="text" class="form-control text-center fz-18 font-weight-bold actionCalculoCantidad"  id="txt_cant' + pos + '" value="' + data[pos][8] + '" data-pos="' + pos + '">';
            stock    = '<input type="hidden" id="txt_stk' + pos + '" value="' + data[pos][9] + '">';
            campoInfo = '<a id="btnDetailInventary" class="cursor-pointer float-left text-hover-primary mr-7" data-toggle="tooltip" title="" data-original-title="Click para ver detalle" data-id="' + data[pos][0] + '"><i class="ti-info-alt"></i></a>';

            let row = '<tr>' +
                '<td class="text-center fz-18">' + fila + '</td>' +
                '<td class="text-center font-weight-bold fz-18">' + campoInfo +data[pos][1] + '</td>' +
                '<td class="text-left fz-18">' + data[pos][2] + '</td>' +
                '<td class="text-center fz-18">' + data[pos][11] + '</td>' +
                '<td class="text-center fz-18">' + data[pos][3] + '</td>' +
                '<td class="text-left fz-18">' + cantidad + '</td>' +
                '<td class="font-weight-bold fz-18 text-primary-400 text-center">' + stock + sga.funcion.leftPad(data[pos][9],2) +'</td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-hover-transform" data-pos="' + pos + '" title="Eliminar ítem" id="btnDelete_Item"><i class="fa fa-minus"></i></button></td>' +
                '</tr>';
            TblDetalleItem.append(row);
            fila++;
        }
    }
    else{
        TblDetalleItem.append('<tr><td colspan="8" class="text-center">No se encontraron ítems agregados.</td></tr>');
    }
}

$(document).on('click','#btnDelete_Item',function() {
    "use strict";
    let position = parseInt($(this).attr('data-pos'));
    eliminar_DetalleItem(position);
});

function eliminar_DetalleItem(ele){
    "use strict";
    objinit.eliminarP(ele);
    ConsultarDetalles_Item();
}

$(document).on('change','.actionCalculoCantidad',function() {
    "use strict";
    let position = $(this).attr('data-pos');
    let optionDevolver = $('#btnLoad_ItemAlmacen').attr('data-op-d');
    ModificarCantidad_Item(position,optionDevolver);
});

function ModificarCantidad_Item(pos,optionDevolver){
    "use strict";
    let cant = $('#txt_cant'+pos);
    let stock = $('#txt_stk'+pos);
    if(parseInt(optionDevolver) === 1){
        if(parseFloat(cant.val()) > 0){
            elementosP[pos][8] = cant.val();
            ConsultarDetalles_Item();
        }
        else {
            swal.fire({
                html: "Debe ingresar una cantidad mayor a cero.",
                type: "warning",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK'
            }).then(function () {
                cant.val(1);
                cant.focus();
            });
        }
    }
    else {
        if ((parseFloat(cant.val()) <= parseFloat(stock.val())) && parseFloat(cant.val()) > 0) {
            elementosP[pos][8] = cant.val();
            ConsultarDetalles_Item();
        }
        else {
            swal.fire({
                html: "Debe ingresar una cantidad menor o igual al Stock Actual.",
                type: "warning",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK'
            }).then(function () {
                cant.val(1);
                cant.focus();
            });
        }
    }
}

$(document).on('click','#importItem_Btn',function(){
    "use strict";
    let divTabla = $('#divTabla');
    let divImport = $('#divLoad_import');
    let divResponse = $('#divResponse');
    divTabla.hide();
    divImport.empty();
    divResponse.append(inputFIle);
    divResponse.show();
    sga.plugins.inputFile('#filedata_import','excel');
});

$(document).on('submit','#form_Viewtable_listDatos',function(e){
    "use strict";
    e.preventDefault();
    let $divFileInput = $('#div_FileInput');
    let divRespondeAll = $('#divResponse_All');
    let titulo = "Lista de ítems a cargar.";
    $divFileInput.show();
    divRespondeAll.hide();
    let formdata = new FormData($(this)[0]);
    $.ajax({
        url: '../controller/InventarioController.php?action=list_View_Rows_File',
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
            if(parseInt(response.status)===0 || parseInt(response.status)===3){
                swal.fire({
                    text: response.message,
                    type: "error",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    reset_campos_InputFile();
                });
            }
            else if(parseInt(response.status)===2){
                swal.fire({
                    text: response.message,
                    type: "warning",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    reset_campos_InputFile();
                });
            }
            else if(parseInt(response.status)===1){
                let lista = response.data;
                if (lista !== null && lista.length > 0) {
                    $divFileInput.hide();
                    divRespondeAll.show();
                    contendResponse( titulo);
                    sga.plugins.inputFile_clear('#filedata_import');
                    sga.plugins.inputFile_enable('#filedata_import');
                    $Nregistros = parseInt(lista.length);
                    for (let t = 0; t < parseInt(lista.length); t++) {
                        setDatos_xTipo(lista[t], t);
                        if(t === parseInt(lista.length)-1){
                            $('.btnDisabled_stand').prop('disabled',false);
                        }
                    }
                }
                else{
                    swal.fire({
                        text: "El archivo adjunto no cumple con los criterios del tipo de archivo a cargar.",
                        type: "warning",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        reset_campos_InputFile();
                    });
                }
            }
        }
    }).fail(function () {
        swal({
            text: "Error a intentar visualizar los datos del adjunto.",
            type: "error",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'Ok'
        }).then(function () {
            reset_campos_InputFile();
        });
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

function setDatos_xTipo(lista, i) {
    "use strict";
    let lineTbl;
    lineTbl  = '<tr id="dat_tr_' + i + '">';
    lineTbl += '  <td class="text-center text-center">' + (parseInt(i)+1) + '</td>';
    lineTbl += '  <td class="text-center n_1">' + lista[0] + '</td>';
    lineTbl += '  <td class="text-left n_2">' + lista[1] + '</td>';
    lineTbl += '  <td class="text-left n_3">' + lista[2] + '</td>';
    lineTbl += '  <td class="text-left n_4">' + lista[3] + '</td>';
    lineTbl += '  <td class="text-left n_5">' + lista[4] + '</td>';
    lineTbl += '  <td class="text-left n_6 d-none">' + lista[5] + '</td>';
    lineTbl += '  <td class="text-left n_7 d-none">' + lista[6] + '</td>';
    lineTbl += '  <td class="text-left n_8 d-none">' + lista[7] + '</td>';
    lineTbl += '  <td class="text-left n_9 d-none">' + lista[8] + '</td>';
    lineTbl += '  <td class="text-left n_10 d-none">' + lista[9] + '</td>';
    lineTbl += '  <td class="text-left n_11 d-none">' + lista[10] + '</td>';
    lineTbl += '  <td class="text-left n_12 d-none">' + lista[11] + '</td>';
    lineTbl += '  <td class="text-left n_13 d-none">' + lista[12] + '</td>';
    lineTbl += '  <td class="text-left n_14 d-none">' + lista[13] + '</td>';
    lineTbl += '  <td class="text-left n_15 d-none">' + lista[14] + '</td>';
    lineTbl += '  <td class="text-left n_16 d-none">' + lista[15] + '</td>';
    lineTbl += '  <td class="text-left n_17 d-none">' + lista[16] + '</td>';
    lineTbl += '  <td class="text-left n_18 d-none">' + lista[17] + '</td>';
    lineTbl += '  <td class="text-left n_19 d-none">' + lista[18] + '</td>';
    lineTbl += '  <td class="text-left n_20 d-none">' + lista[19] + '</td>';
    lineTbl += '</tr>';
    $('#TblStandar_Load > tbody').append(lineTbl);
}

$(document).on('click', '#btn_LoadDatos', function(){
    "use strict";
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
                importarDatos_Inventario($('#dat_tr_' + $count));
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

function contendResponse(titulo) {
    "use strict";
    let $contenedor = $('#divResponse_All');
    let row='    <div class="row">';
    row+='      <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-20 align-middle">';
    row+='         <h5 class="display-6">'+titulo+'</h5>';
    row+='      </div>';
    row+='      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-right mb-20 align-middle">';
    row+='        <button type="button" class="btn btn-outline-danger btnDisabled_stand mr-10 " id="btnCancel_Load" disabled>';
    row+='          <i class="icon-ban position-left"></i>Cancelar';
    row+='        </button>';
    row+='        <button type="button" class="btn btn-primary btnDisabled_stand btn-hover-transform" id="btn_LoadDatos" disabled>';
    row+='          <i class="icon-share-alt position-left"></i>Cargar Datos';
    row+='        </button>';
    row+='      </div>';
    row+='    </div>';
    row+='<div class="card card-shadow">';
    row+='  <div class="table-responsive"> ' + tblLoadM +'</div>';
    row+='  <div class="card-footer row">';
    row+='    <label class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-right  text-danger font-weight-bold"> Error <i class="fa fa-times position-right"></i></label>';
    row+='    <label class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-center text-warning font-weight-bold"> Existe ítems <i class="fas fa-history position-right"></i></label>';
    row+='    <label class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left  text-success font-weight-bold"> Correcto <i class="fa fa-check position-right"></i></label>';
    row+='  </div>';
    row+='</div>';
    $contenedor.append(row);
}

function importarDatos_Inventario(fila) {
    "use strict";
    let idAlmacen = $('#IdAlmacen').val();
    let opImport = $('#acc_import').val();
    let idustk = $('#idustk').val();
    let tipoload = $('#tipoload').val();
    let data = {
        'idalm':idAlmacen,
        'n1': fila.find('.n_1').text(),
        'n2': fila.find('.n_2').text(),
        'n3': fila.find('.n_3').text(),
        'n4': fila.find('.n_4').text(),
        'n5': fila.find('.n_5').text(),
        'n6': fila.find('.n_6').text(),
        'n7': fila.find('.n_7').text(),
        'n8': fila.find('.n_8').text(),
        'n9': fila.find('.n_9').text(),
        'n10': fila.find('.n_10').text(),
        'n11': fila.find('.n_11').text(),
        'n12': fila.find('.n_12').text(),
        'n13': fila.find('.n_13').text(),
        'n14': fila.find('.n_14').text(),
        'n15': fila.find('.n_15').text(),
        'n16': fila.find('.n_16').text(),
        'n17': fila.find('.n_17').text(),
        'n18': fila.find('.n_18').text(),
        'n19': fila.find('.n_19').text(),
        'n20': fila.find('.n_20').text(),
        'idus':idustk,
        'tipoload':tipoload
    };

    $.post('../controller/InventarioController.php?action=registrar_Inventario_Import_JSON', data, function(response){
        if(parseInt(response.status) === 2){ // ya existe registro
            $('#dat_tr_' + $count).addClass('table-warning').removeClass('table-success').removeClass('table-danger');
        }
        else if(parseInt(response.status) === 1){//registro satisfactorio
            $('#dat_tr_' + $count).addClass('table-success').removeClass('table-danger').removeClass('table-warning');
            successFileLoad++;
        }
        else if(parseInt(response.status) === 0) {//error en registro
            $('#dat_tr_' + $count).addClass('table-danger').removeClass('table-warning').removeClass('table-success');
        }
        $count++;
        if(parseInt($count) < parseInt($Nregistros)){
            importarDatos_Inventario($('#dat_tr_' + $count));
        }

        if(parseInt($count) === parseInt($Nregistros)){
            registrar_log_upload_Item(idAlmacen,successFileLoad,idustk);
            if(parseInt(tipoload) === 1) {
                valida_existenciaInvetario(idAlmacen, opImport);
            }
            swal({
                html: "<code>["+$count+"]</code> Registros cargados satisfactoriamente.",
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
                successFileLoad = 0;
            });
        }
    },"json");
}

function registrar_log_upload_Item(id,total,idustk){
    "use strict";
    //let cantidad = $('#almac_cant');
    //let fecha = $('#almac_update');
    $.post('../controller/AlmacenController.php?action=registrar_log_Upload_Almacen', {'id':id, 'total':total,'idus':idustk}, function(response){
        if(parseInt(response.status)===1){
            //fecha.text(response.fecha);
            //cantidad.text(response.cant);
        }
    },"json");
}

$(document).on('click','#retirarItem_Btn',function(){
    "use strict";
    let idus = $('#idustk').val();
    let idsu = $('#IdServicioUsuario').val();
    let idalm = $('#IdAlmacen').val();
    if(idalm !== "") {
        let $divTabla = $('#divTabla');
        let $divResponse = $('#divResponse');
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/InventarioController.php?action=loadCampos_RetirarItem_Ajax', {
            'idus': idus,
            'idsu': idsu,
            'idalm': idalm
        }, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
            sga.plugins.flatpickr_all('.inputFecha');
            sga.plugins.formatter_date('.inputFecha', '/');
            sga.funcion.pageTop();
        });
    }
    else{
        swal.fire({
            html: "Debe seleccionar un Almacén para realizar un <br><code class='fz-18 font-weight-bold'>Retiro</code>",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('keyup','#om_itm_change', function() {
    "use strict";
    let nroorden = $(this).val();
    let codigo = $('#nroretiro_i');
    let infoCod = $('#infoCorrelativo');
    let formatCod = codigo.val().split("-")
    if($.trim(nroorden).length > 0){
        codigo.val("SW-"+ formatCod[1]+"-"+formatCod[2]);
        infoCod.text("SW-"+ formatCod[1]+"-"+formatCod[2]);
    }
    else{
        codigo.val("SO-"+ formatCod[1]+"-"+formatCod[2]);
        infoCod.text("SO-"+ formatCod[1]+"-"+formatCod[2]);
    }
});

$(document).on('submit','#formRetiroItem', function(e) {
    "use strict";
    let $divTabla = $('#divTabla');
    let $divResponse = $('#divResponse');
    $divTabla.hide();
    $divResponse.show();
    e.preventDefault();
    let data = {
        'idusitm_tk': $('#idusitm_tk').val(),
        'idalm_i': $('#idalm_i').val(),
        'fechareg_i': $('#fechareg_i').val(),
        'idalc': $('#idalc').val(),
        'valalc': $('#valalc').val(),
        'nroretiro_i': $('#nroretiro_i').val(),
        'autogenvale': $('#autogenvale_i').val(),
        'IdAlmacen_itm': $('#IdAlmacen_itm').val(),
        'nrovale_itm': $('#nrovale_itm').val(),
        'solicitado_itm': $('#aper_itm').val(),
        'docsolicitado_itm': $('#adoc_itm').val(),
        'autorizado_itm': $('#autorizado_itm').val(),
        'chk_notAutorizado': $('#chk_notAutorizado').val(),
        'areaoperativa': $('#area_itm').val(),
        'tipocargo': $('#tipocargo_itm').val(),
        'obs_itm': $('#obs_itm').val(),
        'detalle' : JSON.parse(objinit.consultarP())
    };
    if(elementosP.length > 0) {
        Swal.fire({
            html: 'Se va realizar el retiro de los Ítems seleccionados.<br>Desea continuar...!.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.value) {
                sga.blockUI.loading_body();
                $.post('../controller/InventarioController.php?action=retirar_Item_JSON', data, function (response) {
                    if (parseInt(response.status) === 1) {
                        Swal.fire({
                            title: '<strong>Retiro Satisfactorio.</strong>',
                            html: response.message,
                            type: "success",
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6e7881',
                            confirmButtonText: 'Generar Vale',
                            cancelButtonText: 'Salir'
                        }).then((result) => {
                            if (result.value) {
                                generar_valeMovimiento(response.idmovimiento);
                            }
                            else{
                                resetIni_Campos();
                            }
                        });
                    }
                    else if (parseInt(response.status) === 0) {
                        sga.error.show('danger', 'mensajes_actions_act', response.message);
                        window.setTimeout(function () {
                            $('#mensajes_actions_act').html("");
                        }, 6000);
                    }
                },"json").fail(function (e) {
                    sga.error.show('danger', 'mensajes_actions_act', "Error al realizar el retiro de los ítems, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                    window.setTimeout(function () {
                        $('#mensajes_actions_act').html("");
                    }, 6000);
                }).always(function () {
                    sga.blockUI.unblock_body();
                });
            }
        });
    }
    else{
        swal.fire({
            html: "Debe Agregar al menos 01 item a retirar.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('click','#devolverItem_Btn',function(){
    "use strict";
    let idus = $('#idustk').val();
    let idsu = $('#IdServicioUsuario').val();
    let idalm = $('#IdAlmacen').val();
    if(idalm !== "") {
        let $divTabla = $('#divTabla');
        let $divResponse = $('#divResponse');
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/InventarioController.php?action=loadCampos_searchVale_JSON', {
            'idus': idus,
            'idsu': idsu,
            'idalm': idalm
        }, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
            sga.funcion.pageTop();
        });
    }
    else{
        swal.fire({
            html: "Debe seleccionar un Almacen para realizar una <br><code class='fz-18 font-weight-bold'>Devolución</code>",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('submit','#busarValeForm', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    let container = $('#divBusquedaEvol');
    container.empty();
    sga.blockUI.loading_body();
    $.get('../controller/InventarioController.php?action=loadCampos_DevolverItem', data, function (response) {
        container.append(response);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btnDelete_ItemRetiro', function() {
    "use strict";
    let row = $(this);
    let sizeTbl = $('#nItemMov');
    if(parseInt(sizeTbl.val()) > 1){
        let diferent = sizeTbl.val() - 1;
        row.parent().parent().remove();
        sizeTbl.val(diferent);
    }
    else {
        swal.fire({
            html: "No es posible eliminar el Ítem seleccionado por el ser único en la lista.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'Aceptar'
        });
    }
});

$(document).on('change','#cantidadDevol',function() {
    "use strict";
    let pendienteDevolver = $(this).attr('data-devolver');
    let aDevolver = $(this);

    if ((parseInt(aDevolver.val()) <= pendienteDevolver) && parseInt(aDevolver.val()) > 0) {
        aDevolver.addClass('is-valid');
    }
    else {
        aDevolver.removeClass('is-valid');
        swal.fire({
            html: "Debe ingresar una cantidad pendiente a devolver",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        }).then(function () {
            aDevolver.val(1);
            aDevolver.focus();
        });
    }
});

$(document).on('submit','#formDevolverItem', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    
    Swal.fire({
        html: 'Se va realizar la devolución de los Ítems descritos.<br>Desea continuar...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/InventarioController.php?action=devolver_Item_JSON', data, function (response) {
                if (parseInt(response.status) === 1) {
                    swal({
                        text: response.message,
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
                else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensajes_actions_act', response.message);
                    window.setTimeout(function () {
                        $('#mensajes_actions_act').html("");
                    }, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger', 'mensajes_actions_act', "Error al realizar la devolución, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () {
                    $('#mensajes_actions_act').html("");
                }, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#btnDetailInventary', function(){
    "use strict";
    let thisIdInvent = $(this).attr('data-id');
    let modalLoading = $('#ModalProgressBar_Load');
    modalLoading.empty();
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.empty();
    $.get('../controller/InventarioController.php?action=loadCampos_viewDetalle_Item_Ajax', {'id':thisIdInvent}, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('click','a.toggle-vis__', function (e) {
    e.preventDefault();
    let actionn = $(this).attr('data-action');
    let column = $Tbl_Inventario.column( $(this).attr('data-column') );
    console.log(column);
    column.visible( ! column.visible() );
    if(parseInt(actionn) === 1){
        $(this).removeClass('bg-warning');
        $(this).attr('data-action',0);
    }
    else if(parseInt(actionn) === 0){
        $(this).addClass('bg-warning');
        $(this).attr('data-action',1);
    }
} );//no se usa

$(document).on('click','a.toggle-vis', function (e) {
    e.preventDefault();
    let actionn = $(this).attr('data-action');
    let column = $Tbl_ItemStock.column( $(this).attr('data-column') );
    column.visible( ! column.visible() );
    if(parseInt(actionn) === 1){
        $(this).removeClass('bg-warning');
        $(this).attr('data-action',0);
    }
    else if(parseInt(actionn) === 0){
        $(this).addClass('bg-warning');
        $(this).attr('data-action',1);
    }
} );

function generar_valeMovimiento(idMovimiento) {
    "use strict";
    let page = '../app/Export-Vale-PDF-Item.php?idMovimiento='+idMovimiento;
    $.ajax({
        url: page,
        type: 'POST',
        beforeSend: function() {
            sga.blockUI.loading_body();
        },
        success: function(){
            window.location = page;// you can use window.open also
        },
        error: function(xhr) { // if error occured
            console.log("Error occured.please try again");
            console.log(xhr.statusText + " - " +xhr.responseText);
        },
        complete: function() {
            window.setTimeout(function () {
                sga.blockUI.unblock_body();
                resetIni_Campos();
            }, 5000);
        }
    });
}

$(document).on('click','#chk_prestamo',function(){
    "use strict";
    let equipoReal = $('#equiporeal_itm_change');
    let tituloEquipoReal= $('#tituloEquiporeal');
    equipoReal.prop('disabled',true);
    equipoReal.val("");
    tituloEquipoReal.empty();
    tituloEquipoReal.text('Equipo real instalado');
    if( $(this).is(':checked') ) {
        equipoReal.prop('disabled',false);
        tituloEquipoReal.html('Equipo real instalado<span class="text-danger font-weight-bold">*</span>');
    }
});

$(document).on('click','#chk_notAutorizado',function(){
    "use strict";
    let inputAutorizadoPor = $('#autorizado_itm');
    let tituloAutorizadoPor = $('#tituloAutorizadoPor');
    inputAutorizadoPor.prop('disabled',false);
    inputAutorizadoPor.val("");
    tituloAutorizadoPor.empty();
    tituloAutorizadoPor.html('Autorizado Por<span class="text-danger font-weight-bold">*</span>');
    if( $(this).is(':checked') ) {
        inputAutorizadoPor.prop('disabled',true);
        tituloAutorizadoPor.text('Autorizado Por');
    }
});

$(document).on('click','.tipoTransAlmacen', function() {
    "use strict";
    let rdbOption = $(this).val();
    let containerID = $('#contedAlmDestinity');
    let containerTransito = $('#contedTransito');
    let IdServicio = $('#idService_i').val();
    let IdAlmacen = $('#idalm_i').val();
    let divCampos, divCardT;
    containerID.empty();
    containerTransito.empty();
    sga.wait.append('#contedAlmDestinity');
    if(parseInt(rdbOption) === 1){
        divCampos =
            '<div class="form-group row">' +
            '   <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '       Almacén Destino <span class="text-danger font-weight-bold">*</span>' +
            '   </label>' +
            '   <div class="col-sm-6">' +
            '       <select name="IdAlmacen_itm" id="IdAlmacen_itm" class="form-control selectedClass" data-placeholder="Seleccione..." required>' +
            '           <option></option>' +
            '       </select>' +
            '   </div>' +
            '</div>';
        containerID.append(divCampos);
        load_Almacenes_xServicio(IdServicio,IdAlmacen);
    }
    if(parseInt(rdbOption) === 2){
        divCampos =
            '<div class="form-group row">' +
            '   <label for="servicio_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '       Servicio <span class="text-danger font-weight-bold">*</span>' +
            '   </label>' +
            '   <div class="col-sm-6">' +
            '       <select name="servicio_itm" id="servicio_itm" class="form-control selectedClassSearch" data-placeholder="Seleccione..." required>' +
            '           <option></option>' +
            '       </select>' +
            '   </div>' +
            '</div>'+
            '<div class="form-group row">' +
            '   <label for="IdAlmacen_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '       Almacén Destino <span class="text-danger font-weight-bold">*</span>' +
            '   </label>' +
            '   <div class="col-sm-6">' +
            '       <select name="IdAlmacen_itm" id="IdAlmacen_itm" class="form-control selectedClass" data-placeholder="Seleccione..." required disabled>' +
            '           <option></option>' +
            '       </select>' +
            '   </div>' +
            '</div>'+
            '<div class="form-group row">' +
            '   <label for="motivo_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '       Motivo <span class="text-danger font-weight-bold">*</span>' +
            '   </label>' +
            '   <div class="col-sm-6">' +
            '       <select name="motivo_itm" id="motivo_itm" class="form-control selectedClass" data-placeholder="Seleccione..." required>' +
            '           <option></option>' +
            '           <option value="Baja">Baja</option>' +
            '           <option value="Calibración">Calibración</option>' +
            '           <option value="Préstamo">Préstamo</option>' +
            '           <option value="Transferencia">Transferencia</option>' +
            '           <option value="Mantenimiento y Reparación">Mantenimiento y Reparación</option>' +
            '           <option value="Devolución">Devolución</option>' +
            '       </select>' +
            '   </div>' +
            '</div>';
        containerID.append(divCampos);
        load_Servicios_All();
        divCardT =
            '<div class="card mb-20 card-shadow">' +
            '   <div class="card-header bg-secondary-light-5">' +
            '       <h4 class="card-title font-weight-bold">Datos Transito</h4>' +
            '   </div>' +
            '   <div class="card-body">' +
            '       <p class="text-muted">' +
            '           De completar los campos descritos a continuación que hacen referencia al transito de la transferencia.' +
            '       </p>' +
            '       <div class="form-group row">' +
            '           <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '               Fecha Guía <span class="text-danger font-weight-bold">*</span>' +
            '           </label>' +
            '           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">' +
            '               <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off" required' +
            '                   name="fguia_itm" id="fguia_itm" maxlength="10" placeholder="**/**/****">' +
            '           </div>' +
            '       </div>' +
            // '       <div class="form-group row">' +
            // '           <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            // '               Número de Guía <span class="text-danger font-weight-bold">*</span>' +
            // '           </label>' +
            // '           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">' +
            // '               <input type="text" class="form-control input-md text-left" autocomplete="off" required' +
            // '                   id="nguia_itm" name="nguia_itm" maxlength="11" placeholder="***-*******">' +
            // '           </div>' +
            // '       </div>' +
            '       <div class="form-group row">' +
            '           <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '               Tiempo llegada estimada <span class="text-danger font-weight-bold">*</span>' +
            '           </label>' +
            '           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">' +
            '               <input type="number" class="form-control input-md text-left" autocomplete="off" required' +
            '                   id="ndias_itm" name="ndias_itm" maxlength="10" placeholder="tiempo estimado">' +
            '               <small class="form-text text-muted">Número de días</small>' +
            '           </div>' +
            '       </div>' +

            '       <div class="form-group row">' +
            '           <label for="aper1_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">' +
            '               Con atención a:' +
            '           </label>' +
            '           <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5">' +
            '               <label for="aper1_itm" class="col-form-label text-lg-right text-md-right text-left">' +
            '                   Persona #1' +
            '               </label>' +
            '               <input type="text" class="form-control input-md text-left" autocomplete="off"' +
            '                   id="aper1_itm" name="aper1_itm" maxlength="40" placeholder="valor">' +
            '           </div>' +
            '           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">' +
            '               <label for="adoc1_itm" class="col-form-label text-lg-right text-md-right text-left">' +
            '                   Nro. documento #1' +
            '               </label>' +
            '               <input type="text" class="form-control input-md text-left" autocomplete="off"' +
            '                   id="adoc1_itm" name="adoc1_itm" maxlength="12" placeholder="valor">' +
            '           </div>' +
            '       </div>' +

            '       <div class="form-group row">' +
            '           <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5 offset-xl-4 offset-lg-4 ">' +
            '               <label for="aper2_itm" class="col-form-label text-lg-right text-md-right text-left">' +
            '                   Persona #2' +
            '               </label>' +
            '               <input type="text" class="form-control input-md text-left" autocomplete="off"' +
            '                   id="aper2_itm" name="aper2_itm" maxlength="40" placeholder="valor">' +
            '           </div>' +
            '           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">' +
            '               <label for="adoc2_itm" class="col-form-label text-lg-right text-md-right text-left">' +
            '                   Nro. documento #2' +
            '               </label>' +
            '               <input type="text" class="form-control input-md text-left" autocomplete="off"' +
            '                   id="adoc2_itm" name="adoc2_itm" maxlength="12" placeholder="valor">' +
            '           </div>' +
            '       </div>' +



            '   </div>' +
            '</div>';
        containerTransito.append(divCardT);
        sga.plugins.flatpickr_all('.inputFecha');
        sga.plugins.formatter_date('#fguia_itm', '/');
        sga.plugins.formatter_nguia('#nguia_itm');
        sga.plugins.formatter_numero('#adoc1_itm');
        sga.plugins.formatter_numero('#adoc2_itm');
    }
    sga.plugins.select2('.selectedClass');
    sga.plugins.select2_search('.selectedClassSearch');

});

function load_Almacenes_xServicio(IdServicio,IdAlmacen) {
    "use strict";
    let selectID = $('#IdAlmacen_itm');
    selectID.empty();
    selectID.prop('disabled', true);

    $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_Servicio_JSON', {'IdServicio': IdServicio,'IdAlmacen':IdAlmacen}, function (lista) {
        selectID.empty();
        selectID.append('<option></option>');
        if (lista !== null && parseInt(lista.length) > 0) {
            for (let i = 0; i < parseInt(lista.length); i++) {
                let option = $("<option></option>");
                option.val(lista[i].id);
                option.text(lista[i].texto);
                selectID.append(option);
            }
        }
    }, "json").always(function () {
        selectID.prop('disabled', false);
        sga.plugins.select2('.selectClass');
        sga.wait.remove('#contedAlmDestinity');
    });
}

function load_Servicios_All() {
    "use strict";
    let selectID = $('#servicio_itm');
    selectID.empty();
    selectID.prop('disabled', true);
    $.get('../controller/ServicioController.php?action=loadSelect_Servicio_JSON', function (lista) {
        selectID.empty();
        selectID.append('<option></option>');
        if(lista!== null && parseInt(lista.length)>0){
            for(let i=0; i<parseInt(lista.length); i++){
                let optgroup;
                if(parseInt(lista[i].datos.length)>0){
                    optgroup = $('<optgroup></optgroup>');
                    optgroup.attr('label',lista[i].label);
                    for(let j=0; j<parseInt(lista[i].datos.length); j++){
                        let option = $("<option></option>");
                        option.val(lista[i].datos[j].id);
                        option.text(lista[i].datos[j].texto);
                        optgroup.append(option);
                    }
                }
                selectID.append(optgroup);
            }
        }
    }, "json").always(function () {
        selectID.prop('disabled', false);
        sga.plugins.select2_search('.selectedClassSearch');
        sga.wait.remove('#contedAlmDestinity');
    });
}

$(document).on('change', '#servicio_itm', function() {
    "use strict";
    let IdServicio = $(this).val();
    let IdAlmacen = $('#idalm_i').val();
    load_Almacenes_xServicio(IdServicio,IdAlmacen);
});

$(document).on('click','#bajaItem_Btn',function(){
    "use strict";
    let thisIdInventario = $(this).attr('data-id');
    let $divTabla = $('#divTabla');
    let $divResponse = $('#divResponse');
    let idustk = $('#idustk');
    $divTabla.show();
    $divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/InventarioController.php?action=loadCampos_bajaItem', {'idinv':thisIdInventario, 'idus':idustk.val()}, function (response) {
        $divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        $divTabla.hide();
        $divResponse.show();
        sga.plugins.inputFile('#filedata_itm','pdf');
        sga.funcion.pageTop();
    });
});

$(document).on('submit','#formBajaItem',function(e){
    "use strict";
    e.preventDefault();
    let $divTabla = $('#divTabla');
    let $divResponse = $('#divResponse');
    $divTabla.hide();
    $divResponse.show();
    let formdata = new FormData($(this)[0]);
    $.ajax({
        url: '../controller/InventarioController.php?action=register_Baja_Inventario_JSON',
        type: "POST",
        data: formdata,
        dataType: "json",
        cache: false,
        contentType: false, //No especificamos ningún tipo de dato
        enctype: 'multipart/form-data',
        processData: false, //Evitamos que JQuery procese los datos, daría error
        beforeSend: function () {
            sga.blockUI.loading_body();
        },
        success: function (response) {
            if (parseInt(response.status) === 0) {
                sga.error.show('danger', 'mensajes_actions_bja', response.message);
                window.setTimeout(function () { $('#mensajes_actions_bja').html(""); }, 6000);
            } else if (parseInt(response.status) === 1) {
                swal.fire({
                    text: response.message,
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
        }
    }).fail(function () {
        sga.error.show('danger', 'mensajes_actions_bja', "Error al intentar realizar la baja del ITEM.");
        window.setTimeout(function () { $('#mensajes_actions_bja').html(""); }, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('change', '#clasifica_itm', function() {
    "use strict";
    let IdClasifica = $(this).val();
    let contendActivo = $('#camposActivo');
    contendActivo.empty();
    if(parseInt(IdClasifica) === 1){
        $.get('../controller/InventarioController.php?action=loadCampos_Depreciacion_Activo', function (datos) {
            contendActivo.append(datos);
        }).always(function () {
            sga.plugins.flatpickr_all('.inputFecha');
            sga.plugins.formatter_date('#fInicialDepre_itm', '/');
        });
    }
});

$(document).on('click','#btnChangeCodMate',function(){
    "use strict";
    $(this).prop('disabled',true);
    let thisIdAlmacen = $(this).attr('data-idalm');
    let thisIdInventario = $(this).attr('data-idinv');
    let thisCodeMaterial = $(this).attr('data-codmat');
    let divChange = $('#divchangeCodigo');
    divChange.empty();
    sga.wait.append('#divchangeCodigo');
    $.get('../controller/InventarioController.php?action=loadCampos_newCodMaterial', {'idinv':thisIdInventario, 'idalm':thisIdAlmacen,'codmat':thisCodeMaterial}, function (response) {
        divChange.append(response);
    }).always(function () {
        sga.wait.remove('#divchangeCodigo');
    });
});

$(document).on('click','#btnValidate_Cancel',function(){
    "use strict";
    $('#btnChangeCodMate').prop('disabled',false);
    let divChange = $('#divchangeCodigo');
    divChange.empty();
});

$(document).on('submit','#formValidate_Codigo', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    let divchangeCodigo = $('#divchangeCodigo');
    sga.blockUI.loading_body();
    $.post('../controller/InventarioController.php?action=valida_codmaterialNew_Item_JSON', data, function (response) {
        divchangeCodigo.empty();
        divchangeCodigo.append(response);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btnActualizar_Cod',function(){
    "use strict";
    let cod = $(this).attr('data-cod');
    $('#btnChangeCodMate').prop('disabled',false);
    let divChange = $('#divchangeCodigo');
    let codInput = $('#codigo_itm');
    let codText = $('#cod_temp');
    codInput.val(cod);
    codText.val(cod);
    divChange.empty();
});

$(document).on('click','#btnSearchPersonal',function(){
    "use strict";
    let ndoc = $('#adoc_itm');
    let datper = $('#aper_itm');
    sga.wait.append('#progressID');
    if(ndoc.val().length > 0 ){
        $.get('../controller/ColaboradorController.php?action=searching_Colaborador_Autocomplete_JSON', {'ndoc':ndoc.val()}, function (response) {
            if(parseInt(response.status) === 1){
                datper.val(response.idntify);
            }
            else{
                datper.empty();
                sga.plugins.toastr('error', 'toast-top-right', 'Personal no se encuentra registrado.', 'Error');
            }
        },"json").always(function () {
            sga.wait.remove('#progressID');
        });
    }
});

$(document).on('keyup','#adoc_itm', function() {
    "use strict";
    let ndoc = $(this).val();
    let datper = $('#aper_itm');
    if(parseInt(ndoc.length) <= 1){ datper.val(""); }
});

$(document).on('click','#btnValidate_codMat',function(){
    "use strict";
    let thisCodMaterial = $('#codigo_itm').val();
    let thisIdAlmacen = $('#idalm_i').val();
    let btnRegister = $('#btnRegisterView');
    if(thisCodMaterial.length > 0) {
        sga.blockUI.loading_body();
        $.get('../controller/InventarioController.php?action=busca_codMaterialNew_Item_JSON', {
            'idalm': thisIdAlmacen,
            'codmat': thisCodMaterial
        }, function (response) {
            if(parseInt(response.status) === 0){
                btnRegister.hide();
                sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
            }
            else if(parseInt(response.status) === 1){
                btnRegister.show();
                sga.plugins.toastr('success', 'toast-top-right', response.message, 'SUCCESS');
            }
        },"json").always(function () {
            sga.blockUI.unblock_body();
        });
    }
});

$(document).on('keyup','#codigo_itm', function() {
    "use strict";
    let nCodigo = $(this).val();
    console.log('nCodigo');
    let btnRegister = $('#btnRegisterView');
    btnRegister.hide();
    console.log('change');
});