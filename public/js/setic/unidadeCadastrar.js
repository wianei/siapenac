$(document).ready(function(){

    $('#horarioTerminoAtendimentoAdv').mask('00:00');
    $('#horarioInicioAtendimentoAdv').mask('00:00');
    $('#horarioInicioIntervaloAlmoco').mask('00:00');
    $('#horarioTerminoIntervaloAlmoco').mask('00:00');
    $('#horaInicioPlantao').mask('00:00');
    $('#qtdParlatorio').mask('00');
    $('#qtdHorasPlantao').mask('00');

    $("#formDados").submit(function (e) {

        $("#flashRetorno").html('');

        var horarioInicioAtendimentoAdv = $('#horarioInicioAtendimentoAdv').val();
        if(horarioInicioAtendimentoAdv.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');

            control = 1;
        }

        var horarioTerminoAtendimentoAdv = $('#horarioTerminoAtendimentoAdv').val();
        if(horarioTerminoAtendimentoAdv.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');

            control = 1;
        }

        var horarioInicioIntervaloAlmoco = $('#horarioInicioIntervaloAlmoco').val();
        if(horarioInicioIntervaloAlmoco.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');

            control = 1;
        }

        var horarioTerminoIntervaloAlmoco = $('#horarioTerminoIntervaloAlmoco').val();
        if(horarioTerminoIntervaloAlmoco.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');

            control = 1;
        }

        if(control == 1) return false;



    });


});