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

        $.each(interno, function(i, v) {

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


    $(function () {
        // Where are we going to be putting the webcam stream?
        var cameraContainer = $('#camera');

        // Initialize the jQuery plugin
        cameraContainer.userCam({
            /* Called when the camera starts streaming */
            start: function (event, video) {
            }
        });

        // When our snapshot button is clicked, take a Snapshot!
        $('#btnSavePhoto').on('click', function (e) {
            // Get the userCam object from the cameraContainer data
            var dataURI = cameraContainer.data('userCam').getStill();

            // Create a new image with a src of the dataUri and append to the body
            $('<input>', {
                type: 'hidden',
                name: 'imagens[]',
                value: dataURI,

            }).appendTo($("#imgOut"));

            $('<img>', {
                src: dataURI,
                alt: 'foto da visita'
            }).appendTo($("#imgOut"));
        });

    });

    /**
     * adiciona mais campos de telefone
     */
    $( document ).on( "click", ".btAddFone", function() {

        $('#grupoContato').append(
            '<div class="form-group col-sm-3">' +
            '<label for="telefone">Telefone</label>' +
            '<input type="text" name="telefone[]" class="form-control telefone" placeholder="Informe o telefone">' +
            '<i class="fas fa-minus-circle cursorPointer remove_field"></i>' +
            '</div>'
        );

        $('.telefone').mask('0#');


        $('#grupoContato').on("click", ".remove_field", function () { //user click on remove text
            $(this).parent('div').remove();
        });


    });
//    ------------------------- !! -----------------------------------


    $("#btnAbrirWebcam").click(function () {

        $("#divWebcam, #btnSavePhoto").toggleClass("displayNone");

    });

    /**
     * Adiciona mais Documentos
     */
    $( document ).on( "click", ".btAddDoc", function() {

        $('#grupoDoc').append(
            '<div class="form-row width100Percent">'+
            '<div class="form-group col-sm-3">'+
            '<label for="doc">Documento</label>'+
            '<input type="file" name="inputFileDoc[]" class="form-control" id="inputFileDoc" accept="application/pdf">'+
            '</div>'+

            '<div class="form-group col-sm-3">'+
            '<label for="nomeDoc">Nome</label>'+
            '<input type="text" name="nomeDoc[]" class="form-control" id="nomeDoc" placeholder="Informe um nome ou descrição">'+
            '<i class="fas fa-minus-circle cursorPointer removeFieldDoc"></i>' +
            '</div>'+
            '</div>'
        );


    });

    $('#grupoDoc').on("click", ".removeFieldDoc", function () { //user click on remove text
        $(this).parent('div').parent('div').remove();
    });


//    ------------------------- !! -----------------------------------

    /**
     * faz a validação do cpf
     */
    function validaCpf(strCPF) {
        var Soma;
        var Resto;
        Soma = 0;
        if (strCPF == "00000000000") return false;

        for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

        Soma = 0;
        for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
        return true;
    }


    /**
     * Validacoes e mascaras
     */

    $("#cpf").blur(function(){
        var cpf = $("#cpf").val();
        var validacaoCpf = validaCpf(cpf);
        if(validacaoCpf == false)
        {
            $(this).focus();
            $("#cpf").css("border", "1px solid #CC0000")
        } else {

            $("#cpf").css("border", "1px solid #0d47a1")

        }
    });

    $('#nome').bind('keyup blur',function(){
        var node = $(this);
        node.val(node.val().replace(/[0-9]/g,'') ); }
    );
    $('#rg_data').mask('00/00/0000');
    $('#rg_numero, #telefone').mask('0#');
    $('#cpf').mask('00000000000', {reverse: true});


    $( document ).on("submit", "#formCadastro", function() {
        $("#flashRetorno").html('');

        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }

        if(control == 1) return false;
    });


});

