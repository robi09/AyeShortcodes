<?php

namespace Aye\Shortcodes;

class Shortcodes {
	private $tab_titles = array();

	function __construct() {
		add_action( 'init', array( $this, 'loadAssets') );

	}

	public function loadAssets() {
		wp_enqueue_style( 'bootstrap', PLUGIN_URL . 'assets/css/main.min.css' );
		wp_enqueue_style( 'fontawesome', PLUGIN_URL . 'assets/libs/font-awesome/css/font-awesome.min.css' );
		wp_enqueue_script( 'ayeshortcode', PLUGIN_URL . 'assets/js/scripts.js', array('jquery') );
	}

	static function aye_column($atts, $content = '') {
		$args = shortcode_atts( array(
	        "lg"          => '',
            "md"          => '',
            "sm"          => '',
            "xs"          => '',
            "pull_lg"     => '',
            "pull_md"     => '',
            "pull_sm"     => '',
            "pull_xs"     => '',
            "push_lg"     => '',
            "push_md"     => '',
            "push_sm"     => '',
            "push_xs"     => '',
            "offset_lg"   => '',
            "offset_md"   => '',
            "offset_sm"   => '',
            "offset_xs"   => '',
	    ), $atts );

	    $class  = '';
		$class .= ( $args['lg'] )                                      ? ' col-lg-' . $args['lg'] : '';
		$class .= ( $args['md'] )                                      ? ' col-md-' . $args['md'] : '';
		$class .= ( $args['sm'] )                                      ? ' col-sm-' . $args['sm'] : '';
		$class .= ( $args['xs'] )                                      ? ' col-xs-' . $args['xs'] : '';
		$class .= ( $args['pull_lg']   || $args['pull_lg'] === "0" )   ? ' col-lg-pull-' . $args['pull_lg'] : '';
		$class .= ( $args['pull_md']   || $args['pull_md'] === "0" )   ? ' col-md-pull-' . $args['pull_md'] : '';
		$class .= ( $args['pull_sm']   || $args['pull_sm'] === "0" )   ? ' col-sm-pull-' . $args['pull_sm'] : '';
		$class .= ( $args['pull_xs']   || $args['pull_xs'] === "0" )   ? ' col-xs-pull-' . $args['pull_xs'] : '';
		$class .= ( $args['push_lg']   || $args['push_lg'] === "0" )   ? ' col-lg-push-' . $args['push_lg'] : '';
		$class .= ( $args['push_md']   || $args['push_md'] === "0" )   ? ' col-md-push-' . $args['push_md'] : '';
		$class .= ( $args['push_sm']   || $args['push_sm'] === "0" )   ? ' col-sm-push-' . $args['push_sm'] : '';
		$class .= ( $args['push_xs']   || $args['push_xs'] === "0" )   ? ' col-xs-push-' . $args['push_xs'] : '';
		$class .= ( $args['offset_lg'] || $args['offset_lg'] === "0" ) ? ' col-lg-offset-' . $args['offset_lg'] : '';
		$class .= ( $args['offset_md'] || $args['offset_md'] === "0" ) ? ' col-md-offset-' . $args['offset_md'] : '';
		$class .= ( $args['offset_sm'] || $args['offset_sm'] === "0" ) ? ' col-sm-offset-' . $args['offset_sm'] : '';
		$class .= ( $args['offset_xs'] || $args['offset_xs'] === "0" ) ? ' col-xs-offset-' . $args['offset_xs'] : '';

		return '<div class="' . $class . '">' . do_shortcode($content) . '</div>';
	}

	static function aye_tabs($atts, $content = '') {
		$args = shortcode_atts( array(
	        "orientation"          => 'horizontal'
	    ), $atts );

		$return = '<div class="row aye_tabs '.$args['orientation'].'">';

		// Start tabs
	    if($args['orientation'] == 'horizontal') {
			$return .= '<div class="tabs col-md-12 col-sm-12 col-xs-12 col-lg-12">';
	    } else {
			$return .= '<div class="tabs col-md-4 col-sm-4 col-xs-12 col-lg-4">';
	    }

	    $tab_content = do_shortcode(wp_strip_all_tags($content));

	    foreach($this->tab_titles as $key => $title) {
	    	$return .= '<div class="tab'.($key == 0 ? ' active' : '').'" data-tab="'. esc_attr($key) .'">' . esc_html($title) . '</div>';
	    }

	    // End tabs
		$return .= '</div><!--/.tabs-->';

		// Start contennt
		if($args['orientation'] == 'horizontal') {
			$return .= '<div class="content col-md-12 col-sm-12 col-xs-12 col-lg-12">';
		} else{
			$return .= '<div class="content col-md-8 col-sm-8 col-xs-12 col-lg-8">';
		}

		// Content and closing divs
		$return .= $tab_content . '</div><!--/.content--></div><!-- / .row -->';

		return $return;
	}

	static function aye_tab($atts, $content = "") {
		$args = shortcode_atts( array(
	        "title"          => ''
	    ), $atts );

	    $title = $args['title'];
		if(!empty($args['title']) and !in_array($args['title'], $this->tab_titles)) {
	    	$count = array_push($this->tab_titles, $title);
		}

		return '<div class="tab_content" style="display: '.(($count - 1) == 0 ? 'block' : 'none').';" data-tabcontent="'. ($count - 1) .'">' . do_shortcode($content) . '</div>';

	}

	static function aye_button($atts) {
		$args = shortcode_atts( array(
	        "url"          => '',
	        "label"          => '',
	        "target"          => '',
	        "id"          => '',
	        "icon"          => ''
	    ), $atts );

		// Build class
	    $class = "aye_button";
	    if(!empty($args['icon'])) {
	    	$class .= ' fa fa-' . $args['icon'];
	    }

	    // Get permalink if id it's used
	    $permalink = $args['url'];
	    if(!empty($args['id'])) {
	    	$permalink = get_permalink($args['id']);
	    }

	    // Build target
    	$current_url = parse_url(home_url());
    	$shortcode_url = parse_url($permalink);

    	if($current_url['host'] != $shortcode_url['host'] and empty($args['target'])) {
    		$target = '_blank';
    	} elseif(!empty($args['target'])) {
    		$target = $args['target'];
    	} else {
    		$target = '';
    	}

    	return '<a class="'.esc_attr($class).'" '.(empty($target) ? '' : 'target="'.esc_attr($target).'"').' href="'.esc_url($permalink).'">'.$args['label'].'</a>';


	}
}