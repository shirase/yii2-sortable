(function($) {
    $.fn.sortableItems = function(options) {

        var settings = {
            id:'',
            url:null,
            items:null,
            paginationSelector: '.pagination li:not(.active):not(.previous):not(.next) a'
        };

        $.extend(settings, options);

        return this.each(function() {
            var el = $(this);

            var dropped = false;

            el.find(settings.paginationSelector).droppable({
                tolerance: 'pointer',
                drop: function(event, ui) {
                    dropped = true;
                    //el.sortable('cancel');
                    var page = parseInt($(this).text());
                    $.ajax(settings.url, {
                        cache:false,
                        type:'post',
                        data:{Sortable:{id:settings.id, model:ui.draggable.find('[data-sortable-id]').data('sortable-id'), page:page}},
                        complete: function() {
                            location.reload();
                        }
                    });
                }
            });

            el.sortable({
                items:settings.items,
                connectWith:el.find(settings.paginationSelector),
                handle: '[data-sortable-id]',
                stop: function(event, ui) {
                    if (dropped) {
                        dropped = false;
                        return;
                    }

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

                            }
                        });
                    }
                }
            });
        });
    }
})(jQuery)