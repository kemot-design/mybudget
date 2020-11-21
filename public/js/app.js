/**
 * Add jQuery Validation plugin method for a valid password
 * 
 * Valid passwords contain at least one letter and one number.
 */
$.validator.addMethod('validPassword',
    function(value, element, param) {

        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }

        return true;
    },
    'Must contain at least one letter and one number'
);

/**
 * Add jQuery Validation plugin method for a valid category selection
 * 
 * Valid income/expension/payment category cant have value == 0
 */
$.validator.addMethod('validCategory',
    function(value, element, param) {

        if (value == '0') 
        {
            return false;
            
        } else {
            
            return true;   
        }
    },

    'Please choose something'
);

/**
 * Add jQuery Validation plugin method for a valid date range selection
 * 
 * Date between specified date range
 */

$.validator.addMethod('dateRange',
    function(value, element) {

        var earliestValidDate = new Date(2000, 0, 1);
        var today = new Date();
        var incomeDate = new Date(value);



        if (incomeDate.getTime() > earliestValidDate.getTime() && incomeDate.getTime() <= today.getTime()) 
        {
            return true;

        } else {

            return false;   
        }

    },

    'Please choose date from 2020:01:01 up to today'
);