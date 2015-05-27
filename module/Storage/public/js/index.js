$(function(){
    $.fn.loadFile = function(settings)
    {
        var $this = $(this);
        var $inputFile = $this.find('[name="file"]');

        var options = $.extend( {
            action: $this.attr('action'),
            send : function(e, data, $block){},
            done : function(e, data, $block){}
        }, settings);

        $inputFile.fileupload({
            url : options.action,
            dataType : 'json',
            multipart: true,
            singleFileUploads : true,
            send: function(e, data){
                options.send(e, data, $this);
                $this.iformOnLoadBlock(true);
            },
            done  : function(e, data){
                options.done(e, data, $this);
                $this.iformOffLoadAll();
            }
        });
    }

    $.fn.loadImage = function(settings)
    {
        $(this).each(function(){
            var $this = $(this);
            var options = $.extend( {
                action: $this.attr('action'),
                send : function(e, data, $block){},
                done : function(e, data, $block){}
            }, settings);

            $this.loadFile({
                action: options.action,
                send: function(e, data, $block){
                    options.send(e, data, $block);
                },
                done: function(e, data, $block){
                    $this.find('input[type="hidden"]').val(data.result.object_id);
                    $block.find('img').remove();
                    var $image = $('<img iform-clear-remove src="' + data.result.image_href + '">');
                    $block.append($image);
                    options.done(e, data, $block);

                }
            })
        });
    }

    $.fn.loadImageCrop = function(settings)
    {
        $(this).each(function(){
            var $this = $(this);
            var options = $.extend( {
                action: $this.attr('action'),
                send : function(e, data, $block){},
                done : function(e, data, $block){}
            }, settings);

            $this.loadFile({
                action: options.action,
                send: function(e, data, $block){
                    options.send(e, data, $block);
                },
                done: function(e, data, $block){
                    $this.find('input[type="hidden"]').val(data.result.object_id);
                    $block.find('img').remove();
                    var $image = $('<img src="' + data.result.image_href + '">');
                    $block.prepend($image);
                    options.done(e, data, $block);

                }
            })
        });
    }

    $.fn.loadListImage = function(settings)
    {
        $(this).each(function(){
            var $this = $(this),
                $inputFile = $this.find('[name="file"]'),
                action = $this.attr('action');

            var options = $.extend( {
                action: action,
                send : function(e, data, $block){},
                done : function(e, data, $block){},
                delete : function(e, data, $block){}
            }, settings);


            $this.loadFile({
                action: options.action + '/create-image',
                send: function(e, data, $block){
                    options.send(e, data, $this);
                },
                done: function(e, data, $block){
                    $this.find('[block-name="list"]').append(data.result.html);
                    options.done(e, data, $this);
                }
            });

            $this.on('click', '.delete', function(){
                var $blockImage = $(this).closest('.image');
                $blockImage.iformSendAjax({
                    url: options.action + '/delete-image',
                    addToData: function(data){
                        data['image_id'] = $blockImage.find('input[type="hidden"]').val();
                        return data;
                    },
                    success: function(){
                        options.delete($blockImage);
                        $blockImage.remove();
                    }
                });
                return false;
            });
        })
    }

    $('[helper="storage-load-image"]').loadImage();
    $('[helper="storage-load-image-crop"]').loadImageCrop();
    $('[helper="storage-load-list-images"]').loadListImage();
});