$(document).ready(function(){

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
            autoclose: true,
            orientation: "bottom auto"
        });

    });

    $( document ).on( "change", "#unidade", function() {
        
        $("#horaInicioPlantao").html('<option value="">Selecione um horário</option>');
        
        var unidade = $(this).val();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/get-horarios-plantao-ajax",
            data: {unidade: unidade},
            beforeSend: function() {

                $("#horaInicioPlantao").html('Carregando informações...');
            },
            success: function (dados) {
                
                var objt = (dados);

                $("#horaInicioPlantao").html('');
                
                $.each(objt, function(i, v) {

                    $("#horaInicioPlantao").append('<option value="'+v+'">'+v+'</option>');

                });

            }
        });

    });

});