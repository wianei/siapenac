$(document).ready(function(){
    var tipoArmamento = $('#tipoEqupEditar').val();

    switch (tipoArmamento){

        case "armaFogo":

            var equipamento_especie = $('#equipamento_especie').val();
            var equipamento_especificacao = $('#equipamento_especificacao').val();

            $('label[for="tipo"]').html("Tipo do Armamento");
            $("#divN_serie, #divTipo, #divCalibre, #divFabricante, #divModelo, #divArmamento").css("display","block");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='longa'>Longa</option>" +
                "<option value='curta'>Curta</option>");
            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');


               if($("#tipo").val() == "longa"){

                    $("#armamento").html("<option value=''>Selecione</option>"+
                        '<option value="ct">CT</option>'+
                        '<option value="carabina">Carabina</option>'+
                        '<option value="espingarda">Espingarda</option>'+
                        '<option value="Fuzil">Fuzil</option>'+
                        '<option value="Lancador">Lançador</option>'+
                        '<option value="smt">SMT</option>');
                }
                if($("#tipo").val() == "curta"){

                    $("#armamento").html("<option value=''>Selecione</option>"+
                        '<option value="pistola">Pistola</option>'+
                        '<option value="revolver">Revólver</option>');
                }
            $('#armamento option[value='+equipamento_especificacao+']').attr('selected','selected');


            break;

        case "dei":

            var equipamento_especie = $('#equipamento_especie').val();
            var equipamento_especificacao = $('#equipamento_especificacao').val();

            $('label[for="tipo"]').html("Tipo do Armamento");
            $("#divN_serie, #divTipo, #divFabricante, #divModelo, #divArmamento").css("display","block");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='longa'>Longa</option>" +
                "<option value='curta'>Curta</option>");
            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');


               if($("#tipo").val() == "longa"){

                    $("#armamento").html("<option value=''>Selecione</option>"+
                        '<option value="ct">CT</option>'+
                        '<option value="carabina">Carabina</option>'+
                        '<option value="espingarda">Espingarda</option>'+
                        '<option value="Fuzil">Fuzil</option>'+
                        '<option value="Lancador">Lançador</option>'+
                        '<option value="smt">SMT</option>');
                }
                if($("#tipo").val() == "curta"){

                    $("#armamento").html("<option value=''>Selecione</option>"+
                        '<option value="spark">Spark</option>');
                }
            $('#armamento option[value='+equipamento_especificacao+']').attr('selected','selected');


            break;

        case "municao":

            var equipamento_especie = $('#equipamento_especie').val();
            var equipamento_especificacao = $('#equipamento_especificacao').val();
            var municaoEspecie = $('#municaoEspecie').val();


            $('label[for="tipo"]').html("Letalidade");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='letal'>Letal</option>" +
                "<option value='menosLetal'>Menos Letal</option>");
            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');

                var tipo = $("#tipo").val();
                var calibre = $("#calibre").val();

            if (calibre == 12 && tipo == "letal") {

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="ttt">TTT</option>'+
                        '<option value="ps">PS</option>'+
                        '<option value="knockDonw">Knock Down</option>'+
                        '<option value="sg">SG</option>');


            }if(calibre == 12 && tipo == "menosLetal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="403AM">403 AM</option>'+
                        '<option value="403AMP">403 AM/P</option>'+
                        '<option value="403AMPSR">403 AM/PSR</option>');

                }if(calibre == 40 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="CHPP">CHPP</option>'+
                        '<option value="CHOG">CHOG</option>'+
                        '<option value="CSCV">CSCV</option>'+
                        '<option value="EXPP">EXPP</option>'+
                        '<option value="ETPP">ETPP</option>'+
                        '<option value="ETOG">ETOG</option>'+
                        '<option value="FRANGIVEL">FRANGIVEL</option>'
                    );

                }if(calibre == 38 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="CHPP">CHPP</option>'+
                        '<option value="CHCV">CHCV</option>'+
                        '<option value="CSCV">CSCV</option>'+
                        '<option value="CHOG">CHOG</option>'+
                        '<option value="EXPO">EXPO</option>'+
                        '<option value="EXPP">EXPP</option>'+
                        '<option value="ETPP">ETPP</option>'
                    );

                }if(calibre == 556 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="comum">Comum</option>'+
                        '<option value="tracanteM196">TracanteM196</option>'+
                        '<option value="tracanteL110">TracanteL110</option>'+
                        '<option value="SS109">SS109</option>'+
                        '<option value="highPerformance">High Performance</option>'+
                        '<option value="IRTracer">IR Tracer</option>'
                    );

                }if(calibre == 762 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="comum">Comum</option>'+
                        '<option value="perfurante">Perfurante</option>'+
                        '<option value="tracante">Traçante</option>'+
                        '<option value="IRTracer">IR Tracer</option>'
                    );

                }
            $("#calibre").change(function() {

                var tipo = $("#tipo").val();
                var calibre = $("#calibre").val();

                if (calibre == 12 && tipo == "letal") {

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="ttt">TTT</option>'+
                        '<option value="ps">PS</option>'+
                        '<option value="knockDonw">Knock Down</option>'+
                        '<option value="sg">SG</option>');

                }if(calibre == 12 && tipo == "menosLetal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="403AM">403 AM</option>'+
                        '<option value="403AMP">403 AM/P</option>'+
                        '<option value="403AMPSR">403 AM/PSR</option>');

                }if(calibre == 40 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="CHPP">CHPP</option>'+
                        '<option value="CHOG">CHOG</option>'+
                        '<option value="CSCV">CSCV</option>'+
                        '<option value="EXPP">EXPP</option>'+
                        '<option value="ETPP">ETPP</option>'+
                        '<option value="ETOG">ETOG</option>'+
                        '<option value="FRANGIVEL">FRANGIVEL</option>'
                    );

                }if(calibre == 38 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="CHPP">CHPP</option>'+
                        '<option value="CHCV">CHCV</option>'+
                        '<option value="CSCV">CSCV</option>'+
                        '<option value="CHOG">CHOG</option>'+
                        '<option value="EXPO">EXPO</option>'+
                        '<option value="EXPP">EXPP</option>'+
                        '<option value="ETPP">ETPP</option>'
                    );

                }if(calibre == 556 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="comum">Comum</option>'+
                        '<option value="tracanteM196">TracanteM196</option>'+
                        '<option value="tracanteL110">TracanteL110</option>'+
                        '<option value="SS109">SS109</option>'+
                        '<option value="highPerformance">High Performance</option>'+
                        '<option value="IRTracer">IR Tracer</option>'
                    );

                }if(calibre == 762 && tipo == "letal"){

                    $("#municao").html("<option value=''>Selecione</option>"+
                        '<option value="comum">Comum</option>'+
                        '<option value="perfurante">Perfurante</option>'+
                        '<option value="tracante">Traçante</option>'+
                        '<option value="IRTracer">IR Tracer</option>'
                    );

                }

            });

            $('#municao option[value='+municaoEspecie+']').attr('selected','selected');


            $("#divTipo, #divCalibre, #divQtd, #divFabricante, #divLote, #divValidade, #divTipoMunicao, #divMunicao").css("display","block")
            break;

        case "carregador":


            var equipamento_especie = $('#equipamento_especie').val();
            var armamentoCarregador = $('#armamentoCarregador').val();
            var capacidadeCarregador = $('#capacidadeCarregador').val();

            tipoPost = "carregador";

            $('label[for="tipo"]').html("Tipo de Carregador");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='bifilar'>Bifilar</option>" +
                "<option value='monofilar'>Monofilar</option>");
            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');


            $("#armamento").html("<option value=''>Selecione</option>"+
                '<option value="ct">CT</option>'+
                '<option value="carabina">Carabina</option>'+
                '<option value="espingarda">Espingarda</option>'+
                '<option value="Fuzil">Fuzil</option>'+
                '<option value="Lancador">Lançador</option>'+
                '<option value="pistola">Pistola</option>'+
                '<option value="revolver">Revólver</option>',
                '<option value="smt">SMT</option>');
            $('#armamento option[value='+armamentoCarregador+']').attr('selected','selected');


            for (var i = 15; i < 50; i = i + 5) {

                $("#capacidade").append('<option value="'+i+'">'+i+'</option>');

            }
            $('#capacidade option[value='+capacidadeCarregador+']').attr('selected','selected');


            $("#divTipo, #divCalibre, #divQtd, #divFabricante, #divModelo, #divArmamento, #divCapacidade").css("display","block")
            break;

        case "algema":

            var equipamento_especie = $('#equipamento_especie').val();
            var equipamento_especificacao = $('#equipamento_especificacao').val();

            tipoPost = "algema";

            $('label[for="tipo"]').html("Tipo de Algema");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='metalica'>Metálica</option>" +
                "<option value='mista'>Mista</option>" +
                "<option value='plastica'>Plástica</option>");

                if(equipamento_especificacao == "metalica"){

                    $("#tipoAlgema").html("<option value=''>Selecione</option>" +
                        "<option value='dobradica'>Dobradiça</option>" +
                        "<option value='corrente'>Corrente</option>")
                }
                if(equipamento_especificacao == "mista"){

                    $("#tipoAlgema").html("<option value=''>Selecione</option>" +
                        "<option value='pe'>Pé</option>" +
                        "<option value='maoCintura'>Mão/Cintura</option>")
                }
                if(equipamento_especificacao == "plastica"){

                    $("#tipoAlgema").html("<option value=''>Selecione</option>")

                }

            $("#tipo").change(function(){

                if($("#tipo").val() == "metalica"){
                    $("#tipoAlgema").html("<option value=''>Selecione</option>" +
                        "<option value='dobradica'>Dobradiça</option>" +
                        "<option value='corrente'>Corrente</option>")
                }
                if($("#tipo").val() == "mista"){
                    $("#tipoAlgema").html("<option value=''>Selecione</option>" +
                        "<option value='pe'>Pé</option>" +
                        "<option value='maoCintura'>Mão/Cintura</option>")
                }
                if($("#tipo").val() == "plastica"){

                    $("#tipoAlgema").html("<option value=''>Selecione</option>")

                }
            })



            $('#tipo option[value='+equipamento_especificacao+']').attr('selected','selected');

            $('#tipoAlgema option[value='+equipamento_especie+']').attr('selected','selected');


            $("#divN_serie, #divTipo, #divQtd, #divTipoAlgema, #divFabricante").css("display","block")
            break;

        case "granada":

            var equipamento_especie = $('#equipamento_especie').val();
            var equipamento_especificacao = $('#equipamento_especificacao').val();
            var modeloGranada = $('#modeloGranadaEditar').val();

            tipoPost = "granada";

            $('label[for="tipo"]').html("Tipo da Granada");

            $("#tipo").html("<option value=''>Selecione</option>" +
                "<option value='letal'>Letal</option>" +
                "<option value='menosLetal'>Menos Letal</option>");

            $("#tipoGranada").html("<option value=''>Selecione</option>" +
                "<option value='fumigena'>Fumígena</option>" +
                "<option value='Explosiva'>Explosiva</option>");

            // criar ajax com modelos
            $("#modeloGranada").html("<option value=''>Selecione</option>" +
                "<option value='GL304'>GL304</option>");

            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');
            $('#tipoGranada option[value='+equipamento_especificacao+']').attr('selected','selected');
            $('#modeloGranada option[value='+modeloGranada+']').attr('selected','selected');



            $("#divTipo, #divFabricante, #divQtd, #divTipoGranada, #divModeloGranada, #divLote, #divValidade").css("display","block")
            break;

        case "epi":

            var equipamento_especie = $('#equipamento_especie').val();

            tipoPost = "epi";

            $("#divTipo, #divQtd").css("display","block");

            $('label[for="tipo"]').html("Tipo de Equipamento");

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

            $('#tipo option[value='+equipamento_especie+']').attr('selected','selected');
            $('#tipo option').not(':selected').remove();

                var tipoEPI = $("#tipo").val();

                if(tipoEPI == 'capacete' || tipoEPI == 'escudo'){
                    $("#divN_serie, #divFabricante, #divLote, #divValidade, #divQtd").css("display","block");
                }else{
                    $("#divN_serie, #divFabricante, #divLote, #divValidade").css("display","none");
                }


            break;

        case "colete":

            tipoPost = "colete";

            $("#divN_serie, #divFabricante, #divValidade, #divTamanho").css("display","block")
            break;

        case "espargidor":

            tipoPost = "espargidor";


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../armaria/ajax-equipamento-fabricante",
                data: {tipo: tipoPost},
                beforeSend: function() {

                },
                success: function (dados) {

                    $('#fabricante').html('');

                    console.log(dados);


                    $.each(dados,function(key, value)
                    {
                        $('#fabricante').append('<option value=' + value.id + '>' + value.nome + '</option>');
                    });

                }
            });


            $("#divFabricante, #divValidade, #divQtd, #divVolumePeso, #divModelo").css("display","block")
            break;

    }

});