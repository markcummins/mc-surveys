<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_survey_admin;
class mc_survey_admin{
        
    public function __construct() {
        
        global $wpdb;
        $this->wpdb = $wpdb;

        // ACTIONS 
        add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'add_style'));
        
        add_action('admin_enqueue_scripts', array($this, 'add_admin_style'));
        add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));
        add_action('save_post_mc_survey', array($this, 'save_post'), 10, 1);
        
        add_filter('excerpt_length', array($this, 'set_excerpt_length'));
        add_action('pre_get_posts', array($this, 'exclude_unlisted_surveys_from_frontend'), 10, 1);
    }
    
    function exclude_unlisted_surveys_from_frontend($q){
        
        if(is_post_type_archive('mc_survey'))
            $q->set('post_status','publish');
            
        return $q;
    }
    
    function set_excerpt_length($length) {
        
        global $post;
        if ($post->post_type == 'mc_survey')
            $length = 28;

        return $length;
    }

    function add_style() {
        wp_register_style( 'survey_style', plugins_url('mc-surveys/assets/css/mc-survey-style.css') );
        wp_enqueue_style( 'survey_style' );
    }  
    
    function add_scripts() {
        wp_register_script('survey_chart_script', plugins_url('mc-surveys/assets/js/chart.min.js'), array('jquery'),'1.1', true);
        wp_enqueue_script('survey_chart_script');
        wp_register_script('survey_script', plugins_url('mc-surveys/assets/js/mc-survey.js'), array('jquery'),'1.1', true);
        wp_enqueue_script('survey_script');
    }
    
    function add_admin_style() {        
        wp_register_style( 'survey_admin_style', plugins_url('mc-surveys/assets/admin/css/mc-survey-admin.css') );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'survey_admin_style' );
    }
    function add_admin_scripts() {        
        wp_register_script('survey_script', plugins_url('mc-surveys/assets/admin/js/mc-survey-admin.js'), array('jquery', 'wp-color-picker'),'1.1', true);
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
        $survey_options = $_POST['mc-options'];
        
        // UPDATE SURVEY QUESTIONS
        if(is_array($survey) && sizeof($survey)>0)
            $this->update_survey_fields($post_id, $survey);
        
        // UPDATE SETTINGS OR DELETE IF EMPTY
        if(is_array($survey_options) && sizeof($survey_options)>0)
            $this->update_survey_post_options_meta($post_id, $survey_options);
        else
            delete_post_meta($post_id, '_mc_options'); 
    }
    
    function update_survey_fields($post_id, $survey){
                
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        $order=0;
                
        foreach($survey as $id=>$opts){
            
            $s = serialize($opts);

            $prepare = $wpdb->prepare("UPDATE {$table_name} SET `attributes` = %s, `order` = %s WHERE `id` = %s AND `post_id` = %s", array($s, $order, $id, $post_id));
            
            $order++;
            $wpdb->query($prepare);
        }
    }
    
    function update_survey_post_options_meta($post_id, $survey_options){
                
        // SET POST STATUS
        remove_action( 'save_post_mc_survey', array( $this, 'save_post'), 10, 1);
        
        if(array_key_exists ( 'unlisted' , $survey_options ))
            wp_update_post( array('ID' => $post_id, 'post_status' => 'unlisted') );
        else
            wp_update_post( array('ID' => $post_id, 'post_status' => 'publish') );
        
        add_action( 'save_post_mc_survey', array( $this, 'save_post'), 10, 1);
            
        // UPDATE SETTINGS META
        update_post_meta($post_id, '_mc_options', $survey_options); 
    }
}
