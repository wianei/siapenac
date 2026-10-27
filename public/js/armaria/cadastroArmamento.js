$(document).ready(function () {


    $("#tipoArmamento").change(function(){

        $('#formCadastroArmamento').find('input:not(input[name="_token"]), select, textarea').not(this).val('');

        $('#fabricante').html('');

        $("#dadosGerais").removeClass('displayNone')

        $("#divInputs div").css("display","none");

        var tipoArmamento = $(this).val();
        var tipoPost = "";

        switch (tipoArmamento){
            case "armaFogo":

                $('label[for="tipo"]').html("Tipo do Armamento");
                $("#divN_serie, #divTipo, #divCalibre, #divFabricante, #divModelo, #divArmamento").css("display","block");
                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='longa'>Longa</option>" +
                    "<option value='curta'>Curta</option>");

                tipoPost = "armaFogo";

                $("#tipo").change(function(){

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

                })

                break;

            case "dei":

                $('label[for="tipo"]').html("Tipo do Armamento");
                $("#divN_serie, #divTipo, #divFabricante, #divModelo, #divArmamento").css("display","block");
                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='longa'>Longa</option>" +
                    "<option value='curta'>Curta</option>");

                tipoPost = "dei";

                $("#tipo").change(function(){

                    if($("#tipo").val() == "longa"){

                        $("#armamento").html("<option value=''>Selecione</option>");
                    }
                    if($("#tipo").val() == "curta"){

                        $("#armamento").html("<option value=''>Selecione</option>"+
                            '<option value="spark">Spark</option>');
                    }

                })

                break;

            case "municao":

                tipoPost = "municao";

                $('label[for="tipo"]').html("Letalidade");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='letal'>Letal</option>" +
                    "<option value='menosLetal'>Menos Letal</option>");

                $("#calibre").change(function() {

                    var tipo = $("#tipo").val();
                    var calibre = $("#calibre").val();

                    if (calibre == 12 && tipo == "letal") {

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="3T">3T</option>'+
                            '<option value="3T TREINA">3T TREINA</option>'+
                            '<option value="SG">SG</option>'+
                            '<option value="PROJETIL SINGULAR">PROJETIL SINGULAR</option>'+
                            '<option value="BALOTE FOSTER">BALOTE FOSTER</option>'+
                            '<option value="BALOTE KNOCK DOWN">BALOTE KNOCK DOWN</option>'+
                            '<option value="OPEN DOOR">OPEN DOOR</option>');

                    }if(tipo == "menosLetal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="ANTI-MOTIM PLÁSTICO">ANTI-MOTIM PLÁSTICO</option>'+
                            '<option value="AM 403/PSR">AM 403/PSR</option>'+
                            '<option value="AM 403">AM 403 </option>'+
                            '<option value="AM 403/P">AM 403/P</option>'+
                            '<option value="AM 403A">AM 403A</option>'+
                            '<option value="AM 403/M">AM 403/M</option>'+
                            '<option value="AM 404">AM 404</option>'+
                            '<option value="AM 404/12E">AM 404/12E</option>'+
                            '<option value="AM 470">AM 470</option>'+
                            '<option value="AM 405">AM 405</option>'+
                            '<option value="AM 405/A">AM 405/A</option>'+
                            '<option value="GL 101">GL 101</option>'+
                            '<option value="GL 102">GL 102</option>'+
                            '<option value="GL 103">GL 103</option>'+
                            '<option value="GL 104">GL 104</option>'+
                            '<option value="GL 103/A">GL 103/A</option>'+
                            '<option value="GL 104/A">GL 104/A</option>'+
                            '<option value="GL 201">GL 201</option>'+
                            '<option value="GL 202">GL 202</option>'+
                            '<option value="GL 203">GL 203</option>'+
                            '<option value="GL 203/T">GL 203/T</option>'+
                            '<option value="GL 203/L">GL 203/L</option>'+
                            '<option value="NT 400">NT 400</option>'+
                            '<option value="NT 907-CS">NT 907-CS0</option>'+
                            '<option value="NT 907-i">NT 907-i</option>'+
                            '<option value="MSK 106">MSK 106</option>'+
                            '<option value="MSK 108">MSK 108</option>'+
                            '<option value="Projétil Fum. Color.">Projétil Fum. Color.</option>'
                        );

                    }if(calibre == 40 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="EOPP">EOPP</option>'+
                            '<option value="CSCV">CSCV</option>'+
                            '<option value="CHPP">CHPP</option>'+
                            '<option value="ETPP">ETPP</option>'+
                            '<option value="CXPO">CXPO</option>'+
                            '<option value="EXPO GOLDEN HEX">EXPO GOLDEN HEX</option>'+
                            '<option value="EXPO BONDED">EXPO BONDED</option>'+
                            '<option value="EXPO GOLD">EXPO GOLD</option>'
                        );

                    }if(calibre == 38 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="EOPP">EOPP</option>'+
                            '<option value="CHOG TREINA">CHOG TREINA</option>'+
                            '<option value="CHOG CURTO">CHOG CURTO</option>'+
                            '<option value="CHOG">CHOG</option>'+
                            '<option value="CHCV">CHCV</option>'+
                            '<option value="EXPO">EXPO</option>'+
                            '<option value="EXPO + P">EXPO + P</option>'+
                            '<option value="EXPO + P +">EXPO + P +</option>'+
                            '<option value="EXPO + P +  GOLDEN HEX">EXPO + P +  GOLDEN HEX</option>'+
                            '<option value="EXPO + P + BONDED">EXPO + P + BONDED</option>'
                        );

                    }if(calibre == 556 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="M193 TREINA">M193 TREINA</option>'+
                            '<option value="M193">M193</option>'+
                            '<option value="SS109">SS109</option>'+
                            '<option value="M196">M196</option>'+
                            '<option value="L110">L110</option>'+
                            '<option value="HIGH PERFORMANCE">HIGH PERFORMANCE</option>'+
                            '<option value="OTM">OTM</option>'+
                            '<option value="IR TRACER">IR TRACER</option>'+
                            '<option value="SAT">SAT</option>'
                        );

                    }if(calibre == 762 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="COMUM TREINA">COMUM TREINA</option>'+
                            '<option value="COMUM">COMUM</option>'+
                            '<option value="PERFURANTE">PERFURANTE</option>'+
                            '<option value="TRAÇANTE">TRAÇANTE</option>'+
                            '<option value="IR TRACER">IR TRACER</option>'
                        );

                    }if(calibre == 9 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="EOGG">EOGG</option>'+
                            '<option value="CHOG">CHOG</option>'+
                            '<option value="ETOG">ETOG</option>'+
                            '<option value="EXPP FLAT">EXPP FLAT</option>'+
                            '<option value="ETPP SUBSONICO">ETPP SUBSONICO</option>'+
                            '<option value="EXPO">EXPO</option>'+
                            '<option value="CXPO">CXPO</option>'+
                            '<option value="EXPO +P+ GOLDEN HEX">EXPO +P+ GOLDEN HEX</option>'+
                            '<option value="EXPO +P+ BONDED">EXPO +P+ BONDED</option>'+
                            '<option value="EXPO +P BONDED">EXPO +P BONDED</option>'+
                            '<option value="FRANGÍVEL">FRANGÍVEL</option>'
                        );

                    }if(calibre == 45 && tipo == "letal"){

                        $("#municao").html("<option value=''>Selecione</option>"+
                            '<option value="CSCV">CSCV</option>'+
                            '<option value="ETOG">ETOG</option>'+
                            '<option value="CXPO + P">CXPO + P</option>'+
                            '<option value="EXPO + P">EXPO + P</option>'
                        );

                    }

                });

                $("#divTipo, #divCalibre, #divQtd, #divFabricante, #divLote, #divValidade, #divTipoMunicao, #divMunicao").css("display","block")
                break;

            case "carregador":

                tipoPost = "carregador";

                $('label[for="tipo"]').html("Tipo de Carregador");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='bifilar'>Bifilar</option>" +
                    "<option value='monofilar'>Monofilar</option>");

                $("#armamento").html("<option value=''>Selecione</option>"+
                    '<option value="ct">CT</option>'+
                    '<option value="carabina">Carabina</option>'+
                    '<option value="espingarda">Espingarda</option>'+
                    '<option value="Fuzil">Fuzil</option>'+
                    '<option value="Lancador">Lançador</option>'+
                    '<option value="pistola">Pistola</option>'+
                    '<option value="revolver">Revólver</option>',
                    '<option value="smt">SMT</option>');


                for (var i = 15; i < 50; i = i + 5) {

                    $("#capacidade").append('<option value="'+i+'">'+i+'</option>');

                }

                $("#divTipo, #divCalibre, #divQtd, #divFabricante, #divModelo, #divArmamento, #divCapacidade").css("display","block")
                break;

            case "algema":

                tipoPost = "algema";

                $('label[for="tipo"]').html("Tipo de Algema");

                $("#tipo").html("<option value=''>Selecione</option>" +
                    "<option value='metalica'>Metálica</option>" +
                    "<option value='mista'>Mista</option>" +
                    "<option value='plastica'>Plástica</option>");

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


                $("#divN_serie, #divTipo, #divQtd, #divTipoAlgema, #divFabricante").css("display","block")
                break;

            case "granada":

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
                    "<option value='GA 100'>GA 100</option>"+
                    "<option value='GB 704'>GB 704</option>"+
                    "<option value='GB 704-i'>GB 704-i</option>"+
                    "<option value='GB 705'>GB 705</option>"+
                    "<option value='GB 705-i'>GB 705-i</option>"+
                    "<option value='GB 707'>GB 707</option>"+
                    "<option value='GB 708'>GB 708</option>"+
                    "<option value='GL 120-oc'>GL 120-OC</option>"+
                    "<option value='GL 700'>GL 700</option>"+
                    "<option value='GL 302-i'>GL 302-i</option>"+
                    "<option value='GL 304'>GL 304</option>"+
                    "<option value='GL 305'>GL 305</option>"+
                    "<option value='GL 307'>GL 307</option>"+
                    "<option value='GL 308'>GL 308</option>"+
                    "<option value='AM 500'>AM 500</option>"+
                    "<option value='GL 310'>GL 310</option>"+
                    "<option value='GL 309'>GL 309</option>"+
                    "<option value='GL 301'>GL 301 </option>"+
                    "<option value='GL 302'>GL 302</option>"+
                    "<option value='GL 303'>GL 303</option>"+
                    "<option value='GL 311'>GL 311</option>"+
                    "<option value='GL 300/TH'>GL 300/TH</option>"+
                    "<option value='GL 300/T'>GL 300/T</option>"+
                    "<option value='GM 100'>GM 100</option>"+
                    "<option value='GM 101'>GM 101</option>"+
                    "<option value='GM 102'>GM 102</option>"+
                    "<option value='SS 601'>SS 601</option>"+
                    "<option value='SS 601-VD-i'>SS 601-VD-i</option>"+
                    "<option value='SS 601-AM-i'>SS 601-AM-i</option>"+
                    "<option value='MB 306/TI'>MB 306/TI</option>"+
                    "<option value='MB 502'>MB 502</option>"
                );



                $("#divTipo, #divFabricante, #divQtd, #divTipoGranada, #divModeloGranada, #divLote, #divValidade").css("display","block")
                break;

            case "epi":

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

                $( document ).on( "change", "#tipo", function() {

                    var tipoEPI = $("#tipo").val();

                    if(tipoEPI == 'capacete' || tipoEPI == 'escudo'){
                        $("#divN_serie, #divFabricante, #divLote, #divValidade, #divQtd").css("display","block");
                    }else{
                        $("#divN_serie, #divFabricante, #divLote, #divValidade").css("display","none");
                    }

                });

                break;

            case "colete":

                tipoPost = "colete";


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


        $( document ).on( "change", "#tipo", function() {


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

        });

    });

    $("#validade").mask("00/00/0000")

});
