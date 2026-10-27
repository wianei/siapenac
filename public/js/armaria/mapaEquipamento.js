$(document).ready(function () {


    /*
     Filtros
     */
    $("#filtroArmaFogo").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tBodyArmaFogo tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#filtroMunicao").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tBodyMunicao tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#filtroGranada").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tBodyGranada tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#filtroColete").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tBodyColete tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


    $( document ).on( "click", "#expandirArmaFogo", function() {

        $("#tableArmaFogo").toggleClass("displayNone");

        if ($("#tableArmaFogo").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirDei", function() {

        $("#tableDei").toggleClass("displayNone");

        if ($("#tableDei").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirMunicao", function() {

        $("#tableMunicao").toggleClass("displayNone");

        if ($("#tableMunicao").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirCarregador", function() {

        $("#tableCarregador").toggleClass("displayNone");

        if ($("#tableCarregador").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirAlgema", function() {

        $("#tableAlgema").toggleClass("displayNone");

        if ($("#tableAlgema").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });


    $( document ).on( "click", "#expandirGranada", function() {

        $("#tableGranada").toggleClass("displayNone");

        if ($("#tableGranada").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirEpi", function() {

        $("#tableEpi").toggleClass("displayNone");

        if ($("#tableEpi").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirColete", function() {

        $("#tableColete").toggleClass("displayNone");

        if ($("#tableColete").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });

    $( document ).on( "click", "#expandirEspargidor", function() {

        $("#tableEspargidor").toggleClass("displayNone");

        if ($("#tableEspargidor").hasClass('displayNone')) {
            $(this).html('<i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista')
        }else{
            $(this).html('<i class="mdi mdi-arrow-up-drop-circle"></i> Recolher Lista')
        }

    });
});
