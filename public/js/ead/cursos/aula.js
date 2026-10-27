/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 * Data: 11/04/2020.
 */
$(document).ready(function(){

    var url = window.location.href;

    var video = document.getElementById("video");
    // tempo salvo pelo usuario
    var progressoAula = $("#progressoAula").val();
    video.currentTime = progressoAula;

    var timeStarted = Math.floor(progressoAula);
    var timePlayed = Math.floor(progressoAula);
    var duration = 0;
    var tempoDecorrido = 0;

    if(video.readyState > 0)
        getDuration.call(video);

    else
    {
        video.addEventListener('loadedmetadata', getDuration);
    }
    function videoStartedPlaying() {
        timeStarted = new Date().getTime()/1000;
    }
    function videoStoppedPlaying(event) {

        // Hora de início menor que zero significa que o evento de parada foi acionado
        if(timeStarted>0) {
            var playedFor = (new Date().getTime()/1000 - timeStarted);
            timeStarted = -1;
            timePlayed+=playedFor;
        }
        //document.getElementById("played").innerHTML = Math.round(timePlayed)+"";
            saveProgress(null);
        if(timePlayed>=duration && event.type=="ended") {
            saveProgress("S");
        }
    }

    video.onended = function() {
        saveProgress("S");
        saveProgress("S");

        $(".itemAula").each(function(e){

            var urlAttr = $(this).attr('href');

            if(url == urlAttr)
            {
                $(this).addClass('aulaAssistida');
                $(this).children( ".addEndVideo").html('<i class="mdi mdi-checkbox-marked fontSize-18px iconeAssistido ml-2"></i>');
            }

        });

        var idProximaAula = $("#idProximaAula").val();
        if(idProximaAula != 0) {
        $(".textProximoAula").removeClass('displayNone')
        var idCurso = $("#idCurso").val();
        var segundos = 5;
            setInterval(function () {

                $(".countRegressive").html(segundos);
                segundos = segundos - 1;

                if (segundos == 0) {
                    $(location).attr('href', '/../ead/aulas/' + idCurso + '/' + idProximaAula + '');
                }

            }, 1000);

        }


    };


    function saveProgress(endedVideo)
    {


        var idAula = $("#idAula").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ead/save-progress",
            data: {idAula: idAula, progresso: parseInt(timePlayed, 10), end: endedVideo},
            beforeSend: function() {

            },
            success: function (dados) {
                console.log(dados);
            }
        });

        console.log('tempo: ' + parseInt(timePlayed, 10))
    }


    function getDuration() {
        duration = video.duration;
        //document.getElementById("duration").appendChild(new Text(Math.round(duration)+""));
    }

    video.addEventListener("play", videoStartedPlaying);
    video.addEventListener("pause", videoStoppedPlaying);

    /**
     * class active na aula ativa
     */

    $(".itemAula").each(function(e){

        var urlAttr = $(this).attr('href');

        if(url == urlAttr)
        {
            $(this).addClass('active')
            $(this).parent().parent().siblings().addClass('show');
            $(this).parent().parent().addClass('show');
        }

    });


    $(document).on('click', '.modulosTitulo', function(){

        if($(this).hasClass('collapsed'))
        {
            $(this).parent().parent().siblings('.collapse ').removeClass('show');
        }else{
            $(this).parent().parent().siblings('.collapse ').addClass('show');
        }

    });



});
