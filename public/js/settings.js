
var editingCtgId = "";


$('.edit-btn').on("click", function() {

    editingCtgId = $(this).data('categoryId');
    var editingCtgName = $('#ctgId' + editingCtgId).html();

    $("#edit-category-modal").modal({
        show: true 
    });     

    $('.category_name_edit').val(editingCtgName);
    
    if($("#ctgLimit" + editingCtgId).length) {
        
        $("#setLimit").attr("checked", true);
        
        $(".category_limit_edit").attr("disabled", false);
        
        var currentLimit = $("#ctgLimit" + editingCtgId).text();

        $(".category_limit_edit").val(currentLimit);
        
    } else {
        
        $("#setLimit").attr("checked", false);
        
        $(".category_limit_edit").attr("disabled", true);
    }

}); 

$('.delete-btn').on("click", function() {

    editingCtgId = $(this).data('categoryId');
    var ctgName = $('#ctgId' + editingCtgId).html();
    $("#ctgToDel").html(ctgName);

    $("#delete-category-modal").modal({
        show: true 
    }); 
}); 

$(document).ready( function() {

    $(".save-btn").click( function() {

        var newName = $(".category_name_edit").val();

        $.post("http://localhost/settings/editExpenseCategory", {
            ctgId: editingCtgId,
            newCtgName: newName,
            userId: userId
        }, function(data, status) {
            if(data == "success") {
                $('#ctgId' + editingCtgId).html(newName);   
            } else {
                alert("Category havent been modified, sorry");
            }
        });
        
        if($('#setLimit').is(":checked")) {
                      
            var newCtgLimit = $(".category_limit_edit").val();
            
            $.ajax({
                type: "POST",
                url: "http://localhost/settings/changeExpenseCategoryLimit",
                data: {
                    ctgLimit: newCtgLimit,
                    userId: userId,
                    ctgId: editingCtgId
                },
                success: function(response) {
                    if(response == "success") {
                        var ctgLimit = "<div class='ctgLimit' id='ctgLimit" + editingCtgId + "'> Limit: </div>";
                        $("#editBtnCtg" + editingCtgId).after(ctgLimit);

                        $("#ctgLimit" + editingCtgId).append(newCtgLimit);    
                    } else {
                        alert("Nie udao się zmienić limitu dla kategori");
                    }
                }
            });
            
        } else {
            alert("limit not set");
        }
    }); 

});

$(document).ready(function(){
    $("#del-ctg-btn").click(function(){

        $.ajax({
            type: "POST",
            url: "http://localhost/settings/deleteExpenseCategory",
            data: { ctgId: editingCtgId, userId: userId},
            success: function(response) {
                if (response == "success") {
                    $("#expenseCtgItem" + editingCtgId).remove();
                } else {
                    alert("Category havent been deleted, sorry");
                }
            }
        });  
    });

});

$(document).ready(function(){
    $('#setLimit').click(function(){
        if($(this).is(":checked")){
            $(".category_limit_edit").removeAttr("disabled");
        } else {
            $(".category_limit_edit").val("");
            $(".category_limit_edit").attr("disabled", true);
        }
    });
});


