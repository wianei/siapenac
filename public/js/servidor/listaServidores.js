$(document).ready(function(){

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


    $("#filtroNome").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableServidores #bodyTableServidores tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


})