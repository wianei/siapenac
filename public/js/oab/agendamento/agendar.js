$(document).ready(function(){

    var diasPermitidos = [];

    $( document ).on( "change", "#interno", function() {

        var idInterno = $(this).val();
        $("#dataAtendimento").val("");
        $("#horario").html("").append('')

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../oab/verifica-unidade-interno-ajax",
            data: {idInterno: idInterno},
            beforeSend: function() {

            },
            success: function (dados) {

                diasPermitidos = dados;

                $("#dadosAtendimento").removeClass('displayNone');

            }


        });


    });

       $( function() {

        $("#dataAtendimento").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            minDate: 1,
            beforeShowDay: function(date) {
                var day = date.getDay();
                //return [day != 0 && day != 6, ''];
                return [ diasPermitidos.indexOf(day) == -1 ]
            },
            orientation: "bottom auto"
        });

    });

    $( document ).on( "change", "#dataAtendimento", function() {

        $("#horario").html('Carregando dados...');

        var dataPost = $(this).val();
        var internoPost = $("#interno").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../oab/verifica-horario-ajax",
                data: {data: dataPost, interno: internoPost},
                beforeSend: function() {

                },
                success: function (dados) {

                    $("#horario").html('');
                    var objt = (dados);

                    $.each(objt, function(i, v) {


                            $("#horario").append('<option value="' + v.value + '"> ' + v.nome + '</option>')

                    });


                }


            });


        });

    $( document ).on("submit", "#formCadastro", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var interno = $('#interno').val();
        if(interno == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione um interno.</p></div>');
            control = 1;
        }

        var dataAtendimento = $('#dataAtendimento').val();
        if(dataAtendimento == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a data do atendimento.</p></div>');
            control = 1;
        }

        var horario = $('#horario').val();
        if(horario == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o horário.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
    });



});
