$(document).ready(function() {

    $( function() {
        $("#data_retorno").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            onSelect: function(selected) {
                $("#data_saida").datepicker("option", "maxDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#data_saida").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            onSelect: function(selected) {
                $("#data_retorno").datepicker("option", "minDate", selected)
            },
            orientation: "bottom auto"
        });

    });


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
                $('#spanInternosCelaRecebimento').removeClass('displayNone');
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

        if($('#retornoInterno').children().length == 0){
            $('#spanInternosCelaRecebimento').addClass('displayNone')
        }
    });

    //---------------------------------------------------------------------------------------------------------------------------------------

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
    $( document ).on( "blur", "#vtr_placa", function() {

        var vtr = $(this).val();

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



    /**
     * inicio dc equipamento
     * @type {*[]}
     */
    var equipamentos = [];

    function clearArrayReturn()
    {
        equipamentos = [];

    }


    $( document ).on( "change", "#tipoEquipamento", function() {

        clearArrayReturn();

        $("#qtd").val("");
        $('#divTipo, #divQtd, #divAdd, #divQtd, #divAdd, #tableEquipamentosEscolta').addClass('displayNone')
        $("#retornoEquipamentos").html('');

        var tipoArmamento = $(this).val();

        switch (tipoArmamento) {

            case "armaFogo":

                $('#divTipo').removeClass('displayNone');



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='longa'>Longa</option>" +
                    "<option value='curta'>Curta</option>");
                $('#divQtd').addClass("displayNone");


                $("#tipo").change(function () {

                    var especiePost = $(this).val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>'+ v.equipamento_especificacao+' ' + v.calibre +' - '+ v.modelo+' | Nº de Série: '+ v.n_serie+' | Fabricante: '+ v.fabNome+'</td>'+
                                    '</tr>');
                                return;

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;

            case "municao":

                $('#divTipo').removeClass('displayNone');


                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='letal'>Letal</option>" +
                    "<option value='menosLetal'>Menos Letal</option>");


                $("#tipo").change(function () {

                    var especiePost = $(this).val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>' +
                                    '<input class="form-control mr-4 inputQtd" type="text" max="'+ v.estoque +'" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                    ''+ v.modelo+' - ('+ v.equipamento_especificacao.toUpperCase()+') - .'+ v.calibre+' - '+ v.equipamento_especie.toUpperCase()+' - '+v.fabNome+'' +
                                    '</td>'+
                                    '</tr>');
                                return;

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;



            case "carregador":

                $('#divTipo').removeClass('displayNone');



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='bifilar'>Bifilar</option>" +
                    "<option value='monofilar'>Monofilar</option>");


                $("#tipo").change(function () {

                    var especiePost = $('#tipo').val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>' +
                                    '<input class="form-control mr-4 inputQtd" type="text"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                    ''+ v.modelo+' - .'+ v.calibre+' - '+ v.equipamento_especie.toUpperCase()+' - Capacidade: '+v.volume_peso+'Mun. - '+v.fabNome+'' +
                                    '</td>'+
                                    '</tr>');
                                return;

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;

            case "algema":

                $('#divTipo').removeClass('displayNone');



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='metalica'>Metálica</option>" +
                    "<option value='mista'>Mista</option>" +
                    "<option value='plastica'>Plástica</option>");

                $("#tipo").change(function () {

                    var especiePost = $('#tipo').val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>' +
                                    '<input class="form-control mr-4 inputQtd" type="text"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                    ''+ v.equipamento_especificacao.toUpperCase()+' - '+ v.equipamento_especie.toUpperCase()+' - '+v.fabNome+'' +
                                    '</td>'+
                                    '</tr>');
                                return;

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;

            case "granada":

                $('#divTipo').removeClass('displayNone');



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='fumigena'>Fumígena</option>" +
                    "<option value='Explosiva'>Explosiva</option>");


                $("#tipo").change(function () {

                    var especiePost = $('#tipo').val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>' +
                                    '<input class="form-control mr-4 inputQtd" type="text"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                    ''+ v.equipamento_especificacao.toUpperCase()+' - '+ v.equipamento_especie.toUpperCase()+' - '+v.modelo+' - '+v.fabNome+'' +
                                    '</td>'+
                                    '</tr>');
                                return;

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;

            case "epi":

                $('#divTipo').removeClass('displayNone');



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='capacete'>Capacete</option>" +
                    "<option value='perneira'>Perneira</option>"+
                    "<option value='cotoveleira'>Cotoveleira</option>"+
                    "<option value='joelheira'>Joelheira</option>"+
                    "<option value='tonfa'>Tonfa</option>"+
                    "<option value='bastaoRetratil'>Bastão Retrátil</option>"+
                    "<option value='bastao90cm'>Bastão 90cm</option>"+
                    "<option value='escudo'>Escudo</option>"
                );


                $("#tipo").change(function () {

                    var especiePost = $('#tipo').val();
                    var equipamentoPost = $('#tipoEquipamento').val();

                    $("#retornoEquipamentos").html('');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../armaria/busca-equipamento-unidade-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                if(v.equipamento_especie == "capacete" || v.equipamento_especie == "escudo") {

                                    $("#retornoEquipamentos").append('<tr class="">' +
                                        '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="' + v.id + '"></td>' +
                                        '<td>' +
                                        '' + v.equipamento_especie.toUpperCase() + ' - ' + v.n_serie + ' - ' + v.fabNome + '' +
                                        '</td>' +
                                        '</tr>');
                                }else{

                                    $("#retornoEquipamentos").append('<tr class="">'+
                                        '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                        '<td>' +
                                        '<input class="form-control mr-4 inputQtd"  max="'+ v.estoque +'"  type="text" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                        ''+ v.equipamento_especie.toUpperCase()+'' +
                                        '</td>'+
                                        '</tr>');

                                }

                            });

                            equipamentos = objt;

                        }
                    });


                });

                break;

            case "colete":



                $('label[for="tipo"]').html("Tipo do Armamento");

                $('#divQtd').addClass("displayNone");


                var especiePost = "";
                var equipamentoPost = $('#tipoEquipamento').val();

                $("#retornoEquipamentos").html('');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../armaria/busca-equipamento-unidade-ajax",
                    data: {especie: especiePost, equipamento: equipamentoPost},
                    beforeSend: function() {

                    },
                    success: function (dados) {

                        var objt = (dados);

                        $("#retornoEquipamentos").html('');

                        $.each(objt, function(i, v) {

                            $("#retornoEquipamentos").append('<tr class="">'+
                                '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                '<td>Tamanho: '+ v.tamanho+' | Nº de Série: '+ v.n_serie+' | Fabricante: '+ v.fabNome+'</td>'+
                                '</tr>');
                            return;

                        });

                        equipamentos = objt;

                    }
                });


                break;

            case "espargidor":





                var especiePost = $('#tipo').val();
                var equipamentoPost = $('#tipoEquipamento').val();

                $("#retornoEquipamentos").html('');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../armaria/busca-equipamento-unidade-ajax",
                    data: {especie: especiePost, equipamento: equipamentoPost},
                    beforeSend: function() {

                    },
                    success: function (dados) {

                        var objt = (dados);

                        $("#retornoEquipamentos").html('');

                        $.each(objt, function(i, v) {

                            $("#retornoEquipamentos").append('<tr class="">'+
                                '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                '<td>' +
                                '<input class="form-control mr-4 inputQtd"  max="'+ v.estoque +'"  type="text" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
                                ''+ v.modelo+' - Capacidade: '+v.volume_peso+'' +
                                '</td>'+
                                '</tr>');
                            return;

                        });

                        equipamentos = objt;

                    }
                });


                break;



        }

    });

    $( document ).on( "change", "#tipo", function() {
        $("#tableEquipamentos").removeClass("displayNone");
    });

    $( document ).on( "click", "#btnAddEquipamentos", function() {

        $("#tableEquipamentosCautela").removeClass('displayNone')
        $("#tipo option[value='']").attr('selected', 'selected');

        var tipoEquipamento = $("#tipoEquipamento").val();

        if(tipoEquipamento == "armaFogo") {

            $('input[name="equipamento[]"]:checked').each(function () {

                var idEquipamento = this.value;

                $.each(equipamentos, function (key, val) {

                    if ((val.id == idEquipamento)) {

                        var equipText = val.equipamento_especificacao + ' ' + val.calibre + ' - ' + val.modelo + ' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">' +
                            '<td scope="row"></td>' +
                            '<td>' + val.equipamento_especificacao + ' ' + val.calibre + ' - ' + val.modelo + ' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome + '</td>' +
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="' + val.id + '//'+equipText+'">' +
                            '<td class="btn-acoes color_01579b">' +
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>' +
                            '</td>' +
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected', 'selected');


            });
        }

        if(tipoEquipamento == "municao")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Munições - '+ val.modelo+' - ('+ val.equipamento_especificacao.toUpperCase()+') - .'+ val.calibre+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Munições - '+ val.modelo+' - ('+ val.equipamento_especificacao.toUpperCase()+') - .'+ val.calibre+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.fabNome+'</td>'+
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+equipText+'">'+
                            '<td class="btn-acoes color_01579b">'+
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                            '</td>'+
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

        if(tipoEquipamento == "carregador")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Carregador(es) - '+ val.modelo+' - .'+ val.calibre+' - '+ val.equipamento_especie.toUpperCase()+' - Capacidade: '+val.volume_peso+'Mun. - '+val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Carregador(es) - '+ val.modelo+' - .'+ val.calibre+' - '+ val.equipamento_especie.toUpperCase()+' - Capacidade: '+val.volume_peso+'Mun. - '+val.fabNome+'</td>'+
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+equipText+'">'+
                            '<td class="btn-acoes color_01579b">'+
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                            '</td>'+
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

        if(tipoEquipamento == "algema")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Algema(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Algema(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.fabNome+'</td>'+
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+equipText+'">'+
                            '<td class="btn-acoes color_01579b">'+
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                            '</td>'+
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

        if(tipoEquipamento == "granada")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Granada(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.modelo+' - '+val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Granada(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.modelo+' - '+val.fabNome+'</td>'+
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+$(this).parent().text()+'">'+
                            '<td class="btn-acoes color_01579b">'+
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                            '</td>'+
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

        if(tipoEquipamento == "epi")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = val.equipamento_especie.toUpperCase() + ' - ' + val.n_serie + ' - ' + val.fabNome;

                        if(val.equipamento_especie == "capacete" || val.equipamento_especie == "escudo")
                        {
                            $("#retornoEquipamentosEscolta").append('<tr class="">'+
                                '<td scope="row"></td>'+
                                '<td>' + val.equipamento_especie.toUpperCase() + ' - ' + val.n_serie + ' - ' + val.fabNome + '</td>'+
                                '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+equipText+'">'+
                                '<td class="btn-acoes color_01579b">'+
                                '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                                '</td>'+
                                '</tr>');

                        }else{

                            $("#retornoEquipamentosEscolta").append('<tr class="">'+
                                '<td scope="row"></td>'+
                                '<td>'+ qtdEquip +' - '+ val.equipamento_especie.toUpperCase()+'</td>'+
                                '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+$(this).parent().text()+'">'+
                                '<td class="btn-acoes color_01579b">'+
                                '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                                '</td>'+
                                '</tr>');

                        }

                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

        if(tipoEquipamento == "colete") {

            $('input[name="equipamento[]"]:checked').each(function () {

                var idEquipamento = this.value;

                $.each(equipamentos, function (key, val) {

                    if ((val.id == idEquipamento)) {

                        var equipText = 'Colete tamanho: '+val.tamanho+' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">' +
                            '<td scope="row"></td>' +
                            '<td>Tamanho: '+val.tamanho+' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome + '</td>' +
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="' + val.id + '//'+equipText+'">' +
                            '<td class="btn-acoes color_01579b">' +
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>' +
                            '</td>' +
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected', 'selected');


            });
        }

        if(tipoEquipamento == "espargidor")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Espargidor(es) '+ val.modelo+' - Capacidade: '+val.volume_peso;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Espargidor(es) '+ val.modelo+' - Capacidade: '+val.volume_peso+'</td>'+
                            '<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+$(this).parent().text()+'">'+
                            '<td class="btn-acoes color_01579b">'+
                            '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerEquipamento" title="Remover Equipamentos"><i class="mdi mdi-close-circle-outline"></i></a>'+
                            '</td>'+
                            '</tr>');
                    }
                });

                $('#tableEquipamentos, .titleInterno').addClass('displayNone');
                $('#retornoEquipamentos').html('');
                $("#tipoArmamento option[value='']").attr('selected','selected');



            });

        }

    });


    $( document ).on( "click", ".checkboxEquipamento", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    $( document ).on( "click", ".removerEquipamento", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="hiddenEquipamentoCautela[]"]').length == 0){
            $('#tableEquipamentosEscolta').css("display","none");

        }
    });

    $('#hora_saida, #hora_retorno').mask('00:00', {reverse: true});

    $( document ).on("submit", "#formNovaEscolta", function() {
        $("#flashRetorno").html('');

        var control = 0;


        var internosSelecionados = $('#retornoInterno tr').length;
        var motivacaoEscolta = $("#obs").val();
        if(internosSelecionados == 0){
            //$("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            //control = 1;
            $("#motivacaoEscoltaSemInterno").removeClass("displayNone")
            if(motivacaoEscolta == ""){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Justifique a escolta sem interno(s).</p></div>');

                control = 1;
            }

        }
        var agentesHidden = $('.agentesHidden').length;
        if(agentesHidden == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione os agentes.</p></div>');
            control = 1;
        }

        var data_saida = $('#data_saida').val();
        if(data_saida == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de saída.</p></div>');
            control = 1;
        }

        //var hora_saida = $('#hora_saida').val();
        //if(hora_saida == ""){
        //    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a hora de saída.</p></div>');
        //    control = 1;
        //}

        var vtr_placa = $('#vtr_placa').val();
        if(vtr_placa == ""){
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

        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o Motivo.</p></div>');
            control = 1;
        }

        var hora = $('#hora_saida').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário de saída no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }

        var horaRetorno = $('#hora_retorno').val();
        if(horaRetorno != "") {
            if (horaRetorno.length != 5) {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Preencha o horário de retorno, se já houve, no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
                control = 1;
            }
        }

        if(control == 1) return false;

    });



});