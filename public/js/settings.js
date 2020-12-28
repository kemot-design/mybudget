
$(document).ready(function() {
    
    var editingCtgId = "";


    $('.edit-btn').on("click", function() {
        
        editingCtgId = $(this).data('categoryId');
        var editingCtgName = $('#ctgId' + editingCtgId).text();

        $("#edit-category-modal").modal({
            show: true 
        });     

        $('.category_name_edit').val(editingCtgName);

        if($("#ctgLimit" + editingCtgId).length) {

            $(".setLimit").attr("checked", true);

            $(".category-limit").attr("disabled", false);

            var currentLimit = $("#ctgLimit" + editingCtgId).text();

            $(".category-limit").val(currentLimit);

        } else {

            $(".setLimit").attr("checked", false);

            $(".category-limit").attr("disabled", true);

            $(".category-limit").val('');
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
        
        if($('.setLimit').is(":checked")) {
                      
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
                        if($("#ctgLimit" + editingCtgId).length) {
                            
                            $("#ctgLimit" + editingCtgId).text(newCtgLimit);
                            
                        } else {
                            var ctgLimit = "<div class='ctgLimit'> Limit: <span id='ctgLimit" + editingCtgId + "'>" + newCtgLimit + "</span></div>";
                            
                            $("#editBtnCtg" + editingCtgId).after(ctgLimit);
                            //$("#ctgLimit" + editingCtgId).append(newCtgLimit);
                        }
                        
                    } else {
                        alert("Nie udao się zmienić limitu dla kategori");
                    }
                }
            });
            
        } else {
            
            $.ajax({
                type: "POST",
                url: "http://localhost/settings/changeExpenseCategoryLimit",
                data: {
                    ctgLimit: 0,
                    userId: userId,
                    ctgId: editingCtgId
                },
                success: function(response) {
                    if(response == "success") {                                    
                        if($("#ctgLimit" + editingCtgId).length) {
                            
                            $("#ctgLimit" + editingCtgId).parent().remove();   
                        }           
                    } else {
                        alert("Nie udao się zmienić limitu dla kategori");
                    }
                }
            });
            
        }
    }); 


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


    $('.setLimit').click(function(){
        if($(this).is(":checked")){
            $(".category-limit").removeAttr("disabled");
        } else {
            $(".category-limit").val("");
            $(".category-limit").attr("disabled", true);
        }
    });
    
    $("#addNewExpCtg").click(function() {
        
        $("#new-category-modal").modal({
            show: true 
        });
        
        $('.category_name_edit').val('');
        $('#new-category-limit').val('');
    });
                             

    $("#save-new-ctg-btn").click(function(){

        var ctgName = $(".category-name").val();
        var ctgLimit = $("#new-category-limit").val();

        $.ajax({
            type: "POST",
            url: "http://localhost/settings/addExpenseCategory",
            data: {
                name: ctgName,
                limit: ctgLimit
            },
            success: function(response) {
               if (response != "failure") {
                    
                   var newCategoryElement = '<div class="expenseCategory" id="expenseCtgItem' + response + '"><span id="ctgId' + response + '">' + ctgName + '</span><span class="delete-btn" data-category-id="' + response + '"><img src="/img/clear.png" class="float-right ml-2"/></span><span class="edit-btn" id="editBtnCtg' + response + '" data-category-id="' + response + '"><img src="/img/edit2.png" class="float-right"/></span>';
                                    
                    if(ctgLimit != "") {
                        newCategoryElement = newCategoryElement + '<div class="ctgLimit"> Limit: <span id="ctgLimit' + response + '">' + ctgLimit + '</span></div>';
                    }            
                    
                   newCategoryElement = newCategoryElement + '<hr class="line"/></div>';
                   
                   $("#addNewExpCtg").before(newCategoryElement);
                   
                } else {
                    alert("New category haven't been added");
                } 
            }

        });   

    });
        
    
});


