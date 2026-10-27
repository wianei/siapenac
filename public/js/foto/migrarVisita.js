$(document).ready(function(){

    $( document ).on( "click", "#btnMigrar", function() {

        setInterval(function(){

            $( function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../foto/migracao-fotos-vistas-ajax/",
                    data: {},
                    beforeSend: function() {

                    },
                    success: function (dados) {

                        $("#retorno").prepend(dados+'<br>');


                    }
                });


            });


        }, 1000 );

    });


});