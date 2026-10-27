$(document).ready(function(){

    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));

        if($(this).is(":checked")){
            $('#retornoMensagens').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoMensagens').children().hasClass('background_bddaff')){
                $('#retornoMensagens').children().removeClass('background_bddaff');
            }
        }
    });


    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    $( document ).on( "click", "#btMarcarLida", function() {

        var idMensagensPost = $("#retornoMensagens input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../mensagem/marcar-lida",
            data: {idMensagens: idMensagensPost},
            beforeSend: function() {

            },
            success: function (dados) {

                $.each(dados, function(key, val){
                    $('#trMensagem'+val).addClass('lida');
                    $('#checkMensagem'+val).prop('checked', false);
                    $('#btnCheckTodosInternos').prop('checked', false);
                    $('#checkMensagem'+val).parent().parent().toggleClass('background_bddaff');
                });
            }
        });

    });

    $( document ).on( "click", "#btnExcluir", function() {

        var confirmacao = confirm("Deseja excluir essa(s) mensagem(ns)");

        if(confirmacao) {

            var idMensagensPost = $("#retornoMensagens input:checkbox:checked").map(function () {
                return $(this).val();
            }).get();


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../mensagem/excluir-mensagem-ajax",
                data: {idMensagens: idMensagensPost},
                beforeSend: function () {

                },
                success: function (dados) {

                    $.each(dados, function (key, val) {
                        $('#trMensagem' + val).remove();
                    });
                }
            });

        }

    });


});