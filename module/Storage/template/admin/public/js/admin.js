$(function(){
    $('[block-name="admin-storage-manager"] [block-name="table-create-modal"]').each(function(){
        var $this = $(this);

        $this.find('[name="type"] option').each(function(index, val){
            var $_this = $(val);
            var $block = $this.find('fieldset').eq(index);
            $block.attr({
                'name': $_this.attr('value'),
                'form-object': ''
            })
        });

        $this.on('change', '[name="type"]', function(){
            $this.find('fieldset').hide();
            $this.find('fieldset[name="' + $(this).val() + '"]').show();
        });
        $this.find('[name="type"]').trigger('change');
    });
});