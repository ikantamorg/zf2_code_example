$(function(){
    $.fn.itable = function(){
        var $this = $(this),
            $createForm = $this.find('[block-name="table-create-modal"]').eq(0),
            $editForm = $this.find('[block-name="table-edit-modal"]').eq(0);

        $this.getOptionsData = function(){
            return $this.find('[block-name="table-options"]').iformSerialize();
        };

        $this.getFiltersData = function(){
            return $this.find('[block-name="filters"]').iformSerialize();
        };

        $this.getPage = function(){
            return $this.find('[block-name="pagination"] input').val();
        };

        $this.setPage = function(page){
            $this.find('[block-name="pagination"] input').val(page);
        };

        $this.on('table-reload', function(){
            $this.reload();
        });

        $this.on('delete', 'tr', function(){
            var $trBlock = $(this);
            $this.iformSendAjax({
                url: $this.attr('action'),
                addToData: function(data){
                    data = {};
                    data['object_id'] = $trBlock.attr('data-id');
                    data['action'] = 'delete';
                    return data;
                },
                success: function(data){
                    $this.reload();
                }
            });
            return false;
        });

        $this.reload = function(){
            $this.iformSendAjax({
                url: $this.attr('action'),
                addToData: function(data){
                    data = {};
                    data['page'] = $this.getPage();
                    data['filters'] = $this.getFiltersData();
                    data['options'] = $this.getOptionsData();
                    return data;
                },
                success: function(data){
                    if(data.html){
                        var html = data.html;
                        if(html.table){
                            var $tbody = $this.find('tbody');
                            $tbody.empty();
                            var $table = $(html.table);
                            $table.find("[data-toggle='tooltip']").tooltip();
                            $tbody.append($table);
                        }

                        if(html.pagination){
                            var $pagination = $this.find('[block-name="pagination"]');
                            $pagination.empty();
                            $pagination.append($(html.pagination).find('[block-name="pagination"]'));
                        }

                        if(html.filters){
                            var $filters = $this.find('[block-name="filters"]');
                            $filters.empty();
                            $filters.append($(html.filters));
                        }

                        $this.trigger('reload-end');
                    }

                }
            })
        };



        $this.on('change', '[block-name="table-options"] input, [block-name="table-options"] select', function(){
            $this.setPage(1);
            $this.trigger('table-reload');
        });

        $this.on('click', '[btn-page]', function(){
            $this.setPage($(this).attr('btn-page'));
            $this.trigger('table-reload');
            return false;
        });

        $this.on('click', '[btn-name="table-filters"]', function(){
            $this.setPage(1);
            $this.trigger('table-reload');
            return false;
        });



        $this.on('click', '[btn-name="table-create-new"]', function(){
            $createForm.modal('show');
            return false;
        });

        $this.on('edit', 'tr', function(){
            var $_this = $(this);
            $this.iformSendAjax({
                url: $this.attr('action'),
                addToData: function(data){
                    data['action'] = 'load-edit';
                    data['object_id'] = $_this.attr('data-id');
                    return data;
                },
                success: function(data){
                    $editForm.find('.modal-content').html(data.html);
                    setTimeout(function(){
                        $_this.trigger('edit-end-load-form');
                        $this.trigger('table-tr-edit');
                        $editForm.modal('show');
                    }, 300);
                }
            });
        });

        $this.on('click', '[btn-name="table-tr-edit"]', function(){
            $(this).closest('tr').trigger('edit');
            return false;
        });





        $this.on('click', 'tr [btn-name="table-tr-delete"]', function(){
            $(this).trigger('delete');
        });




        $createForm.on('click', '[btn-name="table-create-cancel"]', function(){
            $createForm.modal('hide');
            return false;
        });

        $createForm.on('click', '[btn-name="table-create-save"]', function(){
            var $_form,
                $forms = $createForm.find('form');

            if($createForm.find('form').length > 1){
                $_form = $createForm.find('form[active]')
            } else {
                $_form = $createForm.find('form');
            }
            $_form.iformSendAjax({
                url: $this.attr('action'),
                addToData: function(data){
                    data['action'] = 'create';
                    return data;
                },
                success: function(){
                    $createForm.modal('hide');
                    setTimeout(function(){
                        $createForm.iformClearInput();
                        $this.trigger('table-reload');
                    }, 500);
                }
            });
            return false;
        });


        $editForm.on('click', '[btn-name="table-edit-cancel"]', function() {
            $editForm.modal('hide');
            return false;
        });

        $editForm.on('click', '[btn-name="table-edit-save"]', function() {
            $editForm.find('form').iformSendAjax({
                url: $this.attr('action'),
                addToData: function(data){
                    data['action'] = 'edit';
                    return data;
                },
                success: function () {
                    $editForm.modal('hide');
                    setTimeout(function () {
                        $this.trigger('table-reload');
                    }, 500);
                }
            });
            return false;
        });

    };

    $('[helper="table"]').itable();
});
