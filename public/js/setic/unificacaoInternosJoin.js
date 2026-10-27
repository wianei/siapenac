$(document).ready(function(){

    $( document ).on( "click", function(e) {
        if(e.target.id != "internoA"){
            $('#filter-recordsA').css('display','none');
        }
        else if(e.target.id == "internoA"){
            $('#filter-recordsA').css('display','block');
        }
        if(e.target.id != "internoB"){
            $('#filter-recordsB').css('display','none');
        }
        else if(e.target.id == "internoB"){
            $('#filter-recordsB').css('display','block');
        }
    });

    var internoA = [
    ]

    $( document ).on( "keyup", "#internoA", function() {

        $('#filter-recordsA').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-recordsA').css('display','none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'geralAtIn'},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){
                    output += '<div class="w-100 resultSearchA" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário: '+val.id+ ' - Localização: '+val.sigla+'</h6>';
                    output += '</div>';
                    count++;
                    internoA = objt;
                });
                output += '</div>';
                $('#filter-recordsA').html(output);

            }
        });
    });

    var internoB = [
    ]

    $( document ).on( "keyup", "#internoB", function() {

        $('#filter-recordsB').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-recordsB').css('display','none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'geralAtIn'},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchB" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário: '+val.id+ ' - Localização: '+val.sigla+'</h6>';
                    output += '</div>';
                    count++;
                    internoB = objt;
                });
                output += '</div>';
                $('#filter-recordsB').html(output);

            }
        });
    });

    $( document ).on( "click", ".resultSearchA", function() {

        $('#tableInternosA').css('display','block');

        $('#filter-recordsA').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        $("#retornoInternoA").empty();
        if($("#Btr"+idInterno).length){
            $("#Btr"+idInterno).remove();
            $('#tableInternosB').css('display','none');
        }

        for( var i = 0; i < internoA.length; i++){
            if ( internoA[i].id != idInterno) {
                internoA.splice(i, 1);
            }
        }
        $.each(internoA, function(i, v) {

            if(v.status == 'I'){
                var cor = 'class="background_ffd7d7"';
            }

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoIdA').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoInternoA").append('<tr id="Atr'+ v.id+'" '+cor+'>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto +'" data-content="'+ v.fototipo +','+ v.fotolegenda +','+ v.fotodatainserida +','+ v.fotodataalterada +'" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.mae+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInternoA" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="idA[]" class="hiddenInternoIdA" id="internos" value="'+ v.id+'"/>'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;
                }

            }

        });

        // foto modal
        $(".imagemSrc").on("click", function() {
            $('#imagepreview').attr('src', $(this).attr('src'));
            $('#imagemodal').modal('show');
            var datacontent = $(this).attr("data-content").split(',');
            var fototipo = datacontent[0];
            var fotolegenda = datacontent[1];
            var fotodatainserida = datacontent[2];
            $('#fototipo').html("Tipo: "+fototipo+"<br/>");
            $('#fotolegenda').html("Descrição: "+fotolegenda+"<br/>");
            $('#fotodatainserida').html("Data da inserção da foto: "+fotodatainserida+"<br/>");
        });

        $(this).css("display","none")

        $("#internoA").val("");

    });

    $( document ).on( "click", ".removerInternoA", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="idA[]"]').length == 0){
            $('#tableInternosA').css("display","none");
        }
    });

    $( document ).on( "click", ".resultSearchB", function() {

        $('#tableInternosB').css('display','block');

        $('#filter-recordsB').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        $("#retornoInternoB").empty();
        if($("#Atr"+idInterno).length){
            $("#Atr"+idInterno).remove();
            $('#tableInternosA').css('display','none');
        }
        for( var i = 0; i < internoB.length; i++){
            if ( internoB[i].id != idInterno) {
                internoB.splice(i, 1);
            }
        }
        $.each(internoB, function(i, v) {

            if(v.status == 'I'){
                var cor = 'class="background_ffd7d7"';
            }

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoIdB').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoInternoB").append('<tr id="Btr'+ v.id+'" '+cor+'>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto +'" data-content="'+ v.fototipo +','+ v.fotolegenda +','+ v.fotodatainserida +','+ v.fotodataalterada +'" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.mae+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInternoB" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="idB[]" class="hiddenInternoIdB" id="internos" value="'+ v.id+'"/>'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;
                }

            }

        });

        // foto modal
        $(".imagemSrc").on("click", function() {
            $('#imagepreview').attr('src', $(this).attr('src'));
            $('#imagemodal').modal('show');
            var datacontent = $(this).attr("data-content").split(',');
            var fototipo = datacontent[0];
            var fotolegenda = datacontent[1];
            var fotodatainserida = datacontent[2];
            $('#fototipo').html("Tipo: "+fototipo+"<br/>");
            $('#fotolegenda').html("Descrição: "+fotolegenda+"<br/>");
            $('#fotodatainserida').html("Data da inserção da foto: "+fotodatainserida+"<br/>");
        });

        $(this).css("display","none")

        $("#internoB").val("");

    });
    
    $( document ).on( "click", ".removerInternoB", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="idB[]"]').length == 0){
            $('#tableInternosB').css("display","none");
        }
    });


    $( document ).on("submit", "#proximo", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var idA = $('input[name="idA[]"]').length;
        var idB = $('input[name="idB[]"]').length;
        if(idA == 0 || idB == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o interno principal e o que será excluído.</p></div>');
            control = 1;
        }

        if(control == 1) return false;
    });


    $( document ).on("submit", "#campos", function() {
        $('#unificarBtn').attr('disabled', true);
    });


    $('#data_nascimento, #rg_data').mask('00/00/0000');
    $('#cpf').mask('00000000000');
    $('.ceps').mask('00000-000');





    $( document ).on("click", "#buttonNomeUsarA", function(){
        $('#nome').val(($('#nomeUsarA').text()).substring(($('#nomeUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonNomeUsarB", function(){
        $('#nome').val($('#nomePrincipalB').val());
    });

    $( document ).on("click", "#buttonMaeUsarA", function(){
        $('#mae').val(($('#maeUsarA').text()).substring(($('#maeUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonMaeUsarB", function(){
        $('#mae').val($('#nomeMaeB').val());
    });

    $( document ).on("click", "#buttonPaiUsarA", function(){
        $('#pai').val(($('#paiUsarA').text()).substring(($('#paiUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonPaiUsarB", function(){
        $('#pai').val($('#nomePaiB').val());
    });

    $( document ).on("click", "#buttonCpfUsarA", function(){
        $('#cpf').val(($('#cpfUsarA').text()).substring(($('#cpfUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonCpfUsarB", function(){
        $('#cpf').val($('#cpfB').val());
    });

    $( document ).on("click", "#buttonrg_numeroUsarA", function(){
        $('#rg_numero').val(($('#rg_numeroUsarA').text()).substring($('#rg_numeroUsarA').text().indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonrg_numeroUsarB", function(){
        $('#rg_numero').val($('#rg_numeroB').val());
    });

    $( document ).on("click", "#buttonrg_orgaoexpedidorUsarA", function(){
        $('#rg_orgaoexpedidor').val(($('#rg_orgaoexpedidorUsarA').text()).substring(($('#rg_orgaoexpedidorUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonrg_orgaoexpedidorUsarB", function(){
        $('#rg_orgaoexpedidor').val($('#rg_orgaoexpedidorB').val());
    });

    $( document ).on("click", "#buttonrg_dataUsarA", function(){
        $('#rg_data').val(($('#rg_dataUsarA').text()).substring(($('#rg_dataUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonrg_dataUsarB", function(){
        $('#rg_data').val($('#rg_dataB').val());
    });

    $( document ).on("click", "#buttonAlturaUsarA", function(){
        $('#altura').val(($('#alturaUsarA').text()).substring(($('#alturaUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonAlturaUsarB", function(){
        $('#altura').val($('#alturaB').val());
    });

    $( document ).on("click", "#buttonPesoUsarA", function(){
        $('#peso').val(($('#pesoUsarA').text()).substring(($('#pesoUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonPesoUsarB", function(){
        $('#peso').val($('#pesoB').val());
    });

    $( document ).on("click", "#buttoncor_cabelosUsarA", function(){
        $('#cor_cabelos').val(($('#cor_cabelosUsarA').text()).substring(($('#cor_cabelosUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttoncor_cabelosUsarB", function(){
        $('#cor_cabelos').val($('#cor_cabelosB').val());
    });

    $( document ).on("click", "#buttondata_nascimentoUsarA", function(){
        $('#data_nascimento').val(($('#data_nascimentoUsarA').text()).substring(($('#data_nascimentoUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttondata_nascimentoUsarB", function(){
        $('#data_nascimento').val($('#data_nascimentoB').val());
    });

    $( document ).on("click", "#buttonfaccao_postoUsarA", function(){
        $('#faccao_posto').val(($('#faccao_postoUsarA').text()).substring(($('#faccao_postoUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonfaccao_postoUsarB", function(){
        $('#faccao_posto').val($('#faccao_postoB').val());
    });

    $( document ).on("click", "#buttonfaccao_padrinhoUsarA", function(){
        $('#faccao_padrinho').val(($('#faccao_padrinhoUsarA').text()).substring(($('#faccao_padrinhoUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonfaccao_padrinhoUsarB", function(){
        $('#faccao_padrinho').val($('#faccao_padrinhoB').val());
    });

    $( document ).on("click", "#buttonfaccao_quebradaUsarA", function(){
        $('#faccao_quebrada').val(($('#faccao_quebradaUsarA').text()).substring(($('#faccao_quebradaUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonfaccao_quebradaUsarB", function(){
        $('#faccao_quebrada').val($('#faccao_quebradaB').val());
    });

    $( document ).on("click", "#buttondocumento_entrada_numeroUsarA", function(){
        $('#documento_entrada_numero').val(($('#documento_entrada_numeroUsarA').text()).substring(($('#documento_entrada_numeroUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttondocumento_entrada_numeroUsarB", function(){
        $('#documento_entrada_numero').val($('#documento_entrada_numeroB').val());
    });

    $( document ).on("click", "#buttonobservacaoUsarA", function(){
        $('#observacao').val(($('#observacaoUsarA').text()).substring(($('#observacaoUsarA').text()).indexOf(":") + 2));
    });
    $( document ).on("click", "#buttonobservacaoUsarB", function(){
        $('#observacao').val($('#observacaoB').val());
    });


});