$(document).ready(function(){
    
    function updateLimitInfo()
    {
        var currentExpenseAmount = parseFloat($("#expense-value").text());

        var categoryLimit = parseFloat($("#limit-value").text());
        
        var monthExpensesSum = parseFloat($("#existing-expenses").text());
        
        var limitLeft = categoryLimit - monthExpensesSum - currentExpenseAmount;
        limitLeft = (Math.round(limitLeft * 100) / 100).toFixed(2);

        $("#limit-minus-expense").text(limitLeft);   
        
        if(limitLeft < 0) {
            $("#category-limit").removeClass("limit-above-zero");
            $("#category-limit").addClass("limit-below-zero");
            $("#limit-message").text("Przekroczyłeś limit miesięczny dla kategori");
        } else {
            $("#category-limit").removeClass("limit-below-zero");
            $("#category-limit").addClass("limit-above-zero");
            $("#limit-message").text("W tym miesiącu możesz jeszcze wydać " + limitLeft + " zł w tej kategori");
        }
        
        
    }
    
    $("#expense-category").change(function(){
        
        var categoryId = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "/expense/checkCategoryLimit",
            data: {categoryId: categoryId},
            success: function(limitResponse){
                if(limitResponse == "No limit") {
                    $("#category-limit").hide();
                    
                } else if (limitResponse == "Serwer error"){
                    $("#category-limit").hide();
                    alert(limitResponse);
                } else {
                    $("#category-limit").show();
                    $("#limit-value").text(limitResponse);
                    
                    $.ajax({
                        type: "POST",
                        url: "/expense/getCurrentMonthCategoryExpenses",
                        data: {categoryId: categoryId},
                        success: function(expensesSum) {
                            if(expensesSum != 'Serwer error') {
                                $("#existing-expenses").text(expensesSum); 
                                $("#limit-minus-expense").text(limitResponse - expensesSum);
                            } else {
                                alert("serwer error, sorry");
                            }  
                        },
                        complete: function(){
                            updateLimitInfo();
                        }
                    });
                }
            }
        });      
        
    });
    
    $("#expense-amount").change(function(){
        
        var currentExpenseAmount = parseFloat($(this).val());       
        $("#expense-value").text(currentExpenseAmount);
        
        updateLimitInfo();
    });
    
});