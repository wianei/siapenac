$(document).ready(function(){

    /**
     * Marca todos os checkboxes da lista de internos por recebimento
     */
    $( document ).on( "click", "#checkTodosInternos", function() {
        $("#tableInternosSolicitacao .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoInternosRecebimento tr').addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosRecebimento tr').hasClass('background_bddaff')){
                $('#retornoInternosRecebimento tr').removeClass('background_bddaff');
            }
        }
    });

    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    $( document ).on("submit", "#formCancelarSolicitacao", function() {
        $("#flashRetorno").html('');
        var control = 0;
        if($('input[name="cancelamentoSolicitacaoCheck[]"]:checked').length == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Escolha pelo menos um interno para cancelar a solicitação.</p></div>');
            control = 1;
        }

        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo da mudança de cela.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }

    });

});