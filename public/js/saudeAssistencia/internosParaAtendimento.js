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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'restrito'},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário.: '+val.id+ ' - Localização: '+val.sigla+'</h6>';
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

        $('#tableInternos').css('display','block');

        $('#filter-records').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        /**
         * nao e necesario a verificacao de interno presente na lista, uma vez que o mesmo pode requerer atendimento para outas areas
         */
        //$("#tr"+idInterno).remove();

        $.each(interno, function(i, v) {

            // verifica se o interno ja esta na lista
            /**
             * nao e necesario a verificacao de interno presente na lista, uma vez que o mesmo pode requerer atendimento para outas areas
             */
            //if($('.hiddenInternoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoInterno").prepend('<tr id="tr'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" class="imagemSrc cursorPointer" src="'+ v.foto+'" data-content="'+v.fototipo+','+v.fotolegenda+','+v.fotodatainserida+','+v.fotodataalterada+'" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td scope="row"><select id="tipoAtendimento" name="tipoAtendimento[]" class="form-control parentesco" required>'+
                        '<option value="">Selecione</option>'+
                        '<option value="Médico">Médico</option>'+
                        '<option value="Odontológico">Odontológico</option>'+
                        '<option value="Assistente Social">Assistente Social</option>'+
                        '<option value="Assistência Jurídica">Assistência Jurídica</option>'+
                        '<option value="Psicológico">Psicológico</option>'+
                        '</select></td>'+
                        '<td scope="row"><select id="prioridade" name="prioridade[]" class="form-control parentesco" required>'+
                        '<option value="">Selecione</option>'+
                        '<option value="A">Alta</option>'+
                        '<option value="B">Média</option>'+
                        '<option value="C">Baixa</option>'+
                        '</select></td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+

                        '<a href="#" data-toggle="tooltip" data-placement="top" data-content="'+ v.id+'" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+

                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ v.id+'"/>'+

                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+

                        '</td>'+
                        '</tr><tr id="trText'+ v.id+'"><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><textarea name="descricao[]" id="descricao" class="form-control" placeholder="Descrição breve da situação do interno" required></textarea></td></tr>');
                        
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
                
                    return;
                }

            //} //fim da verificacao de interno presente na lista

        });

        $(this).css("display","none")

        $("#interno").val("");

        $("#btnSave").removeClass('btn-primary').addClass('btn-success');


    });

    $( document ).on( "click", ".removerInterno", function() {
        var idInterno = $(this).attr("data-content");
        $("#trText"+idInterno).remove();

        $(this).parent('td').parent('tr').remove();

        if($('select[name="tipoAtendimento[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }

        $("#btnSave").removeClass('btn-primary').addClass('btn-success');

    });

    $( document ).on( "click", ".removerInternosCadastrados", function() {

        var idInterno = $(this).data("content");
        //$("#trText"+idInterno).remove();

        $(this).parent('td').parent('tr').next('tr').remove();
        $(this).parent('td').parent('tr').remove();

        if($('select[name="tipoAtendimento[]"]').length == 0){
            $('#tableInternos, .titleAgente').css("display","none");
        }

        $("#btnSave").removeClass('btn-primary').addClass('btn-success');

        $("#tableInternos").append('<input type="hidden" name="internosRemover[]" id="agentesRemover" value="'+ idInterno +'">')
    });


});