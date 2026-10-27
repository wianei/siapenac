$(document).ready(function(){

    $( document ).on("click", "#btnCarregaAgentes", function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../facade/get-todos-servidores",
            data: {tipo: 'restrito'},
            beforeSend: function() {

            },
            success: function (dados) {

                $('#tableAgentes, .titleAgente').css('display','block');
                $('#divRetornoAgentes').html('').css('display','none');

                var objt = (dados);

                $.each(objt, function(key, v){

                    $("#retornoAgente").append('<tr class="">'+
                        '<td scope="row">'+ v.matricula+'</td>'+
                        '<td><img id="'+ v.cpf+'img" src="'+URL+'/imagens/usuario/avatarInterno.png" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.lotacao+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="agentesHidden[]" id="agentesHidden" class="hiddenServidorId" value="'+ v.matricula+'"/>'+
                        '</td>'+
                        '</tr>');

                });

            }
        });

    });


        $("#data").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
        autoclose: true,
        orientation: "bottom auto",

    });

    $( document ).on("submit", "#formServidores", function() {

        $("#flashRetorno").html('');
        var control = 0;
        var servidoresSelecionados = $('.hiddenServidorId').length;
        if(servidoresSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um servidor.</p></div>');
            control = 1;
        }

        var mesImpressao = $('#mesImpressao').val();
        if(mesImpressao == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o mês de impressão.</p></div>');
            control = 1;
        }

        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de início.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#btnSubmitForm').attr('disabled', true);
    });


});
