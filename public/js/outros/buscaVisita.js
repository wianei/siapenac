$(document).ready(function(){

    var visitas = [
    ]

    $( document ).on( "keyup", "#visita", function() {

        $('#filter-records-visita').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records-visita').css('display','none');
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
            url: "/../visita/busca-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchVisita" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - CPF: '+val.cpf+ ' - Status: '+val.status+'</h6>';
                    output += '</div>';
                    count++;

                    visitas = objt;

                });
                output += '</div>';
                $('#filter-records-visita').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearchVisita", function() {

        $('#tableVisitas').css('display','block');

        $('#filter-records-visita').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(visitas, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenVisitaId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoVisita").append('<tr id="tr'+ v.id+'">'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" src="'+ v.foto+'" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.cpf+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerVisita" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="visitas[]" class="hiddenVisitaId" id="visitas" value="'+ v.id+'"/>'+
                        '<a href="/visita/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                
                
                    $.ajax({
                        type: "POST",
                        url: "/../visita/visita-fotoprincipal-ajax",
                        data: {idInterno: idInterno},
                        beforeSend: function () {

                        },
                        success: function (dados) {

                            var objt = (dados);
                            document.getElementById('foto_frame'+v.id).src = dados;

                        }
                    });


                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#visita").val("");

    });

    $( document ).on( "click", ".removerVisita", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="visitas[]"]').length == 0){
            $('#tableVisitas').css("display","none");
        }
    });

});