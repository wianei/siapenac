$(document).ready(function (){

    $(document).on("change", "#tipo", function () {

        $("#divDataEvento").addClass('displayNone');
        $("#divDataEvento2").addClass('displayNone');
        $("#divHoraEvento").addClass('displayNone');
        $("#divTextoEvento").addClass('displayNone');
        $("#divTextoEvento2").addClass('displayNone');
        $("#divOpreracaoNaCela").addClass('displayNone');

        var tipo = $(this).val();

        if(tipo != "")
        {
            document.getElementById('interno').disabled = false;
        } else {
            document.getElementById('interno').disabled = true;
        }
        while($('input[name="internos[]"]').length != 0){
            $('.removerInterno').parent('td').parent('tr').remove();
        }
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }

        switch (tipo) {

            case "doente":

                $("#divDataEvento").removeClass('displayNone');
                $("#divTextoEvento").removeClass('displayNone');

                $('label[for="dataEvento"]').html("Data da Internação/Cirurgia/Procedimento");
                $('label[for="textoEvento"]').html("Hospital/Cliníca");

                break;

            case "greve":

                $("#divDataEvento").removeClass('displayNone');
                $("#divDataEvento2").removeClass('displayNone');

                $('label[for="dataEvento"]').html("Data da Assembléia");
                $('label[for="dataEvento2"]').html("Início da Greve");

                break;

            case "lapsoTemporal":

                $("#divDataEvento").removeClass('displayNone');
                $("#divHoraEvento").removeClass('displayNone');
                $("#divTextoEvento").removeClass('displayNone');


                $('label[for="dataEvento"]').html("Data da Solicitação");
                $('label[for="horaEvento"]').html("Hora da Solicitação");
                $('label[for="textoEvento"]').html("Ofício nº / Hermes nº");

                break;

            case "localDistante":

                $("#divTextoEvento").removeClass('displayNone');
                $("#divTextoEvento2").removeClass('displayNone');


                $('label[for="textoEvento"]').html("Vara Que Fez a Solicitação/Comarca-UF");
                $('label[for="textoEvento2"]').html("Número do Ofício");

                break;

            case "naoLocalizado":

                document.getElementById('interno').disabled = true;

                $("#divTextoEvento3").removeClass('displayNone');

                $('label[for="textoEvento3"]').html("Nome do Interno Não Localizado");

                break;

            case "operacao":

                $("#divOpreracaoNaCela").removeClass('displayNone');

                break;

            case "presoDistante":

                $("#divTextoEvento").removeClass('displayNone');
                $("#divTextoEvento2").removeClass('displayNone');


                $('label[for="textoEvento"]').html("Ofício nº / Hermes nº");
                $('label[for="textoEvento2"]').html("Comarca-UF (onde o interno encontra-se");

                break;

            case "presidioFederal":

                $("#divTextoEvento").removeClass('displayNone');
                $('label[for="textoEvento"]').html("Cidade/UF do Presídio Federal");

                break;


        }

    });

    $( function() {
        $("#dataDesignada").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            orientation: "bottom auto"
        });

    });

    $('#horaDesignada').mask('00:00', {reverse: true});
    $('#dataEvento, #dataEvento2').mask('00/00/0000');
    $('#nOficio').mask('00000000');


});