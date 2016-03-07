<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_surveys;
class mc_surveys{
        
    public function __construct() {
        
        add_action( 'init', array($this, 'register_custom_post_types') );
        add_filter('template_include', array($this, 'survey_templates') );
        $this->setup();
    }
    
    function setup() {
        include_once plugin_dir_path( __FILE__ ) . "admin.php";
        include_once plugin_dir_path( __FILE__ ) . "ajax.php"; 
        include_once plugin_dir_path( __FILE__ ) . "fields.php";        
        include_once plugin_dir_path( __FILE__ ) . "frontend.php";
        
        global $wpdb;
        if ( !isset($wpdb->mc_survey_fields) )
            $wpdb->mc_survey_fields = $wpdb->prefix . 'mc_survey_fields';
        
//        include_once plugin_dir_path( __FILE__ ) . "download.php";
    }
    
    function register_custom_post_types() {
        
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
        
        register_post_type('mc_surveys', $survey_args);
    }
    
    function add_survey_metaboxes() {
        
        add_meta_box('mc_survey_questions',__( "<span class='dashicons dashicons-megaphone'></span> Questions", 'sv_survey' ), array($this, 'mc_survey_questions'), 'mc_surveys', 'normal', 'default');
        
//        add_meta_box('sv_survey_options',__( "<span class='dashicons dashicons-admin-settings'></span> Options", 'sv_survey' ), array($this, 'mc_survey_options'), 'mc_surveys', 'side', 'default');
//        
//        add_meta_box('sv_survey_download',__( "<span class='dashicons dashicons-chart-area'></span> Download", 'sv_survey' ), array($this, 'mc_survey_download'), 'mc_surveys', 'side', 'default');
//        
//        add_meta_box('sv_survey_results',__( "<span class='dashicons dashicons-chart-area'></span> Download", 'sv_survey' ), array($this, 'mc_survey_results'), 'mc_surveys', 'normal', 'default');
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

        if ( is_post_type_archive('mc_surveys') ) {

            $theme_files = array('archive-mc_surveys.php');
            $exists_in_theme = locate_template($theme_files, false);

            if ( $exists_in_theme != '' ) {
                return $exists_in_theme;
            } else {
                return WP_PLUGIN_DIR . '/mc-surveys/templates/archive.php';
            }
        }
        
        if ( is_singular('mc_surveys') ) {

            $theme_files = array('single-mc_surveys.php');
            $exists_in_theme = locate_template($theme_files, false);

            if ( $exists_in_theme != '' ) {
                return $exists_in_theme;
            } else {
                return WP_PLUGIN_DIR . '/mc-surveys/templates/single.php';
            }
        }
        return $template;
    }
    
    function mc_survey_options($post){
        
    }   
    
    function mc_survey_download($post){
        
    }   
    
    function mc_survey_results($post){
        
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