<?php
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! get_theme_support( 'post-thumbnails' )) add_theme_support('post-thumbnails');

//Include all files in directory
	foreach (glob( plugin_dir_path( __FILE__ ) . "*." . "php" ) as $filename){
		include_once( $filename );
	}

//Front end hooks
	function MYPLUGIN_pt_archive(){
		global $post;
		$cl = null; 
		foreach( MYPLUGIN_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('MYPLUGIN_pt_archive');
			$cl->reg_hooks_archive();

			$classes = get_post_class();

			do_action('MYPLUGIN_pt_archive');

		}else{
			"No posts for this post-type.";
		}
	}

	function MYPLUGIN_pt_single(){
		global $post;
		$cl = null; 

		foreach( MYPLUGIN_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('MYPLUGIN_pt_single');
			$cl->reg_hooks_single();

			do_action('MYPLUGIN_pt_single');
		}else{
			echo "No posts for this post-type.";
		}
	}


//Create Admin Page
    new MYPLUGIN_dash_page( "Post Types", "Post Type" );