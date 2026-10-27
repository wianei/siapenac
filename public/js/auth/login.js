/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com - matricula(2222272) SEAP | geovanni_ti@hotmail.com matricula(2224232) - SEAP
 */

$(document).ready(function (){

    $('#cpf').mask('00000000000');

    $('form').on('submit', function(e) {
        if(grecaptcha.getResponse() == "") {
            e.preventDefault();
            $("#flashRetorno").append('<br><div class="flash-message"><p class="alert alert-danger">Marque a caixa acima "Não sou um robô" para validar seu login!</p></div>');
        }
    });

});