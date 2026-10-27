$(document).ready(function () {

    CKEDITOR.replace( 'relatorio' );

    $("#btnMapaEquipamento").click(function(){
        $("#divMapaArmamento").toggleClass('displayNone')
    })

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


            case "municao":

                $('#divTipo').removeClass('displayNone');
                $("#tableEquipamentos").removeClass("displayNone");


                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='letal'>Letal</option>" +
                    "<option value='menosLetal'>Menos Letal</option>");


                $( document ).on("change", "#tipo", function() {

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
                                    '<input class="form-control mr-4 inputQtd" type="text" max="'+ v.estoque +'" name="qtdEquip[]" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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




            case "granada":

                $('#divTipo').removeClass('displayNone');
                $("#tableEquipamentos").removeClass("displayNone");



                $('label[for="tipo"]').html("Tipo do Armamento");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='fumigena'>Fumígena</option>" +
                    "<option value='Explosiva'>Explosiva</option>");


                $( document ).on("change", "#tipo", function() {

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
                                    '<input class="form-control mr-4 inputQtd" type="text"  max="'+ v.estoque +'"  name="qtdEquip[]" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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
                                '<input class="form-control mr-4 inputQtd"  max="'+ v.estoque +'"  type="text" name="qtdEquip[]" id="qtdEquip'+ v.id+'" style="width: 50px; float: left">'+
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


        if(tipoEquipamento == "municao")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                if(qtdEquip == "")
                {
                    alert("informe quantidade utilizada")

                    $("#tableEquipamentosCautela").addClass('displayNone')

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


        if(tipoEquipamento == "granada")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                if(qtdEquip == "")
                {
                    alert("informe quantidade utilizada")

                    $("#tableEquipamentosCautela").addClass('displayNone')

                    return false;
                }


                $.each(equipamentos, function(key, val){

                    if ((val.id == idEquipamento)) {

                        //var qtdEquip = $("#qtdEquip"+ val.id).val();
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

        if(tipoEquipamento == "espargidor")
        {
            $('input[name="equipamento[]"]:checked').each(function() {

                var idEquipamento = this.value;
                var qtdEquip = $(this).closest('tr').find('td:nth-child(2) input:last').val();

                if(qtdEquip == "")
                {
                    alert("informe quantidade utilizada")

                    $("#tableEquipamentosCautela").addClass('displayNone')

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

    $( document ).on("submit", "#formRelatorioUso", function() {
        $("#flashRetorno").html('');
        var control = 0;

        //var qtdAgentes = $("#agentesHidden").length;
        var qtdAgentes = $('input[name="agentesHidden[]"]').length;
        if(qtdAgentes  > 1){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">É permitido apenas um servidor na lista.</p></div>');
            control = 1;
        }

        if(qtdAgentes  == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o servidor que fez uso do material.</p></div>');
            control = 1;
        }

        var qtdEquipamento = $('input[name="hiddenEquipamentoCautela[]"]').length;
        if(qtdEquipamento  == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o equipamento utilizado.</p></div>');
            control = 1;
        }



        if(CKEDITOR.instances.relatorio.getData() == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Descreva o uso.</p></div>');
            control = 1;
        }

        if(control == 1) return false;

    });


    $("#data_inicio, #data_final").mask("00/00/0000")

});

