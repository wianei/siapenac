$(document).ready(function(){
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    $('#back-to-top').tooltip('show');

});

// coloca a foto do interno no modal
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
