$(document).ready(function () {

    $("#cautelaPara").change(function(){
        var cautelaPara = $(this).val();

        if(cautelaPara == "unidade")
        {
            $("#divUnidade").removeClass('displayNone');
        }else{
            $("#divUnidade").addClass('displayNone');
        }
    });

    $("#data_inicial, #data_final").mask('00/00/0000');

});
