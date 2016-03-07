<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/*
 * CLASS SV_FRONTEND
 */
new mc_frontend;
class mc_frontend{
        
    public function __construct() {
        
        add_action( 'init', array($this, 'process_submitted_survey') );
        add_action( 'mc_get_survey', array($this, 'get_survey'), 10, 1 );
    }
    
    function get_survey($post_id){
        
        $fields = $this->get_fields($post_id);
        $form_action = get_the_permalink($post_id);
        ?>
        
        <form method='post' action='<?= $form_action; ?>'>
        
        <?php 
        foreach($fields as $field){
            switch ($field->type){
                case '_text': $this->get_text_question($field->id, unserialize($field->attributes)); break;
                case '_list': $this->get_list_question($field->id, unserialize($field->attributes)); break;
        }} ?>
        
        <br/>
        <input type="hidden" name="mc-survey-submission" value="<?= $post_id; ?>" />
        <?php wp_nonce_field(-1,'mc_noncename'); ?>
        <button type="submit">Submit</button>
        </form>
        
    <?php }
    
    function get_fields($post_id){
        
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        
        $prepare = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `post_id` = %s ORDER BY `order` ASC;", array($post_id));
        $fields = $wpdb->get_results($prepare);
        
        return $fields;
    }
    
    function get_list_question($field_id, $atts){
    
        $list_type = $atts['list_type'];

        echo "<label>{$atts['label']}</label><br/>";
        
        switch ($list_type){
            case 'checkbox': $this->loop_checkbox($atts['options']); break;
            case 'radio': $this->loop_radio($atts['options']); break;
        }
    }
        
    function loop_checkbox($options){
        
        foreach($options as $key => $o){
            echo "<label><input type='checkbox' value='{$o['label']}' name='mc-survey[{$field_id}][{$key}]'/> - {$o['label']}</label><br/>";
        }
    }       
        
    function loop_radio($options){
        
        foreach($options as $key => $o){
            echo "<label><input type='radio' value='{$o['label']}' name='mc-survey[{$field_id}]'/> - {$o['label']}</label><br/>";
        }
    }
    
    function get_text_question($field_id, $atts){
        
        echo "<label>{$atts['question']}</label><br/>";
        echo "<input name='mc-survey[{$field_id}]' type='text'/><br/>";
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
        
        // OK, WE'RE GOOD
        $post_id = $_POST['mc-survey-submission'];
        $answers = $_POST['mc-survey'];
        
        $this->update_survey_results($post_id, $answers);
    }
    
    function update_survey_results($post_id, $answers){
        
        error_log(print_r($post_id, 1));
        error_log(print_r($answers, 1));
    }
}
