var Swal;
var sga = {

    page:{
        getLoadPage: function (rutaUrl) {
            let $mainContainer = $('#mainContainer_Page');
            $.ajax({
                url: rutaUrl+ ".php",
                type: 'GET',
                beforeSend: function() {
                    sga.blockUI.loadingbody();
                }
            }).done( function(page) {
                $mainContainer.html(page);
            }).fail( function(jqXHR, textStatus, errorThrown) {
                let $rpta;
                if (jqXHR.status === 0) {
                    $rpta = sga.error.page('!','Sin conexón','No se ha podido establecer conexión con Internet, verifique su conexion de red o wifi.');
                } else if (jqXHR.status === 404) {
                    $rpta = sga.error.page('404','Página no encontrada','Lo sentimos, pero la página que busca no existe, por favor verifique la opcioón seleccionada e intentelo nuevamente');
                } else if (jqXHR.status === 500) {
                    $rpta = sga.error.page('500','Internal server error','Lo sentimos, al parecer ocurrio un error interno el servidor, por favor pongase en contacto con el area de soporte pertinente o envie un correo a soporte-imc@confipetrol.pe');
                } else if (textStatus === 'parsererror') {
                    $rpta = sga.error.page('!','Requested JSON parse failed','Lo sentimos, al parecer se genero un problema al conectarse con el servidor, por favor intentelo nuevamente.');
                } else if (textStatus === 'timeout') {
                    $rpta = sga.error.page('!','Time out error','No se ha podido establecer conexión con Internet, verifique su conexion de red o wifi.');
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    $rpta = sga.error.page('!','Ajax request aborted','Lo sentimos al parecer no se completo la llamada al codigo establecido. por favor vuelva a intentarlo refrescando la pagina.');
                } else {
                    $rpta = sga.error.page('!','Uncaught Error',jqXHR.responseText);
                }
                $mainContainer.html($rpta);
            }).always( function() {
                sga.unblokUI.loadingbodyUnblok();
            });
        },
        logout:function () {
            Swal.fire({
                title: 'Cerrar sesión',
                text: "Esta seguro de salir del sistema",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Si, salir',
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
                        showConfirmButton: false,
                        timer: 1800
                    });
                }
            });
        }
    },

    blockUI:{
        loading_body: function () {
            $.blockUI({
                message: '<img src="../assets/img/gif/loading.gif" />',
                overlayCSS: {
                    backgroundColor: '#000',
                    opacity: 0.5,
                    cursor: 'wait'
                },
                css: {
                    border: 0,
                    padding: 0,
                    backgroundColor: 'transparent',
                    opacity: 0.5
                }
            });
        },
        unblock_body: function () {
            $.unblockUI();
        }
    },

    error: {
        show: function (tipo, contenedor, mensaje) {
            let msj, classtipo;
            if (tipo === "info") {
                classtipo = " alert-info ";
            }
            else if (tipo === "success") {
                classtipo = " alert-success ";
            }
            else if (tipo === "danger") {
                classtipo = " alert-danger ";
            }
            else if (tipo === "warning") {
                classtipo = " alert-warning ";
            }
            msj = '<div class="alert ' + classtipo + ' mb-10 text-center alert-msje">' + mensaje + '</div>';
            $('#' + contenedor).html(msj);
        },
        response: function (tipo,contenedor,titulo,mensaje) {
            let msj, classtipo;
            if(tipo === "info"){
                classtipo = " bg-info ";
            }
            else if(tipo === "success"){
                classtipo = " bg-success ";
            }
            else if(tipo === "danger"){
                classtipo = " bg-danger ";
            }
            else if(tipo === "warning"){
                classtipo = " bg-warning ";
            }
            msj  ='<div class="alert ' + classtipo + ' alert-styled-right">';
            msj +='  <span class="text-semibold fz-20">' + titulo + '</span><br>'+mensaje;
            msj +='</div>';
            $('#'+contenedor).html(msj);
        },
        page: function ($number, $title, $mensaje) {
            let $divT = '<div class="container-fluid">';
            $divT +='    <div class="error-wrap text-center"><div class="row"><div class="col-12">';
            $divT +='       <h1 class="text-info">' + $number + '</h1>';
            $divT +='       <h3 class="text-uppercase">' + $title + '</h3>';
            $divT +='       <p class="text-muted m-t-30 m-b-30">' + $mensaje + '</p>';
            $divT +='       <a href="sistema.php" class="btn btn-info btn-rounded waves-effect waves-light m-b-40">ir a Inicio</a>';
            $divT +='    </div></div></div>';
            $divT +='  </div>';
            return $divT;
        },
        action: function (tipo, contenedor, mensaje) {
            let icon;
            if (tipo === "primary") { icon = "fa fa-bell-o "; }
            else if (tipo === "secondary") { icon = "ti-alert "; }
            else if (tipo === "success"){ icon = "ti-check-box "; }
            else if (tipo === "danger") { icon = "ti-na "; }
            else if (tipo === "warning"){ icon = "ti-unlink "; }
            else if (tipo === "light") { icon = "ti-light-bulb "; }
            else if (tipo === "dark") { icon = "ti-targe "; }
            let msj = '<div class="alert alert-' + tipo + ' mb-10 text-center border-0"><i class="' + icon + 'pr-2"></i>' + mensaje + '</div>';
            $('#' + contenedor).html(msj);
        },

    },

    table:{
        refreshDatatable: function (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            let $table = $(selector).DataTable();
            $table.ajax.reload();
        },
        refreshDatatable_chk: function (id) {
            let $myCallback = function () {
                $('.dt-checkboxes-cell input').addClass('scale-chk-1-5 cursor-pointer');
                $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
            };
            initComplete: $myCallback;
            let $table = $(id).DataTable();
            $table.ajax.reload($myCallback);
        }
    },

    wait: {
        modal: function (idModal) {
            let htmlText = '<div class="modal-dialog modal-dialog-centered">'
                + '<div class="modal-content modal-width">'
                + '  <div class="modal-header">'
                + '    <h4 class="modal-title">Cargando</h4>'
                + '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                + '  </div>'
                + '  <div class="modal-body form-horizontal">'
                + '    <div class="progress-bar bg-info active progress-bar-striped" role="progressbar" style="width:100%;height:23px;">'
                + '      <span class="sr-only">Cargando</span>'
                + '    </div>'
                + '  </div>'
                + '  <div class="modal-footer"></div>'
                + '</div>'
                + '</div>';
            $("#" + idModal).html(htmlText);
        },
        append: function (selector) {
            let htmlText = '<div class="progress-bar bg-info active progress-bar-striped mb-10 sga-progress" role="progressbar" style="width:100%;height:20px;">'
                + '<span class="sr-only">Cargando</span>'
                + '</div>';

            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).html(htmlText);
        },
        remove: function (selector) {
            var a = selector.charAt(0);
            if (a !== '.' && a !== '#'){ selector = '#' + selector;}
            $(selector + ' .sga-progress').remove();
        }
    },

    spinner:{
        captcha: function (idContainer) {
            let spinerx;
            let $container = $(idContainer);
            $container.empty();
            spinerx  ='<div class="container-fluid text-info text-center" style="margin-top:8px;">';
            spinerx +='  <i class="fa fa-spinner fa-spin fa-2x"></i>';
            spinerx +='</div>';
            $container.html(spinerx);
        },
    },

    funcion: {
        fechaHora_spa_eng: function (fechahora) {
            let datos = fechahora.split(" ");
            let fecha = datos[0].split(".");
            let hora = datos[1];
            let d = fecha[0];
            let m = fecha[1];
            let y = fecha[2];
            return  y + "-" + m + "-" + d + " " + hora + ":00";
        },
        leftPad: function  (str, max) {
            str = str.toString();
            return str.length < max ? sga.funcion.leftPad("0" + str, max) : str;
        },
        existeFecha: function (fecha) {
            let fechaf = fecha.split("/");
            let d = fechaf[0];
            let m = fechaf[1];
            let y = fechaf[2];
            if (m > 0 && m < 13 && y > 2000 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate()){return true;}
            else{return false}
        },
        existeHora: function (hour) {
            let hora = hour.split(":");
            let h,m;
            h = parseInt(hora[0]);
            m = parseInt(hora[1]);
            if((h>=0 && h<=23) && (m>=0 && m<=59)){return true;}
            else{return false}
        },
        deshabilitaRetroceso: function () {
            window.location.hash="no-back-button";
            window.location.hash="Again-No-back-button";

            window.onhashchange=function(){window.location.hash="no-back-button";}
        },
        compara_dateMayor: function(fecha1, fecha2){
            "use strict";
            var xdat = fecha1.split(" ");
            var zdat = fecha2.split(" ");
            var x = xdat[0].split("/");
            var z = zdat[0].split("/");
            fecha1 = x[1] + '-' + x[0] + '-' + x[2] + ' ' + xdat[1];
            fecha2 = z[1] + '-' + z[0] + '-' + z[2] + ' ' + zdat[1];
            //Comparamos las fechas
            if (Date.parse(fecha1) < Date.parse(fecha2)){
                return true;
            }else{
                return false;
            }
        },
        compara_dateMenorMayor: function(fecha1, fecha2){
            "use strict";
            let x = fecha1.split("/");
            let z = fecha2.split("/");
            fecha1 = x[2] + '-' + x[1] + '-' + x[0];
            fecha2 = z[2] + '-' + z[1] + '-' + z[0];
            //Comparamos las fechas
            let resul = false;
            if (Date.parse(fecha1) <= Date.parse(fecha2)){
                resul = true;
            }
            return resul;
        },
        split_array: function ($array) {
            "use strict";
            let $texto = "";
            for(let i=0; i<$array.length; i++){
                if(i === parseInt($array.length)-1){
                    $texto += $.trim($array[i]);
                }
                else{
                    $texto += $.trim($array[i])+"|";
                }
            }
            return $texto;
        },
        pageTop: function() {
            $('html, body').animate({scrollTop : 0},'slow'); //800
            return false;
        },
        pageButtom: function() {
            $('html, body').animate({scrollTop:1000},'50');
            return false;
        },
        disabled: function (selector,booleano){
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).prop('disabled', booleano)
        },
        mayus: function(e) {
            e.value = e.value.toUpperCase();
        },
        valideKey: function (evt) {
            let code = (evt.which) ? evt.which : evt.keyCode;
            if (code === 8) {
                //backspace
                return true;
            }
            else if (code === 13) {
                //enter
                return true;
            }
            else if (code >= 48 && code <= 57) {
                //is a number
                return true;
            }
            else {
                return false;
            }
        },
        valida_sinEspacios : function (e) {
            let resul = false;
            if (e.target.value.trim() !== "") {
                resul = true;
            }
            return resul;
        }
    },

    plugins:{
        uniform: function  (selector, colors) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).uniform({
                radioClass: 'choice',
                wrapperClass: colors
            });
        },
        formatter_date: function  (selector,separator) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{99}}'+separator+'{{99}}'+separator+'{{9999}}'
            });
        },
        formatter_datetime: function  (selector,separator) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{99}}'+separator+'{{99}}'+separator+'{{9999}} {{99}}:{{99}}'
            });
        },
        formatter_daterange: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{99}}/{{99}}/{{9999}} to {{99}}/{{99}}/{{9999}}'
            });
        },
        formatter_nguia: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{999}}-{{9999999}}'
            });
        },
        formatter_nguia_ie: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{99}}-{{99999}}-{{9999999}}'
            });
        },
        formatter_numero: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{999999999999}}'
            });
        },
        formatter_hour: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).formatter({
                pattern: '{{99}}:{{99}}'
            });
        },
        datetimepicker: function  (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).datetimepicker({
                format: 'DD.MM.YYYY HH:mm',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar"
                }
            });
        },
        inputFile: function(selector,tipo) {
            let a = selector.charAt(0);
            let $fileExtension = "";
            let $fileSize = 0;
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            if(tipo === "excel"){
                $fileExtension = ['xls', 'xlsx'];
                $fileSize = 3000;
            }
            else if(tipo === "pdf"){
                $fileExtension = ['pdf'];
                $fileSize = 3000;
            }
            else if(tipo === "img"){
                $fileExtension = ["jpg", "png", "jpeg", "bmp"];
                $fileSize = 3000;
            }
            $(selector).fileinput({
                previewFileType:'any',
                showUpload : false,
                browseLabel: 'Examinar',
                removeLabel: 'Eliminar',
                maxFilesNum: 1,
                maxFileSize: $fileSize,
                allowedFileExtensions: $fileExtension,
                initialCaption: 'Seleccione un archivo',
                overwriteInitial: false
            });
        },
        inputFile_clear: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).fileinput("clear");
        },
        inputFile_enable: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).fileinput("enable");
        },
        inputFile_disable: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).fileinput("disable");
        },
        select2_cod: function (selectID) {
            let a = selectID.charAt(0);
            if (a !== '.' && a !== '#') {
                selectID = '#' + selectID;
            }
            $(selectID).select2({
                placeholder: "Seleccione..",
                templateResult: textCodigo,
                containerCssClass: 'select-md',
                minimumResultsForSearch: Infinity,
                templateSelection: textCodigo,
                escapeMarkup: function(m) { return m; }
            });

            $(selectID).on("select2:open", function () {
                $(".select2-search__field").attr("placeholder", "Escriba para filtrar...");
            });
            $(selectID).on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
            function textCodigo($tcodigo) {
                "use strict";
                let $textElement;
                if (!$tcodigo.id) { return $tcodigo.text; }
                $textElement  = $tcodigo.text+'<span class="text-indigo-800 textCodigoElem text-semibold">'+ $($tcodigo.element).data('codigo') +'</span>';
                return $textElement;
            }
        },
        select2_search: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).select2({
                placeholder: "Seleccione..",
            });
            $(selector).on("select2:open", function () {
                $(".select2-search__field").attr("placeholder", "Escriba para filtrar...");
            });
            $(selector).on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
        },
        select2_search_icon: function (selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).select2({
                placeholder: "Seleccione..",
                templateResult: textCodigo,
                templateSelection: textCodigo,
                escapeMarkup: function(m) { return m; }
            });
            $(selector).on("select2:open", function () {
                $(".select2-search__field").attr("placeholder", "Escriba para filtrar...");
            });
            $(selector).on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
            function textCodigo(state) {
                "use strict";
                let $textElement;
                if (!state.id) { return state.text; }
                $textElement  = state.text+'<span class="textCodigoElem bold-800"><icon class="'+ $(state.element).data('icon') +'"></icon></span>' ;
                return $textElement;
            }
        },
        select2_search_cod: function (selectID) {
            let $selectID = $('#'+selectID);
            $selectID.select2({
                placeholder: "Seleccione..",
                templateResult: textCodigo,
                templateSelection: textCodigo,
                escapeMarkup: function(m) { return m; }
            });

            $selectID.on("select2:open", function () {
                $(".select2-search__field").attr("placeholder", "Escriba para filtrar...");
            });
            $selectID.on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
            function textCodigo($tcodigo) {
                "use strict";
                let $textElement;
                if (!$tcodigo.id) { return $tcodigo.text; }
                $textElement  = $tcodigo.text+'<span class="textCodigoElem bold-800">'+ $($tcodigo.element).data('cod') +'</span>' ;
                return $textElement;
            }

        },
        select2: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).select2({
                placeholder: "Seleccione..",
                minimumResultsForSearch: Infinity
            });
        },
        select2_inClear: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).select2({
                allowClear: true,
                minimumResultsForSearch: Infinity
            });
        },
        fancybox: function(selector) {
            $(selector).fancybox({
                padding: 3
            });
        },
        flatpickr_range: function(selector1,selector2) {
            let a = selector1.charAt(0);
            if (a !== '.' && a !== '#') {
                selector1 = '#' + selector1;
            }
            let b = selector2.charAt(0);
            if (b !== '.' && b !== '#') {
                selector2 = '#' + selector2;
            }

            let check_in = flatpickr(selector1,  { dateFormat: "d/m/Y",locale: "es" });
            let check_out = flatpickr(selector2, { minDate: new Date(), dateFormat: "d/m/Y",locale: "es"});

            check_in.element.addEventListener("change", function(){
                check_out.set( "minDate" , check_in.element.value );
            });

            check_out.element.addEventListener("change", function(){
                check_in.set( "maxDate" , check_out.element.value );
            });
        },
        flatpickr: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: false,
                dateFormat: "d/m/Y",
                minDate: "today",
                locale: "es"
            });
        },
        flatpickr_all: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: false,
                dateFormat: "d/m/Y",
                locale: "es"
            });
        },
        flatpickr_mindate: function(selector,mindate) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: false,
                dateFormat: "d/m/Y",
                minDate: mindate,
                locale: "es"
            });
        },
        flatpickr_hour: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        },
        flatpickr_datetime: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: true,
                dateFormat: "d/m/Y H:i",
                time_24hr: true,
                minDate: "today",
                locale: "es"
            });
        },
        dualListBox: function(selector,txtNonSelect,txtSelectedList) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).bootstrapDualListbox({
                moveOnSelect: false,
                nonSelectedListLabel: txtNonSelect,
                selectedListLabel: txtSelectedList,
                infoText: 'Mostrando {0} registros',
                infoTextFiltered: '<span class="label label-warning">Mostrando</span> {0} de {1}',
                infoTextEmpty: 'Lista vacia',
                filterPlaceHolder: 'Escriba para filtrar...',
                filterTextClear: 'Borrar filtro'
            });
        },
        spectrum: function(pickerSelector, colorSelector){
            let a = pickerSelector.charAt(0);
            if (a !== '.' && a !== '#') {
                pickerSelector = '#' + pickerSelector;
            }
            let b = colorSelector.charAt(0);
            if (b !== '.' && b !== '#') {
                colorSelector = '#' + colorSelector;
            }
            $(pickerSelector).spectrum({
                move: function(c) {
                    let label = $(colorSelector);
                    label.val(c.toHexString());
                }
            });
        },
        spectrum_setColor: function(pickerSelector, color){
            let a = pickerSelector.charAt(0);
            if (a !== '.' && a !== '#') {
                pickerSelector = '#' + pickerSelector;
            }
            $(pickerSelector).spectrum("set", color);
        },
        tagsinput: function(selector, tags) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            if(parseInt(tags) === 0){
                $(selector).tagsinput();
            }
            else{
                $(selector).tagsinput({
                    maxTags: tags
                });
            }
        },
        multiSelect: function (selectID) {
            "use strict";
            let a = selectID.charAt(0);
            if (a !== '.' && a !== '#') {
                selectID = '#' + selectID;
            }
            let $selectID = $(selectID);
            $selectID.multiSelect({
                selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Escriba para buscar...'>",
                selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Escriba para buscar...'>",
                afterInit: function (ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function (e) {
                            if (e.which === 40) {
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function (e) {
                            if (e.which == 40) {
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });

        },
        summernote: function(selector, height) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).summernote({
                height: height,              // set editor height
                minHeight: null,             // set minimum height of editor
                maxHeight: null,             // set maximum height of editor
                focus: true                  // set focus to editable area after initializing summernote
            });
        },
        selectpicker: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).selectpicker();
        },
        selectpicker_refresh: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).selectpicker('refresh');
        },
        sortable: function(selector, group) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).sortable({
                group: group,
                pullPlaceholder: false
            });
        },
        flatpickr_rangeInput: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }
            $(selector).flatpickr({
                allowInput:true,
                enableTime: false,
                dateFormat: "d/m/Y",
                maxDate: "today",
                locale: "es",
                mode: "range"
            });
        },
        owlCarousel: function(selector) {
            let a = selector.charAt(0);
            if (a !== '.' && a !== '#') {
                selector = '#' + selector;
            }

            let feedbackSlider = $(selector);
            feedbackSlider.owlCarousel({
                items: 1,
                nav: true,
                dots: true,
                autoplay: false,
                loop: true,
                mouseDrag: true,
                touchDrag: true,
                transitionStyle:"fade",
                navText: ["<i class='fa fa-long-arrow-left'></i>", "<i class='fa fa-long-arrow-right'></i>"],
                responsive:{
                    // breakpoint from 767 up
                    767:{
                        nav: true,
                        dots: false
                    }
                }
            });

            feedbackSlider.on("translate.owl.carousel", function(){
                $(".feedback-slider-item h2").removeClass("animated fadeIn").css("opacity", "0");
                $(".feedback-slider-item img, .feedback-slider-thumb img, .customer-rating").removeClass("animated zoomIn").css("opacity", "0");
            });

            feedbackSlider.on("translated.owl.carousel", function(){
                $(".feedback-slider-item h2").addClass("animated fadeIn").css("opacity", "1");
                $(".feedback-slider-item img, .feedback-slider-thumb img, .customer-rating").addClass("animated zoomIn").css("opacity", "1");
            });
            feedbackSlider.on('changed.owl.carousel', function(property) {
                $('.thumb-next').addClass('btn-hover-transform')
                $('.thumb-prev').addClass('btn-hover-transform')
            });
            $('.thumb-next').on('click', function() {
                feedbackSlider.trigger('next.owl.carousel', [300]);
                return false;
            });
            $('.thumb-prev').on('click', function() {
                feedbackSlider.trigger('prev.owl.carousel', [300]);
                return false;
            });
        },
        toastr: function (tipo,position,contenido,titulo) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "progressBar": true,
                "positionClass": position,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            if(tipo === "success") {
                toastr.success(contenido, titulo);
            }
            else if(tipo === "info") {
                toastr.info(contenido, titulo);
            }
            else if(tipo === "warning") {
                toastr.warning(contenido, titulo);
            }
            else if(tipo === "error") {
                toastr.error(contenido, titulo);
            }
        }
    }
};