$(document).ready(function (){
    $('#data_entrada').mask('00/00/0000');
    $('#hora_entrada').mask('00:00', {reverse: true});

    $(document).on("submit", "#reativarForm", function () {
        var control=0;

        var data_entrada = $("#data_entrada").val().split("/")[2].substring(0,2);
        if(data_entrada != "19" && data_entrada != "20")
        {
            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Insira uma data de entrada entre 1900 e o ano atual.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');
            $('#reativarBtn').attr('disabled', false);
            return false;


        }

        var data_entrada_l = $("#data_entrada").val();
        if(data_entrada_l.length != 10){
            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Insira uma data válida.</p></div>');
            $('#reativarBtn').attr('disabled', false);
            return false;
        }

        var hora_entrada = $('#hora_entrada').val();
        if(hora_entrada.length != 5){
            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            $('#reativarBtn').attr('disabled', false);
            return false;
        }
        
        $('#reativarBtn').attr('disabled', true);
    });
});