
$(function(){
    $(document).on('click', '[btn-name="admin-load-share-image"]', function(){
        var $this = $(this);
        var $block = $(this).closest('.form-group');
        $this.loadFile({
            done : function(e, data){
                $block.find('img').attr('src', data.result.href).show();
                $block.find('[name="share_image_file_id"]').val(data.result.image_id);
            }
        });
    });
});
