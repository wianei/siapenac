$(document).ready(function(){
    /**
     * Adiciona mais fotos
     */
    $( document ).on( "click", ".btAddFoto", function() {

        $('#grupoEndereco').append(
            '<div class="form-row width100Percent">'+
            '<div class="form-group col-sm-3">'+
            '<label for="fotos">Fotos</label>'+
            '<input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="image/*">'+
            '</div>'+

            '<div class="form-group col-sm-3">'+
            '<label for="tipoFoto">TipoFoto</label>'+
            '<select id="tipoFoto" name="tipoFoto[]" class="form-control tipoFoto">'+
            '<option value="">Selecione</option>'+
            '<option value="principal">Principal</option>'+
            '<option value="tatuagem">Tatuagem</option>'+
            '<option value="perfil">Perfil</option>'+
            '<option value="cicatriz">Cicatriz</option>'+
            '<option value="sinais">Sinais</option>'+
            '<option value="outros">Outros</option>'+
            '</select>'+
            '</div>'+

            '<div class="form-group col-sm-6">'+
            '<label for="legenda">Legenda</label>'+
            '<input style="width: 95% !important;" type="text" name="legenda[]" class="form-control" id="legenda" placeholder="Legenda para foto">'+
            '<i class="fas fa-minus-circle cursorPointer removeFieldFone"></i>' +
            '</div>'+
            '</div>'
        );


    });

    $('#grupoEndereco').on("click", ".removeFieldFone", function () {
        $(this).parent('div').parent('div').remove();
    });

    $(document).on("click", ".btAlterarFoto", function () {
        var idFoto = $(this).attr("data-content");
        $('#div'+idFoto).removeClass('displayNone');
        $(this).addClass('displayNone');
    });

    $(document).on("click", ".fecharFoto", function () {
        var idFoto = $(this).attr("data-content");
        $('#div'+idFoto).addClass('displayNone');
        $('#btAlterarFoto'+idFoto).removeClass('displayNone');
    });

    $(document).on("change", ".tipoFoto", function () {
        if($(this).val() == 'principal') {
            selectUnicoTipo($(this));
        }
    });

    function selectUnicoTipo(element) {
        var $element = element;
        var value = $element.val();

        $.each($('.tipoFoto').not($element), function() {
            var subValue = $(this).val();
            if (subValue === value) {
                $(this).val('');
            }
        });
    }
    /**
     * clicar no btn gravar
     */
    $( document ).on( "click", ".gravarFoto", function() {

        var idFoto          = $(this).attr("data-content");
        var idInternoPost   = $('#idPessoa').val();
        var tipoFotoPost    = $("#tipoFoto"+idFoto).val();
        var legendaPost     = $("#textArea"+idFoto).val();
        var tabelaPost          = $("#tabelaFoto").val();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../foto/gravar-ajax",
            data: {idPessoa: idInternoPost, foto: idFoto, tipo: tipoFotoPost, legenda: legendaPost, tabela: tabelaPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var retorno = dados.split(',');

                if(retorno[0] == 'success')
                {
                    $('#h6'+idFoto).html(tipoFotoPost.toUpperCase());
                    $('#p'+idFoto).html(legendaPost);
                    $('#div'+idFoto).addClass('displayNone');
                    $('#btAlterarFoto'+idFoto).removeClass('displayNone');
                    $('#card'+idFoto).css('border','1px solid #43a047');

                    if($.isNumeric(retorno[1]))
                    {
                        $('#h6'+retorno[1]).html("OUTROS");
                        $('#tipoFoto'+retorno[1]+' option[value=outros]').attr('selected','selected');
                    }
                }

            }
        });

    });
    // -------------------------- !! ------------------------------------------------------------------

    /**
     * clicar no btn excluir
     */
    $( document ).on( "click", ".excluirFoto", function() {

        var confirmacao = confirm("Deseja mesmo apagar esta foto?");

        if(confirmacao) {

            var idFoto = $(this).attr("data-content");
            var tabelaPost = $("#tabelaFoto").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../foto/excluir-ajax",
                data: {foto: idFoto, tabela: tabelaPost},
                beforeSend: function () {

                },
                success: function (dados) {

                    if (dados == 'success') {
                        $('#card' + idFoto).css('display', 'none');

                    }

                    if (dados == 'errorMove') {
                        $('#card' + idFoto).css('display', 'none');

                    }

                }
            });

        }

    });
    // -------------------------- !! ------------------------------------------------------------------


});