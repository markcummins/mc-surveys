<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_survey_ajax;
class mc_survey_ajax{

    public function __construct() {
        
        add_action("wp_ajax_mc_survey_add_question", array($this, "add_question"));
        add_action("wp_ajax_mc_survey_delete_question", array($this, "delete_question"));
        
        add_action("wp_ajax_mc_survey_add_list_option", array($this, "add_list_option"));
        add_action("wp_ajax_mc_survey_remove_list_option", array($this, "remove_list_option"));
    }

    function add_question(){
                       
        if(!isset($_POST) || !isset($_POST['type']))
            wp_die();
        
        $type = $_POST['type'];
        $post_id = $_POST['post_id'];        

        switch($type){
                
            case '_text': $this->create_text_question($post_id); break;
            case '_list': $this->create_list_question($post_id); break;
            default: $this->create_text_question($post_id);
        }
        wp_die();
    }
            
    function delete_question(){
        
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        
        $post_id = $_POST['post_id'];
        $field_id = $_POST['field_id'];
        
        $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE id = %s AND post_id = %s", array($field_id, $post_id)));

        wp_die();
    }
    
    function create_text_question($post_id){
        
        global $wpdb;
        $mc_survey_fields = new mc_survey_fields;   
        $table_name = $wpdb->mc_survey_fields;
        
        $default_atts = array();
        $default_atts['input_type'] = 'text';
        $attributes = serialize($default_atts);
        
        $wpdb->query($wpdb->prepare("INSERT INTO {$table_name} (`post_id`, `type`, `order`, `attributes`) VALUES (%s, '_text', '0', '{$attributes}')", array($post_id)));
        
        $field_id = $wpdb->insert_id;
        
        $field = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$field_id} AND post_id = {$post_id} LIMIT 1" );
        $mc_survey_fields->get_admin_text_question($post_id, $field_id, unserialize($field->attributes));
        
        wp_die();
    }
        
    function create_list_question($post_id){
        
        global $wpdb;
        $mc_survey_fields = new mc_survey_fields;   
        $table_name = $wpdb->mc_survey_fields;
        
        $list_defaults = array();
        
        $list_defaults['question'] = 'Question';
        $list_defaults['list_type'] = 'checkbox';
        $list_defaults['graph_type'] = 'line';
        
        $list_defaults['options'] = array();
        array_push($list_defaults['options'], array('question'=>'Question 1'));
        array_push($list_defaults['options'], array('question'=>'Question 2'));
        array_push($list_defaults['options'], array('question'=>'Question 3'));
        
        $attributes = serialize($list_defaults);
        
        $wpdb->query($wpdb->prepare("INSERT INTO {$table_name} (`post_id`, `type`, `order`, `attributes`) VALUES (%s, '_list', '0', '{$attributes}')", array($post_id)));
        
        $field_id = $wpdb->insert_id;
        $field = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$field_id} AND post_id = {$post_id} LIMIT 1");
        
        $mc_survey_fields->get_admin_list_question($post_id, $field_id, unserialize($field->attributes));
        wp_die();
    }       
    
    function add_list_option(){
                
        $post_id = $_POST['post_id'];
        $field_id = $_POST['field_id'];
        $key_id = $_POST['key_id'];
        
        $add = $this->update_list_option('add', $post_id, $field_id, $key_id);
        
        if($add){
            $mc_survey_fields = new mc_survey_fields;
            $mc_survey_fields->get_admin_list_item($post_id, $field_id, $key_id);
        }
        
        wp_die();
    }
    
    function remove_list_option(){
        
        $post_id = $_POST['post_id'];
        $field_id = $_POST['field_id'];
        $key_id = $_POST['key_id'];
        
        $this->update_list_option('remove', $post_id, $field_id, $key_id);
        wp_die();
    }
    
    function update_list_option($update, $post_id, $field_id, $key_id){
        
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        
        $field_atts = $wpdb->get_var($wpdb->prepare("SELECT attributes FROM {$table_name} WHERE id = %s AND post_id = %s LIMIT 1", array($field_id, $post_id)));
                  
        if(!$field_atts)
            wp_die();
        
        //REMOVE THE ITEM!
        $field_atts = unserialize($field_atts);
        
        if($update == 'add')
            array_push($field_atts['options'], array('question'=>'New Item'));
        elseif($update == 'remove')
            unset($field_atts['options'][$key_id]);
        
        $field_atts = serialize($field_atts);

        $update_arr = array($field_atts, $field_id, $post_id);
        return $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET attributes=%s WHERE id=%s AND post_id=%s", $update_arr));
    }
}