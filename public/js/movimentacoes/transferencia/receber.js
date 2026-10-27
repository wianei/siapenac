$(document).ready(function(){

    $('#diasAdd').mask('000000');
    $('#hora_entrada_r').mask('00:00', {reverse: true});
    $('#data_entrada_r').mask('00/00/0000');


    /**
     * Marca todos os checkboxes da lista de internos por recebimento
     */
    $( document ).on( "click", "#btnCheckTodosInternosRecebimento", function() {
        $("#recebimentoInternos .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoInternosRecebimento tr').addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosRecebimento tr').hasClass('background_bddaff')){
                $('#retornoInternosRecebimento tr').removeClass('background_bddaff');
            }
        }
    });

    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternosCelaReceber", function() {
        $("#celaAlocar .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoInternosCelaReceber tr').addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosCelaReceber tr').hasClass('background_bddaff')){
                $('#retornoInternosCelaReceber tr').removeClass('background_bddaff');
            }
        }
    });

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

    $(document).on("click", "#btnAddTodosInternosR",function(){
        $('#buttonRec').val("Confirmar");
    });

    $(document).on("click", "#btnAddTodosInternosC",function(){
        $('#buttonRec').val("Cancelar");
    });

    $( document ).on("click", ".bntClicacoAcao", function() {
        var valorBt = $(this).val();
        $("#getClickBtn").val(valorBt);
    });

    $( document ).on( "click", "#btnCarregarMaisConfirmRec", function() {

        var skipPost = $("#countSkipConfirmRec").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../movimentacoes/transferencia/internos-pendentes-recebimento-confirmacao-ajax",
            data: {skip: skipPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                console.log(objt);

                $("#countSkipConfirmRec").val(parseInt(skipPost) + parseInt(5));

                $.each(objt, function(i, v) {
                    $("#retornoInternosRecebimento").append('<tr class="" id="'+ v.id+'">'+
                    '<td scope="row"><input type="checkbox" name="internosReceberCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+    
                    '<td scope="row">'+v.id+'</td>'+
                        // '<td scope="row"> --FOTO-- </td>'+
                        '<td><img style="text-align: center" src="'+ URL + '/' + v.foto +'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao_nome+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.regime + '</td>'+
                        '<td>'+ v.lotacao + '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                });

            }


        });


    });


    $( document ).on( "click", "#btnCarregarMaisAlocacaoCela", function() {

        var skipPost = $("#countSkipAlocacaoCela").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../movimentacoes/transferencia/internos-pendentes-alocacao-cela-ajax",
            data: {skip: skipPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                console.log(objt);

                $("#countSkipAlocacaoCela").val(parseInt(skipPost) + parseInt(5));

                $.each(objt, function(i, v) {
                    $("#retornoInternosCelaReceber").append('<tr class="" id="'+ v.id+'">'+
                    '<td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+    
                    '<td scope="row">'+v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ URL + '/' + v.foto +'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.mae+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca + '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                });

            }


        });


    });


    $( document ).on("submit", "#receberInternoForm", function() {

        $("#flashRetornoReceber").html('');
        var control = 0;
        
        var btn = $("#getClickBtn").val();
        var obs = $("#obss").val();
        if(btn == "Cancelar" && obs == ""){
            $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Motive o cancelamento.</p></div>');
            control = 1;
            return false;
        }

        if($('input[name="internosReceberCheck[]"]:checked').length == 0){
            $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Escolha pelo menos um interno.</p></div>');
            control = 1;
            return false;
        }
        
        var dataEntrada = $("#data_entrada_r").val();
        if(dataEntrada == "")
        {
            $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de Entrada.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;

        }
        
        else{
            
            var data_entrada_r = $("#data_entrada_r").val().split("/")[2].substring(0,2);
            if(data_entrada_r != "19" && data_entrada_r != "20")
            {
                $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada entre 1900 e o ano atual.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
            }

        }
        
        var hora = $('#hora_entrada_r').val();
        if(hora.length != 5){
            $("#flashRetornoReceber").html('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var diasTriagem = $('#diasAdd').val();
        if(diasTriagem == "" && document.activeElement.id == "btnAddTodosInternosR"){
            $("#flashRetornoReceber").append('<div class="flash-message"><p class="alert alert-danger">Escolha a quantidade de dias na triagem.</p></div>');
            control = 1;
        }
        
        if(control == 1) {
            return false;
        }
        $('#btnAddTodosInternosC, #btnAddTodosInternosR, #alocaBtn').attr('disabled', true);
    });

    $( document ).on("submit", "#alocarInternoForm", function() {

        $("#flashRetornoAlocacao").html('');

        var control = 0;
        if($('input[name="internosCelaCheck[]"]:checked').length == 0){
            $("#flashRetornoAlocacao").append('<div class="flash-message"><p class="alert alert-danger">Escolha pelo menos um interno para alocar.</p></div>');
            control = 1;
        }
        var celaAlocacao = $('#celaAlocacao').val();
        if(celaAlocacao == ""){
            $("#flashRetornoAlocacao").append('<div class="flash-message"><p class="alert alert-danger">Escolha a cela para a alocação do(s) interno(s).</p></div>');
            control = 1;
        }
        if(control == 1) {
            return false;
        }
        $('#btnAddTodosInternosC, #btnAddTodosInternosR, #alocaBtn').attr('disabled', true);
    });

});
