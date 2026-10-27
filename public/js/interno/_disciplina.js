$(document).ready(function (){


    $(document).on("click","#btnPesquisarFaltas", function (e) {

        $(".allItensFaltas .itensFalta").addClass('displayNone');

        if($('input[name="checkFiltroFalta"]:checked').length == 0)
        {
            $(".allItensFaltas .itensFalta").removeClass('displayNone');

        }else {

            $('input[name="checkFiltroFalta"]:checked').each(function () {
                //console.log(this.value);

                var classe = this.value;

                $(".allItensFaltas .itensFalta").map(function () {

                    if ($(this).hasClass(classe)) {
                        $(this).removeClass('displayNone');
                    }

                });

            });
        }

    });

});
