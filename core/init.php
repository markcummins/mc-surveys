<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_survey;
class mc_survey{
        
    public function __construct() {
        
        add_action( 'init', array($this, 'register_survey_questions_custom_post') );
        add_action( 'init', array($this, 'register_survey_responses_custom_post') );
        add_action( 'init', array($this, 'my_custom_post_status') );
        add_filter( 'template_include', array($this, 'survey_templates') );
        $this->setup();
    }
    
    function setup() {
        
        include_once plugin_dir_path( __FILE__ ) . "download.php";
        include_once plugin_dir_path( __FILE__ ) . "admin.php";
        include_once plugin_dir_path( __FILE__ ) . "ajax.php"; 
        include_once plugin_dir_path( __FILE__ ) . "fields.php";        
        include_once plugin_dir_path( __FILE__ ) . "update.php";
        include_once plugin_dir_path( __FILE__ ) . "frontend.php";
        
        global $wpdb;
        if ( !isset($wpdb->mc_survey_fields) )
            $wpdb->mc_survey_fields = $wpdb->prefix . 'mc_survey_fields';
    }
    
    function my_custom_post_status(){
        
        register_post_status( 'unlisted', array(
            'label'                     => _x( 'Unlisted', 'post' ),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Unlisted <span class="count">(%s)</span>', 'Unlisted <span class="count">(%s)</span>' ),
        ) );
    }
    
    function register_survey_questions_custom_post() {
        
        $survey_labels = array(
            'name'                 => _x('Surveys', 'post type general name'),
            'singular_name'        => _x('Survey', 'post type singular name'),
            'add_new'              => _x('Add New', 'Surveys'),
            'add_new_item'         => __('Add New Survey'),
            'edit_item'            => __('Edit A Survey'),
            'new_item'             => __('New Survey'),
            'view_item'            => __('View Survey'),
            'search_items'         => __('Search Surveys'),
            'not_found'            =>  __('No Survey found'),
            'not_found_in_trash'   => __('No Surveys found in Trash'), 
            '_builtin'             =>  false, 
            'parent_item_colon'    => '',
            'menu_name'            => 'Surveys' );
        
        $survey_args = array(
            'labels'              => $survey_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_menu'        => true, 
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'surveys', 'with_front' => true ),
            'register_meta_box_cb'=> array( $this, 'add_survey_metaboxes'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 8,
            'menu_icon'           => 'dashicons-list-view',
            'supports'            => array('title','thumbnail', 'editor') );
        
        register_post_type('mc_survey', $survey_args);
    }
    
    function register_survey_responses_custom_post() {
        
        $survey_labels = array(
            'name'                 => _x('Survey Results', 'post type general name'),
            'singular_name'        => _x('Survey Result', 'post type singular name'),
            'edit_item'            => __('Edit A Survey Result'),
            'view_item'            => __('View Survey Result'),
            'search_items'         => __('Search Survey Results'),
            'not_found'            =>  __('No Survey Result found'),
            'not_found_in_trash'   => __('No Survey Results found in Trash'), 
            '_builtin'             =>  false, 
            'parent_item_colon'    => '',
            'menu_name'            => 'Survey Results' );
        
        $survey_args = array(
            'labels'              => $survey_labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
            'capabilities'        => array('create_posts' => false),
            'create_posts'        => false,
            'map_meta_cap'        => true,
            'exclude_from_search' => true,
            'show_ui'             => false,
            'show_in_menu'        => false, 
            'query_var'           => true,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 8,
            'menu_icon'           => 'dashicons-list-view',
            'supports'            => array('title','thumbnail', 'editor') );
        
        register_post_type('mc_survey_response', $survey_args);
    }
    
    function add_survey_metaboxes() {
        
        add_meta_box('mc_survey_questions',__( "<span class='dashicons dashicons-megaphone'></span> Questions", 'sv_survey' ), array($this, 'mc_survey_questions'), 'mc_survey', 'normal', 'default');
        
        add_meta_box('mc_survey_options',__( "<span class='dashicons dashicons-admin-settings'></span> Options", 'sv_survey' ), array($this, 'mc_survey_options'), 'mc_survey', 'side', 'default');

        add_meta_box('mc_survey_download',__( "<span class='dashicons dashicons-chart-area'></span> Download", 'sv_survey' ), array($this, 'mc_survey_download'), 'mc_survey', 'side', 'default');
    }
    
    function mc_survey_download($post){
        $url= get_admin_url().'?mc-survey-download='.$post->ID;
        echo "<a class='button button-primary' href='{$url}'> Download Survey Results</a>";
    }
            
    function mc_survey_options($post){
                
        $settings_array = array(
            //KEY               //DESCRIPTION
            'unlisted'      => 'Unlisted survey',
            'member-only'   => 'Members only',
            'hide-results'  => 'Hide results',
            'closed'        => 'Close the survey');
        
        $settings_meta = get_post_meta($post->ID, '_mc_options', true);        
        foreach($settings_array as $key=>$description){
            
            $checked="";
            if(is_array($settings_meta) && array_key_exists($key ,$settings_meta))
                $checked = 'checked="checked"';
            
            echo "<div><label><input name='mc-options[{$key}]' {$checked} type='checkbox'/> {$description}</label></div>";
        }
    }
    
    function mc_survey_questions($post){ ?>
            
        <div>
            <span class="mc-survey-add-question button button-primary" data-type="_text" data-post-id="<?= $post->ID; ?>"><?php _e('Add Question (Text)'); ?></span>
            <span class="mc-survey-add-question button button-primary" data-type="_list" data-post-id="<?= $post->ID; ?>"><?php _e('Add Question (List)'); ?></span>
        </div>
        <ul id="mc-survey-body" class="sortable ui-sortable"><?= $this->get_survey_questions($post->ID); ?></ul>
        <?php wp_nonce_field( 'mc_survey', 'mc_survey_noncename' );    
    }
    
    function survey_templates( $template ) {

        if ( is_post_type_archive('mc_survey') ) {

            $theme_files = array('archive-mc_survey.php');
            $exists_in_theme = locate_template($theme_files, false);

            if ( $exists_in_theme != '' ) {
                return $exists_in_theme;
            } else {
                return WP_PLUGIN_DIR . '/mc-surveys/templates/archive.php';
            }
        }
        
        if ( is_singular('mc_survey') ) {

            $theme_files = array('single-mc_survey.php');
            $exists_in_theme = locate_template($theme_files, false);

            if ( $exists_in_theme != '' ) {
                return $exists_in_theme;
            } else {
                return WP_PLUGIN_DIR . '/mc-surveys/templates/single.php';
            }
        }
        return $template;
    }  
    
    function get_survey_questions($post_id){
        
        global $wpdb;
        $mc_survey_fields = new mc_survey_fields;
        $res = $wpdb->get_results( "SELECT * FROM {$wpdb->mc_survey_fields} WHERE post_id = {$post_id} ORDER BY `order` ASC" );
        
        foreach($res as $field){
            
            switch($field->type){        
                case '_text': $mc_survey_fields->get_admin_text_question($post_id, $field->id, unserialize($field->attributes)); break;
                case '_list': $mc_survey_fields->get_admin_list_question($post_id, $field->id, unserialize($field->attributes)); break;
            }
        }
    }
}