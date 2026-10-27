$(document).ready(function(){

    var interno = [
    ]

    $( document ).on( "keyup", "#interno", function() {

        $('#filter-records').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records').css('display','none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'geral'},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário.: '+val.id+ ' - Localização: '+val.sigla+'</h6>';
                    output += '</div>';
                    count++;
                    interno = objt;
                });
                output += '</div>';
                $('#filter-records').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearch", function() {

        if($('.hiddenInternoId').length >= 1)
        {
            $("#divDanger").removeClass('displayNone').html('É permitido apenas um interno por solicitação.');
            $('#filter-records').html('').css('display', 'none');


        }else {

            $("#divDanger").addClass('displayNone');
            $('#tableInternos').css('display', 'block');

            $('#filter-records').html('').css('display', 'none');

            var nomeInterno = $(this).data('name');
            var idInterno = $(this).data('content');

            //verifica se o interno já esta presente na lista de internos e remove da lista da cela
            $("#tr" + idInterno).remove();

            $.each(interno, function (i, v) {

                // verifica se o interno ja esta na lista
                if ($('.hiddenInternoId').val() != v.id) {
                    if (v.id == idInterno) {
                        $("#retornoInterno").append('<tr id="tr' + v.id + '">' +
                            '<td scope="row">' + v.id + '</td>' +
                            '<td><img style="text-align: center" src="' + v.foto + '" class="" alt=""></td>' +
                            '<td>' + v.nome + '</td>' +
                            '<td>' + v.faccao + '</td>' +
                            '<td>' + v.lideranca + '</td>' +
                            '<td>' + v.sigla + '</td>' +
                            '<td class="btn-acoes color_01579b">' +
                            '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>' +
                            '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="' + v.id + '"/>' +
                            '<a href="/interno/show/' + v.id + '" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>' +
                            '</td>' +
                            '</tr>');
                        return;
                    }

                }

            });

            $(this).css("display", "none")

            $("#interno").val("");

        }

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });


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
            autoclose: true,
            minDate: 2,
            beforeShowDay: function(date) {
                var day = date.getDay();
                return [day != 0 && day != 6, ''];
            },
            orientation: "bottom auto"
        });

    });

    $(document).on("submit", "#formCadastroAgendamento", function () {

        $("#flashRetorno").html('');

        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;

        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }

        if(control == 1) return false;

        if( document.getElementById("inputFile").files.length == 0){

            $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Selecione o documento a ser enviado<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

            return false;
        }

    });


});