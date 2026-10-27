$(document).ready(function(){


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
        {"prontuario":"123456", "foto": "http://localhost:8000/imagens/usuario/face1.jpg", "nome": "Jose da Silva", "faccao": "PCC", "lideranca": "Sim", "localizacao": "	CP-MOSSORO - PAVILHAO 2 - PRINCIPAL 2 - 02"},
        {"prontuario":"789", "foto": "http://localhost:8000/imagens/usuario/face2.jpg", "nome": "Maria da Silva", "faccao": "PCC", "lideranca": "Não", "localizacao": "	CP-MOSSORO - PAVILHAO 2 - PRINCIPAL 2 - 02"},
        {"prontuario":"6", "foto": "http://localhost:8000/imagens/usuario/face1.jpg", "nome": "João Tiago", "faccao": "Sindicato do RN", "lideranca": "Não", "localizacao": "	CP-MOSSORO - PAVILHAO 2 - PRINCIPAL 2 - 03"}
    ]




    $('#nome').keyup(function(){
        $('#filter-records').css('display','block');

        var searchField = $(this).val();
        if(searchField === '')  {
            $('#filter-records').html('');
            return;
        }

        var regex = new RegExp(searchField, "i");
        var output = '<div class="row">';
        var count = 1;

        $.each(interno, function(key, val){
            if ((val.nome.search(regex) != -1)) {
                output += '<div class="w-100 resultSearch" data-name="'+val.nome+'">';
                output += '<h6>' + val.nome + ' - Pront. '+val.prontuario+ '</h6>';
                output += '</div>';

                //if(count%2 == 0){
                //    output += '</div><div class="row">'
                //}
                count++;

            }
        });
        output += '</div>';
        $('#filter-records').html(output);
    });


    $( document ).on( "click", ".resultSearch", function() {
        $('#tableInternos').css('display','block');

        $('#filter-records').html('').css('display','none');

        var nomeInterno = $(this).data('name');

        $.each(interno, function(i, v) {
            if (v.nome == nomeInterno) {
                $("#retornoInterno").append('<tr class="">'+
                    '<td scope="row">'+ v.prontuario+'</td>'+
                    '<td><img style="text-align: center" src="'+ v.foto+'" class="" alt="Foto do Perfil"></td>'+
                    '<td>'+ v.nome+'</td>'+
                    '<td>'+ v.faccao+'</td>'+
                    '<td>'+ v.lideranca+'</td>'+
                '<td>'+ v.localizacao+'</td>'+
                '<td class="btn-acoes color_01579b">'+
                    '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover desta transferência"><i class="mdi mdi-close-circle-outline"></i></a>'+
                    '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ v.prontuario+'"/>'+
                    '<a href="/interno/show/'+ v.prontuario+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                    '</td>'+
                    '</tr>');
                return;
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


    $( document ).on("submit", "#prisaodomiciliarForm", function() {

        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var hora = $('#hora').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
        $('#prisDomBtn').attr('disabled', true);
    });


    $('#hora').mask('00:00', {reverse: true});
    $('#data').mask('00/00/0000');
    
    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ocorrencia/get-fechamento-livro-ajax",
            data: {},
            beforeSend: function() {

            },
            success: function (dados) {

                var diasLiberados = [dados.inicioPlantao,dados.terminoPlantao];

                function datasLiberadas(data) {
                    var sdate = $.datepicker.formatDate( 'dd/mm/yy', data)
                    if($.inArray(sdate, diasLiberados) != -1) {
                        return [true];
                    }
                    return [false];
                }

                $("#data").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    autoclose: true,
                    orientation: "bottom auto"
                });


            }
        });


    });
});
