$(document).ready(function() {

    $('#data').mask('00/00/0000');
    $('#hora').mask('00:00', {reverse: true});

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

                $("#data").datepicker({
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

    /*
     *  Progressão e Regressão
     */

    $( document ).on( "change", ".selectUnidadeAlocacao", function() {
        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "unidade_prisionalAlocacao":
                $('#pavilhaoAlocacao, #alaAlocacao, #celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhaoAlocacao';

                break;

            case "pavilhaoAlocacao":
                $('#alaAlocacao, #celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'alaAlocacao';
                break;

            case "alaAlocacao":
                $('#celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'celaAlocacao';
                break;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/get-dados",
            data: {dado: valor},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);

                $.each(objt,function(key, value)
                {
                    $.each(value,function(k, v)
                    {
                        $('#'+selectOp).append('<option value=' + v + '>' + k + '</option>');
                    });

                });

            }
        });

    });
    $(document).on("change", "#tipo", function () {

        var tipo = $(this).val();

        if(tipo == 'progressaosemiaberto'){
            $("#btnSubmitForm").html('<i class="fas fa-check"></i> Realizar transferência com progressão para o semiaberto')
            $('#temporariaDiv').addClass('displayNone');
            $("#temporaria").val("Não");
            $('#diasDiv').addClass('displayNone');
            $("#dias").val("0");
            $('#assinaunidadeDiv').removeClass('displayNone');
            $("#assinaunidade").val("Sim");
            $('#saidasexternasDiv').removeClass('displayNone');
            $("#saidasexternas").val("Não");
            $('#fimsancaoDiv').removeClass('displayNone');
            $("#fimsancao").val("Não");
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
        }
        else if(tipo == 'progressaoaberto'){
            $("#btnSubmitForm").html('<i class="fas fa-check"></i> Realizar transferência com progressão para o aberto')
            $('#temporariaDiv').addClass('displayNone');
            $("#temporaria").val("Não");
            $('#diasDiv').addClass('displayNone');
            $("#dias").val("0");
            $('#assinaunidadeDiv').removeClass('displayNone');
            $("#assinaunidade").val("Sim");
            $('#saidasexternasDiv').addClass('displayNone');
            $("#saidasexternas").val("Não");
            $('#fimsancaoDiv').removeClass('displayNone');
            $("#fimsancao").val("Não");
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
        }
        else if(tipo == 'regressaofechado'){
            $("#btnSubmitForm").html('<i class="fas fa-check"></i> Realizar transferência com regressão para o fechado')
            $('#temporariaDiv').removeClass('displayNone');
            $("#temporaria").val("Não");
            $('#diasDiv').addClass('displayNone');
            $("#dias").val("0");
            $('#assinaunidadeDiv').addClass('displayNone');
            $("#assinaunidade").val("Sim");
            $('#saidasexternasDiv').addClass('displayNone');
            $("#saidasexternas").val("Não");
            $('#fimsancaoDiv').addClass('displayNone');
            $("#fimsancao").val("Não");
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
        }
        else if(tipo == 'regressaosemiaberto'){
            $("#btnSubmitForm").html('<i class="fas fa-check"></i> Realizar transferência com regressão para o semiaberto')
            $('#temporariaDiv').removeClass('displayNone');
            $("#temporaria").val("Não");
            $('#diasDiv').addClass('displayNone');
            $("#dias").val("0");
            $('#assinaunidadeDiv').removeClass('displayNone');
            $("#assinaunidade").val("Sim");
            $('#saidasexternasDiv').removeClass('displayNone');
            $("#saidasexternas").val("Não");
            $('#fimsancaoDiv').addClass('displayNone');
            $("#fimsancao").val("Não");
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
        }
        else if(tipo == ''){
            $("#btnSubmitForm").html('<i class="fas fa-check"></i> Realizar transferência')
            $('#temporariaDiv').addClass('displayNone');
            $("#temporaria").val("Não");
            $('#diasDiv').addClass('displayNone');
            $("#dias").val("0");
            $('#assinaunidadeDiv').addClass('displayNone');
            $("#assinaunidade").val("Não");
            $('#saidasexternasDiv').addClass('displayNone');
            $("#saidasexternas").val("Não");
            $('#fimsancaoDiv').addClass('displayNone');
            $("#fimsancao").val("Não");
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
        }

    });
    $(document).on("change", "#assinaunidade", function () {
        if($(this).val() == "Sim"){
            $('#unidade_prisionalDiv').removeClass('displayNone');
            $('#pavilhaoDiv').removeClass('displayNone');
            $('#alaDiv').removeClass('displayNone');
            $('#celaDiv').removeClass('displayNone');
            $("#saidasexternas").val("Não");
            if($('#tipo').val() == 'progressaosemiaberto' || $('#tipo').val() == 'regressaosemiaberto'){
                $('#saidasexternasDiv').removeClass('displayNone');
            }
            else{
                $('#saidasexternasDiv').addClass('displayNone');
            }
        }
        else{
            $('#unidade_prisionalDiv').addClass('displayNone');
            $('#pavilhaoDiv').addClass('displayNone');
            $('#alaDiv').addClass('displayNone');
            $('#celaDiv').addClass('displayNone');
            $("#saidasexternas").val("Sim");
            $('#saidasexternasDiv').addClass('displayNone');
        }
    });
    $(document).on("change", "#temporaria", function () {

        var sancao = $(this).val();

        if(sancao == 'Sim'){
            $("#dias").val("0");
            $('#diasDiv').removeClass('displayNone');
        }
        else if(sancao == 'Não'){
            $("#dias").val("0");
            $('#diasDiv').addClass('displayNone');
        }
    });

    $('#horaPr').mask('00:00', {reverse: true});
    $('#dataPr').mask('00/00/0000');
    $( function() {

        $("#dataPr").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            maxDate: 0,
            orientation: "bottom auto"
        });


    });


    $( document ).on("submit", "#transferirInternoForm", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var destinoSelecionado = $('#destino').val();
        if(destinoSelecionado == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a unidade de destino.</p></div>');
            control = 1;
        }
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo da transferência.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data da transferência.</p></div>');
            control = 1;
        }
        var hora = $('#hora').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário da transferência no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento, referente a transferência, em anexo.</p></div>');
            control = 1;
        }

        /*
         * validacao progressao e regressao
         */
        var tipo = $('#tipo').val();
        if(tipo  == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o Tipo.</p></div>');
            control = 1;
        }
        var dias = $('#dias').val();
        if(dias  == "" || (dias[0] == "0" && dias.length != 1)){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Digite uma quantidade válida de dias de regressão provisória, não digite zeros à esquerda.</p></div>');
            control = 1;
        }
        var data = $('#dataPr').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data da progressão ou regressão</p></div>');
            control = 1;
        }
        var hora = $('#horaPr').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário da progressão ou regressão no formato 00:00, com os zeros à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var qtdDocumentos = $('#documentosPr')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento, referente a progressão ou regressão, em anexo.</p></div>');
            control = 1;
        }

        var celaAlocacao = $('#celaAlocacao').val();
        var assinaunidade = $('#assinaunidade').val();
        if(celaAlocacao == "" && assinaunidade == "Sim"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Escolha a cela para a alocação do(s) interno(s).</p></div>');
            control = 1;
        }

        if(control == 1){
            return false;
        }
        $('#btnSubmitForm').attr('disabled', true);
    });


});