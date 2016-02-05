function registerFormControls() {
    if ($.fn.datetimepicker) {
        $('input.date-input').each(function () {
            $(this).datetimepicker($.extend({
                format: $(this).attr('data-format')
            }, $.parseJSON($(this).attr('data-settings'))));
        });

        $('.date-input-container.no-js').remove();
    }

    if ($.fn.autocomplete) {
        $('input.suggestion-input').each(function () {
            el = $(this);
            var cache = [];

            el.autocomplete($.extend({
                source: function (request, response) {
                    var term = request.term;

                    if (term in cache) {
                        response(cache[term]);
                        return;
                    }

                    $.getJSON(el.attr('data-url'), request, function (data) {
                        var values = [];
                        var i = 0;

                        $.each(data, function (index, value) {
                            values[i] = {};

                            values[i].label = value;
                            values[i].value = index;
                            i++;
                        });

                        cache[term] = values;
                        response(values);
                    });
                },
                select: function (ui, item) {
                    console.log(item);
                }
            }, $.parseJSON(el.attr('data-suggestion'))));
        });
    }

    if ($.fn.inputmask) {
        $('input[data-mask-input]').each(function () {
            var settings = $.parseJSON($(this).attr('data-mask-input'));
            if (settings.regex) {
                $(this).inputmask('Regex', $.parseJSON($(this).attr('data-mask-input')));
            } else {
                $(this).inputmask($.parseJSON($(this).attr('data-mask-input')));
            }
        });
    }

    if ($.fn.selectize) {
        $('input.tag-input').selectize({
            delimiter: ',',
            createTranslate: i18n.translate('selectize.add'),
            persist: false,
            create: function (input) {
                return {value: input, text: input}
            }
        });
    }
}

$(document).ready(function () {
    registerFormControls();

    if (typeof $.nette === 'object') {
        $.nette.ext('registerControls', {
            'complete': function () {
                registerFormControls();
            }
        });
    }
});