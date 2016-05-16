var $ = jQuery.noConflict();
$(document).ready(function () {
    
    // SURVEY FORM VALIDATION
    $('#mc-survey-form').submit(mc_survey_form_submit);
    
    function mc_survey_form_submit(){
        
        $('#mc-survey-form div').removeClass('mc-has-error');        
        var error_free = true;
        
        // VALIDATE RADIO
        $('#mc-survey-form .mc-question-radio.required').each( function(){
            if( !($(this).find('input[type=radio]:checked').length > 0) ){
                $(this).addClass('mc-has-error');
                error_free = false;
            }
        });        
        
        // VALIDATE CHECKBOXES
        $('#mc-survey-form .mc-question-checkbox.required').each( function(){
            if( !($(this).find('input[type=checkbox]:checked').length > 0) ){
                $(this).addClass('mc-has-error');
                error_free = false;
            }
        });
        
        // VALIDATE INPUT
        $('#mc-survey-form .mc-question-text.required').each( function(){
            if((!($(this).find('input[type=text]').val().length > 0))){
                $(this).addClass('mc-has-error');
                error_free = false;
            }
        });
                
        // VALIDATE TEXTAREA
        $('#mc-survey-form .mc-question-textarea.required').each( function(){
            if((!($(this).find('textarea').val().length > 0))){
                $(this).addClass('mc-has-error');
                error_free = false;
            }
        });
        
        if(error_free == false)
            $('#mc-validation-errors').html('<div class="mc-alert mc-alert-danger"><strong>Oops!</strong> looks like you might have missed something.</div>');
        
        return error_free;
    };
    
    $('.mc-survey-canvas').each(function(i, obj) {
        
        var chart_colors = ["#FF6384","#4BC0C0","#FFCE56","#E7E9ED","#36A2EB"];
        var json_data = $(this).data('json');
        
        console.log(json_data.type);
        
        if(typeof json_data !== 'object')
            return;
        
        json_data.data.datasets[0].backgroundColor = ran_col(json_data.type, json_data.data.datasets[0].data.length);
        
        new Chart($(this), json_data);
    });

    function ran_col(chart_type, data_length) {
        
        var letters = ['#34485E','#3F6EA2','#278DFF','#FF6000'];
        var i=0;
        
        if(chart_type == 'line'){
            var color = '';
            color += letters[Math.floor(Math.random() * letters.length)];
            return color;
        }
        else{
            var color = [];
            while(i < data_length){
                color.push(letters[Math.floor(Math.random() * letters.length)]);
                i++;
            }
            return color;
        }
    }
    
    var showChar = 100;
    var ellipsestext = "...";
    var moretext = "Show";
    var lesstext = "Hide";

    $('.mc-survey-response').each(function() {
        var content = $(this).html();
 
        if(content.length > showChar) {
 
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);
 console.log(c);
            var html = c + '<span class="mc-survey-more-ellipses">' + ellipsestext+ '&nbsp;</span><span class="mc-survey-more-content"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="mc-survey-more-link">' + moretext + '</a></span>';
 
            $(this).html(html);
        }
    });
 
    $(".mc-survey-more-link").click(function(){
        if($(this).hasClass("mc-survey-less")) {
            $(this).removeClass("mc-survey-less");
            $(this).html(moretext);
        } else {
            $(this).addClass("mc-survey-less");
            $(this).html(lesstext);
        }
        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });
});