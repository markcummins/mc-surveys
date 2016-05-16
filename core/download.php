<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

new mc_survey_export();
class mc_survey_export{
    
    public function __construct(){
        add_action('init', array($this, 'print_csv'));
    }

    function print_csv(){
        
        if( isset($_GET['mc-survey-download']) && is_admin() ){
            
            $csv = $this->get_survey();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"report.csv\";" );
            header("Content-Transfer-Encoding: binary");

            echo $csv;
            exit;
        }
    }
    
    function get_fields($post_id){
        
        global $wpdb;
        $table_name = $wpdb->prefix.'mc_survey_fields';
        
        $prepare = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `post_id` = %s ORDER BY `order` ASC;", array($post_id));
        $fields = $wpdb->get_results($prepare);
        
        return $fields;
    }
    
    function get_survey(){
        
        $post_id = $_GET['mc-survey-download'];
        
        $fields = $this->get_fields($post_id);
        $sql_arr = array();
        
        foreach($fields as $key=>$field){
            
            $field_attr = unserialize($field->attributes);
            $q = trim(strip_tags($field_attr['question']));
            array_push($sql_arr, "MAX(CASE WHEN m.`meta_key` = '_field_{$field->id}' THEN m.`meta_value` END) AS '{$q}'");
        }
        $case = implode(", ", $sql_arr);
        
        global $wpdb;
        $prep = $wpdb->prepare("SELECT  
                                    p.`post_date`, 
                                    u.`user_nicename`,
                                    {$case}
                                    
                                    FROM `wp_posts` p 
                                    JOIN `wp_postmeta` m ON p.`ID` = m.`post_id`
                                    LEFT OUTER JOIN `wp_users` u ON p.`post_author` = u.`ID`
                                    
                                    WHERE p.`post_type` = 'mc_survey_response'
                                    AND m.`post_id` IN 
                
                                    (SELECT m.`post_id` 
                                    FROM `wp_posts` p JOIN `wp_postmeta` m ON p.`ID` = m.`post_id`
                                    WHERE p.`post_type` = 'mc_survey_response'
                                    AND m.`meta_key` = '_form_id' 
                                    AND m.`meta_value` = '%s')
                                    
                                    GROUP BY p.ID", array($post_id));

        $res = $wpdb->get_results($prep, 'ARRAY_A');

        if(empty($res))
            exit;
        
        $csv = "";
        $csv_header = array_keys($res[0]);
        array_unshift($res, $csv_header);
        
        foreach($res as $row){
            
            foreach ($row as $cell){
                
                $ser = unserialize($cell);
                if($ser !== false)
                    $cell = $ser;
                
                $cell = is_array($cell) ? implode($cell, ',') : $cell;
                $csv .= $this->wrap($cell);
            }
            $csv .= "\r\n";
        }
        return $csv;
    }
    
    function wrap($cell){
        $cell = '"'. str_replace('"', '', $cell) .'",';
        return $cell;
    }
}