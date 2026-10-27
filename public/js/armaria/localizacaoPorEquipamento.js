/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 30/01/2020.
 */
$(document).ready(function () {


    $( document ).on( "change", "#tipoEquipamento", function() {

        var tipo = $(this).val();

        if(tipo == "armaFogo")
        {
            $(".divModelo").removeClass('displayNone')
        }else{
            $(".divModelo").addClass('displayNone')
        }

    });

});
