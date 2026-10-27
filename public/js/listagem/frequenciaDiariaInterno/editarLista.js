/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 25/11/2019.
 */
$(document).ready(function(){

    $( document ).on( "click", ".removerInternoLista", function() {

        $(this).parent('td').parent('tr').remove();
        var id = $(this).data("content");
        $("#tableInternos").append('<input type="hidden" name="internosRemover[]" id="internosRemover" value="'+ id +'">')
    });


});