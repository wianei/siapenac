$(document).ready(function (){

    $(document).on("change","#pavilhao", function (e) {

        $(".retornoInternos tr").removeClass('displayNone');

        var pavilhao = $(this).val();

        if(pavilhao == "all")
        {
            $(".retornoInternos tr").removeClass('displayNone');

        }else {

            $(".retornoInternos tr").map(function () {

                if (!$(this).hasClass(pavilhao)) {
                    $(this).addClass('displayNone');
                }

            });

        }

    });

    $(document).on("change","#tipoAtendimento", function (e) {

        $(".tipoAtendimento").removeClass('displayNone');

        var tipoAtendimento = $(this).val();

        if(tipoAtendimento == "all")
        {
            $(".tipoAtendimento").removeClass('displayNone');

        }else {

            $(".tipoAtendimento").map(function () {

                if (!$(this).hasClass(tipoAtendimento)) {
                    $(this).addClass('displayNone');
                }

            });

        }

    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
        $(this).parent('td').parent('tr').next('tr').toggleClass('background_bddaff');
    });

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

    $('#dataAtendimento').mask('00/00/0000');

});
