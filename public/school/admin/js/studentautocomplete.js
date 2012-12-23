$(document).ready(function() {
    $('#year,#grade,#section,#exam_type').live("change", function() {
        var grade = $("#grade").val();
        var year = $("#year").val();
        var section = $("#section").val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            data: 'year=' + year + '&grade=' + grade + '&section=' + section,
            url: site.baseUrl + '/admin/student/student-filter/format/json',
            success: function(res) {
                $("#student-name").replaceWith(res.html);
                $("#roll-number").val("");
            }
        });
    });
    $("#student-name").live("change", function() {
        var roll = $(this).val().split("::");
        $("#roll-number").val(roll[0]);
    });
    $("#roll-number").live("blur", function() {
        var rollno = $(this).val();
//        $.each($("#student-name").find("option"),val{
//            
//        });
//        $("#student-name").attr("option").each(index,val){
//            console.log(index);console.log(val);
//        }
        
    });
});
