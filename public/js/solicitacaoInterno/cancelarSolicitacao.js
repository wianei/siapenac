$(document).ready(function(){

    $( document ).on("submit", "#formCancelarSolicitação", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo do cancelamento.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#submitMudancaCela').attr('disabled', true);
    });
});
