
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

        var newCtgName = $(".category_name_edit").val();
        
        var newCtgLimit = 0;
        
        if($('#limitSetEdit').is(":checked")) {
            newCtgLimit = $("#edit-ctg-limit").val(); 
        } 

        $.post("http://localhost/settings/editExpenseCategory", {
            ctgId: editingCtgId,
            newCtgName: newCtgName,
            userId: userId,
            newCtgLimit: newCtgLimit
        }, function(data, status) {
            if(data == "success") {
                $('#ctgId' + editingCtgId).html(newCtgName);   
                
                if($('#limitSetEdit').is(":checked")) {
                    if($("#ctgLimit" + editingCtgId).length) {

                        $("#ctgLimit" + editingCtgId).text(newCtgLimit);

                    } else {
                        var ctgLimit = "<div class='ctgLimit'> Limit: <span id='ctgLimit" + editingCtgId + "'>" + newCtgLimit + "</span></div>";

                        $("#editBtnCtg" + editingCtgId).after(ctgLimit);
                    }
                } else {
                    
                    if($("#ctgLimit" + editingCtgId).length) {
                            
                        $("#ctgLimit" + editingCtgId).parent().remove();   
                    }
                }
                
            } else {
                alert("Category havent been modified, sorry");
            }
        });
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
            url: "/settings/addExpenseCategory",
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
        
    $("#user-settings-modal-btn").click(function(){
        $("#user-settings-modal").modal({
            show: true 
        });
        
        $("#user-name-edit").val($("#user-name").text());
        $("#user-email-edit").val($("#user-email").text());
    });
    
    $("#save-user-edit-btn").click(function(){
        var newName = $("#user-name-edit").val();
        var newEmail = $("#user-email-edit").val();
        var newPassword = $("#user-password-edit").val();
        
        $.ajax({
            type: "POST",
            url: "/settings/updateUserData",
            data: { 
                name: newName,
                email: newEmail,
                password: newPassword
            },
            success: function(response) {
                if(response == "success") {
                    
                    $("#user-settings-modal").modal('hide');
                    $("#profile-edit-err").html("");
                    
                } else {
                            
                    var errorMsg = "";
                    
                    var errors = $.parseJSON(response);
                    
                    errors.forEach(listErrors);
                    
                    function listErrors(item) {
                        errorMsg = errorMsg + item + "<br/>";
                    }
                    
                    $("#profile-edit-err").html(errorMsg);
                }
            }
        });
    });
    
    $("#cancel-profile-edit-btn").click(function(){
        $("#profile-edit-err").html("");
    });
    
});


