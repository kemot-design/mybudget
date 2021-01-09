$(document).ready(function(){
    
    /**
     * Validate the form
     */
    $('#edit-expense').validate({
        rules: {
            amount: {
                required: true,
                number: true,
                range: [0.01, 999999.99]
            },
            date_of_expense: {
                required: true,
                dateISO: true,
                dateRange: true
            },
            expense_comment: {
                maxlength: 100
            }
        },
        messages: {
            amount: {
                required: 'pole wymagane',
                number: 'wpisz liczbę',
                range: 'wpisz liczbę z zakresu od 0.01 do 999 999.99'
            },
            date_of_expense: {
                required: 'pole wymagane',
                dateISO: 'nipoprawny format daty',
                dateRange: 'data nie może być wcześniejsza niż 01-01-2000 i poźniejsza niż miesiąc od dzisiaj'
            },
            expense_comment: {
                maxlength: 'przekroczono maksymalną liczbę znaków'
            },
            payment_method: {
                required: 'pole wymagane'    
            },
            expense_category: {
                required: 'pole wymagane'
            }
        }
    }); 
    
    $('#edit-income').validate({
        rules: {
            amount: {
                required: true,
                number: true,
                range: [0.01, 999999.99]
            },
            date_of_income: {
                required: true,
                dateISO: true,
                dateRange: true
            },
            income_comment: {
                maxlength: 100
            }
        },
        messages: {
            amount: {
                required: 'pole wymagane',
                number: 'wpisz liczbę',
                range: 'wpisz liczbę z zakresu od 0.01 do 999 999.99'
            },
            date_of_income: {
                required: 'pole wymagane',
                dateISO: 'nipoprawny format daty',
                dateRange: 'data nie może być wcześniejsza niż 01-01-2000 i poźniejsza niż miesiąc od dzisiaj'
            },
            income_comment: {
                maxlength: 'przekroczono maksymalną liczbę znaków'
            },
            income_category: {
                required: 'pole wymagane'
            }
        }
    });     
    
//Selecting balance period    

    $("#balance_period").change(function() {
        var option_value = document.getElementById("balance_period").value;
        if (option_value == "4") {
             $("#balance_date_modal").modal({
                show: true 
             });
        } else {
            document.getElementById("balance_period").setAttribute("value", option_value);
            document.getElementById("balanceForm").submit();
        }
    });
    
//editing income    

    $(".edit-income").click(function(){
        var incomeId = $(this).data("incomeId");

        $('#editing-income-id').val(incomeId);

        var incomeCategory = $("#inc-ctg-" + incomeId).text();
        var incomeCategoryId = $("#inc-ctg-" + incomeId).data("incCtgId");
        $("#income-category").val(incomeCategoryId);
        

        var incomeAmount = parseFloat($("#inc-amount-" + incomeId).text());
        $("#income-amount").val(incomeAmount);

        var incomeDate = $("#inc-date-" + incomeId).text();
        $('#date-of-income').val(incomeDate);

        var incomeComment = $('#inc-comment-' + incomeId).text();
        $('#income-comment').val(incomeComment);

        $("#edit-income-modal").modal('show');
    });

//editing expense
    
    $(".edit-expense").click(function(){
        var expenseId = $(this).data("expenseId");

        $('#editing-expense-id').val(expenseId);

        var expenseCategory = $("#exp-ctg-" + expenseId).text();
        var expenseCategoryId = $("#exp-ctg-" + expenseId).data("expCtgId");
        $("#expense-category").val(expenseCategoryId);
        
        var expenseAmount = parseFloat($("#exp-amount-" + expenseId).text());
        $("#expense-amount").val(expenseAmount);
        
        var expenseDate = $("#exp-date-" + expenseId).text();
        $('#date-of-expense').val(expenseDate);
        
        var expensePayMethod = $('#exp-pay-method-' + expenseId).text();
        $("#expense-payment-method option:contains(" + expensePayMethod + ")").attr('selected','selected');
        
        var expenseComment = $('#exp-comment-' + expenseId).text();
        $('#expense-comment').val(expenseComment);

        $("#edit-expense-modal").modal('show');
    });    
    
//Delete income
    
    var incomeToDelId = null;
    
    $('.del-income').click(function(){

        $("#del-income-modal").modal('show');
        
        incomeToDelId = $(this).data('incomeId');

    });
    
    $('#del-income-btn').click(function(){
        
        $.ajax({
            type: 'POST',
            url: '/Income/deleteSingleIncome',
            data: {incomeId: incomeToDelId},
            success: function(response){
                if(response = "success") {
                    $('#income-' + incomeToDelId).remove();
                } else {
                    alert('response');
                }
            }
        }); 
        
    });
    
//Delete expense
    
    var expenseToDelId = null;
    
    $('.del-expense').click(function(){

        $("#del-expense-modal").modal('show');
        
        expenseToDelId = $(this).data('expenseId');

    });
    
    $('#del-expense-btn').click(function(){
        
        $.ajax({
            type: 'POST',
            url: '/Expense/deleteSingleExpense',
            data: {expenseId: expenseToDelId},
            success: function(response){
                if(response = "success") {
                    $('#expense-' + expenseToDelId).remove();
                } else {
                    alert('response');
                }
            }
        }); 
        
    });

});