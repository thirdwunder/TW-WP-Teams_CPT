<?php
/*
 * Plugin Name: Third Wunder Teams Plugin
 * Version: 1.0
 * Plugin URI: http://www.thirdwunder.com/
 * Description: Third Wunder services CPT plugin
 * Author: Mohamed Hamad
 * Author URI: http://www.thirdwunder.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: tw-team-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mohamed Hamad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-tw-team-plugin.php' );
require_once( 'includes/class-tw-team-plugin-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-tw-team-plugin-admin-api.php' );
require_once( 'includes/lib/class-tw-team-plugin-post-type.php' );
require_once( 'includes/lib/class-tw-team-plugin-taxonomy.php' );

if(!class_exists('AT_Meta_Box')){
  require_once("includes/My-Meta-Box/meta-box-class/my-meta-box-class.php");
}
if(!class_exists('Tax_Meta_Class')){
  require_once("includes/Tax-Meta-Class/Tax-meta-class/Tax-meta-class.php");
}

/**
 * Returns the main instance of TW_Team_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object TW_Team_Plugin
 */
function TW_Team_Plugin () {
	$instance = TW_Team_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = TW_Team_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

TW_Team_Plugin();
$prefix = 'tw_';

$team_slug = get_option('wpt_tw_team_slug') ? get_option('wpt_tw_team_slug') : "team-member";

$team_search = get_option('wpt_tw_team_search') ? true : false;

$team_category = (get_option('wpt_tw_team_category')=='on') ? true : false;
$team_tag      = (get_option('wpt_tw_team_tag')=='on') ? true : false;


$team_cat_slug = get_option('wpt_tw_team_category_slug') ? get_option('wpt_tw_team_category_slug') : "team";
$team_tag_slug = get_option('wpt_tw_tag_tag_slug') ? get_option('wpt_tw_tag_tag_slug') : "team-member-tag";

TW_Team_Plugin()->register_post_type(
                        'tw_team-member',
                        __( 'Team Members',     'tw-team-plugin' ),
                        __( 'Team Member',      'tw-team-plugin' ),
                        __( 'Team CPT', 'tw-team-plugin'),
                        array(
                          'menu_icon'=>plugins_url( 'assets/img/cpt-icon-team.png', __FILE__ ),
                          'rewrite' => array('slug' => $team_slug),
                          'exclude_from_search' => $team_search,
                          'has_archive'     => $team_archive,
                          'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
                        )
                    );
if($team_category){
  TW_Team_Plugin()->register_taxonomy(
    'tw_team_category',
    __( 'Teams', 'tw-team-plugin' ),
    __( 'Team', 'tw' ),
    'tw_team-member',
    array('hierarchical'=>true, 'rewrite'=>array('slug'=>$team_cat_slug))
  );
}

if($team_tag){
  TW_Team_Plugin()->register_taxonomy(
    'tw_team_tag',
    __( 'Team Member Tags', 'tw-team-plugin' ),
    __( 'Team Member Tag', 'tw-team-plugin' ),
    'tw_team-member',
    array(
      'hierarchical'=>false,
      'rewrite'=>array('slug'=>$team_tag_slug),
    )
  );
}

if (is_admin()){
  $team_config = array(
    'id'             => 'tw_team_cpt_metabox',
    'title'          => 'Team Member Details',
    'pages'          => array('tw_team-member'),
    'context'        => 'normal',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => true,
    'use_with_theme' => false
  );
  $team_meta =  new AT_Meta_Box($team_config);
  $social = array(
    'facebook'   => array('title' => 'Facebook',        'desc'=>'Facebook public profile Url'),
    'twitter'    => array('title' => 'Twitter @name',   'desc'=>'Twitter username'),
    'googleplus' => array('title' => 'Google+',         'desc'=>'Googple + public profile url'),
    'linkedin'   => array('title' => 'LinkedIn',        'desc'=>'Linkedin public profile url'),
    'flickr'     => array('title' => 'Flickr',          'desc'=>'Flickr public profile url'),
    'pinterest'  => array('title' => 'Pinterest',       'desc'=>'Pinterest url'),
    'instagram'  => array('title' => 'Instagram @name', 'desc'=>'Instagram username'),
    'youtube'    => array('title' => 'Youtube',         'desc'=>'Youtube channel'),
    'soundcloud' => array('title' => 'SoundCloud',      'desc'=>'Soundcloud public profile url'),
  );
  $team_meta->addText('tw_job_title_url',array('name'=> __('Title','tw-team-plugin')));
  $team_meta->addText('tw_email',array('name'=> __('Email','tw-team-plugin')));
  $team_meta->addText('tw_website',array('name'=> __('Website','tw-team-plugin')));
  foreach($social as $k=>$v){
    $team_meta->addText('tw_'.$k.'_url',array('name'=> __($v['title'],'tw-team-plugin'), 'desc'=>__($v['desc'],'tw-team-plugin')) );
  }
  $team_meta->Finish();


  if($team_category){


    $team_cat_config = array(
      'id' => 'team_category_meta_box',
      'title' => 'Team Meta',
      'pages' => array('tw_team_category'),
      'context' => 'normal',
      'fields' => array(),
      'local_images' => false,
      'use_with_theme' => false
    );
    $team_cat_meta =  new Tax_Meta_Class($team_cat_config);
    $team_cat_meta->addText($prefix.'order',array('name'=> __('Order','tw-team-plugin'),'desc' => 'this is a field desription'));
    $team_cat_meta->addImage($prefix.'image',array('name'=> __('Team Image ','tw-team-plugin')));
    $team_cat_meta->Finish();
  }

}