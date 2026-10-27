$(document).ready(function () {

    $("#unidade_prisional").change(function(){
        $("#divPavilhao").removeClass('displayNone');
        $("#resultados").addClass('displayNone');
    });

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });

    // coloca a foto do interno no modal
    $(".graficoServidorUnidade").on("click", function() {
        if( $('#imagemodal').css('display') != 'none' ) {
            $('#imagemodal').addClass('displayNone');
        } else{
            $('#imagemodal').removeClass('displayNone');
        }
    });
    
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
            minDate: '-1y',
            maxDate: '-1d',
            autoclose: true,
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
    /**
     * busca pavilhao, ala e cela
     
    $( document ).on( "change", ".selectUnidade", function() {

        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "unidade_prisional":
                $('#pavilhao, #ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhao';
                break;

            case "pavilhao":
                $('#ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'ala';
                break;

            case "ala":
                $('#cela').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'cela';
                break;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/get-dados",
            data: {dado: valor},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);


                $.each(objt,function(key, value)
                {
                    $.each(value,function(k, v)
                    {
                        $('#'+selectOp).append('<option value=' + v + '>' + k + '</option>');
                    });

                });

            }
        });

    });
*/

    $(document).ready(function(){
        var unidade = $('#unidade_prisional').val();
        var pavilhao = $('#pavilhao').val();
        var ala = $('#ala').val();
        var cela = $('#cela').val();
        var output = '';


        function replaceAll(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../estatisticsa/get-internos-visita",
            data: {unidade: unidade, pavilhao: pavilhao, ala: ala, cela: cela},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);
                var unidades = [];

                $.each(objt, function(key, val){
                    if(!unidades.includes(val.unidade)){
                        unidades.push(val.unidade);
                        output += '<tr> <td>' + val.unidade + '</td>';
                        output += '<td class="' + replaceAll(val.unidade, " ", "_") + "_M"  + '">' + 0 + '</td>';
                        output += '<td class="' + replaceAll(val.unidade, " ", "_") + "_F"  + '">' + 0 + '</td>';
                        output += '<td class="' + replaceAll(val.unidade, " ", "_") + "_TOTAL"  + '">' + 0 + '</td> </tr>';
                    }
                });
                $('#qntvisitaativa').html(output);

                $.each(objt, function(key, val){
                    var classeHomen =  '.' + replaceAll(val.unidade, " ", "_") + '_M';
                    var classeMulher =  '.' + replaceAll(val.unidade, " ", "_") + '_F';
                    var classeTotal =  '.' + replaceAll(val.unidade, " ", "_") + '_TOTAL';
                    if(val.sexo == 'M'){
                        $(classeHomen).html(val.qnt);
                    }
                    if(val.sexo == 'F'){
                        $(classeMulher).html(val.qnt);
                    }
                    $(classeTotal).html(parseInt($(classeHomen).text()) + parseInt($(classeMulher).text()));

                });

            }
        });
    });


    $('#data_demias ,#data_atendimento, #data').mask('00/00/0000');



    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    $('#back-to-top').tooltip('show');

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })


});
