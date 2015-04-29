<?php
if ( ! defined( 'WPINC' ) ) { die; }

class MYPLUGIN_post_type{

    //Properties
        //Instance Tracking
        static $instances = array(); 

        var $hooks_single = array(); 
        var $hooks_archive = array(); 
        var $hooks_sc = array(); 

        var $name;
        var $name_s;
        var $pt_slug;
        var $s_display = array(
                'isTitle' => true,
                'isFI' => true,
                'isMeta' => true,
                'isContent' => true,
                'isCats' => true,
            );  
        var $a_display = array(
                'isTitle' => true,
                'isFI' => true,
                'isMeta' => true,
                'isContent' => true,
                'isCats' => true,
        );
    //Magic Methods
        public function __construct( $name , $name_s , $default = true ){

            MYPLUGIN_post_type::$instances[] = $this; 

            $this->name = $name;
            $this->name_s = $name_s;
            $this->pt_slug = "pt_" . trim(strtolower($name_s));
            new MYPLUGIN_pt_sc($this->pt_slug);//Creates Shortcodes

            add_action( 'init', array($this, 'initiate_cpt'), 0 );

            if ( $default ){
                $this->def_hooks_single();
                $this->def_hooks_archive();
                $this->def_hooks_shortcode();
            }
        }

        public function debug(){
            global $post;

            echo $post->post_type; 
        }

    //Register Methods
        public function reg_tax($name, $name_s ){
            $a = new MYPLUGIN_pt_tax($name, $name_s, $this->pt_slug );
            return $a;
        }
        public function reg_meta($title, $desc, $hide = false , $typ = "text", $options = null){
            $a = new MYPLUGIN_pt_meta($title, $desc, $this->pt_slug , $hide , $typ, $options );
            return $a;
        }

    //Add hooks
        public function add_hook_single( $function = null ){
            if (  $function == null ){
                return $this->hooks_single; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_single[] = $function; 
                }
            }
        }

        public function add_hook_archive( $function = null ){
            if (  $function == null ){
                return $this->hooks_archive; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_archive[] = $function; 
                }
            }
        }

        public function add_hook_sc( $function = null ){
            if (  $function == null ){
                return $this->hooks_sc; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_sc[] = $function; 
                }
            }
        }

    //Register Hooks
        public function reg_hooks_single(){
            foreach( $this->hooks_single as $hook ){
                 add_action("pt_single" , $hook );
            } 
        }

        public function reg_hooks_archive(){
            foreach( $this->hooks_archive as $hook ){
                 add_action("pt_archive" , $hook );
            } 
        }

        public function reg_hooks_sc(){
            foreach( $this->hooks_sc as $hook ){
                 add_action("pt_shortcode" , $hook );
            } 
        }    

    //Remove Hooks
        public function remove_hook_single( $hook ){ 
            if(($key = array_search($hook, $this->hooks_single )) !== false) {
                unset( $this->hooks_single[$key]);
            }          
        }    

        public function remove_hook_archive( $hook ){ 
            if(($key = array_search($hook, $this->hooks_archive )) !== false) {
                unset( $this->hooks_archive[$key]);
            }          
        }   

        public function remove_hook_sc( $hook ){ 
            if(($key = array_search($hook, $this->hooks_sc )) !== false) {
                unset( $this->hooks_sc[$key]);
            }          
        }   

    //Output Medthods 
        public function single(){

            global $post; 

            if ( $post->post_type == $this->pt_slug ){
                echo MYPLUGIN_func::single( false, $this->s_display , $this->pt_slug , $post->ID );
            }      
        }

        public function archive(){

            global $post; 

            if ( $post->post_type == $this->pt_slug ){
                echo MYPLUGIN_func::single( false, $this->s_display , $this->pt_slug , $post->ID );
            }  
        }

    //Default hooks
        public function def_hooks_single(){
            /*add_action("pt_single" , array("MYPLUGIN_pt_pcs",'pt_title') );
            add_action("pt_single" , array("MYPLUGIN_pt_pcs", 'pt_fi' ) );
            add_action("pt_single" , array("MYPLUGIN_pt_pcs", 'pt_content' ) );
            add_action("pt_single" , array("MYPLUGIN_pt_pcs", 'pt_meta' ) );
            add_action("pt_single" , array("MYPLUGIN_pt_pcs", 'pt_cats' ) );  */

            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pt_title') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pt_fi') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pt_content') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pt_meta') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pt_cats') );

            //$this->reg_hooks_single();

        }

        public function def_hooks_archive(){
            /*add_action("pt_archive" , array("MYPLUGIN_pt_pcs",'pt_title_a'));  
            add_action("pt_archive" , array("MYPLUGIN_pt_pcs", 'pt_fimed' ) );                         
            add_action("pt_archive" , array("MYPLUGIN_pt_pcs", 'pt_content' ) );*/

            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pt_title_a') );
            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pt_fimed') );
            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pt_content') );
        }

        public function def_hooks_shortcode(){
            add_action("pt_shortcode" , array("MYPLUGIN_pt_pcs",'pt_title_a'),10,1);
            add_action("pt_shortcode" , array("MYPLUGIN_pt_pcs", 'pt_fimed' ),10,1);
            add_action("pt_shortcode" , array("MYPLUGIN_pt_pcs", 'pt_content' ),10,1);
        }


    //Core Methods
        public function initiate_cpt(){

            $name = $this->name;
            $name_s = $this->name_s;
            $pt_slug = $this->pt_slug; 

            $labels = array(
                'name'                => _x($name, 'Post Type General Name'), 
                'singular_name'       => _x($name_s, 'Post Type Singular Name', 'twentythirteen'),
                'menu_name'           => __($name, 'twentythirteen'),
                'parent_item_colon'   => __('Parent ' . $name_s, 'twentythirteen'),
                'all_items'           => __( 'All ' .  $name, 'twentythirteen' ),
                'view_item'           => __( 'View ' . $name_s, 'twentythirteen' ),
                'add_new_item'        => __( 'Add New ' . $name_s, 'twentythirteen' ),
                'add_new'             => __( 'Add New', 'twentythirteen' ),
                'edit_item'           => __( 'Edit ' . $name_s, 'twentythirteen' ),
                'update_item'         => __( 'Update ' . $name_s, 'twentythirteen' ),
                'search_items'        => __( 'Search ' . $name_s, 'twentythirteen' ),
                'not_found'           => __( 'There are currently no '. $name, 'twentythirteen' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
            );

            $args = array(
                'label'               => __( $name, 'twentythirteen' ),
                'description'         => __( 'Contains the post data for ' . $name_s . ".", 'twentythirteen' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'can_export'          => true,
                'menu_icon'           => "dashicons-media-document",
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'page',
            );
            register_post_type( $pt_slug , $args );
        }
}