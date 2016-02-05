function startSpinner() {
    $('#ajaxLoader').addClass('show');
}

function stopSpinner() {
    $('#ajaxLoader').removeClass('show');
}

(function($, undefined) {

    $.nette.ext('spinner', {
        start: function () {
            startSpinner();
        },
        complete: function () {
            stopSpinner();
        },
        error: function () {
            startSpinner();
        }
    });

})(jQuery);