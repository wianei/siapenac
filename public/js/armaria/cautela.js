$(document).ready(function () {

    $.jMaskGlobals.watchDataMask = true;

    $("#btnMapaEquipamento").click(function(){
        $("#divMapaArmamento").toggleClass('displayNone')
    })

    var equipamentos = [];

    function clearArrayReturn()
    {
        equipamentos = [];

    }


    $("#filtroNome").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableEquipamentos #retornoEquipamentos tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


    $( document ).on( "change", "#tipoEquipamento", function() {

        clearArrayReturn();

        $("#qtd").val("");
        $('#divTipo, #divQtd, #divAdd, #divQtd, #divAdd, #tableEquipamentosEscolta').addClass('displayNone')
        $("#retornoEquipamentos").empty();
        $("#filtroNome").val("");

        var tipoArmamento = $(this).val();

        switch (tipoArmamento) {

            case "armaFogo":

                $('#divTipo').removeClass('displayNone');
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='longa'>Longa</option>" +
                    "<option value='curta'>Curta</option>");
                $('#divQtd').addClass("displayNone");


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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

            case "dei":

                $('#divTipo').removeClass('displayNone');
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='longa'>Longa</option>" +
                    "<option value='curta'>Curta</option>");
                $('#divQtd').addClass("displayNone");


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
                        data: {especie: especiePost, equipamento: equipamentoPost},
                        beforeSend: function() {

                        },
                        success: function (dados) {

                            var objt = (dados);

                            $("#retornoEquipamentos").html('');

                            $.each(objt, function(i, v) {

                                $("#retornoEquipamentos").append('<tr class="">'+
                                    '<td scope="row"><input type="checkbox" name="equipamento[]" class="checkboxEquipamento" value="'+ v.id+'"></td>'+
                                    '<td>'+ v.equipamento_especificacao+' - '+ v.modelo+' | Nº de Série: '+ v.n_serie+' | Fabricante: '+ v.fabNome+'</td>'+
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
                $("#tableEquipamentos").removeClass("displayNone");


                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='letal'>Letal</option>" +
                    "<option value='menosLetal'>Menos Letal</option>");


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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
                                    '<input class="form-control mr-4 inputQtd" type="text" data-mask="0000" max="'+ v.estoque +'" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='bifilar'>Bifilar</option>" +
                    "<option value='monofilar'>Monofilar</option>");


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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
                                    '<input class="form-control mr-4 inputQtd" type="text" data-mask="0000"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='metalica'>Metálica</option>" +
                    "<option value='mista'>Mista</option>" +
                    "<option value='plastica'>Plástica</option>");

                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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
                                    '<input class="form-control mr-4 inputQtd" type="text" data-mask="0000"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='fumigena'>Fumígena</option>" +
                    "<option value='Explosiva'>Explosiva</option>");


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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
                                    '<input class="form-control mr-4 inputQtd" type="text" data-mask="0000"  max="'+ v.estoque +'"  name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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
                $("#tableEquipamentos").removeClass("displayNone");



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


                $( document ).on("change", "#tipo", function() {

                    $("#filtroNome").val("");

                    $("#tableEquipamentos").removeClass("displayNone");

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
                        url: "/../armaria/equipamento-busca-ajax",
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
                                        '<input class="form-control mr-4 inputQtd" data-mask="0000" max="'+ v.estoque +'"  type="text" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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

                $("#tableEquipamentos").removeClass("displayNone");



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
                    url: "/../armaria/equipamento-busca-ajax",
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

                $("#tableEquipamentos").removeClass("displayNone");




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
                    url: "/../armaria/equipamento-busca-ajax",
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
                                '<input class="form-control mr-4 inputQtd" data-mask="0000" max="'+ v.estoque +'"  type="text" name="qtdEquip" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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


    $( document ).on( "click", "#btnAddEquipamentos", function() {

        $("#tableEquipamentosCautela").removeClass('displayNone')
        //$("#tipoEquipamento option[value='']").attr('selected', 'selected');

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

        if(tipoEquipamento == "dei") {

            $('input[name="equipamento[]"]:checked').each(function () {

                var idEquipamento = this.value;

                $.each(equipamentos, function (key, val) {

                    if ((val.id == idEquipamento)) {

                        var equipText = val.equipamento_especificacao + ' - ' + val.modelo + ' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome;

                        $("#retornoEquipamentosEscolta").append('<tr class="">' +
                            '<td scope="row"></td>' +
                            '<td>' + val.equipamento_especificacao + ' - ' + val.modelo + ' | Nº de Série: ' + val.n_serie + ' | Fabricante: ' + val.fabNome + '</td>' +
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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }

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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }

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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }

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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }

                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = val.equipamento_especie.toUpperCase() + ' - ' + val.n_serie + ' - ' + val.fabNome;
                        var equipText2 = qtdEquip +' - '+ val.equipamento_especie.toUpperCase();

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
                                '<td>'+ qtdEquip +' - '+ val.equipamento_especie.toUpperCase()+'<input type="hidden" name="hiddenEquipamentoCautela[]" value="'+ val.id+'/'+qtdEquip+'/'+equipText2+'"></td>'+
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

                if(qtdEquip == "")
                {
                    alert("Informe a quantidade cautelada")

                    $("#tableEquipamentosCautela").addClass('displayNone')
                    $(this).closest('tr').children().css("border","2px dotted red");

                    return false;
                }


                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
                        var equipText = qtdEquip +' Espargidor(es) '+ val.modelo+' - Capacidade: '+val.volume_peso;

                        $("#retornoEquipamentosEscolta").append('<tr class="">'+
                            '<td scope="row"></td>'+
                            '<td>'+ qtdEquip +' Espargidor(es) '+ val.modelo+' - Capacidade: '+val.volume_peso+'</td>'+
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

        $("#retornoEquipamentos").empty();

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
     * responsavel pela cautela
     */

    $("input[name='cautelaPara']").click(function(){

        var cautelaPara = $("input[name='cautelaPara']:checked").val();

        if(cautelaPara == 'servidor')
        {
            $("#unidade_prisional option[value='']").attr('selected','selected');

            $("#divUnidade, #divPavilhao").addClass('displayNone');
            $("#divRowAgentes").removeClass('displayNone');
            $("#divCautelaOutros").addClass('displayNone');


        }
        if(cautelaPara == 'unidade')
        {
            $("#divUnidade, #divPavilhao").removeClass('displayNone');
            $("#divRowAgentes").removeClass('displayNone');
            $("#divCautelaOutros").addClass('displayNone');

        }

        if(cautelaPara == 'outros')
        {
            $("#divUnidade, #divPavilhao, #divRowAgentes").addClass('displayNone');
            $("#unidade_prisional option[value='']").attr('selected','selected');
            $("#divCautelaOutros").removeClass('displayNone');


        }

    })

    $(function () {
        $( document ).on( "change", ".inputQtd", function() {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max)
            {
                $(this).val(max);
            }
            else if ($(this).val() < min)
            {
                $(this).val(min);
            }
        });
    });

    $( document ).on( "change", "#tempoIndeterminado", function() {

        var tipoIndeterminado = $(this).val();

        if(tipoIndeterminado == "S")
        {

            $("#data_inicio").val("");
            $("#data_final").val("");

            $("#dataInicial").hide("fast");
            $("#dataFinal").hide("fast");
        }

        if(tipoIndeterminado == "N")
        {

            $("#dataInicial").show("fast");
            $("#dataFinal").show("fast");
        }

    });

    $( document ).on("submit", "#formCautela", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var cautelaPara = $("input[name='cautelaPara']:checked").val();
        if(cautelaPara != 'outros') {

            if(cautelaPara == "unidade")
            {
                var unidade_prisional = $("#unidade_prisional").val();
                if (unidade_prisional == "") {
                    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a unidade prisional.</p></div>');
                    control = 1;

                }

            }

            var qtdAgentes = $('input[name="agentesHidden[]"]').length;
            if (qtdAgentes > 1) {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">É permitido apenas um servidor na lista.</p></div>');
                control = 1;
            }

            if (qtdAgentes == 0) {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o servidor responsável pela cautela.</p></div>');
                control = 1;
            }
        }else {
            var outrosNome = $("#outrosNome").val();
            var outrosCpf = $("#outrosCpf").val();
            var outrosOrgao = $("#outrosOrgao").val();
            var outrosTelefone = $("#outrosTelefone").val();

            if (outrosNome == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o nome do responsável pela cautela.</p></div>');
                control = 1;

            }

            if (outrosCpf == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o CPF do responsável pela cautela.</p></div>');
                control = 1;

            }

            if (outrosOrgao == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o órgão do responsável pela cautela.</p></div>');
                control = 1;

            }

            if (outrosTelefone == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o telefone do responsável pela cautela.</p></div>');
                control = 1;

            }
        }

        var qtdEquipamento = $('input[name="hiddenEquipamentoCautela[]"]').length;
        if(qtdEquipamento  == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o(s) equipamentos para cautela.</p></div>');
            control = 1;
        }


        var tempoIndeterminado = $("#tempoIndeterminado").val();
        if (tempoIndeterminado == "") {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe se o tempo será indeterminado.</p></div>');
            control = 1;

        }
        if(tempoIndeterminado == 'N')
        {
            var data_inicio = $("#data_inicio").val();
            if (data_inicio == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de início da cautela.</p></div>');
                control = 1;

            }

            var data_final = $("#data_final").val();
            if (data_final == "") {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de término da cautela.</p></div>');
                control = 1;

            }

        }


        if(control == 1) return false;

    });

    $("#data_inicio, #data_final").mask("00/00/0000");

});

