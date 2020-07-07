// 弹窗表单
Dcat.DialogForm = function (options) {
    // console.log(options);
    // console.log('dialogForm');
    $(options.buttonSelector).off('click').click(function () {
        location.href = $(this).data('url');
    });
    // location.href = $(this).data('url');
    return false;
};
