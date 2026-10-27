$(document).ready(function (){

    $( function() {
        $("#dataInicioPesquisaSaudeAssist").datepicker({
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
                $("#dataFinalSaudeAssist").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#dataFinalSaudeAssist").datepicker({
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
                $("#dataInicioPesquisaSaudeAssist").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });


    $(document).on("click","#btnPesquisarSaudeAssist", function (e) {

        var idInterno = $('#idInterno').val();
        var dataInicio = $("#dataInicioPesquisaSaudeAssist").val();
        var dataFinal = $("#dataFinalSaudeAssist").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/get-saude-assistencia-ajax",
            data: {idInterno: idInterno, dataInicio: dataInicio, dataFinal: dataFinal},
            beforeSend: function() {

            },
            success: function (dados) {

                $('#itensSaudeAssistCol').html(" ");

                $.each(dados,function(key, value)
                {
                    $('#itensSaudeAssistCol').append(

                    '<div class="row align-items-center how-it-works d-flex">'+
                    '<div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">'+
                    '<div style="z-index: 2; background:white !important" class="font-weight-bold">'+
                    '<div class="mt-0 right-col text-right">'+
                    '<div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">'+value.diaAtendimento+' <br>'+value.mesData+'<br><div style="font-size:0.7em;">'+value.anoAtendimento+'</div>'+
                    '</div>'+
                    '</div>'+
                    '</div>'+
                    '</div>'+
                    '<div class="col-md">'+
                    '<h5>'+value.setor+'</h5>'+
                    '<p>'+
                    '<strong>Médico:</strong> '+value.nomeMedico+' - '+value.nomeMedico+''+
                    '<br/><strong>Horário de atendimento:</strong> '+value.horaAtendimento+''+
                    '<br/><strong>Descrição do Atendimento</strong> '+value.descricao+''+
                    '</p>'+

                    '<p class="text-left">'+value.log+'</p>'+
                    '</div>'+
                    '</div>'+
                    '<div class="row timeline">'+
                    '<div class="col-md-2">'+
                    '<div class="corner top-right"></div>'+
                    '</div>'+
                    '<div class="col-md-8">'+
                    '<hr/>'+
                    '</div>'+
                    '<div class="col-md-2">'+
                    '<div class="corner left-bottom"></div>'+
                    '</div>'+
                    '</div>'
                    );

                });

            }
        });

    });

});
