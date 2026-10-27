$(document).ready(function(){

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(function (){
        // Where are we going to be putting the webcam stream?
        var cameraContainer = $('#camera');

        // Initialize the jQuery plugin
        cameraContainer.userCam({
            /* Called when the camera starts streaming */
            start: function (event, video) { }
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

    $("#btnAbrirWebcam").click(function(){

        $("#divWebcam, #btnSavePhoto").toggleClass("displayNone");

    });

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

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="' + val.id + '">';
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

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoId').val() != v.id){

                if (v.nome == nomeInterno) {
                    
                    $("#retornoInterno").append('<tr class="">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" src="" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td id="'+ v.id +'">' +
                        '<select id="parentesco" name="parentesco" class="form-control parentesco" required>'+
                        '<option value="">Selecione</option>'+
                        '<option value="AVÔ (Ó)">AVÔ (Ó) do interno</option>'+
                        '<option value="BISAVÔ (Ó)">BISAVÔ (Ó) do interno</option>'+
                        '<option value="COMPANHEIRO (A)">COMPANHEIRO (A) do interno</option>'+
                        '<option value="CONJUGE">CÔNJUGE do interno</option>'+
                        '<option value="CUNHADO (A)">CUNHADO (A) do interno</option>'+
                        '<option value="ENTEADO (A)">ENTEADO (A) do interno</option>'+
                        '<option value="FILHO (A)">FILHO (A) do interno</option>'+
                        '<option value="GENRO">GENRO do interno</option>'+
                        '<option value="IRMÃ (O)">IRMÃ (O) do interno</option>'+
                        '<option value="MADRASTA">MADRASTA do interno</option>'+
                        '<option value="MÃE">MÃE do interno</option>'+
                        '<option value="NETO (A)">NETO (A) do interno</option>'+
                        '<option value="NORA">NORA do interno</option>'+
                        '<option value="PADRASTO">PADRASTO do interno</option>'+
                        '<option value="PAI">PAI do interno</option>'+
                        '<option value="SOBRINHO (A)">SOBRINHO (A) do interno</option>'+
                        '<option value="PRIMO (A)">PRIMO (A) do interno</option>'+
                        '<option value="SOGRO (A)">SOGRO (A) do interno</option>'+
                        '<option value="TIO (A)">TIO (A) do interno</option>'+
                        '<option value="AMIGO (A)">AMIGO (A) do interno</option>'+
                        '<option value="OUTRO">Parentesco não informado</option>'+
                        //'<option value=" -- Vinculo Inativo -- "> -- Vinculo Inativo -- </option>'+
                        '</select>'+
                        '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos'+ v.id+'" value="'+ v.id+'"/>'+
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

    $( document ).on( "change", ".parentesco", function() {

        var idInterno = $(this).parent('td').attr('id');
        var internoParentesco = $(this).val()+'-'+idInterno
        var valueInputHidden = $('#internos'+idInterno).val(internoParentesco);

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
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

    $('#grupoContato').on("click", ".remove_fieldTelefone", function () { //user click on remove text
        $(this).parent('div').remove();
        var id = $(this).parent('div').data("content");
        $("#divTelefoneContato").append('<input type="hidden" name="TelefoneContato[]" id="telefonesRemovidos" value="'+ id +'">');
        if($(".remove_fieldTelefone").length == 0)
        {
            $('#grupoContato').append(
                '<div class="form-group col-sm-3">' +
                '<label for="telefone">Telefone</label>' +
                '<input type="text" name="telefone[]" class="form-control telefone" id="telefone" placeholder="Informe o telefone">' +
                '<i class="fas fa-minus-circle cursorPointer mr-5 remove_field"></i>' +
                '<i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>'+
                '</div>'

            );

        }


    });

//    ------------------------- !! -----------------------------------

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
     * Validacoes e mascaras
     */

    $('#data_nascimento, #rg_data').mask('00/00/0000');
    $('#rg_numero, #cpf, #telefone').mask('0#');
    $('#endereco_numero').mask('0#');
    $('#endereco_cep').mask('00000000');


    //    ------------------------- !! -----------------------------------




});
