$(document).ready(function(){

    CKEDITOR.replace( 'descricao' );

    $( document ).on( "click", "#btResponder", function() {

        $(this).addClass('displayNone');

        $("#formResponder").removeClass('displayNone');

    });

    $( document ).on( "click", "#btnCancelForm", function() {

        $("#formResponder").addClass('displayNone');

        $("#btResponder").removeClass('displayNone');

    });

});