
var visualdialog;
function DialogView(url, _this) {

    if (typeof visualdialog != 'undefined') {
        visualdialog.close().remove();
    }

    jQuery.getJSON(MODURL+url, function (data) {

        if (data.code == 1) {

            seajs.use(['/static/artDialog/src/dialog'], function (dialog) {

                visualdialog = dialog({
                    title: data.data.title,
                    content: data.data.content,
                    width: data.data.width?data.data.width:500,
                    height: data.data.width?data.data.width:jQuery("#postform").height()
                }).show(_this);
            });
        } else {
            seajs.use(['/static/artDialog/src/dialog'], function (dialog) {
                visualdialog = dialog({
                    title: '提示信息',
                    content: data.msg,
                    width: 500,
                    height: 500,
                    ok: function () {
                    },
                    okValue: ' 确定 '
                }).showModal();
            });
        }
    });
}
function DialogHandle(_this) {
    
    var postdata = jQuery(_this).serialize();

    var posturl = jQuery(_this).attr('action')

    jQuery.post(MODURL + posturl, postdata , function(data){
        if (typeof visualdialog != 'undefined') {
            visualdialog.close().remove();
        }
    
        console.log(data);

        seajs.use(['/static/artDialog/src/dialog'], function (dialog) {
            visualdialog =  dialog({ 
                title: data.code == 1?'操作成功':'操作失败',
                content: data.msg,
                width: 400,
                height: 80,
                ok: function () {
                },
                okValue: ' 确定 '
            }).showModal();
        });
    

    }, "json");

    return false;
}


