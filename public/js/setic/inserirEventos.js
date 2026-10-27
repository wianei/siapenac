/**
 * 
 * @uthor saulo.ricardo_@hotmail.com
 * @date 2020-09-22
 * @company Departamento Penitenciario Nacional - DEPEN
 * 
 */
$(document).ready(function(){
    
        $( document ).on( "change", "#controller", function() {

        $('#retornoEventos').html('');
        $('.formInserirEventos').removeClass('displayNone');
        
        var controllerId = $(this).val();
        var idPerfil = $("#idPerfil").val();
        var texto = "Eventos do Controller "+$( "#controller option:selected" ).text();;
        
        $("#nomeEvento").html(texto);
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../setic/permissoes-get-eventos-ajax",
            data: {idController: controllerId, idPerfil: idPerfil},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = dados;

                    $.each(objt,function(k, v)
                    {
                        $('#retornoEventos').append('<tr><td><input type="checkbox" name="metodos[]" value="'+v.id+'" class="checkbox"></td><td>'+v.nome+'</td></tr>');
                        
                    });

            }
        });

    });
    
    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#checkAll", function() {
        $("#retornoEventos .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoEventos').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoEventos').children().hasClass('background_bddaff')){
                $('#retornoEventos').children().removeClass('background_bddaff');
            }
        }
    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });
    
});