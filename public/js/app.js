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


$.validator.addMethod('dateRange',
    function(value, element) {

        var earliestValidDate = new Date(2000, 0, 1);
    
        var latestValidDate = new Date();
    
        latestValidDate.setMonth(latestValidDate.getMonth() + 1);
    
        var operationDate = new Date(value);

        if (operationDate.getTime() > earliestValidDate.getTime() && operationDate.getTime() <= latestValidDate.getTime()) 
        {
            return true;

        } else {

            return false;   
        }

    },

    'Please choose date from 2020:01:01 up to one month from today'
);

$('#flash-msg-hide-btn').click(function(){
    $('.alert').remove();
});
