var br_saved_timeout;
var br_savin_ajax = false;
var br_each_parent_tr;
(function ($){
    br_each_parent_tr = function(selector, hide, thtd) {
        var better_position = $('.berocket_label_better_position').prop('checked');
        $(selector).each(function(i, o) {
            if( $(o).is('.berocket_label_better_position_hide') && better_position || $(o).is('.berocket_label_better_position_show') && ! better_position) {
                hide = true;
            }
            var whathide = $(o).parents('tr').first();
            if( thtd ) {
                whathide = whathide.find('th, td');
            }
            if( hide ) {
                whathide.hide();
            } else {
                whathide.show();
            }
        });
    }
    $(document).ready( function () {
        $(document).on('change', '.berocket_label_content_type', function() {
            br_each_parent_tr('.berocket_label_', true, false);
            br_each_parent_tr('.berocket_label_'+$(this).val(), false, false);
        });
        $(document).on('change', '.berocket_label_type_select', function() {
            br_each_parent_tr('.berocket_label_type_', true, false);
            br_each_parent_tr('.berocket_label_type_'+$(this).val(), false, false);
        });
        $(document).on('change', '.br_label_backcolor_use', function() {
            br_each_parent_tr('.br_label_backcolor', ! $(this).prop('checked'), false);
        });
        $(document).on('change', '.pos_label', function() {
            br_each_parent_tr('.pos_label_', true, true);
            br_each_parent_tr('.pos_label_'+$(this).val(), false, true);
            $('.pos__').hide();
            $('.pos__'+$(this).val()).show();
        });
        var br_label_ajax_demo = null;
        $(document).on('change', '.br_alabel_settings input, .br_alabel_settings textarea, .br_alabel_settings select', function() {
            if( $(this).is('.br_not_change') ) {
            } else if( $(this).is('.br_js_change') ) {
                if( $(this).data('style') && $(this).data('style').search('use:') != -1 ) {
                    style = $(this).data('style');
                    style = style.replace('use:', '');
                    if( $(this).is('[type=checkbox]') ) {
                        if( $(this).prop('checked') ) {
                            value = $('[data-style='+style+']').val();
                        } else {
                            value = '';
                        }
                    } else {
                        value = $(this).val();
                    }
                } else {
                    if( $(this).val().length ) {
                        if( $(this).data('ext').search('VAL') == -1 ) {
                            var value = $(this).val()+$(this).data('ext');
                        } else {
                            var value = $(this).data('ext').replace('VAL', $(this).val());
                        }
                    } else {
                        var value = $(this).val();
                    }
                    if( $(this).data('from') ) {
                        var style = $($(this).data('from')).val();
                    } else {
                        var style = $(this).data('style');
                    }
                }
                $('.berocket_label_preview').find($(this).data('for')).css(style, value);
            } else {
                var form_data = $(this).parents('form#post').serialize();
                $('.berocket_label_preview .br_alabel').remove();
                if( br_label_ajax_demo != null ) {
                    br_label_ajax_demo.abort();
                }
                br_label_ajax_demo = $.post(ajaxurl, form_data+'&action=br_label_ajax_demo', function(data) {
                    $('.berocket_label_preview .br_alabel').remove();
                    $('.berocket_label_preview').append(data);
                    br_label_ajax_demo = null;
                });
            }
        });
        $('.berocket_label_content_type, .berocket_label_type_select, .br_label_backcolor_use, .pos_label').trigger('change');
    });
})(jQuery);
