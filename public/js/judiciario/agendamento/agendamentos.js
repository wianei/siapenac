$(document).ready(function(){

    var horarios = [
        "08:00", "08:15",
        "08:30", "08:45",
        "09:00", "09:15",
        "09:30", "09:45",
        "10:00", "10:15",
        "10:30", "10:45",
        "11:00", "11:15",
        "11:30", "11:45",
        "12:00", "12:15",
        "12:30", "12:45",
        "13:00", "13:15",
        "13:30", "13:45",
        "14:00", "14:15",
        "14:30", "14:45",
        "15:00", "15:15",
        "15:30", "15:45",
        "16:00", "16:15",
        "16:30", "16:45",
        "17:00"
    ];

    $.each(horarios, function(i, v) {
        $("#horario").append('<option>'+v+'</option>')
    })


    $( function() {
        $("#data").datepicker({
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
            onSelect: function(selected) {
                $("#dataFinal").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#dataFinal").datepicker({
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
            onSelect: function(selected) {
                $("#data").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });

});