$(document).ready(function(){
    $('#hora').mask('00:00', {reverse: true});
    $('#dataEntrada').mask('00/00/0000');

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

                $("#dataEntrada").datepicker({
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


    $( document ).on("submit", "#formSolicitacao", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }

        var destinoSelecionado = $('#cela').val();
        if(destinoSelecionado == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a cela que o interno será alocado</p></div>');
            control = 1;
        }
        var data_entrada_l = $("#dataEntrada").val();
        if(data_entrada_l.length != 10)
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada válida.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        } else {
            var data_entrada = data_entrada_l.split("/")[2].substring(0,2);
            if(data_entrada != "19" && data_entrada != "20")
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada válida.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
            }
        }
        var hora = $('#hora').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo da Solicitacao do interno.</p></div>');
            control = 1;
        }

        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#submitMudancaCela').attr('disabled', true);
    });
});
