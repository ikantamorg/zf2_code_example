/* Serialize */
$.fn.extend({
    iformSetOptions: function(options){
        $.fn.iformOptions = options;
    },
    iformOptions: {
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
                return '<div class="alert alert-block alert-success fade in"><a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>' + text + '</div>';
            },
            setError: function(_this, text){
                $(_this).closest('.form-group').addClass($.fn.iformOptions.class.error);
            },
            setErrorLg: function(_this, text){
                $(_this).closest('.form-group').addClass($.fn.iformOptions.class.error);
            }
        }
    },

    /* LOADER */
    iformOnLoad : function($html){
        var $this = $(this);
        $this.addClass($.fn.iformOptions.class.loadBlock);
        $html.addClass($.fn.iformOptions.class.load);
        $html.css({
            'backgroundColor':'rgba(250,250,250,0)',
            'position':'absolute',
            'z-index' : 999999,
            'opacity' : 0,
            'top' : 0,
            'width' : $this.width() + 'px',
            'height' : $this.height() + 'px'
        });

        $this.css({
            'position': 'relative',
            'transition': 'all .2s linear',
            '-webkit-filter': 'blur(2px)'
        });

        $this.prepend($html);
        return $(this);
    },

    iformOnLoadBlock : function(iconLoad){
        if(!iconLoad && iconLoad != false) iconLoad = true;
        var $html = '';
        if(iconLoad){
            $html = $($.fn.iformOptions.functions.getHtmlLoadBlock());
        } else {
            $html = $("<div></div>");
        }
        $(this).iformOnLoad($html);
    },
    iformOnLoadBtn: function (iconLoad){
        if(!iconLoad) iconLoad = true;
        var $html = '';
        if(iconLoad && iconLoad != false){
            $html = $($.fn.iformOptions.functions.getHtmlLoadBtn());
        } else {
            $html = $("<div></div>");
        }
        $(this).iformOnLoad($html);
    },
    iformOffLoadAll: function() {

        var $this = $(this);
        var block = $this.parent().find('.' + $.fn.iformOptions.class.loadBlock);
        $this.removeAttr('style');
        $this.find('.' + $.fn.iformOptions.class.load).remove();
        $this.removeClass($.fn.iformOptions.class.loadBlock);
    },

    /* ERRORS */
    iformSetError: function(text){
        return $(this).each(function() {
            $.fn.iformOptions.functions.setError(this, text);
        });
    },
    iformSetErrorLg: function(index, text){
        return $(this).each(function() {
            $.fn.iformOptions.functions.setErrorLg(this, index, text);
        });
    },
    iformSetErrors: function(errors){

        var $this = $(this);
        if(errors){
            for(var key in errors){
                var $_block = $this.find('[name="' + key + '"]');
                if($_block.length){
                    if($.isPlainObject(errors[key])){
                        $_block = $_block.eq(0);
                        if($.isPlainObject(errors[key][Object.keys(errors[key])[0]])){
                            $_block.iformSetErrors(errors[key]);
                        } else {
                            $_block.iformSetError(errors[key]);
                        }
                    } else if($.isArray(errors[key])){
                        for(_key in errors[key]){
                            $_block.eq(_key).iformSetErrors(errors[key][_key]);
                        }
                    }
                } else {
                    $this.iformSetErrorLg(key, errors[key]);
                }
            }
        }
    },
    iformClearError: function(){
        var er = $('.' + $.fn.iformOptions.class.error);
        er.removeClass($.fn.iformOptions.class.error);
        if(er.attr('class')=='') er.removeAttr('class');
        return this;
    },

    iformSerialize: function(flag, data){
        if(!data) data = {};
        var $this = $(this), tmp;
        var $list = $this.find('[name]');
        $list.addClass('proc-form');
        $list.each(function(){
            var $input = $(this);
            if($input.is('[form-not-bind-name]')){
                $input.find('[name]').removeClass('proc-form');
            } else {
                if($input.hasClass('proc-form')
                    && !($input.parent().closest('[name]').length
                    && $input.parent().closest('[name]').css('display') == 'none')){
                    if($input.is('[form-array-object]')){
                        if(!data[$input.attr('name')]) data[$input.attr('name')] = [];
                        data[$input.attr('name')].push($input.iformSerialize());
                    } else if($input.is('[form-object]')){
                        if(!data[$input.attr('name')]) data[$input.attr('name')] = [];
                        tmp = $input.iformSerialize();
                        if(!$.isEmptyObject(tmp)) data[$input.attr('name')] = tmp;
                    } else {
                        var val;
                        if($input.attr('type') == 'checkbox'){
                            val = $input.is(':checked') ? $input.val() : null;
                        } else if($input.attr('type') == 'radio'){
                            var $radio = $input.closest('[block-name="radio"]');
                            $radio.find('[type="radio"][name="' + $input.attr('name') + '"]').removeClass('proc-form');
                            val = $radio.find('[type="radio"][name="' + $input.attr('name') + '"]:checked').val();
                        } else {
                            val = $input.val();
                        }
                        if(($input.hasClass('ik-form-input-null') && val) || !($input.hasClass('ik-form-input-null'))){
                            if($input.attr('name') == ''){
                                data = val;
                            } else {
                                data[$input.attr('name')] = val;
                            }
                        }

                    }
                }
            }
            $input.removeClass('proc-form');
        });

        if($this.attr('name') && flag && !$this.is('[form-not-bind-name]')) {
            var $_data = {};
            $_data[$this.attr('name')] = data;
            return $_data;
        } else {
            return data;
        }
    },

    iformClearInput: function(){
        $(this).find('input[type="text"], input[type="email"], input[type="url"], input[type="password"], textarea').each(function(){
            $(this).val('');
        });
        $(this).find('[iform-clear-block]').empty();
        $(this).find('[iform-clear-remove]').remove();
        $(this).find('[iform-clear-input]').val('');
        return this;
    },

    iformSendAjax : function(options){
        var flag_success = false;
        var $form = $(this);
        var send_data = $form.iformSerialize(true);
        var $pageAlertBlock = $('[block-name="page-alert"]');
        var settings = $.extend( {
            offRedirect: false,
            url : $form.attr('action'),
            errorScroll : true,
            iconLoad : true,
            onOnLoad: true,
            noOffLoad: false,
            addToData : function(data){return data;},
            success: function(data, block){},
            error : function(data){
                $form.iformSetErrors(data.errors);
            },
            alert_block: $pageAlertBlock.length ? $pageAlertBlock : $form,
            onLoad: function(){},
            offLoad: function(){}
        }, options);

        if(!settings.url) settings.url = $form.attr('action');
        if(!settings.url) console.log('form.sendAjax: not url');

        if(settings.onOnLoad)
            $form.iformOnLoadBlock(settings.iconLoad);

        $form.iformClearError();
        settings.onLoad();

        $.ajax({
            type: "POST",
            datatype: "json",
            url: settings.url,
            data: settings.addToData(send_data, $form),
            success: function(data){
                var timeout = 0;
                if(data.success_alert){
                    settings.alert_block.prepend($.fn.iformOptions.functions.getHtmlAlertSuccess(data.success_alert));
                    timeout = 3000;
                }

                if(data.error_alert){
                    settings.alert_block.prepend($.fn.iformOptions.functions.getHtmlAlertError(data.error_alert));
                    timeout = 3000;
                }

                if(data.redirect){
                    if(!settings.offRedirect){
                        setTimeout(function(){
                            window.location.href = data.redirect;
                        }, timeout);
                    }
                }

                if(data.success){
                    if(data.redirect && !settings.offRedirect){
                        flag_success = true;
                    } else {
                        flag_success = false;
                    }
                    settings.success(data, $form)
                } else {
                    settings.error(data, $form);
                }
            },
            complete: function(data){

                setTimeout(function(){
                    if(!settings.noOffLoad && !flag_success) {

                        $form.iformOffLoadAll();
                    }
                    settings.offLoad();
                }, 300);
            },
            error: function(data){
                if(data.errors)
                    $form.iformSetErrors(data.errors);

                if(data.error_alert)
                    settings.alert_block.prepend($.fn.iformOptions.functions.getHtmlAlertError(data.error_alert));

                settings.offLoad();
            }
        });
        return false;
    }
});


