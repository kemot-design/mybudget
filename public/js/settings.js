
$(document).ready(function() {
    
    var editingCtgId = "";
    var editingCtgName = "";


    $('.exp-ctg-edit-btn').on("click", function() {
        
        editingCtgId = $(this).data('categoryId');
        editingCtgName = $('#exp-ctg-name-id' + editingCtgId).text();

        $("#edit-exp-ctg-modal").modal({
            show: true 
        });     

        $('.exp-ctg-name-edit').val(editingCtgName);

        if($("#ctg-limit" + editingCtgId).length) {

            $("#limit-status").prop("checked", true);

            $("#edit-exp-ctg-limit").prop("disabled", false);

            var currentLimit = $("#ctg-limit" + editingCtgId).text();

            $("#edit-exp-ctg-limit").val(currentLimit);

        } else {

            $("#limit-status").prop("checked", false);

            $("#edit-exp-ctg-limit").prop("disabled", true);

            $("#edit-exp-ctg-limit").val('');
        }

    }); 

    $('.exp-ctg-delete-btn').on("click", function() {

        editingCtgId = $(this).data('categoryId');
        var ctgName = $("#exp-ctg-name-id" + editingCtgId).html();
        $("#exp-ctg-to-del").html(ctgName);

        $("#del-exp-ctg-modal").modal({
            show: true 
        }); 
    }); 



    $(".save-exp-ctg-edit-btn").click( function() {

        var newCtgName = $(".exp-ctg-name-edit").val();
        
        var newCtgLimit = 0;
        
        if($('#limit-status').is(":checked")) {
            newCtgLimit = $("#edit-exp-ctg-limit").val(); 
        } 

        $.post("/settings/editExpenseCategory", {
            ctgId: editingCtgId,
            oldCtgName: editingCtgName,
            newCtgName: newCtgName,
            userId: userId,
            newCtgLimit: newCtgLimit
        }, function(data, status) {
            if(data == "success") {
                $('#exp-ctg-name-id' + editingCtgId).html(newCtgName);   
                
                if($('#limit-status').is(":checked")) {
                    if($("#ctg-limit" + editingCtgId).length) {

                        $("#ctg-limit" + editingCtgId).text(newCtgLimit);

                    } else {
                        var ctgLimit = "<div class='ctg-limit'> Limit: <span id='ctg-limit" + editingCtgId + "'>" + newCtgLimit + "</span></div>";

                        $("#exp-ctg-edit-id" + editingCtgId).after(ctgLimit);
                    }
                } else {
                    
                    if($("#ctg-limit" + editingCtgId).length) {
                            
                        $("#ctg-limit" + editingCtgId).parent().remove();   
                    }
                }
                
                $("#edit-exp-ctg-modal").modal('hide');
                
            } else {
                $("#exp-ctg-edit-error").text(data);
            }
        });
    }); 


    $("#del-exp-ctg-btn").click(function(){

        $.ajax({
            type: "POST",
            url: "http://localhost/settings/deleteExpenseCategory",
            data: { ctgId: editingCtgId, userId: userId},
            success: function(response) {
                if (response == "success") {
                    $("#exp-ctg-item" + editingCtgId).remove();
                } else {
                    alert("Category havent been deleted, sorry");
                }
            }
        });  
    });


    $('#limit-status').click(function(){
        if($(this).is(":checked")){
            $("#edit-exp-ctg-limit").removeAttr("disabled");
        } else {
            $("#edit-exp-ctg-limit").val("");
            $("#edit-exp-ctg-limit").attr("disabled", true);
        }
    });
    
     $('#new-ctg-limit-status').click(function(){
        if($(this).is(":checked")){
            $("#new-exp-ctg-limit").removeAttr("disabled");
        } else {
            $("#new-exp-ctg-limit").val("");
            $("#new-exp-ctg-limit").attr("disabled", true);
        }
    });
    
    
    
    $("#add-exp-ctg").click(function() {
        
        $("#new-exp-ctg-modal").modal({
            show: true 
        });
        
        $('#new-exp-ctg-name').val('');
        $('#new-exp-ctg-limit').val('');
    });
                             

    $("#save-new-exp-ctg-btn").click(function(){

        var ctgName = $("#new-exp-ctg-name").val();
        var ctgLimit = $("#new-exp-ctg-limit").val();

        $.ajax({
            type: "POST",
            url: "/settings/addExpenseCategory",
            data: {
                name: ctgName,
                limit: ctgLimit
            },
            success: function(response) {
               if (response != "failure") {
                    
                   var newCategoryElement = '<div class="expense-category" id="exp-ctg-item' + response + '"><span id="exp-ctg-name-id' + response + '">' + ctgName + '</span><span class="exp-ctg-delete-btn" data-category-id="' + response + '"><img src="/img/clear.png" class="float-right ml-2"/></span><span class="exp-ctg-edit-btn" id="exp-ctg-edit-id' + response + '" data-category-id="' + response + '"><img src="/img/edit2.png" class="float-right"/></span>';
                                    
                    if(ctgLimit != "") {
                        newCategoryElement = newCategoryElement + '<div class="ctg-limit"> Limit: <span id="ctg-limit' + response + '">' + ctgLimit + '</span></div>';
                    }            
                    
                   newCategoryElement = newCategoryElement + '<hr class="line"/></div>';
                   
                   $("#add-exp-ctg").before(newCategoryElement);
                   
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
                    $("#profile-edit-error").html("");
                    
                    $("#user-name").text(newName);
                    $("#user-email").text(newEmail);
                    
                } else {
                            
                    var errorMsg = "";
                    
                    var errors = $.parseJSON(response);
                    
                    errors.forEach(listErrors);
                    
                    function listErrors(item) {
                        var singleError = "<li>" + item + "</li>"
                        errorMsg += singleError;
                    }
                    
                    
                    $("#profile-edit-error").html("<ul class='pl-1'>" + errorMsg + "</ul>");
                }
            }
        });
    });
    
    $("#cancel-profile-edit-btn").click(function(){
        $("#profile-edit-error").html("");
    });
    
    $("#user-delete-modal-btn").click(function(){
        $("#user-delete-modal").modal('show');
    });
    
    $("#user-delete-btn").click(function(){
        window.location.href = "/settings/deleteUser";
    });
     
});


