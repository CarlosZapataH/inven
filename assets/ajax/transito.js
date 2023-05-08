var swal;

$(function() {
    "use strict";
    sga.plugins.flatpickr_rangeInput('#fecha_ajt');
    sga.plugins.formatter_daterange('#fecha_ajt');
    sga.plugins.select2('#IdTipoTransito');

    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();
        if(parseInt(countAlmacen)===1){
            let IdAlmacen = $('#IdAlmacen').val();
            load_Transito_xAlmacene(IdAlmacen);
        }
    }

    sga.plugins.select2_search('.selectClass');
});

$(document).on('change', '#IdTipoTransito', function() {
    "use strict";
    let IdTipo = $(this).val();
    let IdServicio = $('#IdServicioUsuario_a');
    let IdAlmacen = $('#IdAlmacen_a');
    IdAlmacen.empty();
    IdServicio.prop('disabled', true);
    IdAlmacen.prop('disabled', true);
    if(IdTipo === "SAL") {
        IdServicio.prop('disabled', false);
        IdAlmacen.prop('disabled', true);
    }
});

$(document).on('change', '#IdServicioUsuario', function() {
    "use strict";
    let IdSerUsuario = $(this).val();
    load_Almacenes_xServicioUsuario(IdSerUsuario,'IdAlmacen');
});

$(document).on('change', '#IdServicioUsuario_a', function() {
    "use strict";
    let IdServicio = $(this).val();
    load_Almacenes_xServicio(IdServicio,'IdAlmacen_a');
});

function load_Almacenes_xServicioUsuario(id,selector) {
    "use strict";
    let $selectID = $('#'+selector);
    $selectID.empty();
    $selectID.prop('disabled', true);
    $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_ServicioUsuario_JSON', {'idsu': id}, function (lista) {
        $selectID.empty();
        $selectID.append('<option></option>');
        if (lista !== null && parseInt(lista.length) > 0) {
            for (let i = 0; i < parseInt(lista.length); i++) {
                let $option = $("<option></option>");
                $option.val(lista[i].id);
                $option.text(lista[i].texto);
                $option.attr('data-vista',lista[i].vista);
                $selectID.append($option);
            }
        }
    }, "json").always(function () {
        $selectID.prop('disabled', false);
        sga.plugins.select2('.selectClass');
    });
}

function load_Almacenes_xServicio(id,selector) {
    "use strict";
    let $selectID = $('#'+selector);
    $selectID.empty();
    $selectID.prop('disabled', true);
    $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_ServicioAll_JSON', {'idserv': id}, function (lista) {
        $selectID.empty();
        $selectID.append('<option></option>');
        if (lista !== null && parseInt(lista.length) > 0) {
            for (let i = 0; i < parseInt(lista.length); i++) {
                let $option = $("<option></option>");
                $option.val(lista[i].id);
                $option.text(lista[i].texto);
                $option.attr('data-vista',lista[i].vista);
                $selectID.append($option);
            }
        }
    }, "json").always(function () {
        $selectID.prop('disabled', false);
        sga.plugins.select2('.selectClass');
    });
}

$(document).on('submit', '#transitoSearching', function(e) {
    "use strict";
    e.preventDefault();
    let TipoTransito = $('#IdTipoTransito').val();
    let IdAlmacenDE = $('#IdAlmacen').val();
    let IdAlmacenA = 0;
    if(TipoTransito === "SAL"){ IdAlmacenA = $('#IdAlmacen_a').val(); }
    load_Transito_xAlmacene(TipoTransito,IdAlmacenDE,IdAlmacenA);
});

function load_Transito_xAlmacene(Tipo,IdAlmacenDE,IdAlmacenA) {
    "use strict";
    let containerID = $('#divResponse');
    let datos = {
        'tipoTransito':Tipo,
        'IdAlmacenDE':IdAlmacenDE,
        'IdAlmacenA':IdAlmacenA
    }
    containerID.empty();
    sga.wait.append('#divResponse');
    $.get('../controller/InventarioController.php?action=load_Transito_Tranferencia', datos, function (response) {
        containerID.append(response);
    }).always(function () {
        sga.wait.remove('#divResponse');
    });
}

$(document).on('click','#btnDetailSalida',function(){
    "use strict";
    let IdTransito = $(this).attr('data-id');
    let modalLoading = $('#ModalProgressBar_Load');
    modalLoading.empty();
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.empty();
    $.get('../controller/InventarioController.php?action=loadCampos_Detalle_Salida_TranferirTransito', {'IdTransito':IdTransito}, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('click', '#btnDetailOrder', function() {
    "use strict";
    let IdTransito = $(this).attr('data-id');
    let open = $(this).attr('data-open');
    if(parseInt(open) === 0){
        $(this).attr('data-open',1);
        let containerID = $('#collapseDetailOrder'+IdTransito);
        containerID.empty();
        sga.wait.append('#collapseDetailOrder'+IdTransito);
        $.get('../controller/InventarioController.php?action=loadCampos_Detalle_TranferirTransito', {'IdTransito':IdTransito,}, function (response) {
            containerID.append(response);
        }).always(function () {
            sga.wait.remove('#collapseDetailOrder'+IdTransito);
        });
    }
});

$(document).on('submit','#formValidaIngreso', function(e) {
    "use strict";
    e.preventDefault();
    let data =  $(this).serialize();
    let thisIdTransito = $('#idtranval').val();
    let cardItem = $('#cardDetailOrder'+thisIdTransito);
    let detailItem = $('#collapseDetailOrder'+thisIdTransito);
    let modalDefault = $('#ModalAction_ContainerForm');
    sga.blockUI.loading_body();

    $.post('../controller/InventarioController.php?action=insertar_Transito_Inventario_JSON', data, function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            swal({
                text: "Transito ingresado satisfactoriamente.",
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                modalDefault.modal("hide");
                modalDefault.empty();
                cardItem.remove();
                detailItem.remove();
            });
        }
        else if (parseInt(response.status) === 0) {
            sga.error.show('danger', 'mensaje_error_val', 'Error al realizar la validación del ingreso de la Transacción seleccionada.');
            window.setTimeout(function () { $('#mensaje_error_val').html(""); }, 6000);
        }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensaje_error_val', "Se produjo un error al intentar realizar el ingreso la Transacción seleccionada, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensaje_error_val').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btnReciverOrder',function(){
    "use strict";
    const itemStatus = document.querySelectorAll(
        'input[name="estatusItm[]"]'
    );

    let itemfor = 0;
    itemStatus.forEach((elemento) => {
        if(parseInt(elemento.value) === 1) { itemfor++; }
        //console.log(elemento.parentNode.parentNode.id,elemento.value);
    });

    if(itemStatus.length === itemfor){
        let thisIdTransito = $(this).attr('data-id');
        let modalLoading = $('#ModalProgressBar_Load');
        sga.wait.modal('ModalProgressBar_Load');
        modalLoading.modal("show");
        let modalDefault = $('#ModalAction_ContainerForm');
        $.get('../controller/InventarioController.php?action=load_campos_validarIngreso', {'IdTransito':thisIdTransito}, function (response) {
            modalDefault.html(response);
        }).always(function () {
            modalLoading.modal("hide");
            modalLoading.empty();
            modalLoading.hide();
            modalDefault.modal("show");
        });
    }
    else{
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe verificar todos los item en transito.', 'Advertencia');
    }
});

$(document).on('change','.actionCantidad',function() {
    "use strict";
    let position = $(this).attr('data-pos');
    ModificarCantidad_Transito(position);
});

function ModificarCantidad_Transito(pos){
    "use strict";
    let cantTransito = $('#valTransito'+pos);
    let cantRecepcion = $('#valRecepcion'+pos);
    console.log(cantTransito.val());
    console.log(cantRecepcion.val());
    if(parseFloat(cantRecepcion.val()) === 0 || parseFloat(cantRecepcion.val()) > parseFloat(cantTransito.val())){
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar una cantidad mayor a cero y menor a la cantidad a Transito', 'Advertencia');
        cantRecepcion.val(1);
        cantRecepcion.focus();
    }
}

$(document).on('change','.selectEstados',function(){
    "use strict";
    let thisValue = $(this).val();
    let thisPos = $(this).attr('data-id');
    if(thisValue !== ""){
        let cantTransito = $('#valTransito'+thisPos);
        let cantRecepcion = $('#valRecepcion'+thisPos);
        if(thisValue === "R" && parseFloat(cantRecepcion.val()) < parseFloat(cantTransito.val())){
            sga.plugins.toastr('warning', 'toast-top-right', 'Para seleccionar la entrada Rechazada el valor de transito debe ser igual a la de recepción.', 'Advertencia');
        }
    }
});

$(document).on('click','#btnRecepcion',function(){
    "use strict";
    let thisId = $(this).attr('data-id');
    swal.fire({
        html: 'Se va proceder a validar el Transito del registro seleccionado.<br>Una vez realizada esta acción no podrá ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            let datos = {
                'id':thisId,
                'valtransito' : $('#valTransito'+thisId).val(),
                'valrecepcion' : $('#valRecepcion'+thisId).val(),
                'estado': $('#idEstados'+thisId).val()
            }
            $.post('../controller/InventarioController.php?action=actualizar_Estado_Transito_JSON', datos, function (response) {
                if (parseInt(response.status) === 1) {
                    $('.action'+thisId).remove();
                    $('.enabled'+thisId).prop('disabled',true);
                    $('#valStatus'+thisId).val(1);
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('warning', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('warning', 'toast-top-right', 'Error al procesar el estado del item.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });

        }
    });
});