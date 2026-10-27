$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $("#presidio").change(function(){
        $("#divPavilhao").removeClass('displayNone');
    });

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });

    $("#btnBuscaAvancada").click(function(){
        $("#divBuscaAvancada").toggleClass('displayNone');
    });




})