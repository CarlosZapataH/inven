/**
 * Created by Malal91 and Haziel
 * Select multiple email by jquery.email_multiple
 * **/

(function($){

    $.fn.email_multiple_edit = function(options) {

        let defaults = {
            reset: false,
            fill: false,
            data: null
        };

        let settings = $.extend(defaults, options);
        let email = "";

        return this.each(function(){
            let $orig = $(this);
            let activeCampo = $orig.attr('data-active');
            let inputDisabled = " disabled ";
            if(parseInt(activeCampo) === 1){ inputDisabled = ""; }
            $(this).after("<input type=\"text\" name=\"email\" id=\"emailTemp\" class=\"enter-mail-ide mb-10 text-teal-800 input-lg\" placeholder=\"Ingrese Email a quienes se le enviara la alerta.\" "+inputDisabled+"  autocomplete='off'/>"+
                "<div class=\"all-mail\"></div>\n"
            );

            let $elementEmail = $('#emailreg').val();
            if($.trim($elementEmail).length > 0){
                let arrayEmail = $elementEmail.split(";");
                for(let i=0;i<arrayEmail.length;i++){
                    $('.all-mail').append('<span class="email-idse">' + arrayEmail[i] + '<span class="cancel-email-e">x</span></span>');
                    email += arrayEmail[i] + ';'
                }
                $orig.val(email.slice(0, -1))
                console.log(email);
            }

            let $element = $('.enter-mail-ide');
            $element.keydown(function (e) {
                $element.css('border', '');
                if (e.keyCode === 13 || e.keyCode === 32) {
                    e.preventDefault();
                    let getValue = $element.val();
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(getValue)){
                        $('.all-mail').append('<span class="email-idse">' + getValue + '<span class="cancel-email-e">x</span></span>');
                        $element.val('');

                        email += getValue + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                    $('.enter-mail-ide').focus();
                }

                $orig.val(email.slice(0, -1))
                console.log(email);
            });

            $(document).on('click','.cancel-email-e',function(){
                $(this).parent().remove();
            });

            if(settings.data){
                $.each(settings.data, function (x, y) {
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(y)){
                        $('.all-mail').append('<span class="email-idse">' + y + '<span class="cancel-email-e">x</span></span>');
                        $element.val('');

                        email += y + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                });
                $orig.val(email.slice(0, -1))
            }

            if(settings.reset){
                $('.email-idse').remove()
            }

            return $orig.hide()
        });
    };

})(jQuery);
