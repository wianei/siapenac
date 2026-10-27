$(document).ready(function(){

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });

    var celaInternos = [
    ];



    /**
     * Ao selecionar a cela adiciona os internos na lista
     */
    $( document ).on( "change", "#cela", function() {

        $("#spanInternosCela").css('display','block');
        $("#retornoInternosCela").html('');

        var cela = $("#cela option:selected").text();
        $("#txtCela").html('Internos da  '+cela);


        var celaPost = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/listaInternosCela-ajax",
            data: {cela: celaPost, restrito: true},
            beforeSend: function() {

                $("#retornoInternosCela").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');

            },
            success: function (dados) {
                $("#retornoInternosCela").html('');
                var objt = (dados);

                $.each(objt, function(i, v) {


                    $("#retornoInternosCela").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="../'+ v.foto+'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+' - '+ v.pvNome+' - '+ v.ala+' - '+ v.cela+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</td>'+
                        '</tr>');

                });

                // verifica se o interno já esta presente na lista de internos e remove da lista da cela
                $('input[name="internos[]"]').each(function() {
                    var idRemover = $(this).val();
                    $("#"+idRemover).remove();
                });

                celaInternos = objt;

            }


        });


    });


    /**
     * Marca todos os checkboxes da lista de internos por recebimento
     */
    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));
        $('#retornoInternosRecebimento').children().toggleClass('background_bddaff');
        if($('#retornoMensagens').children().hasClass('background_bddaff'))
        {
            $('#retornoMensagens').children().addClass('background_bddaff');
        }

    });

    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));

        if($(this).is(":checked")){
            $('#retornoInternosCela').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosCela').children().hasClass('background_bddaff')){
                $('#retornoInternosCela').children().removeClass('background_bddaff');
            }
        }

    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    /**
     * adiciona os internos na lista de internos
     */
    $( document ).on( "click", "#btnAddTodosInternos", function() {

        $('input[name="internosCelaCheck[]"]:checked').each(function() {

            var prontuario = this.value;
            //alert(this.value);

            $.each(celaInternos, function(key, val){

                if ((val.id == prontuario)) {

                    $("#retornoInterno").append('<tr class="" id="tr'+ val.id+'">'+
                        '<td scope="row">'+ val.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ val.foto+'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ val.nome+'</td>'+
                        '<td>'+ val.faccao+'</td>'+
                        '<td>'+ val.lideranca+'</td>'+
                        '<td>'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-content="'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover desta Mudança de Cela"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ val.id+'"/>'+
                        '<a href="/interno/show/'+ val.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;

                }
            });

            $('#tableInternos, .titleInterno').css('display','block');
            $("#spanInternosCela").css('display','none');
            //$("#cela").html('<option value="">Selecione</option>');
            $("#cela option[value='']").removeAttr('selected').attr('selected','selected');

        });

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
                    '<td class="btn-acoes color_01579b">'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" data-name="'+ v.nome+'" data-content="'+ v.matricula+'" class="comandanteEscolta" title="Comandante da Escolta"><i class="mdi mdi-star-circle"></i></a>'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top"  data-name="'+ v.nome+'" data-content="'+ v.matricula+'"  class="motoristaEscolta" title="Motorista da Escolta"><i class="mdi mdi-steering"></i></a>'+
                    '<input type="hidden" name="agentesHidden[]" id="agentesHidden" value="'+ v.matricula+'"/>'+
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

        $(".comandanteEscolta").click(function(){

            var vtr = getVTR();

            if(vtr == true)
            {
                $("#vtrComandanteHidden").val($(this).data('content'));
                $("#vtr_comandante").val($(this).data('name'));

                $("#retornoAgente").each(function(){
                    $('a').removeClass('alert-warning')
                });

                if($("#vtrComandanteHidden").val() == $(this).data('content'))
                {
                    $(this).addClass('alert-warning')
                }

            }


        });

        $(".motoristaEscolta").click(function(){

            var vtr = getVTR();

            if(vtr == true)
            {
                $("#vtrMotoristaHidden").val($(this).data('content'));
                $("#vtr_motorista").val($(this).data('name'));

                $("#retornoAgente").each(function(){
                    $('a').removeClass('alert-dark')
                });

                if($("#vtrMotoristaHidden").val() == $(this).data('content'))
                {
                    $(this).addClass('alert-dark')
                }

            }


        });


        $(this).css("display","none")

        $(".titleAgente, #tableAgentes").removeClass('displayNone');

        $("#agente").val("");

    });

    $( document ).on( "click", ".removerAgentes", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="agentesHidden[]"]').length == 0){
            $('#tableAgentes, .titleAgente').css("display","none");
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


    $("input[name='vtr']").click(function(){

        var utilizacaoVTR = $("input[name='vtr']:checked").val();

        var vtr = getVTR();

        if(vtr == false)
        {
            $("#divPlacaVTR, #divComandanteVTR, #divMotoristaVTR").css('display','none');
        }

        if(vtr == true)
        {
            $("#divPlacaVTR, #divComandanteVTR, #divMotoristaVTR").css('display','block');
        }


    });


    function getVTR() {

        var utilizacaoVTR = $("input[name='vtr']:checked").val();

        if (utilizacaoVTR == 'N') {
            return false
        } else {
            return true
        }
    }


    $(".radioTipoEscolta").click(function() {
        if($("label").hasClass('active'))
        {
            var tipo = $(this).children().attr('value');
            $("#tipoEscoltaHidden").val(tipo);

            if(tipo == "gep")
            {
                $("#divRowAgentes, #divHoraSaida, #divDataRetorno, #divHoraRetorno, #divUtilizacaoVTR, #divPlacaVTR, #divComandanteVTR, #divMotoristaVTR, .divEquipamentoUtilizado").hide('fast');

                $("#btnSubmitForm").html('<i class="mdi mdi-export"></i> Solicitar Escolta a FTPEN');
                $(".titleAgente").css('display','none');
                $("#tableAgentes").css('display','none');
                $("#retornoAgente").html('');
            }

            if(tipo == "interna" || tipo == "externa")
            {
                $("#btnSubmitForm").html('<i class="fas fa-save"></i> Salvar Escolta');

                $("#divRowAgentes, #divHoraSaida, #divDataRetorno, #divHoraRetorno, #divUtilizacaoVTR, #divPlacaVTR, #divComandanteVTR, #divMotoristaVTR, .divEquipamentoUtilizado").show('fast');
                var vtr = getVTR();

                if(vtr == false)
                {
                    $("#divPlacaVTR, #divComandanteVTR, #divMotoristaVTR").css('display','none');
                }

                if(vtr == true)
                {
                    $("#divPlacaVTR, #divComandanteVTR, #divMotoristaVTR").css('display','block');
                }
            }
        }
    });


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
                    "<option value='fumigena'>Fulmígena</option>" +
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
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                       // var qtdEquip = $("#qtdEquip"+ val.id).val();
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
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
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
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
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
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                       // var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Granada(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.modelo+' - '+val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Granada(s) - '+ val.equipamento_especificacao.toUpperCase()+' - '+ val.equipamento_especie.toUpperCase()+' - '+val.modelo+' - '+val.fabNome+'</td>'+
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

        if(tipoEquipamento == "epi")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
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
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
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


    /**
     * Validacoes e mascaras
     */

    $( document ).on("submit", "#formCadastroEscolta", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var hora = $('#hora_saida').val();


        var tipoEscolta = $("#tipoEscoltaHidden").val();
        if(tipoEscolta != "gep" && hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }

        if(control == 1) return false;

    });

    $('#hora_saida, #hora_retorno').mask('00:00');

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

                $("#data_saida").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    onSelect: function(selected) {
                        $("#data_retorno").datepicker("option", "minDate", selected);
                    },
                    autoclose: true,
                    orientation: "bottom auto"
                });

                $("#data_retorno").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    onSelect: function(selected) {
                        $("#data_saida").datepicker("option", "minDate", selected);
                    },
                    autoclose: true,
                    orientation: "bottom auto"
                });


            }
        });


    });

//    $( function() {
//        $("#data_saida").datepicker({
//            dateFormat: 'dd/mm/yy',
//            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
//            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
//            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
//            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
//            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
//            nextText: 'Próximo',
//            prevText: 'Anterior',
//            autoclose: true,
//            maxDate: 0,
//            onSelect: function(selected) {
//                $("#data_retorno").datepicker("option", "minDate", selected);
//            },
//            orientation: "bottom auto"
//        });
//    
//    });
//
//
//
//    $( function() {
//        $("#data_retorno").datepicker({
//            dateFormat: 'dd/mm/yy',
//            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
//            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
//            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
//            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
//            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
//            nextText: 'Próximo',
//            prevText: 'Anterior',
//            autoclose: true,
//            maxDate: 0,
//            onSelect: function(selected) {
//                $("#data_saida").datepicker("option", "maxDate", selected)
//            },
//            orientation: "bottom auto"
//        });
//
//    });

});
