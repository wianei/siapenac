$(document).ready(function () {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $(".docModal").click(function () {

        var url = document.URL;
        var arr = url.split('/')[0];
        var newUrl = url.split('/')[0] + '/' + url.split('/')[1] + '/' + url.split('/')[2] + '/documento-view';

        $('#modalIframe').modal('toggle');

        var idDocumento = $(this).attr('id');
        $("#iframeTest").attr('src', newUrl + '/' + idDocumento);

    });
});
