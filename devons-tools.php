<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       SFT Scrolls CPT
 * Plugin URI:        
 * Description:       This is for tracking and posting SFT Scrolls.
 * Version:           v1.0.0
 * Author:            Devon Godfrey
 * Author URI:        http://playfreygames.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
GitHub Plugin URI: https://github.com/devonhg/sft-scrolls-plugin
GitHub Branch:     master

	*IMPORTANT*
	do a "find/replace" accross the directory for "SFT_PLUG" and replace
	with your plugin name. 

	Plugin slug: SFT_PLUG

*/

//Include the core class of the post type api
    include_once('pt-api/class-core.php');
    register_activation_hook( __FILE__, 'SFT_PLUG_ptapi_activate' );

//Create Post-Type Object
    $pt_scroll = new SFT_PLUG_post_type( "Scrolls", "Scroll", "This post-type is for scroll." ); 

//Register Taxonomies
    $pt_scroll->reg_tax("Categories", "Category" );

//Modify Hooks
    $pt_scroll->add_hook_single( "sft_scroll_download" );

//Add Meta
    $scroll_file = $pt_scroll->reg_meta('Scroll PDF', 'Select the pdf file for the scroll here.', true, 'media');

/*******************
* Scrolls Download
*******************/

    function sft_scroll_download( $quer=null ){
        $post = SFT_PLUG_func::get_post( $quer );
        global $scroll_file;

        $scroll_link = $scroll_file->get_val(); 

        $o = ""; 

        if( $scroll_link != "" || $scroll_link != null ){
            $o .= "<div class='scroll-link'>";
                $o .= "<a target='_blank' href='" . $scroll_link . "' title='Click to View Scroll'>";
                    $o .= "View This Scroll"; 
                $o .= "</a>";
            $o .= "</div>";
        }

        echo $o; 
    }