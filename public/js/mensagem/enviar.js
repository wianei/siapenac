$(document).ready(function(){

    $("#presidio").change(function(){
        $("#divPavilhao").removeClass('displayNone');
    });

    $("#enviar_mensagem_para").change(function(){
        $("#divPresidio, #divPavilhao, #divRowAgentes, .btnAddDestinatario").css("display", "none")
        var opcao = $(this).val();
        switch (opcao) {
            case "unidade":
                $("#divPresidio, .btnAddDestinatario").show("fast");
                $(".btnAddDestinatario").click(function(){
                    addDestinatario("unidade");
                });
                break;

            case "pavilhao":
                $("#divPresidio, #divPavilhao, .btnAddDestinatario").show("fast");
                $(".btnAddDestinatario").click(function(){
                    addDestinatario("pavilhao");
                });
                break;

            case "servidor":
                $("#divRowAgentes").show("fast");
                break;
        }
    });

    function addDestinatario(tipoDestinatario)
    {
        if(tipoDestinatario == "unidade")
        {
            var nomeDestinatario = $("#presidio option:selected").text();
            var idDestinatario = "un."+$("#presidio option:selected").val();
        }
        if(tipoDestinatario == "pavilhao")
        {
            var nomeDestinatario = $("#presidio option:selected").text()+ ' - '+ $("#pavilhao option:selected").text();
            var idDestinatario = $("#pavilhao option:selected").val();
        }
        if($("#presidio option:selected").val() == "")
        {
            return false;
        }
        $("#divDestinatarios").append('<div class="destinatariosDiv">'+
            '<span>'+ nomeDestinatario +'</span>'+
            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>'+
            '<input type="hidden" name="destimatario[]" id="destimatarioHidden" value="'+ idDestinatario +'"/>'+
            '</div>');

    }

    CKEDITOR.replace( 'descricao' );


    /**
     * busca agente
     * @type {*[]}
     */
    var agente = [];


    $('#agente').keyup(function(){


        $('#divRetornoAgentes').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#divRetornoAgentes').html('').css('display','none');
            return;
        }


        var regex = new RegExp(searchField, "i");
        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../facade/busca-servidor-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchAgente" data-name="'+val.nome+'" data-content="'+val.cpf+'">';
                    output += '<h6>' + val.nome + ' - Matrícula. '+val.matricula+ '</h6>';
                    output += '</div>';
                    count++;

                    agente = objt;

                });
                output += '</div>';
                $('#divRetornoAgentes').html(output);

            }
        });

    });


    $( document ).on( "click", ".resultSearchAgente", function() {

        $('#tableAgentes, .titleAgente').css('display','block');

        $('#divRetornoAgentes').html('').css('display','none');

        var nomeAgentes = $(this).data('name');
        var cpfAgente = $(this).data('content');

        $.each(agente, function(i, v) {
            if (v.nome == nomeAgentes) {
                $("#divDestinatarios").append('<div class="destinatariosDiv">'+
                    '<span>'+ v.nome+'</span>'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>'+
                    '<input type="hidden" name="destimatario[]" id="destimatarioHidden" value="'+ v.matricula+'"/>'+
                    '</div>');
                return;

            }
        });

        $(this).css("display","none")

        $("#agente").val("");

    });

    $( document ).on( "click", ".removerAgentes", function() {
        $(this).parent('div').remove();
        if($('input[name="agentesHidden[]"]').length == 0){
            $('#tableAgentes, .titleAgente').css("display","none");

        }
    });


});