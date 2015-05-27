
$(function(){
    $('[block-name="user-manager"]').on('click', '[btn-name="user-lock"]', function(){
        var $this = $(this),
            $tr = $this.closest('tr'),
            $block = $tr.closest('[block-name="user-manager"]');

        $this.closest('td').iformSendAjax({
            url: $block.attr('action') + '/locked',
            addToData: function(data){
                data['object_id'] = $tr.attr('data-id');
                return data;
            },
            success: function(data){
                var $i = $tr.find('[btn-name="user-lock"] i')
                $i.removeClass('fa-unlock');
                $i.removeClass('fa-lock');
                if(data.user_status == '1'){
                    $i.addClass('fa-lock');
                } else {
                    $i.addClass('fa-unlock');
                }

                $tr.find('[btn-name="user-lock"][data-val="' + data.user_status + '"]').attr('disabled', 'disabled');
            }
        });

    });

    $(document).on('click', '[btn-name="admin-user-default-avatar"]', function(){
        var $this = $(this);
        var $block = $(this).closest('.form-group');
        $this.loadFile({
            done : function(e, data){
                $block.find('img').attr('src', data.result.href).show();
                $block.find('[name="default_avatar_id"]').val(data.result.default_avatar_id);
            }
        });
    });
});
