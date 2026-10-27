$(document).ready(function(){

    $( function() {
        $("#created_at_inicio").datepicker({
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
                $("#created_at_fim").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#created_at_fim").datepicker({
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
                $("#created_at_inicio").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#data_escolta_inicio").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            onSelect: function(selected) {
                $("#data_escolta_fim").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#data_escolta_fim").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            onSelect: function(selected) {
                $("#data_escolta_inicio").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });

    $( document ).on( "click", ".confirmar", function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var id = $(this).data("content");
        $("#trr"+id).addClass('displayNone');
        $("#trrrepassar"+id).addClass('displayNone');

        $.ajax({
            type: "POST",
            url: "/gep/confirmar-solicitacao",
            data: {idSolicitacao: id},
            beforeSend: function () {

            },
            success: function (dados) {
                if(dados == "true") {
                    $("#bt" + id).removeClass('btn-warning').removeClass('btn-danger').removeClass('btn-dark').removeClass('btn-success').addClass('btn-success').html('Confirmada');
                    $("#acoes" + id).html(
                        '<a href="/escolta/show/' + id + ')" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>' +
                        '<a href="#/" data-content="' + id + '" data-toggle="tooltip" data-placement="top" title="Cancelar" class="cancelar"><i class="mdi mdi-close-circle-outline"></i></a>'
                    );
                }
            }
        });
    });


    $( document ).on( "click", ".cancelar", function() {

        var id = $(this).data("content");

        if($("#trr"+id).hasClass('displayNone')){
            $("#trr"+id).removeClass('displayNone');
        }
        else{
            $("#trr"+id).addClass('displayNone');
        }
        $("#trrrepassar"+id).addClass('displayNone');

        $( document ).on( "click", ".cancelarComObs", function() {

            var obs = $("#btc"+id).val();
            if(obs == ""){
                alert("O motivo do cancelamento não pode estar em branco!");

                return false;

            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/gep/cancelar-solicitacao",
                    data: {idSolicitacao: id, obs: obs},
                    beforeSend: function () {
                    },
                    success: function (dados) {
                        if (dados == "true") {
                            $("#bt" + id).removeClass('btn-warning').removeClass('btn-danger').removeClass('btn-dark').removeClass('btn-success').addClass('btn-danger').html('Cancelada');
                            $("#trr" + id).addClass('displayNone');
                        }
                        $("#acoes" + id).html('<a href="/escolta/show/' + id + ')" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>');
                    }
                });
            }
        });

    });

    $( document ).on( "click", ".repassar", function() {

        var id = $(this).data("content");
        if($("#trrrepassar"+id).hasClass('displayNone')){
            $("#trrrepassar"+id).removeClass('displayNone');
        }
        else{
            $("#trrrepassar"+id).addClass('displayNone');
        }
        $("#trr"+id).addClass('displayNone');

        $( document ).on( "click", ".repassarBT", function() {

            var responsavel = $("#btcrepassar"+id+" option:selected").val();

            if(responsavel == ""){
                alert("Escolha um responsável pela solicitação.");
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/gep/repassar-solicitacao",
                    data: {idSolicitacao: id, responsavel: responsavel},
                    beforeSend: function () {
                    },
                    success: function (dados) {
                        if (dados == "true") {
                            $("#tr"+id).addClass('displayNone');
                            $("#trrrepassar"+id).addClass('displayNone');
                            alert("Escolta repassada com sucesso.")
                        }
                    }
                });
            }
        });

    });

});