(function($){
    $(document).ready(function(){
        for (var key in tecsEvents) {
            if (tecsEvents.hasOwnProperty(key)) {
                $('#' + key).fullCalendar({
                    editable: false,
                    header: {
                        left: 'title',
                        center: '',
                        right: 'today prev,next'
                    },
                    loading: function( isLoading, view ) {
                        if (isLoading) {
                            $('#' + view.el.parent().parent().attr('id') + '-loading').show();
                        } else {
                            $('#' + view.el.parent().parent().attr('id') + '-loading').hide();
                        }
                    },
                    eventRender: function( event, element, view ) {
                        var title = element.find('.fc-title, .fc-list-item-title');
                        title.html(title.text());
                    },
                    eventMouseover: function(calEvent, jsEvent) {
                        var tooltip = '<div id="tecs-tooltipevent" class="tooltip-' + key + '" style="padding:5px;box-shadow:3px 3px 15px #dadada;width:320px;background:#fff;color:#0a0a0a;position:absolute;z-index:10001;"><h4 class="ecs-title entry-title summary">' + calEvent.title + '</h4><div class="ecs-calendar-event-body"><div class="ecs-calendar-duration">' + JSON.parse(calEvent.details).dateDisplay + '</div><div class="ecs-calendar-excerpt">' + calEvent.excerpt + '</div></div>';
                        $("body").append(tooltip);
                        $(this).mouseover(function(e) {
                            $(this).css('z-index', 10001);
                            $('#tecs-tooltipevent').fadeIn('500');
                            $('#tecs-tooltipevent').fadeTo('10', 1.9);
                            $('#tecs-tooltipevent').css('top', e.pageY + 10);
                            $('#tecs-tooltipevent').css('left', e.pageX + 20);
                        }).mousemove(function(e) {
                            $('#tecs-tooltipevent').css('top', e.pageY + 10);
                            $('#tecs-tooltipevent').css('left', e.pageX + 20);
                        });
                    },
                    eventMouseout: function(calEvent, jsEvent) {
                        $(this).css('z-index', 8);
                        $('#tecs-tooltipevent').remove();
                    },
                    events: function(start, end, timezone, callback) {
                        // load from local events without AJAX on first page load
                        if (tecEventCalendarSettings[key]['first_load'] === true) {
                            tecEventCalendarSettings[key]['first_load'] = false;
                            callback(tecsEvents[key]);
                            return;
                        }

                        tecEventCalendarSettings[key]['fromdate'] = start.format('YYYY-MM-DD');
                        tecEventCalendarSettings[key]['todate'] = end.format('YYYY-MM-DD');

                        $.ajax({
                            url: tecEventCalendarSettings[key]['ajaxurl'],
                            type: 'POST',
                            data: tecEventCalendarSettings[key],
                            success: function(data) {
                                try {
                                    callback($.parseJSON(data));
                                } catch (e) {
                                }
                            }
                        });
                    }
                });
                if (tecEventCalendarSettings[key].hasOwnProperty('height') && tecEventCalendarSettings[key].height) {
                    $('#' + key).fullCalendar('option', 'height', tecEventCalendarSettings[key].height);
                }
            }
        }
    });
})(jQuery);