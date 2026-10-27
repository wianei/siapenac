$(document).ready(function(){

    $(".sidebar").remove();

    $(document).on('keydown', '#senha', function(e) {
        if (e.keyCode == 32) {

            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Na senha não é permitido espaço entre caracteres</p></div>');

            return false;
        }

    });

    $(document).on('keydown', '#novaSenha', function(e) {
        if (e.keyCode == 32) {

            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Na senha não é permitido espaço entre caracteres</p></div>');

            return false;
        }
    });

    $( document ).on("submit", "#formAlterarSenha", function() {

        $("#flashRetorno").html('');
        var control = 0;

        var senha = $('#senha').val();
        if(senha == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a senha atual.</p></div>');
            control = 1;
        }

        var novaSenha = $('#novaSenha').val();
        if(novaSenha == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a nova senha.</p></div>');
            control = 1;
        }
        if(novaSenha.length < 6){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">A nova senha não pode conter menos que 6 caracteres.</p></div>');
            control = 1;
        }

        var novaSenhaR = $('#novaSenhaR').val();
        if(novaSenhaR == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Repita a nova senha.</p></div>');
            control = 1;
        }

        if(novaSenha != novaSenhaR){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">As novas senhas não coincidem.</p></div>');
            control = 1;
        }

        if(control == 1) return false;
    });


});
