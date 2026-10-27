$(document).ready(function(){
    
    $('#dataInicio, #dataTermino').mask('00/00/0000');
    $('#cpf').mask('00000000000');



    $("form").submit(function (e) {

        $("#flashRetorno").html('');
        
        var control = 0;

        var cpf = $('#cpf').val();
        if(cpf == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o CPF.</p></div>');

            control = 1;
        }

        var dataInicio = $('#dataInicio').val();
        if(dataInicio == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe uma data inicial.</p></div>');

            control = 1;
        }

        var dataTermino = $('#dataTermino').val();
        if(dataTermino == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe uma data final.</p></div>');

            control = 1;
        }

        var dtInicio = FormataStringData(dataInicio)
        var dtTermino = FormataStringData(dataTermino)
        var start = new Date(dtInicio),
            end   = new Date(dtTermino),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            
            if(days > 7)
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe um período de dias menor ou igual a 7</p></div>');
            control = 1;
            
            }
        
        
        
        if(control == 1) return false;



    });
    
    function FormataStringData(data) {
      var dia  = data.split("/")[0];
      var mes  = data.split("/")[1];
      var ano  = data.split("/")[2];

      return ano + '-' + ("0"+mes).slice(-2) + '-' + ("0"+dia).slice(-2);
      // Utilizo o .slice(-2) para garantir o formato com 2 digitos.
    }
});

