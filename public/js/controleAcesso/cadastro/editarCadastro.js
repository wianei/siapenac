$(document).ready(function(){

    $(function (){
        // Where are we going to be putting the webcam stream?
        var cameraContainer = $('#camera');

        // Initialize the jQuery plugin
        cameraContainer.userCam({
            /* Called when the camera starts streaming */
            start: function (event, video) { }
        });

        // When our snapshot button is clicked, take a Snapshot!
        $('#btnSavePhoto').on('click', function (e) {
            // Get the userCam object from the cameraContainer data
            var dataURI = cameraContainer.data('userCam').getStill();

            // Create a new image with a src of the dataUri and append to the body
            $('<input>', {
                type: 'hidden',
                name: 'imagens[]',
                value: dataURI,

            }).appendTo($("#imgOut"));

            $('<img>', {
                src: dataURI,
                alt: 'foto da visita'
            }).appendTo($("#imgOut"));
        });

    });

    $("#btnAbrirWebcam").click(function(){

        $("#divWebcam, #btnSavePhoto").toggleClass("displayNone");

    });

    $('#contato').mask('0#');


});