(function($, undefined) {

$.nette.ext('scrolling', {
        init: function () {
            this.ext('snippets', true).after($.proxy(function ($el) {
                if (typeof $el.attr('data-ajax-scroll') !== 'undefined') {
                    var offset = $el.offset();
                    var body = $("html, body");
                    body.animate({scrollTop: offset.top}, 500);
                }
            }));
        },
        before: function (xhr, settings) {
            if (!settings.nette) {
                this.href = null;
            } else if (!settings.nette.form) {
                this.href = settings.nette.ui.href;
            } else if (settings.nette.form.get(0).method === 'get') {
                this.href = settings.nette.form.get(0).action || window.location.href;
            } else {
                this.href = null;
            }
        },
        complete: function () {
            if (!this.href) {
                return;
            }

            if (this.href.indexOf("#") != -1) {
                var hash = this.href.substr(this.href.indexOf("#"));

                var offset = $(hash).offset();
                var body = $("html, body");
                if (typeof offset !== 'undefined') {
                    body.animate({scrollTop: offset.top}, 500);
                }
            }
        }
    });

})(jQuery);
