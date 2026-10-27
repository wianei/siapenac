$(document).ready(function(){


    $(document).on("change", ".tipoEscolha", function () {

        var idLotacao = $(this).val();
        $(this).css("border","1px solid green");

        $.each($('.tipoEscolha').not($(this)), function() {
            var value = $(this).val();

            if (idLotacao === value) {
                $(this).val('').css("border","1px solid red");

            }

        });

    });

    $(document).on("click", "#btnSalvar", function () {

        $("#divLotacoes").addClass("displayNone");
        $(this).addClass("displayNone");
        $("#btnSelecionarNovamente, #btnConfirmarEscolha").removeClass("displayNone");

        var i = 1;
        $("#divConfirm").append('<h4>Você confirma a escolha das Lotações abaixo?</h4>');
        $.each($('.tipoEscolha'), function() {

            var opcaoTexto =  $("option:selected", this).text();
            var idOpcao = $("option:selected", this).val();

            if(idOpcao == "")
            {
                $("#divConfirm").append('Selecione sua '+i+'ª opção<br>');
                $("#btnConfirmarEscolha").addClass("displayNone");

                return false;
            }else {
                $("#divConfirm").append(i + 'ª Opção: ' + opcaoTexto + '<br>');
            }
            i++;
        });

    });

    $(document).on("click", "#btnSelecionarNovamente", function () {

        $("#divLotacoes, #btnSalvar").removeClass("displayNone");
        $(this).addClass("displayNone");
        $("#btnConfirmarEscolha").addClass("displayNone");
        $("#divConfirm").html("");

    });


});