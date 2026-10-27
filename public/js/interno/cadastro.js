/*
 |--------------------------------------------------------------------------
 | Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
 |--------------------------------------------------------------------------
 */

$.jMaskGlobals.watchDataMask = true;

$(document).ready(function (){
    $('#data_nascimento, #rg_data, #data_entrada').mask('00/00/0000');
    $('#hora_entrada').mask('00:00', {reverse: true});

    /**
     * adiciona mais campos para outros nomes
     * @type {number}
     */
    var max_fields = 4;
    var wrapper = $("#groupyOutrosNomes");
    var add_button = $("#addFone");

    var x = 1; //initlal text box count
    $(add_button).click(function (e) {
        e.preventDefault();
        if (x < max_fields) {
            x++;
            $(wrapper).append('<div><input type="text" name="outro_nome[]" class="form-control mt-2 outro_nome" id="outro_nome'+x+'" placeholder="vulgo, nome social..."><i class="fas fa-minus-circle cursorPointer remove_field"></i></div>');
        }
    });

    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    });
    // -------------------------- !! ------------------------------------------------------------------


    /**
     * completa o cep buscando no web service
     */

    $( document ).on( "blur", "#endereco_cep", function() {

        var cep = $(this).val().replace(/\D/g, '');

        if (cep != "") {

            var validacep = /^[0-9]{8}$/;

            if (validacep.test(cep)) {

                $('#endereco_logradouro, #endereco_bairro, #endereco_cidade').attr('disabled', true);

                $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#endereco_logradouro").val(dados.logradouro);
                        $("#endereco_bairro").val(dados.bairro);
                        $("#endereco_cidade").val(dados.localidade);
                        $('#endereco_logradouro, #endereco_bairro, #endereco_cidade').attr('disabled', false);

                        var returnUf = dados.uf;

                        $("#endereco_estado option[value='" + returnUf + "']").attr("selected", 1);
                        $("#endereco_cep").addClass("border1pxSolidGreen");

                    }
                    else {
                        //CEP pesquisado não foi encontrado.
                        $('#endereco_logradouro, #endereco_bairro, #endereco_cidade').val('').attr('disabled', false);
                        $("#endereco_cep").addClass("border1pxSolidRed");
                    }
                });
            }
            else {
                //cep é inválido.
                $('#endereco_logradouro, #endereco_bairro, #endereco_cidade').val('').attr('disabled', false);
                $("#endereco_cep").addClass("border1pxSolidRed");
            }
        }
        else {
            //cep sem valor, limpa formulário.
            $('#endereco_logradouro, #endereco_bairro, #endereco_cidade').val('').attr('disabled', false);
            $("#endereco_cep").addClass("border1pxSolidRed");
        }

    });

    $( document ).on( "click", "#addEndereco", function() {

        var max_fields_end = 4;
        var wrapper_end = $("#groupEndereco");


        if (x < max_fields_end) {
            x = parseInt(x) + parseInt(1);
            $(wrapper_end).append('<div><hr>' +
                '<div class="form-row">' +
                '<div class="form-group col-sm-3">' +
                '<label for="endereco_cep">CEP</label>' +
                '<input type="text" name="endereco_cep[]" class="form-control cep" id="endereco_cep' + x + '" placeholder="Informe o CEP">' +
                '</div>' +

                '<div class="form-group col-sm-6">' +
                '<label for="endereco_logradouro">Logradouro</label>' +
                '<input type="text" name="endereco_logradouro[]" class="form-control" id="endereco_logradouro' + x + '" placeholder="Rua, av..">' +
                '</div>' +

                '<div class="form-group col-sm-3">' +
                '<label for="endereco_numero">Número</label>' +
                '<input type="text" name="endereco_numero[]" class="form-control endereco_numero" id="endereco_numero' + x + '" placeholder="Número">' +
                '</div>' +

                '<div class="form-group col-sm-3">' +
                '<label for="endereco_bairro">Bairro</label>' +
                '<input type="text" name="endereco_bairro[]" class="form-control" id="endereco_bairro' + x + '" placeholder="Bairro">' +
                '</div>' +

                '<div class="form-group col-sm-3">' +
                '<label for="endereco_complemento">Complemento</label>' +
                '<input type="text" name="endereco_complemento[]" class="form-control" id="endereco_complemento' + x + '" placeholder="Complemento">' +
                '</div>' +

                '<div class="form-group col-sm-3">' +
                '<label for="endereco_cidade">Cidade</label>' +
                '<input type="text" name="endereco_cidade[]" class="form-control" id="endereco_cidade' + x + '" placeholder="Cidade">' +
                '</div>' +

                '<div class="form-group col-sm-3">' +
                '<label for="endereco_estado">Estado</label>' +
                '<select id="endereco_estado' + x + '" name="endereco_estado[]" class="form-control">' +
                '<option value="">Selecione</option>' +
                '<option value="AC">Acre</option>' +
                '<option value="AL">Alagoas</option>' +
                '<option value="AP">Amapá</option>' +
                '<option value="AM">Amazonas</option>' +
                '<option value="BA">Bahia</option>' +
                '<option value="CE">Ceará</option>' +
                '<option value="DF">Distrito Federal</option>' +
                '<option value="ES">Espírito Santo</option>' +
                '<option value="GO">Goiás</option>' +
                '<option value="MA">Maranhão</option>' +
                '<option value="MT">Mato Grosso</option>' +
                '<option value="MS">Mato Grosso do Sul</option>' +
                '<option value="MG">Minas Gerais</option>' +
                '<option value="PA">Pará</option>' +
                '<option value="PB">Paraíba</option>' +
                '<option value="PR">Paraná</option>' +
                '<option value="PE">Pernambuco</option>' +
                '<option value="PI">Piauí</option>' +
                '<option value="RJ">Rio de Janeiro</option>' +
                '<option value="RS">Rio Grande do Sul</option>' +
                '<option value="RN">Rio Grande do Norte</option>' +
                '<option value="RO">Rondônia</option>' +
                '<option value="RR">Roraima</option>' +
                '<option value="SC">Santa Catarina</option>' +
                '<option value="SP">São Paulo</option>' +
                '<option value="SE">Sergipe</option>' +
                '<option value="TO">Tocantins</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<h6 class="btn btn-outline-danger float-right" id="removeEndereco">' +
                'Excluir endereço ' +
                '<i class="mdi mdi-map-marker-minus"></i></h6><br><br>' +
                '</div>');
        }

        $( document ).on( "blur", "#endereco_cep"+x, function() {

            var cep = $(this).val().replace(/\D/g, '');

            if (cep != "") {

                var validacep = /^[0-9]{8}$/;

                if (validacep.test(cep)) {

                    $('#endereco_logradouro'+x+', #endereco_bairro'+x+', #endereco_cidade'+x+'').attr('disabled', true);

                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                        if (!("erro" in dados)) {
                            //Atualiza os campos com os valores da consulta.
                            $("#endereco_logradouro"+x).val(dados.logradouro);
                            $("#endereco_bairro"+x).val(dados.bairro);
                            $("#endereco_cidade"+x).val(dados.localidade);
                            $('#endereco_logradouro'+x+', #endereco_bairro'+x+', #endereco_cidade'+x+'').attr('disabled', false);

                            var returnUf = dados.uf;

                            $("#endereco_estado"+x+" option[value='" + returnUf + "']").attr("selected", 1);
                            $("#endereco_cep"+x).addClass("border1pxSolidGreen");

                        }
                        else {
                            //CEP pesquisado não foi encontrado.
                            $('#endereco_logradouro'+x+', #endereco_bairro'+x+', #endereco_cidade'+x+'').val('').attr('disabled', false);
                            $("#endereco_cep"+x).addClass("border1pxSolidRed");
                        }
                    });
                }
                else {
                    //cep é inválido.
                    $('#endereco_logradouro'+x+', #endereco_bairro'+x+', #endereco_cidade'+x+'').val('').attr('disabled', false);
                    $("#endereco_cep"+x).addClass("border1pxSolidRed");
                }
            }
            else {
                //cep sem valor, limpa formulário.
                $('#endereco_logradouro'+x+', #endereco_bairro'+x+', #endereco_cidade'+x+'').val('').attr('disabled', false);
                $("#endereco_cep"+x).addClass("border1pxSolidRed");
            }
        });


    });

    $(document).on("click", "#removeEndereco", function () { //user click on remove text
        $(this).parent('div').remove();
    });
    // -------------------------- !! ------------------------------------------------------------------

    /**
     * cadastro de filhos
     */
    $( document ).on( "click", ".addFilhos", function() {
        $("#divFilhos")
                .clone()
                .find("input, select").val("")
                .end()
                .prependTo("#cloneDivFilhos");
    });
    
    $( document ).on( "click", ".btnDelFilhos", function() {
        if($(".btnDelFilhos").length > 1){
            $(this).parent('div').parent('div').remove();

        }

    });


    /**
     * busca o municipio pelo select do estado
     */
    $( document ).on( "change", ".selectEstados", function() {

        var idSelect = $(this).attr('id');

        switch (idSelect){
            case "naturalidade_estado":
                var selectCidade = "naturalidade_cidade";
                break;
        }

        $('#'+selectCidade).html("");

        var ufPost = $("#"+idSelect).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../municipios/pesquisa-ajax",
            data: {uf: ufPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);

                $.each(objt,function(key, value)
                {
                    $('#'+selectCidade).append('<option value=' + value + '>' + value + '</option>');
                });

            }
        });

    });
    // -------------------------- !! ------------------------------------------------------------------



    /**
     * adiciona mais campos de telefone
     */
    $( document ).on( "click", ".btAddFone", function() {

            $('#grupoContato').append(
                '<div class="form-group col-sm-3">' +
                '<label for="telefone">Telefone</label>' +
                '<input type="text" name="telefone[]" class="form-control telefone" placeholder="Informe o telefone">' +
                '<i class="fas fa-minus-circle cursorPointer remove_field"></i>' +
                '</div>'
            );


        $('#grupoContato').on("click", ".remove_field", function () { //user click on remove text
            $(this).parent('div').remove();
        });


    });
//    ------------------------- !! -----------------------------------

    ///**
    // * adiciona mais campos de telefone
    // */
    //$( document ).on( "click", ".btAddArtigo", function() {
    //
    //        $('#grupoArtigo').append(
    //            '<div class="form-group col-sm-3">' +
    //            '<label for="artigo">Artigo</label>' +
    //            '<input type="text" name="artigo[]" class="form-control telefone" placeholder="Informe o artigo">' +
    //            '<i class="fas fa-minus-circle cursorPointer remove_field"></i>' +
    //            '</div>'
    //        );
    //
    //
    //    $('#grupoArtigo').on("click", ".remove_field", function () { //user click on remove text
    //        $(this).parent('div').remove();
    //    });
    //
    //
    //});
//    ------------------------- !! -----------------------------------


    /**
     * Se a naturalidade for diferente de brasil, esconde estado e cidade.
     */
    $( document ).on( "change", "#naturalidade_pais", function() {
        var naturalidade = $(this).val();
        if(naturalidade != "Brasil")
        {
            $("#divNaturalidadeEstado, #divNaturalidadeCidade").hide('fast');
        }else{
            $("#divNaturalidadeEstado, #divNaturalidadeCidade").show('fast');
        }
    });
//    ------------------------- !! -----------------------------------

    $( document ).on( "click", ".btAddDoc", function() {

        $('#grupoDoc').append(
            '<div class="form-row width100Percent">'+
            '<div class="form-group col-sm-3">'+
            '<label for="doc">Documento</label>'+
            '<input type="file" name="inputFileDoc[]" class="form-control" id="inputFileDoc" accept="application/pdf">'+
            '</div>'+

            '<div class="form-group col-sm-3">'+
            '<label for="nomeDoc">Nome</label>'+
            '<input type="text" name="nomeDoc[]" class="form-control" id="nomeDoc" placeholder="Informe um nome ou descrição">'+
            '<i class="fas fa-minus-circle cursorPointer removeFieldDoc"></i>' +
            '</div>'+
            '</div>'
        );


    });

    $('#grupoDoc').on("click", ".removeFieldDoc", function () { //user click on remove text
        $(this).parent('div').parent('div').remove();
    });

    $(document).on("change", "#sentenciado", function () {

        $("#regime").val("");
        $("#saidasexternas").val("");

        var sentenciado = $(this).val();
        if(sentenciado == 'N')
        {
            $('#divRegime').addClass('displayNone');
            $('#divSaidasExternas').addClass('displayNone');
        }else{
            $('#divRegime').removeClass('displayNone');
        }
    });

    $(document).on("change", "#regime", function () {

        var regime = $(this).val();

        if(regime == 'SEMIABERTO')
        {
            $('#divSaidasExternas').removeClass('displayNone');
        }else{
            $('#divSaidasExternas').addClass('displayNone');
        }
    });


//    ------------------------- !! -----------------------------------

    $(document).on("change", "#possuiDeficiencia", function () {
        
        var possuiDeficiencia = $(this).val();
        if(possuiDeficiencia == "S"){
            $("#divTipoDeficiencia").removeClass("displayNone");
        }else{
            $("#divTipoDeficiencia, #displayNone, #divCadeirante").addClass("displayNone");
            $('#tipoDeficiencia option[value=""]').attr('selected','selected');
            $('#cadeirante option[value=""]').attr('selected','selected');
        }
    });
    $(document).on("change", "#tipoDeficiencia", function () {
        
        $("#textTipoDeficiencia").html('');
        $("#textTipoDeficiencia").removeClass('displayNone');
        $("#divCadeirante").addClass('displayNone');
        
        
        var tipoDeficiencia = $(this).val();
        
        switch(tipoDeficiencia){
            
            case "Deficiência Intelectual":
                $("#textTipoDeficiencia").html('Pessoas com deficiência intelectual: apresentam limitações no funcionamento mental, afetando tarefas de comunicação, cuidados pessoais,relacionamento social, segurança, determinação, funções acadêmicas, lazer e trabalho.');
                break;
                
            case "Deficiência Física":
                $("#textTipoDeficiencia").html('Pessoas com deficiência física: apresentam limitação do funcionamento físico-motor; são cadeirantes ou pessoas com deficiência motora, causadas por paralisia cerebral, hemiplegias, lesão medular, amputações ou artropatias.');
                $("#divCadeirante").removeClass('displayNone');
                break;
                
            case "Deficiência Auditiva":
                $("#textTipoDeficiencia").html('Pessoas com deficiência auditiva: apresentam perda total da capacidade auditiva. Perda comprovada da capacidade auditiva entre 95% e 100%.');
                break;
                
            case "Deficiência Visual":
                $("#textTipoDeficiencia").html('Pessoas com deficiência visual: não possuem a capacidade física de enxergar por total falta de acuidade visual.');
                break;
                
            case "Deficiência Múltiplas":
                $("#textTipoDeficiencia").html('Pessoas com deficiências múltiplas: apresentam duas ou mais deficiências.');
                break;
                
        }
        
        if(tipoDeficiencia != "Deficiência Física"){
            $('#cadeirante option[value=""]').attr('selected','selected');
        }
        
    });

    /**
     * Validacoes e mascaras
     */
        $('#cpf, #altura, #peso, #telefone, #diasTriagem').mask('0#');
        $('#cpf').mask('00000000000');
        $('.endereco_numero').mask('0#');
        $('.telefone').mask('0#');
        $('.cep').mask('00000000');
        $('#nome, #mae, #pai, #cor_cabelos, .outro_nome, .filhoNome').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[0-9]/g,'') ); }
        );

        function validaData(strDataNascimento)
        {
          let nasc = strDataNascimento.split("/").map(Number);
          let depois18Anos = new Date(nasc[2] + 18, nasc[1] - 1, nasc[0]);
          let agora = new Date();
          if (depois18Anos <= agora){
            // se for maior de 18
            return 1
          }
          else {
            // se for menor de 18
            return 0
          }

        }
        
    function validaCpf(strCPF) {
        var Soma;
        var Resto;
        Soma = 0;
        if (strCPF == "00000000000") return false;

        for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

        Soma = 0;
        for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
        return true;
    }

    $( document ).on("submit", "#formCadastroInterno", function() {
        $("#flashRetorno").html('');
        var control = 0;

        if( document.getElementById("inputFile").files.length == 0 || $("#tipoFotoUm").val() != "principal"){

            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos uma foto como "principal" para o interno!<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');


            control = 1;
        }

        var sentenciado = $("#sentenciado").val();
        var regime = $("#regime").val();
        if(sentenciado == 'S' && regime == "")
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o regime do Interno!<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        }
        
        var saidasexternas = $("#saidasexternas").val();
        if(regime == 'SEMIABERTO' && saidasexternas == "")
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe se o interno terá saídas externas!<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        }

        var cpf = $("#cpf").val();
        if(cpf == "00000000000")
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">CPF inválido! Campo não obrigatório.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        }

        var data_nascimento = $("#data_nascimento").val().split("/")[2].substring(0,2);
        if(data_nascimento != "19" && data_nascimento != "20")
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de nascimento entre 1900 e o ano atual.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

            control = 1;

        }
        
        var validaDataNascimento = validaData($("#data_nascimento").val());
        if(validaDataNascimento == 0)
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">A data de nascimento informada faz referência a um interno menor de idade.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

            control = 1;

        }

        var possuiDeficiencia = $("#possuiDeficiencia").val();
        var tipoDeficiencia = $("#tipoDeficiencia").val();
        var cadeirante = $("#cadeirante").val();
        if(possuiDeficiencia == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe se o interno(a) possui algum tipo de deficiência.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
        }else{
            if(possuiDeficiencia == "S" && tipoDeficiencia == ""){
                    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o tipo de deficiência.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
            }
            if(tipoDeficiencia == "Deficiência Física"){
                if(cadeirante == ""){
                    $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe se o interno(a) é cadeirante.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
                }
            }
        }

        var rg_data_val = $("#rg_data").val();
        if(rg_data_val != ''){
            var rg_data = rg_data_val.split("/")[2].substring(0,2);
            if(rg_data != "19" && rg_data != "20")
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de expedição do RG entre 1900 e o ano atual.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
            }
        }


        var data_entrada_l = $("#data_entrada").val();
        if(data_entrada_l.length != 10)
        {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada válida.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            control = 1;
        } else {
            var data_entrada = data_entrada_l.split("/")[2].substring(0,2);
            if(data_entrada != "19" && data_entrada != "20")
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada válida.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
                control = 1;
            }
        }

        var hora = $('#hora_entrada').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            
            control = 1;
        }

        if(control == 1) return false;
        $('#insertInterno').attr('disabled', true);
        
    });

    //    ------------------------- !! -----------------------------------



});
