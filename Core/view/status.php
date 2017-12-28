<!-- TAB STATUS-->
<script type="text/javascript">
    var tpModal = $(".tp-modal");
    var getStatus = $(".tp-get-status");
    //var parent = tpModal.parent();
    tpModal.click(function (e) {
        if (!$(e.target).is(".tp-get-status")){
            e.preventDefault();
            return;
        }
        modalClose();
    });

    function verstatus(url, data) {
        $.post(url, data, function () {
        }).done(function (response) {
            tpModal.show('fast');
            tpModal.css("z-index", "100");
            getStatus.append(response);
            innerClose();
        }).fail(function (response) {
            console.log("Failed: " + response);
        });
    }

    function modalClose() {
        getStatus.empty();
        tpModal.hide('fast');
        tpModal.css("z-index", "-10");

    }

    function innerClose() {
        var tpClose = $(".tp-close");
        tpClose.click(function () {
            modalClose();
        });
    }

    /*
        function verstatus(url) {
            var myWindow = window.open(url, "OrderStatus", "width=400,height=550");
            return false;
        }
    */
</script>


<!-- END TAB STATUS-->
