$(document).ready(function () {



    $("#confere").on("click", function() {
        if($("#confere").val() == "resumido" || $("#confere").val() == "simples"){
            $("#divIncluirFotos").addClass('displayNone');
        }

        if($("#confere").val() == "completo"){
            if($("#ala").val() != ""){
                $("#divIncluirFotos").removeClass('displayNone');
            } else {
                $("#divIncluirFotos").addClass('displayNone');
            }
        }
    });


    $("#ala").click(function(){
        if($("#ala").val() != "" && $("#confere").val() == "completo"){
            $("#divIncluirFotos").removeClass('displayNone');
        } else {
            $("#divIncluirFotos").addClass('displayNone');
        }
    });
    
    
    $("#btnGerarConfere").click(function(){
        
        $("#flashRetorno").html("");
        
        var unidade = $("#unidade_prisional").val();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/confere-geral-estatus-ajax",
            beforeSend: function() {

            },
            success: function (dados) {
                
                var objt = jQuery.parseJSON(dados);
                
                if(objt == 0)
                {
                   if(unidade != ""){
                       $('form').submit();
                   }
                    else{
                        
                        $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Escolha uma Unidade Prisional!</p></div>');
                    }
                    
                    
                }else{
                    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Dados em atualização, tente novamente em instantes.</p></div>');
                }

            }
        });

    });

});
