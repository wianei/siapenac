/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 25/11/2019.
 */
$(document).ready(function(){

    $( document ).on("submit", "#formRelatorio", function() {

        $("#flashRetorno").html('');

        var control = 0;

        var lista = $('#lista').val();
        if(lista == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o nome da lista.</p></div>');
            control = 1;
        }

        var mes = $('#mes').val();
        if(mes == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o mês.</p></div>');
            control = 1;
        }

        var ano = $('#ano').val();
        if(ano == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o ano.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
    });


});