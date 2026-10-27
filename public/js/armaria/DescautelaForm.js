$(document).ready(function () {

    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));

        if($(this).is(":checked")){
            $('#retornoMensagens').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoMensagens').children().hasClass('background_bddaff')){
                $('#retornoMensagens').children().removeClass('background_bddaff');
            }
        }
    });

    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    $(".qtdEquipamento").on("blur", function() {

        var id = $(this).attr("data-id");
        var quantidade = $(this).val();

        $('input[type=hidden]').each(function(){

            var idHidden = $(this).attr("data-id");
            var tipoEquipamentoHidden = $(this).attr("data-name");

            if(idHidden == id)
            {

                $(this).val(quantidade+'/'+id+'/'+tipoEquipamentoHidden);

                return false
            }
        })


    });

});