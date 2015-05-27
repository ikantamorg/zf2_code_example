
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