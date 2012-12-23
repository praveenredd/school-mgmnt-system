
$(document).ready(function (){
    $('.permitted,.notpermitted').css('text-indent','-9999px');
    $('input[type="text"]').addClass('form-text').css('height','15px').css('width','100px');
    $('select').addClass('form-select');
    $('.delete-data').live("click",function(e){
        e.preventDefault();
        var Url = $(this).get(0).href;
        var $this = $(this);
        if(confirm("Are you sure you want to delete?")){
            $.ajax({
                url:Url+'/format/json',
                success: function(res)
                {
                    $this.parents('tr:first').remove();
                }
            });
        }
    });
        
    $('.permitted, .notpermitted').live("click", function(e){
        e.preventDefault();
        $that = $(this);
        var siteUrl = $(this).attr("href");
        $.ajax({
            type: 'POST',
            url: siteUrl+'/format/json',
            dataType: 'json',
            success: function(res){
                $that.removeClass().addClass(res.permClass);
            }
        });
    });
});
    


