$(document).ready(function (){


    $(document).on("click","#btnPesquisarEscoltas", function (e) {

        $(".allItensEscoltas .itensEscolta").addClass('displayNone');

        if($('input[name="checkFiltroEscolta"]:checked').length == 0)
        {
            $(".allItensEscoltas .itensEscolta").removeClass('displayNone');

        }else {

            $('input[name="checkFiltroEscolta"]:checked').each(function () {
                //console.log(this.value);

                var classe = this.value;

                $(".allItensEscoltas .itensEscolta").map(function () {

                    if ($(this).hasClass(classe)) {
                        $(this).removeClass('displayNone');
                    }

                });

            });
        }

    });

});
