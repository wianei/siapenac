$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    // foto modal
    $(".imagemSrc").on("click", function() {
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


    /**
     * Validacoes e mascaras
     */

    $('#nome').mask('Z',{translation: {'Z': {pattern: /[a-zA-Z ]/, recursive: true}}})
    $('#oab_numero').mask('0#');


})