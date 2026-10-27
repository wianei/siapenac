$(document).ready(function(){

    $( document ).on( "change", "#tipo", function() {

        var tipo = $(this).val();

        if(tipo == 'visita')
        {
            $("#divVisitaAll").removeClass('displayNone');
            $("#divAdvAll").addClass('displayNone');
            $("#divVisitanteExternoAll").addClass('displayNone');

        }

        if(tipo == 'advogado')
        {
            $("#divAdvAll").removeClass('displayNone');
            $("#divVisitaAll").addClass('displayNone');
            $("#divVisitanteExternoAll").addClass('displayNone');


        }

        if(tipo == 'visitanteExterno')
        {
            $("#divVisitanteExternoAll").removeClass('displayNone');
            $("#divVisitaAll").addClass('displayNone');
            $("#divAdvAll").addClass('displayNone');


        }

    });

    $("#dias").mask("00");


    $(document).on("submit", "#formCadastro", function () {

        $("#flashRetorno").html('');

        var control = 0;
        var advogadosSelecionados               = $('.hiddenAdvogadoId').length;
        var visitaSelecionados                  = $('.hiddenVisitaId').length;
        var visitanteExternoSelecionados        = $('.hiddenVisitanteExternoId').length;

        var tipo = $("#tipo").val();

        if(tipo == "advogado")
        {
            if(advogadosSelecionados == 0){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe quem terá o alerta cadastrado.</p></div>');
                control = 1;
            }

        }

        if(tipo == "visita")
        {
            if(visitaSelecionados == 0){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe quem terá o alerta cadastrado.</p></div>');
                control = 1;
            }

        }

        if(tipo == "visitanteExterno")
        {
            if(visitanteExternoSelecionados == 0){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe quem terá o alerta cadastrado.</p></div>');
                control = 1;
            }

        }

        if(control == 1) return false;

    });


});