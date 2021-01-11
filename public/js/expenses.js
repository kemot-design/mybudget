$(document).ready(function(){
    
    var today = new Date();
    var todayISODate = today.toISOString().substr(0, 10);
    $("#date_of_expense").val(todayISODate);
    
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
    
    function getSelectedMonthCategoryExpenses(categoryId)
    {
        if($('#date_of_expense').val() == "") {
            var today = new Date();
            var expenseDate = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
        } else {
            var expenseDate = $('#date_of_expense').val();
        }     
        
        $.ajax({
            type: "POST",
            url: "/expense/getSelectedMonthCategoryExpenses",
            data: {
                categoryId: categoryId,
                expenseDate: expenseDate
            },
            success: function(expensesSum) {
                if(expensesSum != 'Serwer error') {
                    $("#existing-expenses").text(expensesSum); 
                    //$("#limit-minus-expense").text(limitResponse - expensesSum);
                } else {
                    alert("serwer error, sorry");
                }  
            },
            complete: function(){
                updateLimitInfo();
            }
        });

    }
    
    $("#expense-category").change(function(){
        
        var categoryId = $(this).val();
        
        $.ajax({
            type: "POST",
            url: "/expense/checkCategoryLimit",
            data: {
                categoryId: categoryId
            },
            success: function(limitResponse){
                if(limitResponse == "No limit") {
                    $("#category-limit").hide();
                    
                } else if (limitResponse == "Serwer error"){
                    $("#category-limit").hide();
                    alert(limitResponse);
                } else {
                    $("#category-limit").show();
                    $("#limit-value").text(limitResponse);
                    
                    getSelectedMonthCategoryExpenses(categoryId);
                }
            }
        });      
        
    });
    
    $("#expense-amount").change(function(){
        
        var currentExpenseAmount = parseFloat($(this).val());       
        $("#expense-value").text(currentExpenseAmount);
        
        updateLimitInfo();
    }); 
    
    $("#date_of_expense").change(function(){
        
        var categoryId = $("#expense-category").val();
        
        if(categoryId != "") {
            getSelectedMonthCategoryExpenses(categoryId);
        }
        
        updateLimitInfo();
    });     
    
});