$(document).ready(function (){

    $( function() {
        $("#dataInicioPesquisaAdv").datepicker({
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
                $("#dataFinalPesquisaAdv").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#dataFinalPesquisaAdv").datepicker({
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
                $("#dataInicioPesquisaAdv").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });


    $(document).on("click","#btnPesquisarAdv", function (e) {

        var idInterno = $('#idInterno').val();
        var dataInicio = $("#dataInicioPesquisaAdv").val();
        var dataFinal = $("#dataFinalPesquisaAdv").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/get-atendimento-adv-interno-ajax",
            data: {idInterno: idInterno, dataInicio: dataInicio, dataFinal: dataFinal},
            beforeSend: function() {

            },
            success: function (dados) {

                console.log(dados)

                $('#itensAdvCol').html(" ");

                $.each(dados,function(key, value)
                {
                    $('#itensAdvCol').append(
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
                        '<img id="myImg" src="'+value.fotoAdv+'" alt="Legenda." class="img-thumbnail img-fluid rounded float-left mb-2">'+
                        '</div>'+
                        '<div class="col-md-8">'+
                        '<h5>'+value.nomeAdv+'</h5>'+
                        '<p><strong>OAB: </strong>'+value.oab+''+
                        '<br/><strong>Horário de início do atendimento:</strong> '+value.horaInicio+''+
                        '<br/><strong>Horário de término do atendimento:</strong> '+value.horaTermino+''+
                        '<br/><strong>Duração do contato com o interno:</strong> '+value.duracao+'</p>'+
                        '</div>'+
                        '</div>'
                    );

                });

            }
        });

    });

});
