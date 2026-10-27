$(document).ready(function(){

    CKEDITOR.replace( 'descricao' );

    $( function() {
        $("#dataAtendimento").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            maxDate: 0,
            autoclose: true,
            orientation: "bottom auto"
        });

    });

    /**
     * Validacoes e mascaras
     */

    $( document ).on("submit", "#formRegistroAtendimento", function() {
        $("#flashRetorno").html('');
        var control = 0;

        var motivo = $('#descricao').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Descreva o atendimento.</p></div>');
            control = 1;
        }
        var data = $('#dataAtendimento').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var hora = $('#horaAtendimento').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
    });

    $('#horaAtendimento').mask('00:00', {reverse: true});
    $('#dataAtendimento').mask('00/00/0000');


})