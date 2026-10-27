$(document).ready(function(){

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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'geral'},
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
        $("#tr"+idInterno).remove();

        $.each(interno, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoInterno").append('<tr id="tr'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto +'" data-content="'+ v.fototipo +','+ v.fotolegenda +','+ v.fotodatainserida +','+ v.fotodataalterada +'" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.periculosidade+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ v.id+'"/>'+
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

        $("#interno").val("");

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });


    $("#data_saida").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
        minDate: 0,
        autoclose: true,
        orientation: "bottom auto"
    });

    $( document ).on( "change", "#tipoEscolta", function() {
        var tipo = $(this).val();

        if(tipo == "diversas")
        {
            $("#divDestino").removeClass('displayNone');
            $("#divDestinoVara").addClass('displayNone');
            $("#varaDestino").val("");
        }
        if(tipo == "audiencia")
        {
            $("#divDestinoVara").removeClass('displayNone');
            $("#destino").val("");
            $("#divDestino").addClass('displayNone');
        }
    });


    $('#data_saida').mask('00/00/0000');
    $('#horarioEscolta').mask('00:00');

    $( document ).on("submit", "#formNovaSolicitacao", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var tipoEscolta = $("#tipoEscolta").val();
        if(tipoEscolta == "diversas") {

            var destino = $('#destino').val();
            if (destino == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o destino.</p></div>');
                control = 1;
            }
        }

        if(tipoEscolta == "audiencia") {

            var destinoVara = $('#varaDestino').val();
            if (destinoVara == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o destino.</p></div>');
                control = 1;
            }
        }

        var responsavel = $('#responsavel').val();
        if(responsavel == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o responsável.</p></div>');
            control = 1;
        }

        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o Motivo.</p></div>');
            control = 1;
        }

        //var hora = $('#hora').val();
        //if(hora.length != 5){
        //    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
        //    control = 1;
        //}

        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
    });



});