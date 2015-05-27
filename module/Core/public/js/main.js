

$(function(){
    $(document).on('click', '[form-array] [btn-add]', function(){
        var $this = $(this),
            $block = $this.closest('[form-array-object]'),
            $list = $block.closest('[form-array]');
        $list.append($block.clone().iformClearInput());
        return false;
    });

    $(document).on('click', '[form-array] [btn-delete]', function(){
        $(this).closest('[form-array-object]').remove();
    });

    $(document).on('submit', '[helper="form"]', function(){
        var $this = $(this);
        $this.iformSendAjax({
            success: function($data, $block){

            }
        });
        return false;
    });

    $(document).on('click', '[helper="form"] [btn-name="send-form"]', function(){
        $(this).closest('[helper="form"]').trigger('submit');
        return false;
    });

    $(document).on('click', '[helper="select-block"] [block-name="item"]', function(){
        var $this = $(this),
            $listBlock = $this.closest('[helper="select-block"]');

        $listBlock.find('[block-name="item"]').removeClass('active');
        $listBlock.find('input').val($this.attr('data-value'));
        $this.addClass('active');
        return false;
    });

    $(document).on('click', 'button[href]', function(){
        window.location.href = $(this).attr('href');
    });
});