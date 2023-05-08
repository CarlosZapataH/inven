let $spnier1x;
$spnier1x  ='<div class="container-fluid text-primary text-center" style="margin-top: 8px;">';
$spnier1x +='  <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>';
$spnier1x +='</div>';

// JavaScript Document
var longitud = false,
    minuscula = false,
    numero = false,
    mayuscula = false;

$.ajaxPrefilter(function( options ) {
    "use strict";
    options.async = true;
});

$(function() {
    $(".preloader").fadeOut();
});

jQuery(document).on("click", ".mega-dropdown", function(e) {
    e.stopPropagation();
});


$(document).ready(function() {
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

    $(document).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });

    let $chngpss = $('#changepass').val();
    if(parseInt($chngpss)===0){
       // $('#MDLChange_pass').modal('show');
    }


});

$(document).on('click','#btn_logout',function(e){
    "use strict";
    e.preventDefault();
    sga.page.logout();
});
/*
$(document).on('click','.mn_option',function(){
    "use strict";
    let urlss = $(this).attr('data-url');
    sga.page.getLoadPage(urlss);
});
*/

$(document).on('click','.scrollToTop',function(){
    "use strict";
    $('html, body').animate({scrollTop : 0},'slow'); //800
    return false;
});

$(document).on('click','#optInventario',function(){
    "use strict";
    window.location = "inventario.php";
});

function srnPc() {
    let d = new Date();
    let dateint = d.getTime();
    let letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let total = letters.length;
    let keyTemp = "";
    for (let i = 0; i < 6; i++) {
        keyTemp += letters[parseInt((Math.random() * (total - 1) + 1))];
    }
    keyTemp += dateint;
    return keyTemp;
}


$(document).on('click','#btnGenerarToken',function(){
    "use strict";
    generarToken();
});



function generarToken() {
    if (!localStorage.getItem("srnPc")) {
        localStorage.setItem("srnPc", srnPc());
        Swal.fire({
            type: 'success',
            title: 'Token generado..!',
            text: 'Token: ' + localStorage.getItem("srnPc")
        });
    } else {
        Swal.fire({
            type: 'warning',
            title: 'El token generado es:',
            text: localStorage.getItem("srnPc")
        });
    }
}
