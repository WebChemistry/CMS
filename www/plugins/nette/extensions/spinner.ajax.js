(function($, undefined) {

$.nette.ext('spinner', {
	init: function () {
		this.spinner = this.createSpinner();
		this.spinner.appendTo('body');
	},
    before: function (xhr, settings) {
        var domObj;
        if (settings.nette) {
            domObj = settings.nette.el;
        }

        if (domObj) {
            this.spinner.removeClass('hidden-spinner');
        }
    },
	complete: function () {
		this.spinner.addClass('hidden-spinner');
	},
    error: function () {
        this.spinner.addClass('hidden-spinner');
    }
}, {
	createSpinner: function () {
		return $('<div>', {
			id: 'ajax-spinner',
            class: 'hidden-spinner',
                        html: $('<img>', {
                            src: basePath + '/images/spinner.svg'
                        })
		});
	},
	spinner: null,
	speed: undefined
});

})(jQuery);
