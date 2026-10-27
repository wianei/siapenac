$(document).ready(function (){


    $(document).on("click","#btnFiltro", function (e) {

        $(".allItens .itens, .historicoAntigo").addClass('displayNone');

        if($('input[name="checkFiltro"]:checked').length == 0)
        {
            $(".allItens .itens, .historicoAntigo").removeClass('displayNone');

        }else {

            $('input[name="checkFiltro"]:checked').each(function () {
                //console.log(this.value);

                var classe = this.value;

                $(".allItens .itens").map(function () {

                    if ($(this).hasClass(classe)) {
                        $(this).removeClass('displayNone');
                    }

                });

            });
        }

    });

});
