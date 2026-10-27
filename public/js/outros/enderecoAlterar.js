$(document).ready(function(){

    /**
     * endereco
     */

    var x = 1;
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

    $(document).on("click", "#removeEnderecoEdit", function () { //user click on remove text
        $(this).parent('div').remove();
        var id = $(this).parent('div').data("content");
        $("#divEnderecosRemovidos").append('<input type="hidden" name="enderecosRemovidos[]" id="enderecosRemovidos" value="'+ id +'">')
    });

    $(document).on("click", "#removeEndereco", function () { //user click on remove text
        $(this).parent('div').remove();
    });
    // -------------------------- !! ------------------------------------------------------------------


})