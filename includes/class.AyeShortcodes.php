<?php

namespace Aye\Shortcodes;

class Shortcodes {
	private $tab_titles = array();

	// Load assets
	public $assets;

	public function __construct() {
		$this->assets = new Assets();
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
            "pricing_table"   => '',
            "pricing_highlighted"   => '',
	    ), $atts );

	    $class  = '';
		$class .= ( $args['lg'] )                                      ? ' col-lg-'. $args['lg'] : '';
		$class .= ( $args['md'] )                                      ? ' col-md-'. $args['md'] : '';
		$class .= ( $args['sm'] )                                      ? ' col-sm-'. $args['sm'] : '';
		$class .= ( $args['xs'] )                                      ? ' col-xs-'. $args['xs'] : '';
		$class .= ( $args['pull_lg']   || $args['pull_lg'] === "0" )   ? ' col-lg-pull-'. $args['pull_lg'] : '';
		$class .= ( $args['pull_md']   || $args['pull_md'] === "0" )   ? ' col-md-pull-'. $args['pull_md'] : '';
		$class .= ( $args['pull_sm']   || $args['pull_sm'] === "0" )   ? ' col-sm-pull-'. $args['pull_sm'] : '';
		$class .= ( $args['pull_xs']   || $args['pull_xs'] === "0" )   ? ' col-xs-pull-'. $args['pull_xs'] : '';
		$class .= ( $args['push_lg']   || $args['push_lg'] === "0" )   ? ' col-lg-push-'. $args['push_lg'] : '';
		$class .= ( $args['push_md']   || $args['push_md'] === "0" )   ? ' col-md-push-'. $args['push_md'] : '';
		$class .= ( $args['push_sm']   || $args['push_sm'] === "0" )   ? ' col-sm-push-'. $args['push_sm'] : '';
		$class .= ( $args['push_xs']   || $args['push_xs'] === "0" )   ? ' col-xs-push-'. $args['push_xs'] : '';
		$class .= ( $args['offset_lg'] || $args['offset_lg'] === "0" ) ? ' col-lg-offset-'. $args['offset_lg'] : '';
		$class .= ( $args['offset_md'] || $args['offset_md'] === "0" ) ? ' col-md-offset-'. $args['offset_md'] : '';
		$class .= ( $args['offset_sm'] || $args['offset_sm'] === "0" ) ? ' col-sm-offset-'. $args['offset_sm'] : '';
		$class .= ( $args['offset_xs'] || $args['offset_xs'] === "0" ) ? ' col-xs-offset-'. $args['offset_xs'] : '';
		$class .= ( $args['pricing_table'] || $args['pricing_table'] === "0" ) ? ' aye_pricing_table '. $args['pricing_table'] : '';
		$class .= ( $args['pricing_highlighted'] || $args['pricing_highlighted'] === "0" ) ? ' aye_pricing_highlighted' : '';

		return '<div class="'. $class .'">'. do_shortcode($content) .'</div>';
	}

	static function aye_tabs($atts, $content = '') {
		// array_push(self::$assets, __FUNCTION__);
		wp_enqueue_script( 'ayeshortcode', PLUGIN_URL . 'assets/js/scripts.js', array('jquery') );
		

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
	    	$return .= '<div class="tab'.($key == 0 ? ' active' : '').'" data-tab="'. esc_attr($key) .'">'. esc_html($title) .'</div>';
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

		return '<div class="tab_content" style="display: '. (($count - 1) == 0 ? 'block' : 'none') .';" data-tabcontent="'. ($count - 1) .'">'. do_shortcode($content) .'</div>';

	}

	static function aye_button($atts) {
		$args = shortcode_atts( array(
	        "url"          => '',
	        "label"          => '',
	        "target"          => '',
	        "id"          => '',
	        "icon"          => ''
	    ), $atts );

	    // Require scripts
	    $this->assets->loadStyle('fontawesome');

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

    	return '<a class="'. esc_attr($class) .'" '. (empty($target) ? '' : 'target="'. esc_attr($target) .'"').' href="' .esc_url($permalink) .'">'. $args['label'] .'</a>';
	}

	static function aye_cta($atts, $content = "") {
		$args = shortcode_atts( array(
	        "position"		=> 'left'
	    ), $atts );

		return '<div class="aye_cta '. $args['position'] .'">'. do_shortcode($content) .'</div><!-- / .aye_cta -->';
	}

	static function aye_pricing_title($atts) {
		$args = shortcode_atts( array(
	        "title"          => '',
	        "price"			 => ''
	    ), $atts );

	    return '<div class="aye_pricing_title"><span class="title">'. $args['title'] .'</span><span class="price">'. $args['price'] .'</span></div><!-- / .aye_pricing_title -->';
	}

	static function aye_pricing_row($atts) {
		$args = shortcode_atts( array(
	        "content"          => '',
	        "icon"			 => ''
	    ), $atts );

	    // Require scripts
	    $this->assets->loadStyle('fontawesome');

	    $class = "aye_pricing_row";

		return '<div class="aye_pricing_row">'. (( $args['icon'] || $args['icon'] === "0" ) ? '<i class="fa fa-'. $args['icon'] . '"></i>' : '') . ' '  . $args['content'] .'</div>';
	}

	static function aye_progress_bar($atts) {
		$args = shortcode_atts( array(
	        "percent"          => 0,
	        "icon"			 => '',
	        "label"			 => ''
	    ), $atts );

		// Require scripts
	    $this->assets->loadStyle('fontawesome');

		$return = '<div class="aye_progress_bar"><div class="loading" style="width: '. esc_attr($args['percent']) .'%;"></div><!-- / .loading -->';

		if(!empty($args['icon'])) {
			$return .= '<i class="fa fa-'. esc_attr($args['icon']) .'"></i>';
		}

		if(!empty($args['label'])) {
			$return .= '<span>' . $args['label'] . '</span>';
		}

		$return .= '</div>';

		return $return;
	}

	static function aye_message_box($atts) {
		$args = shortcode_atts( array(
	        "type"			 => '',
	        "text"			 => '',
	        "icon"			 => '',
	        "color"			 => '',
	        "background"	 => ''
	    ), $atts );
	    
	    // Require scripts
	    $this->assets->loadStyle('fontawesome');

    	// Set defaults
	    $icon = ( $args['icon'] ) ? $args['icon'] : '';
	    $background = ( $args['background'] ) ? $args['background'] : '#DDD';
	    $color = ( $args['color'] ) ? $args['color'] : '';

	    // Set background and icon based on type
    	if("error" == $args['type']) {
    		$background = '#FF6347';
    		$color = '#FFF';
    		$icon = 'ban';
    	} elseif("warning" == $args['type']) {
    		$background = '#FF8E47';
    		$color = '#FFF';
    		$icon = 'exclamation-triangle';
    	} elseif("info" == $args['type']) {
    		$background = '#007acc';
    		$color = '#FFF';
    		$icon = 'info-circle';
    	} elseif("success" == $args['type']) {
    		$background = '#1CFF8B';
    		$color = '#FFF';
    		$icon = 'check';
    	}

    	$return = '<div class="aye_message_box '. $args['type'] .'" style="color: '. esc_attr($color) .'; background-color: '. esc_attr($background) .';">';

    	if(!empty($icon)) {
    		$return .= '<i class="fa fa-'. esc_attr($icon) .'"></i> ';
    	}

    	$return .= $args['text'] . '</div>';

    	return $return;
	}

	static function aye_icon($atts) {
		$args = shortcode_atts( array(
	        "type"			 => '',
	    ), $atts );

		// Require scripts
	    $this->assets->loadStyle('fontawesome');

	    return '<i class="fa fa-'. esc_attr($args['type']) .'"></i>';
	}

	static function aye_dropcap($atts, $content = "") {
		$args = shortcode_atts( array(
	        "color"			 => '',
	        "font"			 => ''
	    ), $atts );

		if( !empty($args['color']) or !empty($args['font']) ) {
		    $style = ' style="';

		    if(!empty($args['color'])) {
		    	$style .= 'color: '. esc_attr($args['color']) .';';
		    }

		    if(!empty($args['font'])) {
		    	$style .= 'font-family: '. esc_attr($args['font']) .';';
		    }

		    $style .= '" ';
		}

		return '<span'. $style .' class="aye_dropcap">'. do_shortcode($content) .'</span>';
	}

	static function aye_blockquote($atts, $content = "") {
		$args = shortcode_atts( array(
	        "position"			 => 'left',
	        "columns"			 => 'col-md-4',
	        "author"			 => ''
	    ), $atts );

		$return = '<div class="aye_blockquote '. esc_attr($args['columns']) .' col-lg-12 col-sm-12 col-xs-12" style="float: '. esc_attr($args['position']) .';">' . $content;

		if(!empty($args['author'])) {
			$return .= '<span class="author">'. $args['author'] .'</span>';
		}

		$return .= '</div>';

		return $return;
	}

	static function aye_label($atts) {
		$args = shortcode_atts( array(
	        "icon"			 => '',
	        "background"	 => 'tomato',
	        "label"	 => 'tomato',
	        "arrow"	 => '',
	        "color"			 => 'white'
	    ), $atts );

		// Require scripts
	    $this->assets->loadStyle('fontawesome');

		// Build class
	    $class = "aye_label";
	    if(!empty($args['arrow'])) {
	    	$class .= ' ' . $args['arrow'];
	    }
	    

	    // Build Style
	    $style = ' style="' . 
	    	(!empty($args['color']) ? 'color: '. esc_attr($args['color']) .';' : '') . 
	    	(!empty($args['background']) ? 'background-color: '. esc_attr($args['background']) .';' : '') .
	    	((!empty($args['background']) and !empty($args['arrow'])) ? 'border-color: '. esc_attr($args['background']) .';' : '') . '" ';

	    // Build return
	    $return = '<span class="'. $class .'"'. $style .'>';

	    // Add icon
	    if(!empty($args['icon'])) {
	    	$return .= '<i class="fa fa-' . $args['icon'] . '"></i> ';
	    }

	    $return .= esc_html($args['label']) .'</span>';

	    return $return;
	}

	static function aye_accordion($atts, $content = "") {
		$args = shortcode_atts( array(
	        "title"			 => '',
	        "active"		 => ''
	    ), $atts );

	    if(!empty($args['title'])) {
	    	return '<div class="aye_accordion">
	    		<div class="aye_accordion_title'. (!empty($args['active']) ? ' active' : '') .'">'. $args['title'] .'</div><!-- / .aye_accordion_title -->
	    		<div class="aye_accordion_content"'. (!empty($args['active']) ? ' style="display: block;"' : '') .'>'. do_shortcode($content) .'</div><!-- / .aye_accordion_content -->
	    	</div><!-- / .aye_accordion -->';
	    }
	}
	
	static function aye_divider_gotop($atts) {
		$args = shortcode_atts( array(
	        "border_color"			 => '',
	        "border_height"			 => '',
	        "color"					 => '',
	        "margin"		 => ''
	    ), $atts );

	   	// Build style
	   	$style = ' style="' . 
	    	(!empty($args['border_color']) ? 'border-color: '. esc_attr($args['border_color']) .';' : '') . 
	    	(!empty($args['color']) ? 'color: '. esc_attr($args['color']) .';' : '') . 
	    	(!empty($args['border_height']) ? 'border-width: '. esc_attr($args['border_height']) .';' : '') .
	    	(!empty($args['margin']) ? 'margin: '. esc_attr($args['margin']) .' 0;' : '') . '" ';

		return '<div class="aye_divider_gotop"'. $style .'><span>&#8657; '. __('Back to top', 'ayeshort') .'</span></div><!-- / .aye_divider_gotop -->';
	}

	static function aye_divider_headline($atts, $content = "") {
		$args = shortcode_atts( array(
	        "border_color"			 => '',
	        "color"			 => '',
	        "background_color"					 => ''
	    ), $atts );

	    // Build style
	   	$style = ' style="' . 
	    	(!empty($args['border_color']) ? 'border-color: '. esc_attr($args['border_color']) .';' : '') . 
	    	(!empty($args['color']) ? 'color: '. esc_attr($args['color']) .';' : '') . 
	    	(!empty($args['background_color']) ? 'background-color: '. esc_attr($args['background_color']) .';' : '') . '" ';

		return '<div class="aye_divider_headline"'. $style .'><span>'. do_shortcode($content) .'</span></div><!-- / .aye_divider_headline -->';
	}

	static function aye_lead_paragraph($atts, $content = "") {
		if(!empty($content)) {
			return '<p class="aye_lead_paragraph">'.$content.'</p>';
		}
	}

	static function aye_tooltip($atts, $content = "") {
		$args = shortcode_atts( array(
	        "text"			 => ''
	    ), $atts );

		if(!empty($args['text'])) {
	    	return '<span class="aye_tooltip" data-tooltip="'. esc_attr($args['text']) .'">'. do_shortcode($content) .'</span>';
		} else {
			return do_shortcode($content);
		}
	}

	static function aye_google_font($atts, $content = "") {
		$args = shortcode_atts( array(
	        "font"			 => '',
	        "weight"		 => ''
	    ), $atts );

		// Build font query
		$query = 
		(!empty($args['font']) ? str_replace(' ', '+', $args['font']) .':' : '') . $args['weight'] . 
		(in_array('italic', $atts) ? 'i' : '');

		var_dump($query);

		// Build style
		$style = 
				(!empty($args['font']) ? 'font-family: ' . $args['font'] . ';' : '') . 
				(!empty($args['weight']) ? 'font-weight: ' . $args['weight'] . ';' : '') . 
				(in_array('italic', $atts) ? 'font-style: italic;' : '');

		if(!empty($query)) {
			wp_enqueue_style( str_replace(' ', '_', $args['font']),  '//fonts.googleapis.com/css?family=' . $query );
			return '<span style="'. $style .'">'.do_shortcode($content).'</span>';
		}

	}

}
