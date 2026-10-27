$(document).ready(function(){

    // foto modal
    $(".imagemSrc").on("click", function() {
        $('#imagepreview').attr('src', $(this).attr('src'));
        $('#imagemodal').modal('show');
        var datacontent = $(this).attr("data-content").split(',');
        var fototipo = datacontent[0];
        var fotolegenda = datacontent[1];
        var fotodatainserida = datacontent[2];
        $('#fototipo').html("Tipo: "+fototipo+"<br/>");
        $('#fotolegenda').html("Descrição: "+fotolegenda+"<br/>");
        $('#fotodatainserida').html("Data da inserção da foto: "+fotodatainserida+"<br/>");
    });

    $("#filtroNome").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableVisitas #bodyTableVisitas tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    /**
     * Validacoes e mascaras
     */

    $("#dataSaida").datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
        maxDate: '+1d',
        autoclose: true,
        orientation: "bottom auto"
    });


    $('#horarioSaida').mask('00:00');
    $('#dataSaida').mask('00/00/0000');
    $('#cpf').mask('0#');

})