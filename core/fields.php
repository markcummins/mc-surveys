<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

class mc_survey_fields{
        
    public function __construct() {

    }
    
    function get_admin_text_question($post_id, $field_id, $fields){
                
        $defaults = array('required'=>false,'question'=>'');
        $args = wp_parse_args( $fields, $defaults ); 

        $checked = $args['required'] ? 'checked="checked"' : ''; ?>        
        
        <li class='postbox'>           
            <h3 class="hndle">Text <small>(Field ID:<?= $field_id; ?>)</small></h3>
            <div class="inside">
            
            <div class="question">
                <label>Question</label><br/>
                <input class="widefat" placeholder="question..." type="text" name="mc_survey[<?= $field_id; ?>][question]" value="<?= $args['question']; ?>" />
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
        $field_label = $fields['label'];
        $list_type = $fields['list_type'];
        $field_options = $fields['options']; ?>
        
        <li class='postbox'>           
            <h3 class="hndle">List <small>(Field ID:<?= $field_id; ?>)</small></h3>
            <div class="inside">
            
            <div class="question">
                
                <p><b>Question</b></p>
                <input class="widefat" placeholder="label..." type="text" name="mc_survey[<?= $field_id; ?>][label]" value="<?= $field_label ?>" />
                
                <p><b>List Type</b></p>
                <label>
                <input type="radio" <?= $list_type == 'checkbox' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][list_type]" value="checkbox"> Checkbox
                </label><br/>
                
                <label>
                <input type="radio" <?= $list_type == 'radio' ? 'checked="checked"' : ''; ?> name="mc_survey[<?= $field_id; ?>][list_type]" value="radio"> Radio
                </label><br/>
                
                <p><b>Answers</b></p>
                <?php foreach($field_options as $key=>$field){
            
                    $this->get_admin_list_item($post_id, $field_id, $key, $field['label']);
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
    
    function get_admin_list_item($post_id, $field_id, $key, $label){ ?>
        
        <div class="mc-list-option">
            <a href="#" class="mc-remove-list-option" data-post-id="<?= $post_id; ?>" data-field-id="<?= $field_id; ?>" data-key-id="<?= $key; ?>">
            <span class="dashicons dashicons-dismiss"></span></a>
            <input placeholder="question..." type="text" name="mc_survey[<?= $field_id; ?>][options][<?= $key;?>][label]" value="<?= $label ?>" />
        </div>
    <?php }
}
