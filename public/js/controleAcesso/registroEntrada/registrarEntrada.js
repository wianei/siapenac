$(document).ready(function(){

    $("#cpf").focus();

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

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    $("#cpf").blur(function(){

        $("#tipoEntrada").html('');
        $("#divTableInternos").addClass('displayNone');

        var cpf = $(this).val();

        if(cpf.length == 11)
        {
            $(this).css("border", "1px solid #0d47a1")

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../controle-acesso/buscar-cpf-ajax",
                data: {cpf: cpf},
                beforeSend: function() {

                },
                success: function (dados) {

                    var objt = (dados);

                    if (objt[0].suspensao == true) {

                        $(".flash-message").addClass('alert-danger').html(
                            '<p class="alert alert-danger">' +
                            'Há uma suspensão de entrada até '+ objt[0].dataFinal +' para este CPF!<br><em>Motivacao: </em>'+ objt[0].motivacao +'' +
                            '</p>');

                        $(':input[type="submit"]').prop('disabled', true);

                    } else {

                        if (objt.length > 1) {
                            $("#cpf").toggleClass('border1pxSolidGreen');

                            $("#divRg, #divMatricula, #divOab, #divContato, #divNome, #rowFoto").css('display', 'none');

                            $("#divTipoEntrada").removeClass('displayNone');

                            $("#tipoCadastro").val(true);

                            $("#divFoto").html('');


                            //console.log(objt[objt.length -1].dadosInterno)

                            $.each(objt, function (key, val) {


                                if(val.tipo == "visita")
                                {

                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });

                                    $.ajax({
                                        type: "POST",
                                        url: "/../visita/get-relacao-internos-ajax",
                                        data: {cpf: cpf},
                                        beforeSend: function() {
                                            $("#internosVisitados").html('');
                                        },
                                        success: function (retorno) {

                                            $.each(retorno, function (k, v) {

                                                $("#divTableInternos").removeClass('displayNone');
                                                $("#internosVisitados").append('<tr>' +
                                                    '<td><input type="checkbox" value="'+ v.id+'" class="checkbox" name="checkInterno[]"></td>' +
                                                    '<td>'+ v.nome+'</td>' +
                                                    '<td>'+ v.grau_parentesco+'</td>' +
                                                    '<td>'+ v.sigla+'</td>' +
                                                    '</tr>');
                                            });


                                        }
                                    });


                                }

                                if(val.alerta != false)
                                {
                                    $(".flash-message").addClass('alert-warning').html(
                                        '<p class="alert alert-warning">' +
                                        'CPF encontrado em nossa base de dados.<br><strong><i class="mdi mdi-alert"></i> Alerta!</strong><br>'+ val.alerta +'' +
                                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');

                                }else{

                                    $(".flash-message").addClass('alert-success').html(
                                        '<p class="alert alert-success">' +
                                        'CPF encontrado em nossa base de dados.' +
                                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');

                                }

                                $("#tipoEntrada").append('<option value="' + val.tipo + '">' + val.tipo.toUpperCase() + '</option>');

                                $("#divFoto").html('<img src="'+val.foto+'" width="250"/>');
                                $("#divNomeEntrada").html('<h2>'+val.nomeEntrada+'</h2>');


                            });

                            // apaga os values nulos
                            $("#tipoEntrada").find('option[value=""]').remove();

                        }
                        else {


                            $.each(objt, function (key, val) {


                                if (val.status == false) {
                                    $("#divTipoEntrada").addClass('displayNone');
                                    $("#tipoCadastro").val(false);
                                    $("#divRg, #divMatricula, #divOab, #divContato, #divNome, #rowFoto").css('display', 'block');
                                    $("#cpf").toggleClass('border1pxSolidRed');
                                    $(".flash-message").addClass('alert-danger').html(
                                        '<p class="alert alert-danger">' +
                                        'CPF não encontrado, cadastre aqui pessoas que não sejam visitantes ou advogados!' +
                                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>');


                                }

                            });

                        }

                    }
                }
            });

        }else{
            //$(this).focus();

            $(this).css("border", "1px solid #CC0000");

            return false;
        }

    })


    $( document ).on( "change", "#tipoEntrada", function() {

        var tipoEntrada = $(this).val();

        if(tipoEntrada != 'visita')
        {
            $("#divTableInternos").addClass('displayNone');

        }else{

            $("#divTableInternos").removeClass('displayNone');
        }

    });

    /**
     * Validacoes e mascaras
     */

    $( document ).on("submit", "#formRegistroEntrada", function() {

        var tipoDaEntrada = $("#tipoEntrada").val();
        $("#tipoEntradaHidden").attr('value', tipoDaEntrada);
        var tipoCadastro = $("#tipoCadastro").val();
        //var tipoEntradaHidden = $("#tipoEntradaHidden").val();



        $("#flashRetorno").html('');
        var control = 0;

        var hora = $('#horarioEntrada').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<br><div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }

        //alert(tipoCadastro);

        // se não for um cadastro novo
        if(tipoCadastro != 'false') {
            var tipoEntradaHidden = $('#tipoEntradaHidden').val();
            if (tipoEntradaHidden == "") {
                $("#flashRetorno").append('<br><div class="flash-message"><p class="alert alert-danger">Selecione o tipo da entrada.</p></div>');
                control = 1;
            }
        }else{
            var nome = $('#nome').val();
            if (nome == "") {
                $("#flashRetorno").append('<br><div class="flash-message"><p class="alert alert-danger">Informe o nome do visitante.</p></div>');
                control = 1;
            }

        }

        if(control == 1) return false;

    });

    $('#horarioEntrada').mask('00:00');
    $('#dataEntrada').mask('00/00/0000');
    $('#cpf').mask('00000000000');


    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ocorrencia/get-fechamento-livro-ajax",
            data: {},
            beforeSend: function() {

            },
            success: function (dados) {

                var diasLiberados = [dados.inicioPlantao,dados.terminoPlantao];

                function datasLiberadas(data) {
                    var sdate = $.datepicker.formatDate( 'dd/mm/yy', data)
                    if($.inArray(sdate, diasLiberados) != -1) {
                        return [true];
                    }
                    return [false];
                }

                $("#dataEntrada").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    autoclose: true,
                    orientation: "bottom auto"
                });


            }
        });


    });




})