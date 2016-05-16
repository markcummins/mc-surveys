<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

class mc_survey_fields{
        
    public function __construct() {

    }
    
    function get_admin_text_question($post_id, $field_id, $fields){
                
        $defaults = array('required'=>false,'question'=>'');
        $args = wp_parse_args( $fields, $defaults ); 

        $checked = $args['required'] ? 'checked="checked"' : '';      
        $input_type = $fields['input_type']; ?>
                  
            <li class='postbox'>           
            <h3 class="hndle">Text <small>(Field ID:<?= $field_id; ?>)</small></h3>
            <div class="inside">
            
            <div class="question">
                
                <p><b>Question</b></p>
                <input class="widefat" placeholder="question..." type="text" name="mc_survey[<?= $field_id; ?>][question]" value="<?= $args['question']; ?>" />
                
                <p><b>Type</b></p>
                <label>
                <input type="radio" <?= $input_type == 'text' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][input_type]" value="text">Single Line
                </label>
                 | 
                <label>
                <input type="radio" <?= $input_type == 'textarea' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][input_type]" value="textarea">Multi Line
                </label><br/>
                

            </div>
            <br/>
            <label><input <?= $checked; ?> type="checkbox" name="mc_survey[<?= $field_id; ?>][required]"/> Required</label>
            <br/>
            <br/>
            <a href="#" class="mc-survey-delete-question" data-post-id="<?= $post_id; ?>" data-field-id="<?= $field_id; ?>">remove</a>
            </div>
        <br/>
        </li>
    <?php }    
    
    function get_admin_list_question($post_id, $field_id, $fields){
        
        $defaults = array('required'=>false,'question'=>'');
        $args = wp_parse_args( $fields, $defaults ); 

        $checked = $args['required'] ? 'checked="checked"' : '';
        
        $add_key = 0;
        $field_question = $fields['question'];
        $list_type = $fields['list_type'];
        $graph_type = $fields['graph_type'];
        $field_options = $fields['options']; ?>
        
        <li class='postbox'>           
            <h3 class="hndle">List <small>(Field ID:<?= $field_id; ?>)</small></h3>
            <div class="inside">
            
            <div class="question">
                
                <p><b>Question</b></p>
                <input class="widefat" placeholder="label..." type="text" name="mc_survey[<?= $field_id; ?>][question]" value="<?= $field_question ?>" />
                
                <p><b>List Type</b></p>
                <label><input type="radio" <?= $list_type == 'checkbox' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][list_type]" value="checkbox">Checkbox</label>
                &nbsp;|&nbsp;
                <label><input type="radio" <?= $list_type == 'radio' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][list_type]" value="radio">Radio</label>
                <br/>
                
                <p><b>Graph Type</b></p>
                <label><input type="radio" <?= $graph_type == 'line' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][graph_type]" value="line">line</label>
                &nbsp;|&nbsp;
                <label><input type="radio" <?= $graph_type == 'bar' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][graph_type]" value="bar">Bar</label>
                &nbsp;|&nbsp; 
                <label><input type="radio" <?= $graph_type == 'polarArea' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][graph_type]" value="polarArea">Polar Area</label>
                &nbsp;|&nbsp;
                <label><input type="radio" <?= $graph_type == 'pie' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][graph_type]" value="pie">Pie</label>
                &nbsp;|&nbsp;
                <label><input type="radio" <?= $graph_type == 'doughnut' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][graph_type]" value="doughnut">Doughnut</label>
                <br/>
                
                <p><b>Answers</b></p>
                <?php foreach($field_options as $key=>$field){
            
                    $this->get_admin_list_item($post_id, $field_id, $key, $field['question']);
                    $add_key = $key>$add_key ? $key : $add_key;
                } ?>

                <a href="#" class="button-primary mc-add-list-option" data-post-id="<?= $post_id; ?>" data-field-id="<?= $field_id; ?>" data-key-id="<?= ($add_key+1); ?>">+ Add List Item</a>
                
            </div>
            <br/>
            <label><input <?= $checked; ?> type="checkbox" name="mc_survey[<?= $field_id; ?>][required]"/> Required</label>
            <br/>
            <br/>
            <a href="#" class="mc-survey-delete-question" data-post-id="<?= $post_id; ?>" data-field-id="<?= $field_id; ?>">remove</a>
            </div>
        <br/>
        </li>
    <?php }
    
    function get_admin_list_item($post_id, $field_id, $key, $question=''){ ?>
        
        <div class="mc-list-option">
            <a href="#" class="mc-remove-list-option" data-post-id="<?= $post_id; ?>" data-field-id="<?= $field_id; ?>" data-key-id="<?= $key; ?>">
            <span class="dashicons dashicons-dismiss"></span></a>
            <input placeholder="question..." type="text" name="mc_survey[<?= $field_id; ?>][options][<?= $key;?>][question]" value="<?= $question; ?>" />
        </div>
    <?php }
}
