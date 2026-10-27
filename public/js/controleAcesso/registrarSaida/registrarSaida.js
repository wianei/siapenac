$(document).ready(function(){


    $("#cpf").blur(function(){

        $("#dataDarSaida").html('');

        var cpf = $(this).val();


        if(cpf.length == 11)
        {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../controle-acesso/buscar-horario-aberto-ajax",
                data: {cpf: cpf},
                beforeSend: function() {

                },
                success: function (dados) {

                    $("#dataDarSaida").append('<option value="">Selecione</option>')

                    $.each(dados, function (k, v) {

                        $("#dataDarSaida").append('<option value="'+v+'">'+v+'</option>')

                    })
                }

            });

        }else{
            $("#dataDarSaida").append('<option value="">CPF inválido</option>')

        }

    })
    $( document ).on( "change", "#tipoEntrada", function() {

        var tipoEntrada = $(this).val();

        if(tipoEntrada != 'visita')
        {
            $("#divTableInternos").addClass('displayNone');
        }else{
            $("#divTableInternos").removeClass('displayNone');
        }

    });


    /**
     * Validacoes e mascaras
     */

    $("#dataSaida").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
        maxDate: '+1d',
        autoclose: true,
        orientation: "bottom auto"
    });


    $('#horarioSaida').mask('00:00');
    $('#dataSaida').mask('00/00/0000');
    $('#cpf').mask('0#');

    $( document ).on("submit", "#formRegistroSaida", function() {

        var control = 0;

        $("#flashRetorno").html('');

        var dataDarSaida = $('#dataDarSaida').val();
        if(dataDarSaida == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a data que você deseja dar a saída.</p></div>');
            control = 1;
        }

        if(control == 1) return false;
    });


})