$(document).ready(function(){

    $('#solicitante').change(function(){
        var solicitante = $(this).val();
        if(solicitante == 'unidade')
        {
            $("#divUnidade").toggleClass('displayNone')
        }
    });

});