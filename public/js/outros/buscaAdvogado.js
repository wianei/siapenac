$(document).ready(function(){

    var advogados = [
    ]

    $( document ).on( "keyup", "#advogado", function() {

        $('#filter-records-advogado').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records-advogado').css('display','none');
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
            url: "/../advogado/busca-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchAdvogado" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - OAB: '+val.oab_numero+ ' - Status: '+val.status+'</h6>';
                    output += '</div>';
                    count++;

                    advogados = objt;

                });
                output += '</div>';
                $('#filter-records-advogado').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearchAdvogado", function() {

        $('#tableAdvogados').css('display','block');

        $('#filter-records-advogado').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(advogados, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenAdvogadoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoAdvogado").append('<tr id="tr'+ v.id+'">'+
                        '<td><img style="text-align: center" src="'+ v.foto+'" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.oab_numero+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerAdvogado" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="advogados[]" class="hiddenAdvogadoId" id="advogados" value="'+ v.id+'"/>'+
                        '<a href="/advogado/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#advogado").val("");

    });

    $( document ).on( "click", ".removerAdvogado", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="advogados[]"]').length == 0){
            $('#tableAdvogados').css("display","none");
        }
    });

});