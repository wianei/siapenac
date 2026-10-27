$(document).ready(function(){

    CKEDITOR.replace( 'motivacao' );

    $("#unidade_prisional").change(function(){
        $("#divPavilhao").removeClass('displayNone');
    });

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });


    $( function() {

        var diasPermitidos = $("#diasAtendimentoUnidade").val();

        $("#dataAtendimento").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            minDate: 0,
            beforeShowDay: function(date) {
                var day = date.getDay();
                //return [day != 0 && day != 6, ''];
                return [ diasPermitidos.indexOf(day) == -1 ]
            },
            orientation: "bottom auto"
        });

    });

    $( document ).on("submit", "#formCadastro", function() {

        $("#flashRetorno").html('');

        var control = 0;

        var motivacao = CKEDITOR.instances['motivacao'].getData();
        if(motivacao == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a motivação.</p></div>');
            control = 1;
        }

        if(control == 1) return false;
    });


});
