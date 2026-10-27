$(document).ready(function(){

    $( function() {
        $("#data_saida").datepicker({
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
                $("#data_retorno").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#data_retorno").datepicker({
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
                $("#data_saida").datepicker("option", "maxDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( document ).on( "click", ".finalizar", function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var id = $(this).data("content");
        $.ajax({
            type: "POST",
            url: "/gep/finalizar-escolta",
            data: {idGep: id},
            beforeSend: function () {

            },
            success: function (dados) {
                if(dados == "true") {
                    $("#bt" + id).removeClass('btn-warning').removeClass('btn-danger').removeClass('btn-dark').removeClass('btn-success').addClass('btn-dark').html('Finalizada');
                    $("#acoes" + id).html('<a href="/gep/show/' + id + ')" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>');
                }
                if(dados == "false")
                {
                    alert("É necessário informar a data e hora de retorno!")
                }
            }
        });
    });


    $( document ).on( "click", ".cancelar", function() {

        var id = $(this).data("content");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/gep/cancelar-escolta",
            data: {idGep: id},
            beforeSend: function () {

            },
            success: function (dados) {
                if (dados == "true") {
                    $("#tr" + id).addClass('displayNone');
                }
            }
        });

    });


});