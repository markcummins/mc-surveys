var $ = jQuery.noConflict();
$(document).ready(function () {

    var base_url = window.location.origin + '/wordpress/wp-admin/admin-ajax.php';
    
    // ADD NEW TEXT QUESTIONS
    $(".mc-survey-add-question").click(function () {
        
        var post_id = $(this).data('post-id');
        var type = $(this).data('type');
        
        jQuery.ajax({
            url: base_url,
            type: "POST",
            data: { action: 'mc_survey_add_question', type:type, post_id:post_id },
            dataType: 'html' 
        }).done(function (ret) {      
            $('#mc-survey-body').append(ret);
        }).error(function(erorr){
            console.log(erorr);
        });
    });

    // REMOVE QUESTION
    $("#mc-survey-body").on('click', '.mc-survey-delete-question', function(e){
        
        var this_el = $(this);
        var post_id = this_el.data('post-id');
        var field_id = this_el.data('field-id');
        
        jQuery.ajax({
            url: base_url,
            type: "POST",
            data: { action: 'mc_survey_delete_question', type:'text', post_id:post_id, field_id:field_id },
            dataType: 'html' 
        }).done(function (ret) {
            this_el.closest('.postbox').remove();
        }).error(function(erorr){
            console.log(erorr);
        });
        
        e.preventDefault();
    });

    $("#mc-survey-body").on('click', '.mc-remove-list-option', function(e){
        
        var this_el = $(this);
        var post_id = this_el.data('post-id');
        var field_id = this_el.data('field-id');
        var key_id = this_el.data('key-id');
        
        jQuery.ajax({
            url: base_url,
            type: "POST",
            data: { action: 'mc_survey_remove_list_option', type:'text', post_id:post_id, field_id:field_id, key_id:key_id },
            dataType: 'html' 
        }).done(function (ret) {
            this_el.closest('.mc-list-option').remove();
        }).error(function(erorr){
            console.log(erorr);
        });
        
        e.preventDefault();
    });
    
    $("#mc-survey-body").on('click', '.mc-add-list-option', function(e){
        
        var this_el = $(this);
        
        // LOADING??
        if(this_el.hasClass('loading')){
            e.preventDefault();
            return;
        }
        else{
            this_el.addClass('loading');
        }
        
        var post_id = this_el.data('post-id');
        var field_id = this_el.data('field-id');
        var key_id = this_el.data('key-id');
        
        // DO AJAX NOW!
        jQuery.ajax({
            url: base_url,
            type: "POST",
            data: { action: 'mc_survey_add_list_option', type:'text', post_id:post_id, field_id:field_id, key_id:key_id },
            dataType: 'html' 
        }).done(function (ret) {
            this_el.data('key-id', (key_id+1));
            this_el.before(ret);   
            this_el.removeClass('loading');
        }).error(function(erorr){
            console.log(erorr);
            this_el.removeClass('loading');
        });
        
        e.preventDefault();
    });

    // SORT QUESTIONS
    $( '#mc-survey-body.sortable' ).sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.hndle',
        placeholder: {
            element: function(currentItem) {
                return $("<li style='background:#EEE'>&nbsp;</li>")[0];
            },
            update: function(container, p) {
                return;
            }
        }
    });
    
});