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

    $('#grupoEndereco').on("click", ".removeFieldFone", function () { //user click on remove text
        $(this).parent('div').parent('div').remove();
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

});