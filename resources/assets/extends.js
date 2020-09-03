Dcat.DialogForm = function (options) {
    $(options.buttonSelector).off('click').click(function () {
        var url = $(this).data('url');
        var windowWidth = window.innerWidth;
        var popWidth = windowWidth < 1200 ? '80%' : '1000px';
        top.openPop(url, options.title,[popWidth, '600px'])
        return false;
    });
};



