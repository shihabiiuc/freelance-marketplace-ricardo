<?php
function ricardo_files() {

	wp_enqueue_style( 'OpenSans', '//fonts.googleapis.com/css?family=Open+Sans:300,400,600' );	 //font-family: 'Open Sans', sans-serif;
	wp_enqueue_style( 'ricardo_parent_style-1', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'ricardo_parent_styles-2', get_template_directory_uri() . '/css/custom.css' );
	wp_enqueue_style( 'animatecss', '//cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css' );
	wp_enqueue_style( 'ricardo_child_style', get_stylesheet_uri(), null, microtime() );



	wp_enqueue_script( 'waypoint', get_theme_file_uri('/js/jquery.waypoints.min.js'), array('jquery'), null, true );
	wp_enqueue_script( 'connectslice_mainjs', get_theme_file_uri('/js/main.js'), array('jquery'), '1.0', true );


}
add_action( 'wp_enqueue_scripts', 'ricardo_files', 999 );


function ricardo_themesupport(){
	register_nav_menu( 'faqscroll', __('FAQ Auto Scroll', 'connectslice') );
	register_nav_menu( 'faqscroll-mobile', __('FAQ Auto Scroll Mobile', 'connectslice') );
}
add_action( 'after_setup_theme', 'ricardo_themesupport' );


function ricardo_faq_liextraclass( $classes, $item ){
	$classes[] = 'faq-li-width';
	return $classes;
}
add_filter( 'nav_menu_css_class', 'ricardo_faq_liextraclass', 10, 2 );
