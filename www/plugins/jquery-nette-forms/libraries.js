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

        el.autocomplete($.extend({
            source: el.attr('data-url')
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
    $('select.input-select').selectize();

    $('input.tag-input').selectize({
        delimiter: ',',
        createTranslate: translator ? translator.translate('Add') : 'Add',
        persist: false,
        create: function (input) {
            return {value: input, text: input}
        }
    });
}