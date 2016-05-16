<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new sv_update;
class sv_update{

    public function __construct(){
        
        add_action( 'init', array($this, 'process_submitted_survey') );
    }

    function process_submitted_survey() {
                
        if ( !isset( $_POST['mc-survey-submission'] ) || !is_numeric($_POST['mc-survey-submission']) ) 
            return false;
                  
        if ( !isset( $_POST['mc_noncename'] ) )
            return false;
        
        if ( !isset($_POST['mc-survey']))
            return false;

        if ( !wp_verify_nonce( $_POST['mc_noncename']) )
            return false;
        
        $survey_id = $_POST['mc-survey-submission'];
        $answers = $_POST['mc-survey'];
        
        if(!empty($survey_id) && !empty($answers))
            $this->update_survey_results($survey_id, $answers);
    }
    
    function update_survey_results($survey_ID, $answers){
        
        global $user_ID;
        $new_post = array('post_title' => '',
                          'post_content' => '',
                          'post_status' => 'publish',
                          'post_date' => date('Y-m-d H:i:s'),
                          'post_author' => $user_ID,
                          'post_type' => 'mc_survey_response',
                          'post_category' => array(0));
        
        $post_id = wp_insert_post($new_post);
        
        // UPDATE FORM META
        update_post_meta($post_id, "_form_id", $survey_ID);
        
        // STORE IP ADDRESS
        update_post_meta($post_id, "_ip_address", $_SERVER['REMOTE_ADDR']);
        
        // STORE USER_ID
        if(is_user_logged_in())
            update_post_meta($post_id, "_user_id", get_current_user_id());

        foreach($answers as $key=>$a){
            if(!empty($a))
                update_post_meta($post_id, "_field_{$key}", $a);
        }
    }
}