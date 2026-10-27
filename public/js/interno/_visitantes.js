$(document).ready(function (){

    $( function() {
        $("#dataInicioPesquisaVisitas").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            maxDate: 0,
            autoclose: true,
            onSelect: function(selected) {
                $("#dataFinalPesquisaVisitas").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#dataFinalPesquisaVisitas").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            maxDate: 0,
            autoclose: true,
            onSelect: function(selected) {
                $("#dataInicioPesquisaVisitas").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });


    $(document).on("click","#btnPesquisarVisitasData", function (e) {

        var idInterno = $('#idInterno').val();
        var dataInicio = $("#dataInicioPesquisaVisitas").val();
        var dataFinal = $("#dataFinalPesquisaVisitas").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/get-visitas-interno-ajax",
            data: {idInterno: idInterno, dataInicio: dataInicio, dataFinal: dataFinal},
            beforeSend: function() {

            },
            success: function (dados) {

                $('#itensVisitaCol').html(" ");

                $.each(dados,function(key, value)
                {
                    var horaSaida;
                    var duracao;

                    if(value.horaSaida == null)
                    {
                        horaSaida = "** sem registro **";
                        duracao = "";
                    }else{
                        horaSaida = value.horaSaida;
                        duracao = value.duracao;
                    }

                    $('#itensVisitaCol').append(
                    '<div class="row align-items-center how-it-works d-flex">'+
                    '<div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">'+
                   '<div style="z-index: 2; background:white !important" class="font-weight-bold">'+
                    '<div class="mt-0 right-col text-right">'+
                    '<div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">'+value.diaEntrada+' <br>'+value.mesData+'<br><div style="font-size:0.7em;">'+value.anoEntrada+'</div>'+

                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="col-md">'+
               '<img id="myImg" src="'+value.fotoVisita+'" alt="Legenda." class="img-thumbnail img-fluid rounded float-left mb-2">'+
                    '</div>'+
                    '<div class="col-md-8">'+
                   '<h5>'+value.nomeVisita+'</h5>'+
                '<p>'+value.relacao+''+
                    '<br/><strong>Horário de entrada na unidade prisional:</strong> '+value.horaEntrada+''+
                    '<br/><strong>Horário de saída na unidade prisional:</strong> '+horaSaida+''+
                    '<br/><strong>Duração do contato com o interno:</strong> '+duracao+'</p>'+
                '</div>'+
                '</div>'
                    );

                });

            }
        });

    });

});
