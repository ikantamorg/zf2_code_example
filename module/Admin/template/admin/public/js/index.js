$(function(){
    $.fn.iformSetOptions({
        class: {
            loadBlock : 'ik-load-block',
            load : 'ik-load',
            error : 'has-error'
        },
        functions: {
            getHtmlLoadBlock : function(){
                return '<div><div style="position: relative; top: 50%; margin-top: -20px;">' +
                    '<div class="circle"></div><div class="circle1"></div>' +
                    '</div></div>';
            },
            getHtmlLoadBtn : function(){
                return '<div style="text-align: center;font-size: 30px;"><i style="top: 50%; position: absolute; margin-top: -15px;" class="fa fa-spinner fa-spin"></i></div>';
            },
            getHtmlAlertError: function(text){

                return '<div class="alert alert-block alert-danger fade in"><a class="close" data-dismiss="alert" href="#" aria-hidden="true"><i class="mt-4 glyphicon glyphicon-remove-circle"></i></a><i class="glyphicon glyphicon-warning-sign"></i>' + text + '</div>';
            },
            getHtmlAlertSuccess: function(text){
                var $alert = $('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>' + text + '</b></div>');
                $alert.hide();
                setTimeout(function() {
                    $alert.slideDown(function () {
                        setTimeout(function () {
                            $alert.slideUp(function () {
                                $(this).remove();
                            })
                        }, 3000);
                    });
                }, 600);
                return $alert;
            },
            setError: function(_this, text){
                $(_this).closest('.form-group').addClass($.fn.iformOptions.class.error);
            },
            setErrorLg: function(_this, text){
                $(_this).closest('.form-group').addClass($.fn.iformOptions.class.error);
            }
        }
    });
});