
$(function() {
    "use strict";
    sga.plugins.select2('.selectClass');
    sga.plugins.select2_search('.selectSearch');
});

$(document).on('change','#IdServicioUsuario',function(){
    "use strict";
    let id = $(this).val();
    let selectID = $('#IdAlmacen');
    selectID.empty();
    selectID.prop('disabled',true);
    if(id !== "") {
        sga.blockUI.loading_body();
        $.get('../controller/AlmacenController.php?action=lista_Almacenes_Activos_JSON',{'idserv':id}, function (lista) {
            selectID.append("<option></option>");
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
            sga.plugins.select2('#IdAlmacen');
            selectID.prop('disabled',false);
        });
    }
});

$(document).on('change', '#tipoReporte', function() {
    "use strict";
    let tipoRpte = $(this);
    let periodo = $('#periodo');
    let mes = $('#mes');
    let corte = $('#corte');
    periodo.val(null).trigger("change");
    mes.empty()
    mes.append('<option></option>');
    corte.empty()
    corte.append('<option></option>');
    periodo.prop('disabled',true);
    mes.prop('disabled',true);
    corte.prop('disabled',true);
    if(parseInt(tipoRpte.val()) === 2){
        periodo.prop('disabled',false);
    }
});

$(document).on('change', '#periodo', function() {
    "use strict";
    let selectMes= $('#mes');
    let arrayMeses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    selectMes.empty();
    let date = new Date();
    let mesActual = date.getMonth()+1;
    selectMes.append('<option></option>');
    for(let m = mesActual; m >0; m--){
        selectMes.append('<option value="'+(m)+'">'+arrayMeses[m-1]+'</option>');
    }
    selectMes.prop('disabled',false);
});

$(document).on('change', '#mes', function() {
    "use strict";
    let mes = $(this).val();
    let selectID = $('#corte');
    selectID.empty();
    let datos = {'idalm': $('#IdAlmacen').val(),'periodo': $('#periodo').val(),'mes': mes}
    sga.blockUI.loading_body()
    $.get('../controller/InventarioController.php?action=lst_cortes_bk_Inventario_xAlmacen_JSON', datos,
        function (lista) {
            selectID.empty();
            selectID.append('<option></option>');
            if(lista!== null && parseInt(lista.length)>0){
                for(let i=0; i<parseInt(lista.length); i++){
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    selectID.append($option);
                }
            }
    },"json").always(function () {
        sga.blockUI.unblock_body();
        selectID.prop('disabled',false);
    });
});

$(document).on('click', '#btnAction_Search', function() {
    "use strict";
    let IdAlmacen = $('#IdAlmacen');
    let tipoRpte = $('#tipoReporte');
    let periodo = $('#periodo');
    let mes = $('#mes');
    let corte = $('#corte');
    let result = false;
    if(parseInt(tipoRpte.val()) === 1 ){
        if(IdAlmacen.val() !== "" && tipoRpte.val() !== "") {
            result = true;
        }
    }
    else if(parseInt(tipoRpte.val()) === 2 ){
        if(IdAlmacen.val() !== "" && tipoRpte.val() !== "" && periodo.val() !== "" && mes.val() !== "" && corte.val() !== "") {
            result = true;
        }
    }

    if(result === true){
        let datos = {
            'almacen': IdAlmacen.val(),
            'tipo': tipoRpte.val(),
            'corte': corte.val()
        };
        loadTbl_Inventario_Almacen(datos);
    }
    else{
        sga.error.show('danger', 'mensajes_actions_rpte', 'Debe completar los campos descritos.');
        window.setTimeout(function () { $('#mensajes_actions_rpte').html("");}, 6000);
    }
});

function loadTbl_Inventario_Almacen(datos){
    "use strict";
    let tabla = $('#Tbl_Reporte tbody');
    let infoNumber = $('#divinfo_number');
    let infobutton = $('#divinfo_btn');
    let optionReport = $('#acc_report').val();
    tabla.empty();
    infoNumber.empty();
    infobutton.empty();
    sga.blockUI.loading_body();
    $.get('../controller/InventarioController.php?action=lst_Inventario_Reporte_xAlmacen_JSON', datos,
        function (lista) {
        if(lista != null && lista.length>0){
            let page = '../../app/reporte-inventario-Export.php?almacen='+datos.almacen+'&tipo='+datos.tipo+'&corte='+datos.corte;
            infoNumber.append('Lista de registros del almacén: <code class="font-weight-bold text-danger-700">['+lista.length+']</code>');
            if(parseInt(optionReport) === 1) {
                infobutton.append('<a class="btn btn-success btn-hover-transform cursor-pointer" href="'+page+'"><i class="fa fa-file-excel-o mr-10 fz-18"></i>Exportar</a>');
            }
            for (let i=0; i<lista.length; i++){
                let row='<tr>' +
                    '<td class="text-center">' + lista[i][0] + '</td>'+
                    '<td class="text-center">' + lista[i][1] + '</td>'+
                    '<td class="text-center">' + lista[i][2] + '</td>'+
                    '<td class="text-left">' + lista[i][3] + '</td>'+
                    '<td class="text-center">' + lista[i][4] + '</td>'+
                    '<td class="text-center">' + lista[i][5] + '</td>'+
                    '<td class="text-center">' + lista[i][6] + '</td>'+
                    '</tr>';
                tabla.append(row);
            }
        }
        else{
            tabla.append('<tr><td colspan="7" class="text-center">No existen registros</td></tr>');
            infoNumber.append('Lista de registros del almacén');
            infobutton.empty();
        }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensajes_actions_rpte', 'Error al listas los registros el registro.');
        window.setTimeout(function () { $('#mensajes_actions_rpte').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}