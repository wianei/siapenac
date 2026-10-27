$(document).ready(function(){

    $( function() {
        $("#data_saida, #data_retorno").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            orientation: "bottom auto"
        });

    });

    /**
     * lista de internos pendentes
     */
    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $(document).on("click", ".checkbox", function () {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    /**
     * Marca todos os checkboxes da lista de internos por recebimento
     */
    $(document).on("click", "#btnCheckTodosInternosRecebimento", function () {
        $("#recebimento .checkbox").prop('checked', $(this).is(":checked"));
        if ($(this).is(":checked")) {
            $('#retornoInternosRecebimento').children().addClass('background_bddaff');
        }
        else {
            if ($('#retornoInternosRecebimento').children().hasClass('background_bddaff')) {
                $('#retornoInternosRecebimento').children().removeClass('background_bddaff');
            }
        }
    });


    $(document).on("click", "#btnAddTodosInternos", function () {

        $('#tableInternos, .titleInterno').css('display', 'block');

        $('#retornoInternosRecebimento tr').each(function () {

            if ($(this).hasClass('background_bddaff')) {
                // $('#spanInternosCelaRecebimento').removeClass('displayNone');
                $("#retornoInterno").append($(this));
                $("#retornoInterno .checkboxTD").addClass('displayNone');
                $("#retornoInterno .botaoCancelar").removeClass('displayNone');
            }

        });

        if($('#retornoInternosRecebimento').children().length == 0){
            $('#spanInternosCela').addClass('displayNone')
        }

    });

    $(document).on("click", ".botaoCancelar", function () {
        $("#spanInternosCela").removeClass('displayNone');
        $("#retornoInternosRecebimento").append($(this).parent('td').parent('tr'));
        $("#retornoInternosRecebimento .botaoCancelar").addClass('displayNone');
        $("#retornoInternosRecebimento .checkboxTD").removeClass('displayNone');

        //if($('#retornoInterno').children().length == 0){
        //    $('#spanInternosCelaRecebimento').addClass('displayNone')
        //}
    });

    $( document ).on( "click", ".removerInternoEscolta", function() {

        $(this).parent('td').parent('tr').remove();
        //if($('input[name="agentesHidden[]"]').length == 0){
        //    $('#tableAgentes, .titleAgente').css("display","none");
        //}

        var idInternoEscolta = $(this).data("content");

        $("#tableInternos").append('<input type="hidden" name="internosRemover[]" id="internosRemover" value="'+ idInternoEscolta +'">')

        $("#btnSubmitForm").removeClass("btn-primary").addClass("btn-success")

    });



    // ---------------  fim da lista de internos pendentes ---------------------------------------


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

            if($('.agentesHidden').val() != v.matricula) {
                if (v.nome == nomeAgentes) {
                    $("#retornoAgente").append('<tr class="">' +
                        '<td scope="row">' + v.matricula + '</td>' +
                        '<td><img id="' + cpfAgente + 'img" src="' + URL + '/imagens/usuario/avatarInterno.png" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>' +
                        '<td>' + v.nome + '</td>' +
                        '<td>' + v.lotacao + '</td>' +
                        '<td class="btn-acoes color_01579b">' +
                        '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>' +
                        '<a href="#/" data-toggle="tooltip" data-placement="top" data-name="' + v.nome + '" data-content="' + v.matricula + '" class="comandanteEscolta" title="Comandante da Escolta"><i class="mdi mdi-star-circle"></i></a>' +
                        '<a href="#/" data-toggle="tooltip" data-placement="top"  data-name="' + v.nome + '" data-content="' + v.matricula + '"  class="motoristaEscolta" title="Motorista da Escolta"><i class="mdi mdi-steering"></i></a>' +
                        '<input type="hidden" name="agentesHidden[]" class="agentesHidden" id="agentesHidden" value="' + v.matricula + '"/>' +
                        '</td>' +
                        '</tr>');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../facade/get-foto-servidor",
                        data: {cpf: cpfAgente},
                        beforeSend: function () {

                        },
                        success: function (dados) {
                            $("#" + cpfAgente + "img").attr('src', dados);
                        }
                    });

                    return;
                }
            }
        });

        $(this).css("display","none")

        $(".titleAgente, #tableAgentes").removeClass('displayNone');

        $(".comandanteEscolta").click(function () {

            $("#vtrComandanteHidden").val($(this).data('content'));
            $("#vtr_comandante").val($(this).data('name'));

            $("#retornoAgente").each(function () {
                $('a').removeClass('alert-warning')
            });

            if ($("#vtrComandanteHidden").val() == $(this).data('content')) {
                $(this).addClass('alert-warning')
            }


        });

        $(".motoristaEscolta").click(function () {

            $("#vtrMotoristaHidden").val($(this).data('content'));
            $("#vtr_motorista").val($(this).data('name'));

            $("#retornoAgente").each(function () {
                $('a').removeClass('alert-dark')
            });

            if ($("#vtrMotoristaHidden").val() == $(this).data('content')) {
                $(this).addClass('alert-dark')
            }

        });

        $("#agente").val("");

    });

    $(".comandanteEscolta").click(function () {

        $("#vtrComandanteHidden").val($(this).data('content'));
        $("#vtr_comandante").val($(this).data('name'));

        $("#retornoAgente").each(function () {
            $('a').removeClass('alert-warning')
        });

        if ($("#vtrComandanteHidden").val() == $(this).data('content')) {
            $(this).addClass('alert-warning')
        }

    });
    $(".motoristaEscolta").click(function () {

        $("#vtrMotoristaHidden").val($(this).data('content'));
        $("#vtr_motorista").val($(this).data('name'));

        $("#retornoAgente").each(function () {
            $('a').removeClass('alert-dark')
        });

        if ($("#vtrMotoristaHidden").val() == $(this).data('content')) {
            $(this).addClass('alert-dark')
        }

    });


    $(document).on("click", ".removerAgentes", function () {
        $(this).parent('td').parent('tr').remove();
        if ($('input[name="agentesHidden[]"]').length == 0) {
            $('#tableAgentes, .titleAgente').css("display", "none");
            $("#vtrMotoristaHidden").val('');
            $("#vtr_motorista").val('');
            $("#vtrComandanteHidden").val('');
            $("#vtr_comandante").val('');
        }
        if($(this).parent('td').children('a').hasClass('comandanteEscolta') && $(this).parent('td').children('a').hasClass('alert-warning')){
            $("#vtrComandanteHidden").val('');
            $("#vtr_comandante").val('');
        }
        if($(this).parent('td').children('a').hasClass('motoristaEscolta') && $(this).parent('td').children('a').hasClass('alert-dark')){
            $("#vtrMotoristaHidden").val('');
            $("#vtr_motorista").val('');
        }
    });

    // --------------------- Fim da busca de agentes ----------------------------------------------


    $( document ).on( "click", ".removerAgentesCadastrados", function() {

        $(this).parent('td').parent('tr').remove();
        if($('input[name="agentesHidden[]"]').length == 0){
            $('#tableAgentes, .titleAgente').css("display","none");
        }

        var comandante = $("#vtrComandanteHidden").val();
        var motorista = $("#vtrMotoristaHidden").val();

        var idAgente = $(this).data("content");

        if(comandante == idAgente)
        {
            $("#vtrComandanteHidden").val("");
            $("#vtr_comandante").val("");

        }
        if(motorista == idAgente)
        {
            $("#vtrMotoristaHidden").val("");
            $("#vtr_motorista").val("");

        }
        $("#tableAgentes").append('<input type="hidden" name="agentesRemover[]" id="agentesRemover" value="'+ idAgente +'">')

        $("#btnSubmitForm").removeClass("btn-primary").addClass("btn-success")
    });


    $('#hora_retorno, #hora_saida').mask('00:00', {reverse: true});

    //$( document ).on("submit", "#formEditar", function() {
    //    $("#flashRetorno").html('');
    //    var control = 0;
    //
    //
    //    var data_retorno = $('#data_retorno').val();
    //    if(data_retorno == ""){
    //        $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de retorno.</p></div>');
    //        control = 1;
    //    }
    //
    //    var hora = $('#hora_retorno').val();
    //    if(hora.length != 5){
    //        $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
    //        control = 1;
    //    }
    //
    //    if(control == 1) return false;
    //
    //});




});