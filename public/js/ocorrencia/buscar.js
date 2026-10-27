$(document).ready(function(){

    $("#presidio").change(function(){
        $("#divPavilhao").removeClass('displayNone');
    });


    $( function() {
        $("#data_inicial").datepicker({
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
                $("#data_final").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

    $( function() {
        $("#data_final").datepicker({
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
                $("#data_final").datepicker("option", "maxDate", selected)
            },
            orientation: "bottom auto"
        });

    });

    $( document ).on( "click", ".radioTipo", function() {
        
       $("#flashRetorno").html('');
        
       var valorTipo = $(this).attr("checked", "checked").val();

        if(valorTipo == "livro")
        {
            $("#divHorarioInicioPlantao").removeClass("displayNone");
            $("#divRowDataFinal, #divRowNatureza, #formRowDivInternos, #divRowAgentes, #divRowVisitas, #divRowAdv").hide();
            $("label[for='dt_ocorrencia']").text("Data do Livro");
            
                $( document ).on( "change", "#unidade", function() {

                    var dataLivro = $("#data_inicial").val()

                    if(dataLivro == "")
                    {
                        $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-warning">Informe uma data para busca.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

                    }else{
                

                    $("#horaInicioPlantao").html('<option value="">Selecione um horário</option>');

                    var unidade = $(this).val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/../unidades/get-horarios-plantao-busca-ajax",
                        data: {unidade: unidade, dataLivro: dataLivro},
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
                    
                    }

            });

                
            

        }else{

            $("#divHorarioInicioPlantao").addClass("displayNone");
            $("#divRowDataFinal, #divRowNatureza, #formRowDivInternos, #divRowAgentes, #divRowVisitas, #divRowAdv").show();
            $("label[for='dt_ocorrencia']").text("Data Inicial da Ocorrência");

        }

    });
    
    
        $("#formBuscar").submit(function (e) {

        $("#flashRetorno").html('');
        
        var valorTipo = $('input[name="buscaTipo"]:checked').val();
        
        if(valorTipo == "livro")
        {
            var control = 0;
            
            var unidade = $("#unidade").val();
            if(unidade == "")
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe uma Unidade Prisional.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

                control = 1;
            }
            
            var horarioPlantao = $("#horaInicioPlantao").val();
            if(horarioPlantao == "")
            {
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o Horário do Início do Plantão.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

                control = 1;
            }

        }

        if(control == 1) return false;


    });


});