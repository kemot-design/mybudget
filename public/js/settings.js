
$(document).ready(function() {
    
    var editingExpCtgId = "";
    var editingExpCtgName = "";
    
    var editingIncCtgId = "";
    var editingIncCtgName = "";

// Expenses categories edit
    
    $('.exp-ctg-edit-btn').on("click", function() {
        
        $("#exp-ctg-edit-error").text("");
        
        editingExpCtgId = $(this).data('categoryId');
        editingExpCtgName = $('#exp-ctg-name-id' + editingExpCtgId).text();

        $("#edit-exp-ctg-modal").modal({
            show: true 
        });     

        $('.exp-ctg-name-edit').val(editingExpCtgName);

        if($("#ctg-limit" + editingExpCtgId).length) {

            $("#limit-status").prop("checked", true);

            $("#edit-exp-ctg-limit").prop("disabled", false);

            var currentLimit = $("#ctg-limit" + editingExpCtgId).text();

            $("#edit-exp-ctg-limit").val(currentLimit);

        } else {

            $("#limit-status").prop("checked", false);

            $("#edit-exp-ctg-limit").prop("disabled", true);

            $("#edit-exp-ctg-limit").val('');
        }

    }); 

    $('.exp-ctg-delete-btn').on("click", function() {

        editingExpCtgId = $(this).data('categoryId');
        var ctgName = $("#exp-ctg-name-id" + editingExpCtgId).html();
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
            ctgId: editingExpCtgId,
            oldCtgName: editingExpCtgName,
            newCtgName: newCtgName,
            userId: userId,
            newCtgLimit: newCtgLimit
        }, function(data, status) {
            if(data == "success") {
                $('#exp-ctg-name-id' + editingExpCtgId).html(newCtgName);   
                
                if($('#limit-status').is(":checked")) {
                    if($("#ctg-limit" + editingExpCtgId).length) {

                        $("#ctg-limit" + editingExpCtgId).text(newCtgLimit);

                    } else {
                        var ctgLimit = "<div class='ctg-limit'> Limit: <span id='ctg-limit" + editingExpCtgId + "'>" + newCtgLimit + "</span></div>";

                        $("#exp-ctg-edit-id" + editingExpCtgId).after(ctgLimit);
                    }
                } else {
                    
                    if($("#ctg-limit" + editingExpCtgId).length) {
                            
                        $("#ctg-limit" + editingExpCtgId).parent().remove();   
                    }
                }
                
                $("#edit-exp-ctg-modal").modal('hide');
                $("#exp-ctg-edit-error").text("");
                
            } else {
                $("#exp-ctg-edit-error").text(data);
            }
        });
    }); 


    $("#del-exp-ctg-btn").click(function(){

        $.ajax({
            type: "POST",
            url: "http://localhost/settings/deleteExpenseCategory",
            data: { ctgId: editingExpCtgId, userId: userId},
            success: function(response) {
                if (response == "success") {
                    $("#exp-ctg-item" + editingExpCtgId).remove();
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
        
        $("#new-exp-ctg-error").text("");
        
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
            success: function(data) {
                var response = parseInt(data);
                
               if (Number.isNaN(response) == false) {
                                    
                   var newCategoryElement = '<div class="expense-category" id="exp-ctg-item' + response + '"><span id="exp-ctg-name-id' + response + '">' + ctgName + '</span><span class="exp-ctg-delete-btn del-ctg-btn" data-category-id="' + response + '"><img src="/img/clear.png" class="float-right ml-2"/></span><span class="exp-ctg-edit-btn edit-ctg-btn" id="exp-ctg-edit-id' + response + '" data-category-id="' + response + '"><img src="/img/edit2.png" class="float-right"/></span>';
                                    
                    if(ctgLimit != "") {
                        newCategoryElement = newCategoryElement + '<div class="ctg-limit"> Limit: <span id="ctg-limit' + response + '">' + ctgLimit + '</span></div>';
                    }            
                    
                   newCategoryElement = newCategoryElement + '<hr class="line"/></div>';
                   
                   $("#add-exp-ctg").before(newCategoryElement);
                   
                   $("#new-exp-ctg-modal").modal('hide');
                   
                } else {
                    $("#new-exp-ctg-error").text(data);
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
    
// Income categories editinh
    
    $('.inc-ctg-edit-btn').on("click", function() {
        
        $("#inc-ctg-edit-error").text("");
        
        editingIncCtgId = $(this).data('categoryId');
        editingIncCtgName = $('#inc-ctg-name-id' + editingIncCtgId).text();

        $("#edit-inc-ctg-modal").modal({
            show: true 
        });     

        $('.inc-ctg-name-edit').val(editingIncCtgName);

    }); 
    
    $(".save-inc-ctg-edit-btn").click( function() {

        var newCtgName = $(".inc-ctg-name-edit").val();

        $.post("/settings/editIncomeCategory", {
            ctgId: editingIncCtgId,
            oldCtgName: editingIncCtgName,
            newCtgName: newCtgName,
            userId: userId
        }, function(data, status) {
            if(data == "success") {
                $('#inc-ctg-name-id' + editingIncCtgId).html(newCtgName);   
                                
                $("#edit-inc-ctg-modal").modal('hide');
                
                $("#inc-ctg-edit-error").text("");
                
            } else {
                $("#inc-ctg-edit-error").text(data);
            }
        });
    }); 
    
    
    $('.inc-ctg-delete-btn').on("click", function() {

        editingIncCtgId = $(this).data('categoryId');
        var ctgName = $("#inc-ctg-name-id" + editingIncCtgId).html();
        $("#inc-ctg-to-del").html(ctgName);

        $("#del-inc-ctg-modal").modal({
            show: true 
        }); 
    })    
    
    $("#confirm-inc-ctg-del-btn").click(function(){

        $.ajax({
            type: "POST",
            url: "http://localhost/settings/deleteIncomeCategory",
            data: { ctgId: editingIncCtgId, userId: userId},
            success: function(response) {
                if (response == "success") {
                    $("#inc-ctg-item" + editingIncCtgId).remove();
                } else {
                    alert("Wystąpił błąd, przepraszamy");
                }
            }
        });  
    });
     
    $("#add-inc-ctg").click(function() {
        
        $("#new-inc-ctg-error").text("");
        
        $("#new-inc-ctg-modal").modal({
            show: true 
        });
        
        $('#new-inc-ctg-name').val('');
        $('#new-inc-ctg-limit').val('');
    });   
    
    $("#save-new-inc-ctg-btn").click(function(){

        var ctgName = $("#new-inc-ctg-name").val();

        $.ajax({
            type: "POST",
            url: "/settings/addIncomeCategory",
            data: {
                name: ctgName
            },
            success: function(data) {
                var response = parseInt(data);

               if(Number.isNaN(response) == false) {
                                    
                   var newCategoryElement = '<div class="income-category" id="inc-ctg-item' + response + '"><span id="inc-ctg-name-id' + response + '">' + ctgName + '</span><span class="inc-ctg-delete-btn edit-ctg-btn" data-category-id="' + response + '"><img src="/img/clear.png" class="float-right ml-2"/></span><span class="inc-ctg-edit-btn del-ctg-btn" id="inc-ctg-edit-id' + response + '" data-category-id="' + response + '"><img src="/img/edit2.png" class="float-right"/></span>';
                                    
                   newCategoryElement = newCategoryElement + '<hr class="line"/></div>';
                   
                   $("#add-inc-ctg").before(newCategoryElement);
                   
                   $("#new-inc-ctg-modal").modal('hide');
                   
                } else {
                    $("#new-inc-ctg-error").text(data);
                } 
            }

        });   

    });    
    
});


