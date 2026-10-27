$(document).ready(function(){

    /**
     * busca ala e cela
     */
    $( document ).on( "change", ".selectPvAlaCela", function() {
        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "pavilhaor":
                $('#alar, #celar').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'alar';
                break;

            case "alar":
                $('#celar').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'celar';
                break;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/unidades/get-dados-b",
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


    /**
     * busca pavilhao, ala e cela
     */
    $( document ).on( "change", ".selectUnidade", function() {

        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "unidade_prisional":
                $('#pavilhao, #ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhao';
                break;

            case "pavilhao":
                $('#ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'ala';
                break;

            case "ala":
                $('#cela').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'cela';
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


    // ------------------------------------ REGISTRAR MUDANÇA DE CELA -------------------------------------------------


    /**
     * Ao selecionar a cela adiciona os internos na lista
     */
    $( document ).on( "change", "#cela", function() {

        $("#spanInternosCela").css('display','block');
        $("#retornoInternosCela").html('');

        var cela = $("#cela option:selected").text();
        $("#txtCela").html('Internos da Cela');


        var celaPost = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/listaInternosCela-ajax",
            data: {cela: celaPost, restrito: true},
            beforeSend: function() {
                    $("#retornoInternosCela").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');

            },
            success: function (dados) {
                $("#retornoInternosCela").html('');
                var objt = (dados);

                $.each(objt, function(i, v) {
                    $("#retornoInternosCela").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto+'" data-content="'+v.fototipo+','+v.fotolegenda+','+v.fotodatainserida+','+v.fotodataalterada+'" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla + ' - ' + v.pvNome + ' - ' + v.ala + ' - ' + v.cela + '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</td>'+
                        '</tr>');
                });

                // verifica se o interno já esta presente na lista de internos e remove da lista da cela
                $('input[name="internos[]"]').each(function() {
                    var idRemover = $(this).val();
                    $("#"+idRemover).remove();
                });

                celaInternos = objt;

            }


        });


    });

    /**
     * adiciona os internos na lista de internos
     */
    $( document ).on( "click", "#btnAddTodosInternos", function() {

        $('input[name="internosCelaCheck[]"]:checked').each(function() {

            var prontuario = this.value;
            //alert(this.value);

            $.each(celaInternos, function(key, val){

                if ((val.id == prontuario)) {

                    $("#retornoInterno").append('<tr class="" id="tr'+ val.id+'">'+
                        '<td scope="row">'+ val.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ val.foto+'" data-content="'+val.fototipo+','+val.fotolegenda+','+val.fotodatainserida+','+val.fotodataalterada+'" alt="Foto do Perfil"></td>'+
                        '<td>'+ val.nome+'</td>'+
                        '<td>'+ val.faccao+'</td>'+
                        '<td>'+ val.lideranca+'</td>'+
                        '<td>'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-content="'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover desta Mudança de Cela"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" id="internos" class="hiddenInternoId" value="'+ val.id+'"/>'+
                        '<a href="/interno/show/'+ val.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;

                }
            });

            $('#tableInternos, .titleInterno').css('display','block');
            $("#spanInternosCela").css('display','none');
            //$("#cela").html('<option value="">Selecione</option>');
            $("#cela option[value='']").removeAttr('selected').attr('selected','selected');

        });

    });

    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternosCela", function() {
        $("#internosCela .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoInternosCela').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosCela').children().hasClass('background_bddaff')){
                $('#retornoInternosCela').children().removeClass('background_bddaff');
            }
        }
    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos, .titleInterno').css("display","none");
        }
        $("#spanInternosCela").css('display','none');
        $("#cela option[value='']").attr('selected','selected');
        $("#cela option[value='']").removeAttr('selected');


    });

    $( document ).on( "change", "#celar", function() {
        var celaId = $(this).val();
        if(celaId == ""){
            $('#tableInternosDestino').addClass('displayNone');
            $('#celaDestinoSelecionada').addClass('displayNone');
            return false;
        }

        $('#tableInternosDestino').removeClass('displayNone');

        $('#retornoInternosCelaDestino').html('');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/listaInternosCela-ajax",
            data: {cela: celaId},
            beforeSend: function() {
                $("#retornoInternosCelaDestino").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');
            },
            success: function (dados) {
                $("#retornoInternosCelaDestino").html('');
                var objt = (dados);

                if(objt.length == 0){
                    $('#tableInternosDestino').addClass('displayNone');
                    $('#celaDestinoSelecionada').removeClass('displayNone').html('<h5><span id="txtCela"></span>Internos que estão na cela de destino <i class="mdi mdi-arrow-bottom-right"></i></h5>Não há internos na cela selecionada.');
                }else{
                    $('#celaDestinoSelecionada').addClass('displayNone');
                }

                $.each(objt, function(i, v) {
                    $("#retornoInternosCelaDestino").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto+'" data-content="'+v.fototipo+','+v.fotolegenda+','+v.fotodatainserida+','+v.fotodataalterada+'" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla + ' - ' + v.pvNome + ' - ' + v.ala + ' - ' + v.cela + '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</td>'+
                        '</tr>');
                });

                // verifica se o interno já esta presente na lista de internos e remove da lista da cela
                $('input[name="internos[]"]').each(function() {
                    var idRemover = $(this).val();
                    $("#"+idRemover).remove();
                });

                celaInternos = objt;

            }


        });

        var internosSelecionados = $('input[name="internos[]"]').serialize();

        /**
         * inicio ajax
         */

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/get-sancao-triagem-ativa",
            data: {internos: internosSelecionados},
            beforeSend: function() {

            },
            success: function (dados) {

                $("#mensagemRetornoSancaoTriagem").html('').append('<strong>Atenção! Internos que estão cumprindo Sanção Disciplinar ou Triagem<br><br></strong>');

                $.each(dados, function(key, val){

                    $("#flashRetornoSancaoTriagem").removeClass('displayNone')
                    $("#mensagemRetornoSancaoTriagem").append(val.id+' - '+val.nome+' - '+val.tipo+' Até: '+val.dataFim+'<br>');


                });

                $("#mensagemRetornoSancaoTriagem").append('<em><br>Para encerar a sanção disciplinar ou triagem do(s) interno(s) acima <a href="../interno/saida-sancao-triagem"> clique aqui</a>, selecione o interno e altere a cela. Caso contrário, o(s) interno(s) continuará(ão) aparecendo na lista de cumprimento da Sanção ou Triagem.</em>');

            }


        });


        // !ajax

    });

    $( document ).on( "change", "#tipoMudanca", function() {

        var tipo = $(this).val();

        if(tipo == "sancao" || tipo == "triagem")
        {
            $("#divAddDias").removeClass("displayNone");
        }else{
            $("#divAddDias").addClass("displayNone");
            $("#diasAdd").val("");
        }

    });

    $('#diasAdd').mask('000');

    $( document ).on("submit", "#mudancaCelaForm", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var destinoSelecionado = $('#celar').val();
        if(destinoSelecionado == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a cela de destino.</p></div>');
            control = 1;
        }
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo da mudança de cela.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var hora = $('#hora').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var tipoMudanca = $('#tipoMudanca').val();
        if(tipoMudanca == "sancao" || tipoMudanca == "triagem"){
            var dias = $('#diasAdd').val();
            if(dias == ""){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a quantidade de dias restantes de sanção disciplinar ou de triagem.</p></div>');
                control = 1;
            }
        }

        if(control == 1) {
            return false;
        }
        $('#submitMudancaCela').attr('disabled', true);
    });

    $('#hora').mask('00:00', {reverse: true});
    $('#data').mask('00/00/0000');

//    $("#data").datepicker({
//        dateFormat: 'dd/mm/yy',
//        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
//        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
//        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
//        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
//        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
//        nextText: 'Próximo',
//        prevText: 'Anterior',
//        autoclose: true,
//        orientation: "bottom auto"
//    });

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

});