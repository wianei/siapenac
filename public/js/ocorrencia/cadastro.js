$(document).ready(function(){

    function setTextArea(opcao)
    {
        if(opcao == 27 || opcao == 28)
        {

            CKEDITOR.instances['descricao'].setData('<p><strong>NOME DO POSTO</strong></p>'+
                '<table border="1" cellpadding="0" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">00:00 às 00:00</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">00:00 às 00:00</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td>Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td>Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>');

        }
        if(opcao == 29)
        {
            CKEDITOR.instances['descricao'].setData(
                '<p><strong>Período Diurno/Noturno</strong></p>'+
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px"><strong>1º Horário - 00:00</strong></td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px"><strong>2º Horário - 00:00</strong></td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px"><strong>3º Horário - 00:00</strong></td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 30)
        {
            CKEDITOR.instances['descricao'].setData(
                '<p><strong>Pavilhão ou Bloco</strong></p>'+

                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col" style="text-align:center">ALA</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">VESPERTINO</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">MATUTINO</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">A</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">B</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">C</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">D</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 31)
        {
            CKEDITOR.instances['descricao'].setData(
                '<p><strong>Pavilhão ou Bloco</strong></p>'+

                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col" style="text-align:center">ALA</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">CELAS</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">A</td>'+
                '<td>Cela 00-00 - Nome do Servidor (matrícula), Cela 00-00 - Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">B</td>'+
                '<td>Cela 00-00 - Nome do Servidor (matrícula), Cela 00-00 - Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">C</td>'+
                '<td>Cela 00-00 - Nome do Servidor (matrícula), Cela 00-00 - Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">D</td>'+
                '<td>Cela 00-00 - Nome do Servidor (matrícula), Cela 00-00 - Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 36)
        {
            CKEDITOR.instances['descricao'].setData(
                '<p><strong>Pavilhão ou Bloco</strong></p>'+
                '<p><strong>Quantidade Total de Quentinhas (card&aacute;pio - feij&atilde;o, arroz...) : 00<br />'+
                'Falta: 00<br />'+
                'Sobras: 00<br />'+
                'Quantidade Total de Pães: 00</strong></p>'+
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col" style="text-align:center">ALA</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Descrição</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">A</td>'+
                '<td>Horário de pagamento / Servidor responsável (nome e matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">B</td>'+
                '<td>Horário de pagamento / Servidor responsável (nome e matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">C</td>'+
                '<td>Horário de pagamento / Servidor responsável (nome e matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center; vertical-align:middle">D</td>'+
                '<td>Horário de pagamento / Servidor responsável (nome e matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 32)
        {
            CKEDITOR.instances['descricao'].setData(
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col" style="text-align:center">Pavilhão</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Horário</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center"><strong>01</strong></td>'+
                '<td>Ala "A" de 00:00 às 00:00 - Ala "B" de 00:00 às 00:00</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center"><strong>02</strong></td>'+
                '<td>Ala "A" de 00:00 às 00:00 - Ala "B" de 00:00 às 00:00</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 33)
        {
            CKEDITOR.instances['descricao'].setData(
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col" style="text-align:center">Pavilhão</th>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Servidor</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td style="text-align:center"><strong>01</strong></td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '<tr>'+
                '<td style="text-align:center"><strong>02</strong></td>'+
                '<td>Nome do Servidor (matrícula), Nome do Servidor (matrícula)</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'
            );

        }
        if(opcao == 34)
        {
            CKEDITOR.instances['descricao'].setData(
                '<p><strong>Unidade, Pavilh&atilde;o ou Bloco</strong></p>'+
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th style="background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Equipamentos</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td>00 - ESPINGARDAS CAL.12 XXX. 000000, XXX. 000000, XXX. 000000, XXX. 000000</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - PISTOLAS IMBEL CAL.40 XXX. 000000, XXX. 000000, XXX. 000000, XXX. 000000</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.12 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.12 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.40 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'+
                '<p><em>Responsável pela conferência: Nome do Servidor (matrícula)</em></p>'+

                '<p><strong>Unidade, Pavilh&atilde;o ou Bloco</strong></p>'+
                '<table border="1" cellpadding="1" cellspacing="0" style="width:100%">'+
                '<thead>'+
                '<tr>'+
                '<th scope="col">Equipamentos</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                '<tr>'+
                '<td>00 - ESPINGARDAS CAL.12 XXX. 000000, XXX. 000000, XXX. 000000, XXX. 000000</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - PISTOLAS IMBEL CAL.40 XXX. 000000, XXX. 000000, XXX. 000000, XXX. 000000</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.12 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.12 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '<tr>'+
                '<td>00 - MUNIÇÕES CAL.40 - LETALIDADE - MODELO - FABRICANTE</td>'+
                '</tr>'+
                '</tbody>'+
                '</table>'+
                '<p><em>Responsável pela conferência: Nome do Servidor (matrícula)</em></p>'

            );

        }

        if(opcao == 35)
        {
            CKEDITOR.instances['descricao'].setData('');

        }
        // sempre que inserir uma nova natureza de ocorrencia, que tenha template, aumentar na opcao abaixo
        if(opcao < 27 || opcao > 35 && opcao != 36)
        {
            CKEDITOR.instances['descricao'].setData('');
        }

    }

    CKEDITOR.replace( 'descricao' );

    if(document.URL.split("/")[5] != undefined)
    {
        var opcao = document.URL.split("/")[5];
        setTextArea(opcao);
        $("#formAddOcorrencia").toggleClass('displayNone');
        $("#natureza").val(opcao);
        $("#btnAddOcorrencia").removeClass('btn-dark').addClass('btn-danger').html('<i class="mdi mdi-close"></i> Cancelar');

    }
    //alert(document.URL.split("/")[5])

$("#btnAddOcorrencia").click(function(){
    $("#formAddOcorrencia").toggleClass('displayNone');
    $(this).removeClass('btn-dark').toggleClass('btn-danger');

    if($(this).hasClass('btn-danger'))
    {
        $(this).html('<i class="mdi mdi-close"></i> Cancelar');

    }else{
        $(this).html('<i class="fa fa-plus"></i> Inserir Ocorrência');
        $(this).removeClass('btn-danger').addClass('btn-dark')
    }

})


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })


    $( document ).on( "change", "#natureza", function() {

        var opcao = $(this).val();

        $("#natureza").css("border","1px solid green");

        setTextArea(opcao);

    });
    
    $( document ).on( "change", "#natureza", function() {

        var opcao = $(this).val();

        $("#natureza").css("border","1px solid green");
        $('#tipo_objeto').html('');

        setTextArea(opcao);
        
        if(opcao == 1 || opcao == 47){
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../ocorrencia/get-natureza-objeto",
                data: {tipo: opcao},
                beforeSend: function() {

                },
                success: function (dados) {

                   // var objt = jQuery.parseJSON(dados);

                    $('#tipo_objeto').append('<option value="">Selecione</option>');
                    $.each(dados,function(key, value)
                    {

                        $('#tipo_objeto').append('<option value=' + value.id + '>' + value.nome + '</option>');
                    });
                
                }
                
            });
            
            $("#divTipoObjeto").removeClass('displayNone');
        }else{
            
            $("#divTipoObjeto").addClass('displayNone');
        }

    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    $( document ).on( "change", "#dt_ocorrencia", function() {

        $("#internosObito").html('');

        var dataOcorrencia = $(this).val();

        var naturezaOcorrencia = $("#natureza option:selected").val();

        if(naturezaOcorrencia == 35)
        {
            $("#natureza").css("border","1px solid green");
            var dataOcorrencia = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../interno/get-obito-ajax",
                data: {data: dataOcorrencia},
                beforeSend: function() {

                },
                success: function (dados) {


                    if(dados.length == 0)
                    {
                        //alert("Sem ")
                    $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Sem Óbitos cadastrados nesta data!<br>Para cadastro do óbito ir em:<br> Movimentações > Óbito<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');

                        return false;

                    }else {

                        $("#flashRetorno").html('');

                        $.each(dados, function (k, v) {

                            $("#divTableInternosObito").removeClass('displayNone');
                            $("#internosObito").append('<tr>' +
                                '<td><input type="checkbox" value="' + v.id + '" class="checkbox" name="checkInternoObito[]"></td>' +
                                '<td>' + v.nome + '</td>' +
                                '<td>' + v.sigla + '</td>' +
                                '</tr>');


                        });
                    }

                }
            });


        }

        if(naturezaOcorrencia == "")
        {
            $("#natureza").focus().css("border","1px solid red");
            $(this).val("");

        }

    });

    $("#formAddOcorrencia").submit(function (e) {

        $("#flashRetorno").html('');

        if ($("#natureza option:selected").val() == 35) {

            var control = 0;

            if (!$('input[type=checkbox]:checked').length) {

                $("#flashRetorno").html('<div class="flash-message"><p class="alert alert-danger">Informe o Interno que foi a Óbito!<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p></div>');


                control = 1;
            }


        }

        var hora = $('#hr_ocorrencia').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');

            control = 1;
        }

        if(control == 1) return false;



    });

        /**
     * Validacoes e mascaras
     */
    $('#hr_ocorrencia').mask('00:00');
    $('#dt_ocorrencia').mask('00/00/0000');

    var diasLiberados;



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

                $("#dt_ocorrencia").datepicker({
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




});