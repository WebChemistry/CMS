// Sidebar collapse
$('.sidebar-toggle').on('click', function () {
    $.cookie('admin-sidebar-collapse', $('body').hasClass('sidebar-collapse') ? '' : 'true', {expires: 365, path: cookiePath});
});
// Notifications
function baseFormat() {
    function lz(val) {
        return val[1]?val:"0"+val[0];
    }
    var date = new Date();
    var yyyy = date.getFullYear().toString(), mm = (date.getMonth()+1).toString();
    var dd  = date.getDate().toString();
    var hh = date.getHours().toString();
    var mi = date.getMinutes().toString();
    var ss = date.getSeconds().toString();

    return yyyy + "-" + lz(mm) + "-" + lz(dd) + " " + lz(hh) + ":" + lz(mi) + ":" + lz(ss);
}
function updateNotifications(target) {
    if (typeof notificationCookie === 'undefined') {
        return;
    }
    $target = $(target);
    $.cookie(notificationCookie, baseFormat(), { expires: 30, path: cookiePath });
    $target.find('span').text(0);
}
$('.editor-input').ckeditor();
$('.editor-input').attr('data-nette-rules', {});

$.nette.init();
$.formRenderer.apply();