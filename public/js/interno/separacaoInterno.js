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


});
