/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 11/02/2020.
 */
$(document).ready(function () {

    var interno = [
    ]

    $( document ).on( "keyup", "#interno", function() {

        $('#filter-records').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records').css('display','none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;
        var tipo = $("#tipo").val();
        var consulta = 'restrito';

        if(tipo == "certidaocumprimento"){
            consulta = 'restritoIn';
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: consulta},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário.: '+val.id+' - Regime: '+val.regime+' - Localização: '+val.sigla+'</h6>';
                    output += '</div>';
                    count++;
                    interno = objt;
                });
                output += '</div>';
                $('#filter-records').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearch", function() {



            $('#tableInternos, .titleInterno').css('display', 'block');

            $('#filter-records').html('').css('display', 'none');

            var nomeInterno = $(this).data('name');
            var idInterno = $(this).data('content');

        if($(".hiddenInternoId").length == 0) {

            //verifica se o interno já esta presente na lista de internos e remove da lista da cela
            $("#tr" + idInterno).remove();

            $.each(interno, function (i, v) {

                // verifica se o interno ja esta na lista
                if ($('.hiddenInternoId').val() != v.id) {
                    if (v.id == idInterno) {
                        $("#retornoInterno").append('<tr id="tr' + v.id + '">' +
                            '<td scope="row">' + v.id + '</td>' +
                            '<td><img id="foto_frame" style="text-align: center" class="imagemSrc cursorPointer" src="" alt="Foto do Perfil"></td>' +
                            '<td>' + v.nome + '</td>' +
                            '<td>' + v.faccao + '</td>' +
                            '<td>' + v.lideranca + '</td>' +
                            '<td>' + v.sigla + '</td>' +
                            '<td class="btn-acoes color_01579b">' +
                            '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>' +
                            '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="' + v.id + '"/>' +
                            '<a href="/interno/show/' + v.id + '" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>' +
                            '</td>' +
                            '</tr>');


                        $.ajax({
                            type: "POST",
                            url: "/../interno/interno-fotoprincipal-ajax",
                            data: {idInterno: idInterno},
                            beforeSend: function () {

                            },
                            success: function (dados) {

                                var objt = (dados);
                                document.getElementById('foto_frame').src = dados;

                            }
                        });


                        if ($("#tipo").val() == 'certidaocumprimento' || $("#tipo").val() == 'certidaonaocumprimento') {
                            document.getElementById('interno').disabled = true;
                        }

                        return;
                    }

                }

            });

            $(this).css("display", "none")

            $("#interno").val("");
        }else {
            $(".flash-message").html("<p class='alert alert-danger'>Não é permitido mais de um interno na lista.</p>")
        }

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });

    // ##############################################################################

    $('#dataInquerito, #dataFato, #dataPrisao, #dataProcesso, #dataCondenacaoTransJulgado, #dataCondenacaoSentenca').mask('00/00/0000');
    $('#condenacaoAnos, #condenacaoMeses, #condenacaoDias').mask('000');

    $( document ).on( "change", "#tipo", function() {

        $(".tipoCadastro, #divDispositivoLegal").addClass("displayNone");

        var tipo = $(this).val();

        if(tipo == "inqueritoPolicial")
        {
            $("#divInquerito").removeClass("displayNone");
            $("#divDispositivoLegal").removeClass("displayNone");
        }

        if(tipo == "processo")
        {
            $("#divProcesso").removeClass("displayNone");
            $("#divDispositivoLegal").removeClass("displayNone");
        }

        if(tipo == "condenacao")
        {
            $("#divCondenacao").removeClass("displayNone");
            $("#divDispositivoLegal").removeClass("displayNone");
        }



    });

    // duplica o campo escolaridade
    $( document ).on( "click", "#addDispositivo", function() {
        $("#divDispositivoLegal")
                .clone()
                .find('.resumo')
                .html('')
                .addClass('displayNone')
                .end()
                .prependTo("#divDispositivoLegalClone");
    });
    $( document ).on( "click", ".btnDelDispositivo", function() {
        if($(".btnDelDispositivo").length > 1){
            $(this).parent('div').parent('div').remove();

        }

    });


    $( document ).on( "change", ".selectEstados", function() {

        var idSelect = $(this).attr('id');

        switch (idSelect){
            case "ufInquerito":
                var selectCidade = "municipioInquerito";
                break;

            case "ufProcesso":
                var selectCidade = "municipioProcesso";
                break;
        }

        $('#'+selectCidade).html("");

        var ufPost        = $("#"+idSelect).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../municipios/pesquisa-ajax",
            data: {uf: ufPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);

                $.each(objt,function(key, value)
                {
                    $('#'+selectCidade).append('<option value=' + value + '>' + value + '</option>');
                });

            }
        });

    });

    $( document ).on( "change", ".dispositivoLegal", function() {

        var artigo =  $(this).parent("div").siblings(".divArtigo").children(".artigo");
        var divArtigo =  $(this).parent("div").siblings(".divArtigo");
        var paragrafo =  $(this).parent("div").siblings(".divParagrafo").children(".paragrafo");
        var divParagrafo =  $(this).parent("div").siblings(".divParagrafo");
        var subDois =  $(this).parent("div").siblings(".divSubDois").children(".subDois");
        var divSubDois =  $(this).parent("div").siblings(".divSubDois");
        var divResumo =  $(this).parent("div").siblings(".resumo");


        paragrafo.html("");
        artigo.html("");
        subDois.html("");

        divArtigo.addClass("displayNone");
        divParagrafo.addClass("displayNone");
        divResumo.addClass("displayNone")

            var dispositivoLegalPost = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../lei/artigos-ajax",
                data: {dispositivo: dispositivoLegalPost},
                beforeSend: function() {

                },
                success: function (dados) {
                    divArtigo.removeClass("displayNone");

                    var objt = (dados);

                    artigo.append("<option value=''>Selecione</option>");
                    $.each(objt, function(i, v) {

                        artigo.append('<option value="'+ v.id +'">'+ v.artigo +' - '+ v.nome +'</option>');

                    });
                    
                }
            });
        });


    $( document ).on( "change", ".artigo", function() {
        
            var divResumo =  $(this).parent("div").siblings(".resumo");

            var dispositivoLegalPost = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../lei/get-dados-artigos-ajax",
                data: {dispositivo: dispositivoLegalPost},
                beforeSend: function() {

                },
                success: function (dados) {

                    var objt = (dados);


                    $.each(objt, function(i, v) {

                        divResumo.removeClass("displayNone").html(v.artigo+'<br><strong>'+v.nome+'</strong><br><em>'+v.glossario+'</em>');
                    });
                }
            });


        });
        
//    $( document ).on( "change", ".artigo", function() {
//
//        var artigo =  $(this).parent("div").siblings(".divArtigo").children(".artigo");
//        var divArtigo =  $(this).parent("div").siblings(".divArtigo");
//        var paragrafo =  $(this).parent("div").siblings(".divParagrafo").children(".paragrafo");
//        var tipoParagrafo =  $(this).parent("div").siblings(".divParagrafo").children("label").children(".tipoParagrafo");
//        var divParagrafo =  $(this).parent("div").siblings(".divParagrafo");
//        var subDois =  $(this).parent("div").siblings(".divSubDois").children(".subDois");
//        var divSubDois =  $(this).parent("div").siblings(".divSubDois");
//        var tipoSequenciaUm =  $(this).parent("div").siblings(".tipoSequenciaUm");
//        var tipoSequencia =  $(this).parent("div").siblings(".tipoSequencia");
//
//        paragrafo.html("");
//        subDois.html("");
//        tipoParagrafo.html("");
//
//
//            var dispositivoLegalPost = $(this).val();
//
//            $.ajaxSetup({
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                }
//            });
//
//            $.ajax({
//                type: "POST",
//                url: "/../lei/dispositivo-sequencia-ajax",
//                data: {dispositivo: dispositivoLegalPost},
//                beforeSend: function() {
//
//                },
//                success: function (dados) {
//
//                    divParagrafo.removeClass("displayNone");
//
//                    // hack pra passar a quantidade de itens nos arrays
//                    divSubDois.removeClass("displayNone");
//                    subDois.append("<option value=''>Selecione</option>");
//
//                    var objt = (dados);
//
//                    if(objt[0] == "lei_paragrafo")
//                    {
//                        tipoParagrafo.html("Parágrafo")
//                        tipoSequenciaUm.val("lei_paragrafo")
//                    }
//
//                    if(objt[0] == "lei_inciso")
//                    {
//                        tipoParagrafo.html("Inciso")
//                        tipoSequenciaUm.val("lei_inciso")
//                    }
//
//                    tipoSequencia.val(objt[0]);
//
//                    paragrafo.append("<option value=''>Selecione</option>");
//                    $.each(objt[1], function(i, v) {
//
//                        paragrafo.append('<option value="'+ v.id +'">'+ v.numero+'º - '+ v.descricao +'</option>');
//                    });
//                }
//            });
//
//
//        });
//
//    $( document ).on( "change", ".paragrafo", function() {
//
//        var artigo =  $(this).parent("div").siblings(".divArtigo").children(".artigo");
//        var divArtigo =  $(this).parent("div").siblings(".divArtigo");
//        var paragrafo =  $(this).parent("div").siblings(".divParagrafo").children(".paragrafo");
//        var tipoParagrafo =  $(this).parent("div").siblings(".divParagrafo").children("label").children(".tipoParagrafo");
//        var divParagrafo =  $(this).parent("div").siblings(".divParagrafo");
//        var subDois =  $(this).parent("div").siblings(".divSubDois").children(".subDois");
//        var txtSubDois =  $(this).parent("div").siblings(".divSubDois").children("label").children(".txtSubDois");
//        var divSubDois =  $(this).parent("div").siblings(".divSubDois");
//        var tipoSequenciaUm =  $(this).parent("div").siblings(".tipoSequenciaUm");
//        var tipoSequenciaDois =  $(this).parent("div").siblings(".tipoSequenciaDois");
//        var tipoSequencia =  $(this).parent("div").siblings(".tipoSequencia");
//
//
//        subDois.html("");
//
//            var dispositivoLegalPost = $(this).val();
//            var tipoSequenciaPost = tipoSequencia.val();
//
//            $.ajaxSetup({
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                }
//            });
//
//            $.ajax({
//                type: "POST",
//                url: "/../lei/dispositivo-sequencia-dois-ajax",
//                data: {dispositivo: dispositivoLegalPost, tipoSequencia: tipoSequenciaPost},
//                beforeSend: function() {
//
//                },
//                success: function (dados) {
//
//                    var objt = (dados);
//
//                        divSubDois.removeClass("displayNone");
//
//                        txtSubDois.html(objt[0]);
//                        tipoSequenciaDois.val(objt[2]);
//
//                        subDois.append("<option value=''>Selecione</option>");
//                        $.each(objt[1], function (i, v) {
//
//                            subDois.append('<option value="' + v.id + '">' + v.numero + 'º - ' + v.descricao + '</option>');
//                        });
//
//                }
//            });
//        });
//
    $( document ).on("submit", "#cadastroForm", function() {


        var tipo = $("#tipo").val();
        var varaProcesso = $("#varaProcesso").val();

        $("#flashRetorno").html('');
        var control = 0;

        var emptyvalues = $('.artigo').filter(function(){
            return $(this).val() == ''}).length;
        if (emptyvalues) {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o Dispositivo Legal.</p></div>');
            control = 1;
        }

        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o Interno.</p></div>');
            control = 1;
        }

        if(tipo == "processo" && varaProcesso == "")
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a vara que o processo tramita</p></div>');
            control = 1;
        }

        var qtdAnos = $('#condenacaoAnos').val();
        if(qtdAnos == "" && tipo == "condenacao"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a quantidade de anos da condenação.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#cadastrarFormBtn').attr('disabled', true);

    });


});

