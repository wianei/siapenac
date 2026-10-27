$(document).ready(function(){
    $('#dataAtendimento').mask('00/00/0000');
    $('#horaInicio, #horaTermino').mask('00:00');

    var optionValues =[];
    $('#advogado  option').each(function(){
        if($.inArray(this.value, optionValues) >-1){
            $(this).remove()
        }else{
            optionValues.push(this.value);
        }
    });

    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    $( document ).on( "change", "#advogado", function() {

        $("#internosVisitados").html("");

        var idAdv = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../advogado/get-relacao-interno-ajax",
            data: {id: idAdv},
            beforeSend: function() {

            },
            success: function (retorno) {

                $.each(retorno, function (k, v) {

                    $("#divTableInternos").removeClass('displayNone');
                    $("#internosVisitados").append('<tr>' +
                        '<td><input type="checkbox" value="'+ v.id+'" class="checkbox" name="checkInterno[]"></td>' +
                        '<td>'+ v.nome+'</td>' +
                        '<td>'+ v.sigla+'</td>' +
                        '</tr>');

                });


            }
        });

    });

    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ocorrencia/get-fechamento-livro-ajax",
            data: {},
            beforeSend: function() {

            },
            success: function (dados) {

                var diasLiberados = [dados.inicioPlantao,dados.terminoPlantao];

                function datasLiberadas(data) {
                    var sdate = $.datepicker.formatDate( 'dd/mm/yy', data)
                    if($.inArray(sdate, diasLiberados) != -1) {
                        return [true];
                    }
                    return [false];
                }

                $("#dataAtendimento").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    autoclose: true,
                    orientation: "bottom auto"
                });


            }
        });


    });


    //$( function() {
    //    $("#dataAtendimento").datepicker({
    //        dateFormat: 'dd/mm/yy',
    //        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
    //        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
    //        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
    //        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    //        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    //        nextText: 'Próximo',
    //        prevText: 'Anterior',
    //        maxDate: 0,
    //        autoclose: true,
    //        orientation: "bottom auto"
    //    });
    //
    //});

    /**
     * Validacoes e mascaras
     */

    $( document ).on("submit", "#formAtendimentoAdv", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var dataAtendimento = $("#dataAtendimento").val().split("/")[2].substring(0,2);
        if(dataAtendimento != "19" && dataAtendimento != "20")
        {
            $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada entre 1900 e o ano atual.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        }

        var hora = $('#horaInicio').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }

        var horaTermino = $('#horaTermino').val();
        if(horaTermino.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
    });



})