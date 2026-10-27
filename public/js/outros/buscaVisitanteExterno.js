$(document).ready(function(){

    var visitantesExternos = [
    ]

    $( document ).on( "keyup", "#visitanteExterno", function() {

        $('#filter-records-visitanteExterno').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records-visitanteExterno').css('display','none');
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
            url: "/../controle-acesso/busca-visitante-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchVisitanteExterno" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - CPF: '+val.cpf+ '</h6>';
                    output += '</div>';
                    count++;

                    visitantesExternos = objt;

                });
                output += '</div>';
                $('#filter-records-visitanteExterno').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearchVisitanteExterno", function() {

        $('#tableVisitanteExterno').css('display','block');

        $('#filter-records-visitanteExterno').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(visitantesExternos, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenVisitanteExternoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoVisitanteExterno").append('<tr id="tr'+ v.id+'">'+
                        '<td><img style="text-align: center" src="imagens/usuario/avatarInterno.png" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.cpf+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerVisitanteExterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="visitantesExternos[]" class="hiddenVisitanteExternoId" id="visitanteExternoHidden" value="'+ v.id+'"/>'+
                        '</td>'+
                        '</tr>');
                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#visitanteExterno").val("");

    });

    $( document ).on( "click", ".removerVisitanteExterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="visitantesExternos[]"]').length == 0){
            $('#tableVisitanteExterno').css("display","none");
        }
    });

});