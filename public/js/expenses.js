$(document).ready(function(){
    
    $("#expense-category").change(function(){
        
        var categoryId = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "/expense/checkCategoryLimit",
            data: {categoryId: categoryId},
            success: function(response){
                if(response == "No limit") {
                    $("#category-limit").text("");
                } else if (response == "Serwer error"){
                    $("#category-limit").text("");
                    alert(response);
                } else {
                    $("#limit-value").text(response);
                    
                    $.ajax({
                        type: "POST",
                        url: "/expense/getCurrentMonthCategoryExpenses",
                        data: {categoryId: categoryId},
                        success: function(expensesSum) {
                            alert(expensesSum);
                            if(expensesSum != 'Serwer error') {
                                $("#existing-expenses").text(expensesSum);   
                            } else {
                                alert("serwer error, sorry");
                            }  
                        }
                    });
                }
            }
        
        });
        
    });
    
});