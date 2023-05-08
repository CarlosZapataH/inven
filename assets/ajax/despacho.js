var objinit = new init(), elementosMat = [], Tbl_Historial;

$.extend($.fn.dataTableExt.oStdClasses, {
    "sFilterInput": "form-control",
    "sLengthSelect": "form-control"
});

$.extend( $.fn.dataTable.defaults, {
    autoWidth: false,
    responsive: true,
    dom: '<"datatable-header"ifl><"datatable"t>',
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
    sga.plugins.select2_search('.selectSearch');
    sga.plugins.select2('.selectClass');

    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();
        if(parseInt(countAlmacen)===1){
            let IdAlmacen = $(this).val();
            load_campoSearch(IdAlmacen);
        }
    }

    init;


});

function init() {
    "use strict";

    function consultarMat() {
        return JSON.stringify(elementosMat);
    }

    this.consultarMat = function(){
        return JSON.stringify(elementosMat);
    };

    this.eliminarMat = function(pos){
        pos > -1 && elementosMat.splice(parseInt(pos),1);
    };
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
    load_campoSearch(IdAlmacen);
});

function load_campoSearch(idAlm){
    "use strict";
    let elementSelect = $('#IdAlmacen option:selected')
    let IdTextAlmacen = $('#txtAlmacen');
    IdTextAlmacen.empty();
    IdTextAlmacen.text("Control de EPPS : "+elementSelect.text());
    let divHead = $('#divHead');
    let divSearching = $('#divSearching');
    let divResponse = $('#divResponse');
    divHead.show();
    divSearching.empty();
    divSearching.show();
    divResponse.empty();
    let campos =
        '<h5 class="card-title text-center text-primary pt-20">Identificación del Personal</h5>'+
        '<p class="text-center">Digite el número de : <code class="highlighter-rouge">DNI</code> o <code class="highlighter-rouge">.Carnet de extranjería</code>.</p>'+
        '<form id="frm_searchPersonal">' +
        '   <div class="row">' +
        '       <div class="col-xl-6 offset-xl-2 col-lg-6 offset-lg-2 col-md-6 offset-md-2 col-sm-9 mb-10">' +
        '           <input type="hidden" name="IdAlmacen" value="'+idAlm+'">'+
        '           <input class="form-control form-control-lg text-center text-lg-search mr-2" name="ndoc_col" id="ndoc_col" maxlength="12"' +
        '                       type="text" placeholder="# documento" required autocomplete="off" onkeypress="return sga.funcion.valideKey(event);">' +
        '       </div>' +
        '       <div class="col-xl-2 col-lg-2 col-md-2 col-sm-3">' +
        '           <button type="submit" class="btn btn-info btn-lg mr-2" style="height: 71px">Buscar</button>' +
        '       </div>'+
        '   </div>'+
        '</form>' +
        '<div class="mt-20" id="msg_searching"></div>';
    divSearching.append(campos);
    window.setTimeout(function () {let idNdoc = $('#ndoc_col');idNdoc.focus();}, 500);
}

$(document).on('submit','#frm_searchPersonal', function(e){
    "use strict";
    e.preventDefault();
    let idNdoc = $('#ndoc_col');
    let data = $(this).serialize();
    if(idNdoc.val().length >=8 && idNdoc.val().length <=12) {
        sga.blockUI.loading_body();
        $.post('../controller/ColaboradorController.php?action=searching_Colaborador_JSON', data,
            function (vjson) {
                if (parseInt(vjson.status) === 1) {
                    load_CamposDespacho(vjson.idntify);
                } else if (parseInt(vjson.status) === 0) {
                    idNdoc.val("");
                    idNdoc.focus();
                    sga.plugins.toastr('error', 'toast-top-right', vjson.message, 'Error al realizar búsqueda');
                }
            },"json").always(function () {
            sga.blockUI.unblock_body();
        });
    }
    else{
        idNdoc.val("");
        idNdoc.focus();
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar un format valido del número de documento.<br>DNI: 8 dígitos<br>CEX: 12 dígitos', 'Advertencia');
    }
});

function load_CamposDespacho(id){
    "use strict";
    let divHead = $('#divHead');
    let divSearching = $('#divSearching');
    let divResponse = $('#divResponse');
    let idAllmacen = $('#IdAlmacen').val();
    divResponse.empty();
    $.get('../controller/MaterialController.php?action=load_camposDespacho_Material', {'idntify':id,'idalm':idAllmacen},
        function (response) {
            divResponse.append(response);
    }).always(function () {
        divHead.hide();
        divSearching.empty();
        sga.funcion.pageButtom();
    });
}

$(document).on('click','#btnNewSearch', function(){
    "use strict";
    let IdAlmacen = $('#IdAlmacen').val();
    load_campoSearch(IdAlmacen);
});

$(document).on('submit','#frm_searchMaterial', function(e){
    "use strict";
    e.preventDefault();
    let codMaterial = $('#codmaterial');
    if(codMaterial.val().length >=6 && codMaterial.val().length <=8) {
        sga.blockUI.loading_body();
        let data = { 'idalm':$('#IdAlmacen').val(),'codmat':codMaterial.val()};
        $.post('../controller/MaterialController.php?action=searching_Material_xCodigo_JSON', data,function (response) {
            console.log(response);
                if (parseInt(response.status) === 1) {
                    addMaterial_cart(response.detail.idalm, response.detail.idmat, response.detail.clasificacion, response.detail.codigo, response.detail.des, response.detail.um, response.detail.renova, response.detail.frecrenova,1);

                }
                else if (parseInt(response.status) === 0) {
                    codMaterial.val("");
                    codMaterial.focus();
                    sga.plugins.toastr('warning', 'toast-top-right', response.message, 'Material');
                }
        },"json").always(function () {
            sga.blockUI.unblock_body();
        });
    }
    else{
        codMaterial.val("");
        codMaterial.focus();
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar un formato valido del código del material.<br>Minimo 6 digitos y máximo 8 dígitos', 'Advertencia');
    }
});

function addMaterial_cart(idalm, idmat, clasificacion, codigo, descripcion, umedida, renovacion, frecuencia,cantidad){
    "use strict";
    let detalleMaterial = [parseInt(idalm),parseInt(idmat), clasificacion, codigo, descripcion, umedida, parseInt(renovacion), parseInt(frecuencia), parseInt(cantidad)];
    let coindice = 0;
    let codMaterial = $('#codmaterial');
    const audio = document.createElement("audio");
    audio.preload = "auto";

    if(elementosMat.length > 0) {
        let data = JSON.parse(objinit.consultarMat());
        if(data.length > 0) {
            for (let pos in data) {
                if( $.trim(data[pos][3]) === $.trim(codigo)){
                    coindice++;
                }
            }
        }
    }

    if(coindice === 0){
        elementosMat.push(detalleMaterial);
        console.log(detalleMaterial);
        ConsultarDetalle_Material();
        sga.funcion.pageButtom();
        audio.src = "../assets/mp3/alertSound-Success.mp3";
    }
    else{
        sga.plugins.toastr('warning', 'toast-top-right', 'El ítem seleccionado ya se encuentra agregado.', 'Advertencia');
        audio.src = "../assets/mp3/alertSound-Error.mp3";
    }
    audio.play();
    document.body.appendChild(audio);

    codMaterial.val("");
    codMaterial.focus();
}

function ConsultarDetalle_Material() {
    "use strict";
    let TblDetailMaterial = $("#Tbl_Despacho > tbody");
    TblDetailMaterial.empty();
    let data = JSON.parse(objinit.consultarMat());
    if(parseInt(data.length)>0) {
        let fila = 1;
        for (let pos in data) {
            let cantidad = '<input type="text" class="form-control form-control-lg text-center actionCantidad" ' +
                       '       placeholder="****" style="font-size: 20px;padding: 0.4rem 1rem;"' +
                       '       id="vCantidad' + pos + '" value="' + data[pos][8] + '" data-pos="' + pos + '">';

            let row =
                '<tr class="transition duration-300 ease-in-out hover:shadow-lg">' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' + fila + '</td>' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' + data[pos][3] + '</td>' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' + data[pos][4] + '</td>' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' + data[pos][5] + '</td>' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' + cantidad + '</td>' +
                '   <td class="text-center h-20 bg-white font-light px-4 py-6 align-middle  first:rounded-bl-md first:rounded-tl-md last:rounded-br-md last:rounded-tr-md">' +
                '       <button type="button" class="btn btn-danger btn-hover-transform" data-pos="' + pos + '" title="Eliminar" id="btnDelete_Material">' +
                '           <i class="fa fa-minus"></i>' +
                '       </button>' +
                '   </td>' +
                '</tr>';
            TblDetailMaterial.append(row);
            fila++;
        }
    }
    else{
        TblDetailMaterial.append('<tr><td colspan="6" class="text-center">No se encontraron materiales agregados.</td></tr>');
    }
}

$(document).on('click','#btnDelete_Material',function() {
    "use strict";
    let position = parseInt($(this).attr('data-pos'));
    eliminar_DetalleMaterial(position);
});

function eliminar_DetalleMaterial(ele){
    "use strict";
    objinit.eliminarMat(ele);
    ConsultarDetalle_Material();
}

$(document).on('change','.actionCantidad',function() {
    "use strict";
    let position = $(this).attr('data-pos');
    ModificarCantidad_Material(position);
});

function ModificarCantidad_Material(pos){
    "use strict";
    let cant = $('#vCantidad'+pos);
    if(parseFloat(cant.val()) > 0){
        elementosMat[pos][8] = cant.val();
        ConsultarDetalle_Material();
    }
    else {
        swal.fire({
            html: "Debe ingresar una cantidad mayor a cero.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'Aceptar'
        }).then(function () {
            cant.val(1);
            cant.focus();
        });
    }
}

var tableHistorial =
    '<div class="table-responsive">\n' +
    '   <table id="Tbl_Historial" class="table datatable-responsive-row-control">\n' +
    '       <thead>\n' +
    '           <tr>\n' +
    '               <th>Servicio</th>\n' +
    '               <th>Almacen</th>\n' +
    '               <th>Código</th>\n' +
    '               <th>Descripción material</th>\n' +
    '               <th>U.M.</th>\n' +
    '               <th>Cantidad</th>\n' +
    '               <th>Fecha Entrega</th>\n' +
    '           </tr>\n' +
    '       </thead>\n' +
    '       <tbody></tbody>\n' +
    '   </table>\n' +
    '</div>';

$(document).on('click','#btnHistory',function() {
    "use strict";
    let divDespacho = $('#divDespacho');
    let divHistorial = $('#divHistorial');
    let btnHistorial = $('#btnHistory');
    let btnCancel = $('#btnCancelHistory');
    let textPersona = $('#textPersona');
    let idIdentifiy = $(this).attr('data-id');
    sga.blockUI.loading_body();
    Tbl_Historial = $('#Tbl_Historial').DataTable({
        dom: '<"datatable-header"ifB><"datatable-Columnas"><"datatable"t>',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-outline-secondary btn-sm mr-15 btn-sm-export',
                title: 'HISTORIAL DESPACHO : '+textPersona.text(),
                text: '<span class="fa fa-file-excel-o position-left"></span> Exportar',
                filename: function(){
                    let f = new Date();
                    return 'Historial-Despacho-' + f.getDate() + (f.getMonth() +1) + f.getFullYear();
                },
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    }
                }
            }
        ],
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
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
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [0]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [1]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [2]
            },
            {
                className: 'text-left align-middle',
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
                visible: true,
                searchable: true,
                targets: [5]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [6]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                visible: true,
                searchable: true,
                targets: [7]
            }
        ],
        "paging": false,
        ajax:{
            url: '../controller/MaterialController.php?action=historial_Detalle_xColaborador_JSON',
            type : "get",
            dataType : "json",
            data : {'id': idIdentifiy},
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            divDespacho.hide();
            divHistorial.show();
            btnHistorial.hide();
            btnCancel.show();
            $('#Tbl_Historial_info').addClass('float-left ml-15');
            $('#Tbl_Historial_filter').addClass('float-right mr-15');
        }
    });
});

$(document).on('click','#btnCancelHistory',function() {
    "use strict";
    let divDespacho = $('#divDespacho');
    let divHistorial = $('#divHistorial');
    let btnHistorial = $('#btnHistory');
    let btnCancel = $('#btnCancelHistory');
    divDespacho.show();
    divHistorial.hide();
    btnHistorial.show();
    btnCancel.hide();
    Tbl_Historial.destroy();
});

$(document).on('change','#selTipo',function() {
    "use strict";
    elementosMat.length = 0;
    let codmaterial = $('#codmaterial');
    let TblDetailMaterial = $("#Tbl_Despacho > tbody");
    codmaterial.val("");
    TblDetailMaterial.empty();
    TblDetailMaterial.append('<tr><td colspan="6" class="text-center">No se encontraron materiales agregados.</td></tr>');
});

var datos = [];

$(document).on('click','#btnSaveDespacho',function(e) {
    "use strict";
    e.preventDefault();
    if(elementosMat.length > 0) {
        datos = {
            'idUsusrio_tk': $('#idustk').val(),
            'idColaborador_tk': $('#idcoltk_des').val(),
            'namecol': $('#namecol_des').val(),
            'puestocol': $('#puestocol_des').val(),
            'areaopcol': $('#areaopcol_des').val(),
            'ndoccol': $('#ndoc_des').val(),
            'idAlmacen': $('#idalmacen_des').val(),
            'desAlmacen': $('#desAlmacen_des').val(),
            'idServicio': $('#idServicio_des').val(),
            'fechahora': $('#fechahora_des').val(),
            'codtransaccion': $('#transaccion_des').val(),
            'codigo': $('#codigo_des').val(),
            'seltipo': $('#selTipo').val(),
            'detalle' : JSON.parse(objinit.consultarMat())
        };
        Swal.fire({
            html: 'Se va realizar el registro del despacho.<br>Desea continuar...!.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.value) {
                console.log(datos);
                //secureValidation_open(datos.idColaborador_tk,datos.ndoccol,datos.idServicio,datos.codtransaccion);
                let hoy = new Date();
                let fecha = hoy.getFullYear() + '-' + ( hoy.getMonth() + 1 ) + '-' + hoy.getDate();
                let hora = hoy.getHours() + ':' + hoy.getMinutes() + ':' + hoy.getSeconds();
                let fechaYHora = fecha + ' ' + hora;
                registrarDespacho(datos,'AUTOMATICA',"AUTO",1,fechaYHora);
            }
        });
    }
    else{
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe agregar al menos 01 material.', 'Advertencia');
    }
});

function secureValidation_open(idcol,ndoc,idserv,codtransac) {
    "use strict";
    console.log(idcol+""+ndoc+""+idserv+""+codtransac);
    let modalLoading = $('#ModalProgressBar_Load');
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    $.post('../controller/IdentificadorController.php?action=load_view_securevalidation', {
        'idcol':idcol,'ndoc':ndoc,'idserv':idserv,'codtransac':codtransac
    }, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
}

function registrarDespacho(datos,tipoval,codeval,status,fechaval) {
    "use strict";
    datos.tipovalida = tipoval;
    datos.codigovalida = codeval;
    datos.timevalida = fechaval;
    datos.statusvalida = status;
    console.log(datos);
    $.post('../controller/MaterialController.php?action=registrar_Despacho_JSON', datos, function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            generarPdf_Despacho(response.idDespacho,1);
            swal.fire({
                html: response.message,
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                location.reload();
            });
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Al realizar el despacho, contactese con el Administrador del sistema, para generar un reporte del incidente..', 'Error');
    });
}

function generarPdf_Despacho(idDespacho,sendmailOPT) {
    "use strict";
    let page = '../app/Reporte-Despacho-PDF.php?idDespacho='+idDespacho+'&optionMail='+sendmailOPT;
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
                sga.plugins.toastr('success', 'toast-top-right', "Formato generado satisfactoriamente.", 'Vale Despacho');
            }, 5000);
        }
    });
}

$(document).on('click','#btnCancel_Modal',function() {
    "use strict";
    resetModalSecure();
});

function resetModalSecure() {
    "use strict";
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal("hide");
    modalDefault.empty()
}

let digitValidate = function(ele){
    ele.value = ele.value.replace(/[^a-zA-Z0-9]/g,'');
}

let tabChange = function(val){
    let btnSubmit = $('#btnValidate');
    let ele = document.querySelectorAll('#otp > *[id]');
    if(ele[val-1].value !== ''){
        ele[val].focus();
    }else if(ele[val-1].value === ''){
        ele[val-2].focus();
    }

    btnSubmit.prop('disabled',true);
    if(val === ele.length-1){
        btnSubmit.prop('disabled',false);
    }
}

var blockTimer =
    '<div class="wrapper_cont text-center mt-10">' +
    '   <div class="item">' +
    '       <div class="number">' +
    '           <span id="hours">00</span>' +
    '       </div>' +
    '       <span class="texto">Horas</span>' +
    '   </div>' +
    '   <div class="item">' +
    '       <div class="number">' +
    '           <span id="minutes">00</span>' +
    '       </div>' +
    '       <span class="texto">Minutos</span>' +
    '   </div>' +
    '   <div class="item">' +
    '       <div class="number">' +
    '           <span id="seconds">00</span>' +
    '       </div>' +
    '       <span class="texto">Segundos</span>' +
    '   </div>' +
    '</div>';

$(document).on('click','#sendCodeIdentificator',function() {
    "use strict";
    let btnIdentify = $('#sendCodeIdentificator');
    let ndoc = $(this).attr('data-ndoc');
    let idusertk = $('#idustk').val();
    sga.wait.append('#containerTimer');
    console.log(idusertk);
    $.post('../controller/IdentificadorController.php?action=generar_IdentifyCode_JSON', {
        'numberdoc':ndoc,'idustk':idusertk
    },function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Identificador');
            btnIdentify.hide();
            loadTimer(response.identify);
        }
        else if (parseInt(response.status) === 0) {
            sga.error.show('danger', 'divError_i', response.message);
            window.setTimeout(function () { $('#divError_i').empty();}, 6000);
        }
    },"json").always(function () {
        sga.wait.remove('#containerTimer');
    });
});

function loadTimer(identify) {
    "use strict";
    let containerTimer = $('#containerTimer');
    let btnIdentify = $('#sendCodeIdentificator');
    containerTimer.append(blockTimer);
    let seconds = 300;//181;
    let days, hours, minutes;
    days = Math.floor(seconds / (3600 * 24));
    seconds -= days * 3600 * 24;
    hours = Math.floor(seconds / 3600);
    seconds -= hours * 3600;
    minutes = Math.floor(seconds / 60);
    seconds -= minutes * 60;

    const validationPrint = (timeUnit) => {
        return timeUnit < 10 ? `0${timeUnit}` : timeUnit;
    };

    $("#seconds").text(validationPrint(seconds));
    $("#minutes").text(validationPrint(minutes));
    $("#hours").text(validationPrint(hours));
    $("#days").text(validationPrint(days));

    const changeTimeWithLimit = setInterval(() => {
        seconds -= 1;
        $("#seconds").text(validationPrint(seconds));
        if (seconds === 0 && minutes > 0) {
            seconds = 60;
            minutes -= 1;
            $("#minutes").text(validationPrint(minutes));
            console.log("opt1: finalizo");
        }
        if (seconds === 0 && minutes === 0 && hours > 0) {
            seconds = 60;
            minutes = 60;
            hours -= 1;
            $("#hours").text(validationPrint(hours));

            console.log("opt2: finalizo el minuto");
        }
        if (seconds === 0 && minutes === 0 && hours === 0 && days > 0) {
            seconds = 60;
            minutes = 60;
            hours = 24;
            days -= 1;
            $("#days").text(validationPrint(days));
            console.log("opt3: finalizo");
        }
        if (seconds === 0 && minutes === 0 && hours === 0 && days === 0) {
            clearInterval(changeTimeWithLimit);
            btnIdentify.show();
            containerTimer.empty();
            anularCodeIdentify(identify);
        }
    }, 1000);
}

function anularCodeIdentify(id) {
    "use strict";
    sga.blockUI.loading_body();
    $.post('../controller/IdentificadorController.php?action=anular_IdentifyCode_JSON', {'id':id},function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('warning', 'toast-top-right', response.message, 'Anulación');
        }
        else{
            sga.error.show('danger', 'divError_i', response.message);
            window.setTimeout(function () { $('#divError_i').empty();}, 6000);
        }
    },"json").always(function () {
        sga.blockUI.unblock_body();
    });
}

$(document).on('submit','#formSaveDespachoIdentify',function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal("hide");
    sga.blockUI.loading_body()
    $.post('../controller/IdentificadorController.php?action=saving_IdentifyPersonal_JSON', data,function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Validación');
            modalDefault.empty();
            registrarDespacho(datos,response.tipovalida,response.code,response.status,response.fechavalida);
        }
        else if (parseInt(response.status) === 0) {
            modalDefault.modal("show");
            sga.error.show('danger', 'divError_i', response.message);
            window.setTimeout(function () { $('#divError_i').empty();}, 6000);
        }
    },"json").always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','.navTabValidation a', function(){
    "use strict";
    let option = $(this).attr('data-option');
    if(option === "identificador"){

    }
    else if(option === "fingerprint"){

    }
});

/******************************* SENSOR *************************************/

function activeSensorRead(showMessage) {
    if (!localStorage.getItem("srnPc")) {
        Swal.fire({
            icon: 'warning',
            title: 'Aun no se ha generado un token para este navegador..!',
            text: "No se puede activar el lector."
        });
    } else {
        $(".imgFinger").attr("id", localStorage.getItem("srnPc"));
        $(".txtFinger").attr("id", localStorage.getItem("srnPc") + "_texto");
        $(".dataUser").attr("id", localStorage.getItem("srnPc") + "_user");
        var token = $("input[name='_token']").val();
        var data = new FormData();
        data.append("_token", token);
        data.append("token_pc", localStorage.getItem("srnPc"));
        $.ajax({
            type: 'POST',
            url: base_url + "/active_sensor_read",
            data: data,
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if (!response.code) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error activando el lector..!',
                        text: "Ha ocurrido un error activando el lector."
                    });
                } else {
                    if (showMessage) {
                        Swal.fire({
                            position: 'top-end',
                            icon: "success",
                            title: "Sensor Activado",
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
                }
            }
        });
    }
}

function closeSensorRead() {
    var token = $("input[name='_token']").val();
    var data = new FormData();
    data.append("_token", token);
    data.append("token_pc", localStorage.getItem("srnPc"));
    $.ajax({
        type: 'POST',
        url: base_url + "/sensor_close",
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        success: function (response) {
            if (response.code) {
                Swal.fire({
                    position: 'top-end',
                    icon: "success",
                    title: "Sensor Desactivado",
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        }
    });
}