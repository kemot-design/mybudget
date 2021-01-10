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
    
//graphs
    
    var incomesSum = parseFloat($('#incomes-sum').text());
    var expensesSum = parseFloat($('#expenses-sum').text());
    var balance = Math.floor((incomesSum - expensesSum)*100)/100;
    var balanceMessage = "";
    
    if(balance >= 0) {
        balanceMessage = 'Brawo, zarabiasz więcej niż wydajesz.'
        
        $('#balance-value').addClass('positive-balance');
        $('#balance-value').removeClass('negative-balance');
        
    } else {
        balanceMessage = 'Uważaj, wydajesz więcej niż zarabiasz.'
        
        $('#balance-value').removeClass('positive-balance');
        $('#balance-value').addClass('negative-balance');
    }
    
    $('#balance-value').text(balance);
    $('#balance-message').text(balanceMessage);
    
    var ctx1 = document.getElementById('totals-chart').getContext('2d');
    var myChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Przychody', 'Wydatki'],
            datasets: [{
                label: 'Suma',
                data: [incomesSum, expensesSum],
                backgroundColor: [
                    'rgba(50, 250, 50, 0.6)',
                    'rgba(240, 50, 50, 0.6)'
                ],
                borderColor: [
                    'rgba(50, 250, 50, 1)',
                    'rgba(240, 50, 50, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                        return value + ' zł';
                        }
                    }
                }]
            },
            legend: {
                display: false
            }
        }

    });
    
 //incomes by category pie chart
    
    var incomeCategoryNumber = $('#income-sums-loop').data('loops-number');
    
    var incomeCategoriesInBalance = [];
    var incomeCategorieSumsInBalance = [];
    var chartColor = [];
    var chatBorderColor = [];
    var colorPallette = ['rgba(0, 250, 0', 'rgba(250, 0, 0', 'rgba(0, 0, 250', 'rgba(250, 250, 0', 'rgba(250, 0, 250', 'rgba(0, 250, 250', 'rgba(250, 150, 50', 'rgba(50, 150, 250', 'rgba(150, 250, 50', 'rgba(175, 200, 150', 'rgba(175, 100, 50','rgba(150, 50, 120', 'rgba(100, 50, 200', 'rgba(30, 90, 30', 'rgba(25, 50, 25','rgba(200, 220, 200', 'rgba(20, 50, 80', 'rgba(100, 50, 0', 'rgba(150, 150, 220', 'rgba(100, 50, 50','rgba(200, 200, 200', 'rgba(75, 225, 150', 'rgba(88, 156, 150', 'rgba(250, 150, 200', 'rgba(100, 150, 75', 'rgba(125, 200, 125'];
    
    
    for(var i = 1 ; i <= incomeCategoryNumber ; i++) {
        incomeCategoriesInBalance.push($('#income-category-name-' + i).text());
        
        incomeCategorieSumsInBalance.push($('#income-category-sum-' + i).text());
        
        chartColor.push(colorPallette[i-1] + ", 0.6)");
        chatBorderColor.push(colorPallette[i-1] + ", 1)");
    }
           
    var ctx2 = document.getElementById('incomes-chart').getContext('2d');
    var myChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: incomeCategoriesInBalance,
            datasets: [{
                label: 'Suma',
                data: incomeCategorieSumsInBalance,
                backgroundColor: chartColor,
                borderColor: chatBorderColor,
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    display: false
                },
                x: {
                    display: false
                }
            },
            legend: {
                position: 'right'
            },
            title: {
                display: true,
                text: 'Przychody'
            }
        }

    });
    

//expenses by category pie chart
    
    var expenseCategoryNumber = $('#expense-sums-loop').data('loops-number');
    
    var expenseCategoriesInBalance = [];
    var expenseCategorieSumsInBalance = [];
    var chartColor = [];
    var chatBorderColor = [];
    
    for(var i = 1 ; i <= expenseCategoryNumber ; i++) {
        expenseCategoriesInBalance.push($('#expense-category-name-' + i).text());
        
        expenseCategorieSumsInBalance.push($('#expense-category-sum-' + i).text());
        
        chartColor.push(colorPallette[i-1] + ", 0.6)");
        chatBorderColor.push(colorPallette[i-1] + ", 1)");
    }  
    
    var ctx3 = document.getElementById('expenses-chart').getContext('2d');
    var myChart = new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: expenseCategoriesInBalance,
            datasets: [{
                label: 'Suma',
                data: expenseCategorieSumsInBalance,
                backgroundColor: chartColor,
                borderColor: chatBorderColor,
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    display: false
                },
                x: {
                    display: false
                }
            },
            legend: {
                position: 'right'
            },
            title: {
                display: true,
                text: 'Wydatki'
            }
        }

    });
        
    
});