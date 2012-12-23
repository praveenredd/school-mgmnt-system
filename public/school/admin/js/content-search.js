$(document).ready(function() {
    $("#search-all-content").live("keyup", function() {
        var content = $(this).val();
        if (content.length < 2) {
            return false;
        }
        var menu_type = $("#menu-type").val();
        $.ajax({
            data: 'content=' + content + '&menu_type=' + menu_type,
            dataType: 'json',
            url: site.baseUrl + '/admin/index/search-content/format/json',
            success: function(res) {
                if (res.size != 0) {
                    $("#search-result").replaceWith(res.html);
                }
            }
        });
    });
    $("#menu-type").live("change", function() {
        var content = $("#search-all-content").val();
        if (content.length < 2) {
            return false;
        }
        var menu_type = $("#menu-type").val();
        $.ajax({
            data: 'content=' + content + '&menu_type=' + menu_type,
            dataType: 'json',
            url: site.baseUrl + '/admin/index/search-content/format/json',
            success: function(res) {
                if (res.size != 0) {
                    $("#search-result").replaceWith(res.html);
                }
            }
        });
    })
});