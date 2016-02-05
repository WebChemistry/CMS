var hasFlash = false;

try {
    var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
    if (fo) {
        hasFlash = true;
    }
} catch (e) {
    if (navigator.mimeTypes
        && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
        && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
        hasFlash = true;
    }
}

function swfDisplay() {
    $('div.swf-wrapper.rkl-hidden').each(function () {
        $(this).removeClass('rkl-hidden');
        $(this).closest('.rkl-container').find('img.rkl-element').addClass('rkl-hidden');
    });
}

function swfWrapper() {
    $('.swf-wrapper').each(function () {
        var embed = $(this).find('embed');
        var width = $(this)[0].getBoundingClientRect().width;
        var height = ( parseInt($(this).attr('data-height')) / parseInt($(this).attr('data-width')) ) * width;

        var origWidth = $(this).attr('data-width');
        var origHeight = $(this).attr('data-height');

        embed.attr('width', width < origWidth ? width: origWidth);
        embed.attr('height', width < origWidth ? height : origHeight);
    });
}

$(document).ready(function () {
    if (hasFlash) {
        swfDisplay();
        swfWrapper();
    }
});
$(window).resize(function () {
    if (hasFlash) {
        swfDisplay();
        swfWrapper();
    }
});
