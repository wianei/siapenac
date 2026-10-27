$(document).ready(function (){

    $( function() {
        $("#dataInicioPesquisaOcorrencia").datepicker({
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
                $("#dataFinalPesquisaOcorrencia").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#dataFinalPesquisaOcorrencia").datepicker({
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
                $("#dataInicioPesquisaOcorrencia").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });


    $(document).on("click","#btnPesquisarOcorrencias", function (e) {


        var idVisita = $('#idVisita').val();
        var dataInicio = $("#dataInicioPesquisaOcorrencia").val();
        var dataFinal = $("#dataFinalPesquisaOcorrencia").val();
        var tipo = "visita";
        //
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ocorrencia/get-ocorrencia-envolvido-ajax",
            data: {dataInicio: dataInicio, dataFinal: dataFinal, tipo: tipo, idEnvolvido: idVisita},
            beforeSend: function() {

            },
            success: function (dados) {

                $('#itensOcorrenciaCol').html(" ");

                $.each(dados,function(key, value)
                {
                    var agentesTexto;
                    if(value.servidoresEnvolvidos != null) {
                        agentesTexto = '<br/><strong>Agentes Envolvidos:</strong> '+value.servidoresEnvolvidos;
                    }else{
                        agentesTexto = "";
                    }

                    var internosTexto;
                    if(value.internosEnvolvidos != null) {
                        internosTexto = '<br/><strong>Internos Envolvidos:</strong> '+value.internosEnvolvidos;
                    }else{
                        internosTexto = "";
                    }

                    var advsTexto;
                    if(value.advsEnvolvidos != null) {
                        advsTexto = '<br/><strong>Advogados Envolvidos:</strong> '+value.advsEnvolvidos;
                    }else{
                        advsTexto = "";
                    }

                    var visitasTexto;
                    if(value.visitasEnvolvidas != null) {
                        visitasTexto = '<br/><strong>Visitas Envolvidas:</strong> '+value.visitasEnvolvidas;
                    }else{
                        visitasTexto = "";
                    }

                    var logTexto;
                    if(value.dadosLog != null) {
                        logTexto = '<br/>'+value.dadosLog;
                    }else{
                        logTexto = "";
                    }

                    $('#itensOcorrenciaCol').append(
                        '<div class="row align-items-center how-it-works d-flex">' +
                        '<div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">' +
                        '<div style="z-index: 2; background:white !important" class="font-weight-bold">' +
                        '<div class="mt-0 right-col text-right">' +
                        '<div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">' + value.dia + ' <br>' + value.mesData + '<br><div style="font-size:0.7em;">' + value.ano + '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md">' +
                        '<h5>' + value.naturezaOcorrencia + '</h5>' +
                        '<p>' +
                        '<strong>Horário:</strong> ' + value.horario + '' +
                        '<br/><strong>Unidade Prisional:</strong> ' + value.unidade + '' +
                        '' + agentesTexto + '' +
                        '' + internosTexto + '' +
                        '' + advsTexto + '' +
                        '' + visitasTexto + '' +
                        '</p>' +
                        '<a href="/ocorrencia/visualizar-ocorrencia/' + value.idOcorrencia + '" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="Ver Ocorrência" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-link"></i></a>' +
                        '<p class="text-left">'+
                        '' + logTexto + '' +
                        '</p>'+
                        '</div>' +
                        '</div>' +
                        '<div class="row timeline">' +
                        '<div class="col-md-2">' +
                        '<div class="corner top-right"></div>' +
                        '</div>' +
                        '<div class="col-md-8">' +
                        '<hr/>' +
                        '</div>' +
                        '<div class="col-md-2">' +
                        '<div class="corner left-bottom"></div>' +
                        '</div>' +
                        '</div>'
                    );

                });

            }
        });

    });

});
