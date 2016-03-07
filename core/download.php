<?php defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

class sv_export{
    /**
    * Constructor
    */
    public function __construct(){
        
        if( isset($_GET['sv-report']) ){// && is_admin() && is_numeric($_GET['sv-report']) && current_user_can( 'manage_options' ) ){
            
            $csv = $this->generate_csv();

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

    /**
    * Converting data to CSV
    */
    function generate_csv(){
        
        $sv_admin = new sv_admin;
        $data = $sv_admin->get_response_data($_GET['sv-report']);
        $csv="";
        
        if(!is_array($data))
            return false;
        
        if(count($data) == 0)
            return false;
            
        // FOREACH ROW
        foreach ($data as $row){
            // FOREACH CELL
            foreach ($row as $cell){
                $cell = is_array($cell) ? implode($cell, ',') : $cell;
                $csv .= $this->wrap($cell);
            }
            $csv .= "\n";
        }
            
        return $csv;
    }
    
    function wrap($cell){
        $cell = '"'. str_replace('"', '', $cell) .'",';
        return $cell;
    }
}