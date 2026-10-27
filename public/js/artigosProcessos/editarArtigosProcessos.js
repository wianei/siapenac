/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 11/02/2020.
 */
$(document).ready(function () {


    $('#dataInquerito, #dataFato, #dataPrisao, #dataProcesso, #dataCondenacaoTransJulgado, #dataCondenacaoSentenca').mask('00/00/0000');
    $('#condenacaoAnos, #condenacaoMeses, #condenacaoDias').mask('000');


    $( document ).on( "click", ".removerLei", function() {

        var idLei = $(this).data("content");
        $("#divLeisRemover").append('<input type="hidden" class="leisRemoverHidden" name="LeisRemover[]" value="'+ idLei +'">');

        if($(".leisRemoverHidden").length > 0)
        {
            $(this).parent('div').remove();

        }else{

            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Existe apenas um dispositivo legal cadastrado, não é possível excluí-lo.</p></div>');
        }



    });


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
        $(this).parent('div').parent('div').remove();

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

    $( document ).on( "change", ".paragrafo", function() {

        var artigo =  $(this).parent("div").siblings(".divArtigo").children(".artigo");
        var divArtigo =  $(this).parent("div").siblings(".divArtigo");
        var paragrafo =  $(this).parent("div").siblings(".divParagrafo").children(".paragrafo");
        var tipoParagrafo =  $(this).parent("div").siblings(".divParagrafo").children("label").children(".tipoParagrafo");
        var divParagrafo =  $(this).parent("div").siblings(".divParagrafo");
        var subDois =  $(this).parent("div").siblings(".divSubDois").children(".subDois");
        var txtSubDois =  $(this).parent("div").siblings(".divSubDois").children("label").children(".txtSubDois");
        var divSubDois =  $(this).parent("div").siblings(".divSubDois");
        var tipoSequenciaUm =  $(this).parent("div").siblings(".tipoSequenciaUm");
        var tipoSequenciaDois =  $(this).parent("div").siblings(".tipoSequenciaDois");
        var tipoSequencia =  $(this).parent("div").siblings(".tipoSequencia");


        subDois.html("");

        var dispositivoLegalPost = $(this).val();
        var tipoSequenciaPost = tipoSequencia.val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../lei/dispositivo-sequencia-dois-ajax",
            data: {dispositivo: dispositivoLegalPost, tipoSequencia: tipoSequenciaPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                divSubDois.removeClass("displayNone");

                txtSubDois.html(objt[0]);
                tipoSequenciaDois.val(objt[2]);

                subDois.append("<option value=''>Selecione</option>");
                $.each(objt[1], function (i, v) {

                    subDois.append('<option value="' + v.id + '">' + v.numero + 'º - ' + v.descricao + '</option>');
                });

            }
        });
    });

    $( document ).on("submit", "#cadastroForm", function() {

        var tipo = $("#tipo").val();
        var varaProcesso = $("#varaProcesso").val();

        $("#flashRetorno").html('');
        var control = 0;

        var qtdAnos = $('#condenacaoAnos').val();
        if(qtdAnos == "" && tipo == "condenacao"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a quantidade de anos da condenação.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#submitMudancaCela').attr('disabled', true);
    });


});

