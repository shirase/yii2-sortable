(function($) {
    $.fn.GridView_sortable = function(options) {

        var settings = {
            id:'',
            url:'',
            items:'tbody tr'
        };

        $.extend(settings, options);

        return this.each(function() {
            var el = $(this);

            el.find('.pagination li:not(.active):not(.previous):not(.next) a').droppable({
                drop: function(event, ui) {
                    el.sortable('cancel');
                    var page = parseInt($(this).html());
                    $.ajax(settings.sorturl, {
                        cache:false,
                        type:'post',
                        data:{SortableSerialColumn:{id:settings.id, model:ui.item.find('[data-sortable-serial-column-id]').data('sortable-serial-column-id'), page:page}},
                        complete: function(data) {
                            //el.yiiGridView('applyFilter');
                        }
                    });
                }
            });

            el.sortable({
                items:settings.items,
                connectWith:el.find('.pagination li:not(.active):not(.previous):not(.next)'),
                handle: '[data-sortable-serial-column-id]',
                stop: function(event, ui) {
                    var insert = ui.item.prev();
                    var action = 'after';
                    if(!insert.length) {
                        insert = ui.item.next();
                        action = 'before';
                    }
                    if(insert.length) {
                        var id = insert.find('[data-sortable-serial-column-id]').data('sortable-serial-column-id');
                        $.ajax(settings.url, {
                            cache:false,
                            type:'post',
                            data:{SortableSerialColumn:{id:settings.id, model:ui.item.find('[data-sortable-serial-column-id]').data('sortable-serial-column-id'), insert:id, action:action}},
                            complete: function(data) {
                                //if(el.yiiGridView) el.yiiGridView('applyFilter');
                            }
                        });
                    }
                }
            });
        });
    }
})(jQuery)