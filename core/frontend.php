<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/*
 * CLASS SV_FRONTEND
 */
new mc_frontend;
class mc_frontend{
        
    public function __construct() {
        
        add_action( 'mc_get_survey', array($this, 'get_survey'));
        add_action( 'mc_get_default_thumbnail', array($this, 'get_default_thumbnail'));
    }
    
    function get_default_thumbnail(){
        
        $src = plugins_url('mc-surveys/assets/img/default-thumbnail.png');
        $link = get_the_permalink();
        
        echo "<a href='{$link}'><img src='{$src}'/></a>";
    }
    
    function user_can_do_survey($post_id, $settings_meta, $user_has_done_survey){
        
        $user_can_do_survey = true;
        $msg = "";
                
        if($user_has_done_survey)
            $msg = __("Thank you for participating in the survey", 'mc_survey');
        elseif(!is_user_logged_in() && array_key_exists('member-only', $settings_meta))
            $msg = __("Please log in to complete this survey", 'mc_survey');
        elseif(array_key_exists('closed', $settings_meta))
            $msg = __("This survey is closed", 'mc_survey');
        
        if(!empty($msg)){
            $user_can_do_survey = false;
            echo "<div class='mc-alert mc-alert-info'>{$msg}</div>";
        }
           
        return $user_can_do_survey;
    }
    
    function user_has_done_survey($post_id){
        
        $has_user_done_survey = false;
        global $wpdb;
        
        // CHECK IF USER HAS LOGGED IN AND COMPLETED
        if(is_user_logged_in()){
            
            $prep = $wpdb->prepare("SELECT * FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m 
                                ON p.`ID` = m.`post_id` 
                                WHERE p.`post_type` = 'mc_survey_response'
                                AND p.`post_author` = %s 
                                AND m.`meta_key` = '_form_id' 
                                AND m.`meta_value` = %s", array(get_current_user_id(), $post_id));

            $wpdb->get_results($prep);

            if($wpdb->num_rows > 0)
                $has_user_done_survey = true;
        }
        // CHECK IF USER HAS COMPLETED VIA IP-ADDRESS
        if($has_user_done_survey == false){
                        
            $prep = $wpdb->prepare("SELECT * FROM `wp_postmeta` 
                WHERE `meta_key` = '_ip_address' 
                AND `meta_value` = '%s' 
                AND `post_id` IN 
                
                (SELECT m.`post_id` 
                FROM `wp_posts` p JOIN `wp_postmeta` m ON p.`ID` = m.`post_id`
                WHERE p.`post_type` = 'mc_survey_response'
                AND m.`meta_key` = '_form_id' 
                AND m.`meta_value` = '%s')", array($_SERVER['REMOTE_ADDR'], $post_id));
            
            $wpdb->get_results($prep);

            if($wpdb->num_rows > 0)
                $has_user_done_survey = true;
        }
        
        return $has_user_done_survey;
    }
    
    function user_can_see_results($settings_meta){
        
        return !array_key_exists('hide-results', $settings_meta);
    }
    
    function get_survey($post_id){
        
        $fields = $this->get_fields($post_id);
        $form_action = get_the_permalink($post_id); 
        $settings_meta = get_post_meta($post_id, '_mc_options', true); 
        $settings_meta = empty($settings_meta)? array() : $settings_meta;               
        
        $user_has_done_survey = $this->user_has_done_survey($post_id);
        $user_can_do_survey = $this->user_can_do_survey($post_id, $settings_meta, $user_has_done_survey);
        $user_can_see_results = $this->user_can_see_results($settings_meta); 
            
        if($user_can_do_survey): ?>
            <form id="mc-survey-form" method='post' action='<?= $form_action; ?>'>
            <div id="mc-validation-errors"></div> 
            <?php

            foreach($fields as $field){

                $field_attr = unserialize($field->attributes);

                if($user_can_do_survey){
                    switch ($field->type){
                        case '_text': $this->get_text_question($field->id, $field_attr ,$user_can_do_survey); break;
                        case '_list': $this->get_list_question($field->id, $field_attr ,$user_can_do_survey); break;
                    }
                }

                if($user_has_done_survey && $user_can_see_results)
                    $this->show_result($post_id, $field);
            } ?>

            <input type="hidden" name="mc-survey-submission" value="<?= $post_id; ?>" />
            <?php wp_nonce_field(-1,'mc_noncename'); ?>
            <hr/>
            <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        <?php else: ?>
        <?php
            if($user_has_done_survey && $user_can_see_results){
                foreach($fields as $field)
                    $this->show_result($post_id, $field);
            } ?>
        <?php endif; ?>
        
    <?php }
    
    function show_result($post_id, $field){
                        
        switch ($field->type){
            case '_text': $this->get_text_question_result($post_id, $field); break;
            case '_list': $this->get_list_question_result($post_id, $field); break;
        }
    }
    
    function get_text_question_result($post_id, $field){
        
        global $wpdb;
        $prep = $wpdb->prepare("SELECT `meta_value` AS `answer` FROM `wp_postmeta` 
                WHERE `meta_key` = '%s' AND `post_id` IN 
                
                (SELECT m.`post_id`
                    FROM `wp_posts` p 
                    JOIN `wp_postmeta` m ON p.`ID` = m.`post_id`
                    WHERE p.`post_type` = 'mc_survey_response'
                    AND m.`meta_key` = '_form_id' 
                    AND m.`meta_value` = '%s') 
                
                ORDER BY RAND() LIMIT 6", array("_field_{$field->id}", $post_id));
            
        $res = $wpdb->get_results($prep);
        
        $atts = unserialize($field->attributes);
        echo "<div>";
        echo "<label class='mc-survey-header'>{$atts['question']}</label>";
        echo '<div class="mc-clearfix"></div>';
        
        if(empty($res)){
            echo "<div class='mc_answer'>No response yet</div>";
        }
        else{
            $arr = array();
            foreach($res as $q)
                echo "<p class='mc-survey-response'> {$q->answer} </p>";
        }
        echo '</div>';
        echo '<div class="mc-clearfix"></div>';
    }
    
    function get_list_question_result($post_id, $field){
        
        global $wpdb;
                
        $prep = $wpdb->prepare("SELECT `meta_value` AS `answer` FROM `wp_postmeta` 
                WHERE `meta_key` = '%s' 
                AND `post_id` IN 
                
                (SELECT m.`post_id`
                    FROM `wp_posts` p 
                    JOIN `wp_postmeta` m ON p.`ID` = m.`post_id`
                    WHERE p.`post_type` = 'mc_survey_response'
                    AND m.`meta_key` = '_form_id' 
                    AND m.`meta_value` = '%s') 
                    
                ORDER BY `meta_id` DESC LIMIT 100", array("_field_{$field->id}", $post_id));
            
        $res = $wpdb->get_results($prep, 'ARRAY_A');
        
        $atts = unserialize($field->attributes);
        
        $list_type = $atts['list_type'];
        $graph_type = $atts['graph_type'];
        
        echo "<label class='mc-survey-header'>{$atts['question']}</label>";
        
        if(empty($res)){
            echo "<div class='mc_answer'>No response yet</div>";
        }
        else{                 
            $arr = array();
            $arr_count = array();
            
            if($list_type=='radio'){
                $arr_count = array_count_values(array_column($res, 'answer'));
            }
            elseif($list_type=='checkbox'){
                
                $arr = array();
                foreach($res as $r){
                    $arr = array_merge($arr, unserialize($r['answer']));
                }
                $arr_count = array_count_values($arr);                
            }
                        
            $dataset = array();
            $dataset['label'] = $atts['question'];
            $dataset['data'] = array_values($arr_count);
            
            $chartjs = array();
            
            $chartjs['type'] = $graph_type;
            $chartjs['data']['labels'] = array_keys($arr_count);
            $chartjs['data']['datasets'][] = $dataset;            
            $chartjs['options']['scales']['yAxes'][]['ticks']['beginAtZero'] = true;            
            
            $json = json_encode($chartjs);            
            echo "<canvas class='mc-survey-canvas' data-json='{$json}' width='300' height='300'></canvas>";
            echo '<div class="mc-clearfix"></div>';
        }
    }
    
    function get_fields($post_id){
        
        global $wpdb;
        $table_name = $wpdb->mc_survey_fields;
        
        $prepare = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `post_id` = %s ORDER BY `order` ASC;", array($post_id));
        $fields = $wpdb->get_results($prepare);
        
        return $fields;
    }
    
    function get_list_question($field_id, $atts){
    
        $list_type = $atts['list_type'];
        
        $req_icon = $req = "";
        if($atts['required']){
            $req_icon = "<span style='color:#F00;'>*</span>";
            $req = "required";
        }
        
        echo "<div class='mc-question mc-question-{$list_type} {$req}'>";
        echo "<label class='mc-survey-header'>{$req_icon} {$atts['question']}</label>";
        
        // IS IT CHECKBOX OR RADIO?
        switch ($list_type){
            case 'checkbox': $this->loop_checkbox($field_id, $atts['options'], $required); break;
            case 'radio': $this->loop_radio($field_id, $atts['options'], $required); break;
        }
        echo "</div>";
    }
        
    function loop_checkbox($field_id, $options, $req){
        
        foreach($options as $key => $o){
            echo "<label><input {$req} type='checkbox' value='{$o['question']}' name='mc-survey[{$field_id}][$key]'/> - {$o['question']}</label><br/>";
        }
    }       
        
    function loop_radio($field_id, $options, $req){
        
        foreach($options as $key => $o){
            echo "<label><input {$req} type='radio' value='{$o['question']}' name='mc-survey[{$field_id}]'/> - {$o['question']}</label><br/>";
        }
    }
    
    function get_text_question($field_id, $atts){
        
        $req_icon = $req = "";
        if($atts['required']){
            $req_icon = "<span style='color:#F00;'>*</span>";
            $req = "required";
        }
                        
        echo "<div class='mc-question mc-question-{$atts['input_type']} {$req}'>";
        echo "<label class='mc-survey-header'>{$req_icon} {$atts['question']}</label>";
        
        // IS IT TEXT OR TEXTAREA?
        if($atts['input_type'] == 'textarea')
            echo "<textarea name='mc-survey[{$field_id}]' class='form-control'></textarea><br/>";
        else
            echo "<input name='mc-survey[{$field_id}]' class='form-control' type='text'/><br/>";
        
        echo "</div>";
    }
}
