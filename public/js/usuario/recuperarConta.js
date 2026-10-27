$(document).ready(function(){
    $('#cpf').mask('00000000000', {reverse: true});
    
    function validaCpf(strCPF) {
        if(strCPF.length != 11) return false;
        var Soma;
        var Resto;
        Soma = 0;

        for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

        Soma = 0;
        for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
        return true;
    }

    $( document ).on("submit", "#formRecuperarSenha", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var cpf = $('#cpf').val();
        if(!validaCpf(cpf)){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira um CPF válido com 11 dígitos, incluindo zeros à esquerda e somente números.</p></div>');
            control = 1;
        }
        
        var email = $('#email').val();
        if(email == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe um e-mail válido.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        else return true;
    });


});
