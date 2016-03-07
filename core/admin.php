<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_survey_admin;
class mc_survey_admin{
        
    public function __construct() {
        
        global $wpdb;
        $this->wpdb = $wpdb;

        // ACTIONS 
        add_action( 'admin_enqueue_scripts', array($this, 'add_admin_style') );
        add_action( 'admin_enqueue_scripts', array($this, 'add_admin_scripts') );
        add_action( 'save_post_mc_surveys', array($this, 'save_post'), 10, 1);
    }

    function add_admin_style() {        
        wp_register_style( 'survey_admin_style', plugins_url('mc-surveys/assets/admin/css/mc-survey-admin.css') );
        wp_enqueue_style( 'survey_admin_style' );
    }
    function add_admin_scripts() {        
        wp_register_script('survey_script', plugins_url('mc-surveys/assets/admin/js/mc-survey-admin.js'), array('jquery'),'1.1', true);
        wp_enqueue_script('survey_script');
    }
    
    function save_post( $post_id ) {                
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        if ( !isset( $_POST['mc_survey_noncename'] ) )
            return;

        if (!wp_verify_nonce($_POST['mc_survey_noncename'], 'mc_survey'))
            return;
        
        
        // OK, WE'RE AUTHENTICATED:
        $survey = $_POST['mc_survey'];
        
        // UPDATE SURVEY QUESTIONS
        if(is_array($survey) && sizeof($survey)>0)
            $this->update_survey_fields($post_id, $survey);
    }
    
    function update_survey_fields($post_id, $survey){
                
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        $order=0;
        
        error_log(print_r($survey,1));
        
        foreach($survey as $id=>$opts){
            
            $s = serialize($opts);

            $prepare = $wpdb->prepare("UPDATE {$table_name} SET `attributes` = %s, `order` = %s WHERE `id` = %s AND `post_id` = %s", array($s, $order, $id, $post_id));
            
            $order++;
            $wpdb->query($prepare);
        }
    }
}
