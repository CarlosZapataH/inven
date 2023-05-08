
$(function() {
    "use strict";
    let countServicio = $('#count_servicio').val();
    sga.plugins.select2('#IdAlmacen');

    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();
        if(parseInt(countAlmacen)>1){
            sga.plugins.select2('.selectClass');
        }
    }
    else {
        sga.plugins.select2('.selectClass');
    }
});



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

$(document).on('click', '#btnDepreciacion', function(e) {
    "use strict";
    e.preventDefault();
    let IdAlmacen = $('#IdAlmacen');
    if(IdAlmacen.val() !== "") {
        sga.blockUI.loading_body();
        window.location.href='../../app/reporte-depreciacion-Export.php?idAlmacen='+IdAlmacen.val();
        window.setTimeout(function () { sga.blockUI.unblock_body(); }, 3000);
    }
    else {
        sga.error.show('danger', 'mensaje_action_dp', 'Debe seleccionar un Almacén para proceder a generar el Reporte');
        window.setTimeout(function () { $('#mensaje_action_dp').html("");}, 6000);
    }
});