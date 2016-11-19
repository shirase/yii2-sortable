(function($) {
    $.fn.sortableItems = function(options) {

        var settings = {
            id:null,
            url:null,
            items:null,
            paginationSelector: '.pagination li:not(.active):not(.previous):not(.next) a',
            connectWithSelector: '.pagination li:not(.active):not(.previous):not(.next)'
        };

        $.extend(settings, options);

        return this.each(function() {
            var el = $(this);

            el.find(settings.paginationSelector).droppable({
                drop: function(event, ui) {
                    el.sortable('cancel');
                    var page = parseInt($(this).html());
                    $.ajax(settings.url, {
                        cache:false,
                        type:'post',
                        data:{Sortable:{id:settings.id, model:ui.item.find('[data-sortable-serial-column-id]').data('sortable-serial-column-id'), page:page}},
                        complete: function(data) {
                            //el.yiiGridView('applyFilter');
                        }
                    });
                }
            });

            el.sortable({
                items:settings.items,
                connectWith:el.find(settings.connectWithSelector),
                handle: '[data-sortable-serial-column-id]',
                stop: function(event, ui) {
                    var insert = ui.item.prev();
                    var action = 'after';
                    if(!insert.length) {
                        insert = ui.item.next();
                        action = 'before';
                    }
                    if(insert.length) {
                        var id = insert.find('[data-sortable-id]').data('sortable-id');
                        $.ajax(settings.url, {
                            cache:false,
                            type:'post',
                            data:{Sortable:{id:settings.id, model:ui.item.find('[data-sortable-id]').data('sortable-id'), insert:id, action:action}},
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