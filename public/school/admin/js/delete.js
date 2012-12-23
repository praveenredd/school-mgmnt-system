$("document").ready(function() {
    $('.delete-data').live("click", function(e) {
        e.preventDefault();
        var Url = $(this).get(0).href;
        var $this = $(this);
        if (confirm("Are you sure you want to delete?")) {
            $.ajax({
                url: Url + '/format/json',
                success: function(res)
                {
                    if (res.error) {
                        alert("This content can't be deleted.");
                    } else {
                        $this.parents('tr:first').remove();
                    }

                }
            });
        }
    });
});