/**
 * Created by Malal91 and Haziel
 * Select multiple email by jquery.email_multiple
 * **/

(function($){

    $.fn.email_multiple = function(options) {

        let defaults = {
            reset: false,
            fill: false,
            data: null
        };

        let settings = $.extend(defaults, options);
        let email = "";

        return this.each(function(){
            $(this).after("<input type=\"text\" name=\"email\" id=\"emailTemp\" class=\"enter-mail-id mb-10 text-teal-800 input-lg\" placeholder=\"Ingrese Email a quienes se le enviara la alerta.\" disabled />"+
                "<div class=\"all-mail\"></div>\n"
            );
            let $orig = $(this);
            let $element = $('.enter-mail-id');
            $element.keydown(function (e) {
                $element.css('border', '');
                if (e.keyCode === 13 || e.keyCode === 32) {
                    e.preventDefault();
                    let getValue = $element.val();
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(getValue)){
                        $('.all-mail').append('<span class="email-ids">' + getValue + '<span class="cancel-email">x</span></span>');
                        $element.val('');

                        email += getValue + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                    $('.enter-mail-id').focus();
                }

                $orig.val(email.slice(0, -1))
            });

            $(document).on('click','.cancel-email',function(){
                $(this).parent().remove();
            });

            if(settings.data){
                $.each(settings.data, function (x, y) {
                    if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(y)){
                        $('.all-mail').append('<span class="email-ids">' + y + '<span class="cancel-email">x</span></span>');
                        $element.val('');

                        email += y + ';'
                    } else {
                        $element.css('border', '1px solid red')
                    }
                });
                $orig.val(email.slice(0, -1))
            }

            if(settings.reset){
                $('.email-ids').remove()
            }

            return $orig.hide()
        });
    };

})(jQuery);
