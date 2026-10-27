$(document).ready(function(){

    var interno = [
    ]

    //$( document ).on("blur", "#interno", function() {
    //    $('#filter-records').css('display','none');
    //});


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

        $('#tableInternos, .titleInterno').css('display','block');

        $('#filter-records').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //http://siapen2.sejuc.rn.gov.br/storage/app/arquivos/internos/fotos/2511997/2511997.jpg

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(interno, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoId').val() != v.id){
                if (v.id == idInterno) {
                    $("#retornoInterno").append('<tr id="tr'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" class="imagemSrc cursorPointer" src="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ v.id+'"/>'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');


                    $.ajax({
                        type: "POST",
                        url: "/../interno/interno-fotoprincipal-ajax",
                        data: {idInterno: idInterno},
                        beforeSend: function () {

                        },
                        success: function (dados) {

                            var objt = (dados);
                            document.getElementById('foto_frame'+v.id).src = dados;

                        }
                    });


                    if($("#tipo").val() == 'certidaocumprimento' || $("#tipo").val() == 'certidaonaocumprimento'){
                        document.getElementById('interno').disabled = true;
                    }

                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#interno").val("");

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });

});