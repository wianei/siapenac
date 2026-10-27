$(document).ready(function() {

    $( function() {
        $("#dataEquipe").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            minDate: 0,
            orientation: "bottom auto"
        });

    });

    $("#btnCadastrarEquipe").click(function(){
       $("#formCadastrarEquipe").removeClass('displayNone')
    });

    $("#btnCancelarEquipe").click(function(){
       $("#formCadastrarEquipe").addClass('displayNone')
    });

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
                $("#retornoAgente").append('<tr class="">'+
                    '<td scope="row">'+ v.matricula+'</td>'+
                    '<td><img id="'+ cpfAgente+'img" src="'+URL+'/imagens/usuario/avatarInterno.png" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>'+
                    '<td>'+ v.nome+'</td>'+
                    '<td>'+ v.lotacao+'</td>'+
                    '<td scope="row"><select id="status" name="status[]" class="form-control parentesco" required>'+
                    '<option value="Em exercício">Em exercício</option>'+
                    '<option value="D.O">D.O</option>'+
                    '</select></td>'+
                    '<td class="btn-acoes color_01579b">'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>' +
                    '<a href="#/" data-toggle="tooltip" data-placement="top" data-name="' + v.nome + '" data-content="' + v.matricula + '" class="comandanteEscolta" title="Comandante da Escolta"><i class="mdi mdi-star-circle"></i></a>' +
                    '<a href="#/" data-toggle="tooltip" data-placement="top"  data-name="' + v.nome + '" data-content="' + v.matricula + '"  class="motoristaEscolta" title="Motorista da Escolta"><i class="mdi mdi-steering"></i></a>' +
                    '<input type="hidden" name="agentesHidden[]" class="agentesHidden" id="agentesHidden" value="'+ v.matricula+'"/>'+
                    '</td>'+
                    '</tr>');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../facade/get-foto-servidor",
                    data: {cpf:cpfAgente},
                    beforeSend: function() {

                    },
                    success: function (dados) {
                        $("#"+cpfAgente+"img").attr('src', dados);
                    }
                });

                return;
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

    $( document ).on( "blur", "#vtr", function() {

        var agentesHidden = $('.agentesHidden').length;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../gep/busca-equipe-vtr",
            data: {vtr: vtr},
            beforeSend: function() {

            },
            success: function (dados) {
                agente = dados;

                if (agente.length > 0) {

                    $("#retornoAgente").html("");

                    $('#tableAgentes, .titleAgente').css('display','block');

                    $('#divRetornoAgentes').html('').css('display','none');

                    $.each(agente, function (i, v) {
                        $("#retornoAgente").append('<tr class="">' +
                            '<td scope="row">' + v.matricula + '</td>' +
                            '<td><img id="' + v.cpf + 'img" src="' + URL + '/imagens/usuario/avatarInterno.png" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>' +
                            '<td>' + v.nome + '</td>' +
                            '<td>' + v.lotacao + '</td>' +
                            '<td scope="row"><select id="status" name="status[]" class="form-control parentesco" required>'+
                            '<option value="Em exercício">Em exercício</option>'+
                            '<option value="D.O">D.O</option>'+
                            '</select></td>'+
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
                            data: {cpf: v.cpf},
                            beforeSend: function () {

                            },
                            success: function (dados) {
                                $("#" + v.cpf + "img").attr('src', dados);
                            }
                        });

                        return;
                    });

                    $(this).css("display", "none")

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

                }
            }
        });

    });

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

    // coloca a foto do servidor no modal
    $( document ).on( "click", ".imagemSrc", function() {
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

    //---------------------------------------------------------------------------------------------------------------------------------------


    $("#dataEquipe, #dataBuscar").mask("00/00/0000");
    $("#vtr").mask('AAA9999');


    $( document ).on("submit", "#formCadastrarEquipe", function() {
        $("#flashRetorno").html('');

        var agentesHidden = $('.agentesHidden').length;
        if(agentesHidden == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione os agentes.</p></div>');
            control = 1;
        }

        var dataEquipe = $('#dataEquipe').val();
        if(dataEquipe == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data da equipe.</p></div>');
            control = 1;
        }

        var regiao = $('#regiao').val();
        if(regiao == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a região.</p></div>');
            control = 1;
        }

        var equipeNome = $('#equipeNome').val();
        if(equipeNome == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o nome da equipe.</p></div>');
            control = 1;
        }

        var vtr = $('#vtr').val();
        if(vtr == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a placa da viatura.</p></div>');
            control = 1;
        }

        var vtrComandanteHidden = $('#vtrComandanteHidden').val();
        if(vtrComandanteHidden == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o comandante da viatura.</p></div>');
            control = 1;
        }

        var vtrMotoristaHidden = $('#vtrMotoristaHidden').val();
        if(vtrMotoristaHidden == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o motorista da viatura.</p></div>');
            control = 1;
        }


        if(control == 1) return false;

    });

});