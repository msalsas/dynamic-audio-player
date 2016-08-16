<?php
/*
Plugin Name: Dynamic Audio Player
Plugin URI: http://dynamicaudioplayer.com
Description: This plugin allows you to add an audio player widget with a dynamic playlist and shortcodes for single buttons
Version: 3.0.0
Author: Manolo Salsas Durán
Author URI: http://msalsas.com/
License: GPL2
*/
?>
<?php
/*  Copyright 2014 Manolo Salsas  (email : manolez@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/****************** REGISTER SCRIPTS AND STYLES ********************/


function dyn_scripts( $dyn_option ) {

	wp_register_script( 'dynamicplayer', plugins_url( '/js/dynamicplayer.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'dynamicplayer' );

	wp_register_script( 'dynamic-mousewheel', plugins_url( '/js/jScrollPane/script/jquery.mousewheel.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'dynamic-mousewheel' );

	wp_register_script( 'dynamic-jscrollpane', plugins_url( '/js/jScrollPane/script/jquery.jscrollpane.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'dynamic-jscrollpane' );

	wp_register_style( 'jquery-ui-stylesheet', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'jquery-ui-stylesheet' );

	wp_register_script( 'jquery-ui-base', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js' );
	wp_enqueue_script( 'jquery-ui-base' );

	wp_register_style( 'jscrollpane-stylesheet', plugins_url( '/js/jScrollPane/style/jquery.jscrollpane.css', __FILE__ ) );
	wp_enqueue_style( 'jscrollpane-stylesheet' );

	$styleRegistered = false;
	foreach ( $dyn_option as $option ) {
		if ( is_array( $option ) && isset( $option['dynTotalWidth'] ) && $option['dynTotalWidth'] == 'Small' ) {
			wp_register_style( 'default-stylesheet', plugins_url( '/css/smallPlayer.css', __FILE__ ) );
			$styleRegistered = true;
			break;
		} else if ( is_array( $option ) && isset( $option['dynTotalWidth'] ) && $option['dynTotalWidth'] == 'Large' ) {
			wp_register_style( 'default-stylesheet', plugins_url( '/css/largePlayer.css', __FILE__ ) );
			$styleRegistered = true;
			break;
		} else if ( is_array( $option ) && isset( $option['dynTotalWidth'] ) && $option['dynTotalWidth'] == 'Regular' ) {
			wp_register_style( 'default-stylesheet', plugins_url( '/css/default.css', __FILE__ ) );
			$styleRegistered = true;
			break;
		}
	}

	if ( ! $styleRegistered ) {
		wp_register_style( 'default-stylesheet', plugins_url( '/css/default.css', __FILE__ ) );
	}

	wp_enqueue_style( 'default-stylesheet' );

	wp_localize_script( 'dynamicplayer', 'DynamicAjax', array( 'url'   => admin_url( 'admin-ajax.php' ),
	                                                           'nonce' => wp_create_nonce( 'dynamicAjax-post-comment-nonce' )
	) );


}

function register_dyn_scripts() {
	$dyn_option = get_option( "widget_dynamic-player-widget" );
	ksort( $dyn_option );
	$excludedPages    = array();
	$includedPages    = array();
	$includedHomePage = false;
	$includedShopPage = false;

	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerExcludePages"] ) ) {
			$excludedPages = $option["dynPlayerExcludePages"] ? explode( ",", $option["dynPlayerExcludePages"] ) : array();
			break;
		}
	}

	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerIncludePages"] ) ) {
			$includedPages = $option["dynPlayerIncludePages"] ? explode( ",", $option["dynPlayerIncludePages"] ) : array();
			break;
		}
	}

	$excludedPosts = array();
	$includedPosts = array();

	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerExcludePosts"] ) ) {
			$excludedPosts = $option["dynPlayerExcludePosts"] ? explode( ",", $option["dynPlayerExcludePosts"] ) : array();
			break;
		}
	}
	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerIncludePosts"] ) ) {
			$includedPosts = $option["dynPlayerIncludePosts"] ? explode( ",", $option["dynPlayerIncludePosts"] ) : array();
			break;
		}
	}
	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerShowHomePage"] ) ) {
			$includedHomePage = $option["dynPlayerShowHomePage"] == "true" ? true : false;
			break;
		}
	}
	foreach ( $dyn_option as $option ) {
		if ( isset( $option["dynPlayerShowShopPage"] ) ) {
			$includedShopPage = $option["dynPlayerShowShopPage"] == "true" ? true : false;
			break;
		}
	}

	global $post;
	$postId = $post->ID;
	$postName = $post->post_name;
	$pageObject = get_queried_object();
	$pageId     = get_queried_object_id();
	$pageName = $pageObject->post_name;

	if ( is_page() ) {
		foreach ( $excludedPages as $excludedPage ) {
			$excludedPage = trim( $excludedPage );
			if ( $excludedPage === "all" || is_page( $excludedPage ) ) {
				return null;
			}
		}
		$included = empty( $includedPages ) ? true : false;
		foreach ( $includedPages as $includedPage ) {
			$includedPage = trim( $includedPage );
			if ( $includedPage === "all" || isSamePage($includedPage, $pageId, $pageName) ) {
				$included = true;
			}
		}
		if ( ! $included ) {
			return null;
		}
	} elseif ( is_single() ) {
		foreach ( $excludedPosts as $excludedPost ) {
			$excludedPost = trim( $excludedPost );
			if ( $excludedPost === "all" || is_single( $excludedPost ) ) {
				return null;
			}
		}
		$included = empty( $includedPosts ) ? true : false;
		foreach ( $includedPosts as $includedPost ) {
			$includedPost = trim( $includedPost );
			if ( $includedPost === "all" || isSamePost($includedPost, $postId, $postName) ) {
				$included = true;
			}
		}
		if ( ! $included ) {
			return null;
		}
	} elseif ( is_home() ) {
		if ( ! $includedHomePage ) {
			return null;
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		if ( ! $includedShopPage ) {
			return null;
		}
	}
	dyn_scripts( $dyn_option );
}

function isSamePage($currentPage, $pageId, $pageName) {
	return is_page( $pageId ) && ($currentPage == $pageId) || is_page( $pageName ) && ($currentPage == $pageName);
}

function isSamePost($currentPost, $postId, $postName) {
	return is_single( $postId ) && ($currentPost == $postId) || is_single( $postName ) && ($currentPost == $postName);
}


if ( ! is_admin() ) {
	add_action( "template_redirect", "register_dyn_scripts" );
} else {
	add_action( 'admin_enqueue_scripts', 'dynamic_my_admin_scripts45656754' );
}

function dynamicAjax456534_callback() {
	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'dynamicAjax-post-comment-nonce' ) ) {
		die ();
	}

	//Search
	if ( isset( $_POST['dynamicTracks'] ) && ! isset( $_POST['dynamicSearchTracks'] ) ) {
		if ( ! session_id() ) {
			session_start();
		}

		$dynamicTracks = $_POST['dynamicTracks'];
		if ( is_array( $dynamicTracks ) ) {
			$_SESSION['dynamicTracks'] = json_encode( array_slice( $dynamicTracks, 0, 39 ) );
		}

	} elseif ( isset( $_POST['dynamicSearchTracks'] ) ) {
		if ( ! session_id() ) {
			session_start();
		}
		if ( isset( $_SESSION['dynamicTracks'] ) && $_SESSION['dynamicTracks'] ) {
			echo $_SESSION['dynamicTracks'];
		}
	}
	die();
}

add_action( 'wp_ajax_dynamicAjax456534', 'dynamicAjax456534_callback' );
add_action( 'wp_ajax_nopriv_dynamicAjax456534', 'dynamicAjax456534_callback' );

function dynamic_my_admin_scripts45656754() {

	if ( isset( $_GET["page"] ) && $_GET["page"] === "dynamic_player_register_settings" ) {
		if ( ! wp_script_is( 'media-upload' ) ) {
			wp_enqueue_script( 'media-upload' );
		}
		if ( ! wp_script_is( 'thickbox' ) ) {
			wp_enqueue_script( 'thickbox' );
		}

		wp_enqueue_media();
		wp_register_script( 'my-admin-js', plugins_url( '/js/dynamicplayer-admin.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'my-admin-js' );

		wp_register_style( 'default-stylesheet', plugins_url( '/css/admin-style.css', __FILE__ ) );
		wp_enqueue_style( 'default-stylesheet' );
	}

}

/**********************************************************/

/******************** DEFINE SHORTCODES ********************/

//Shortcode Play Button
function dyn_play_button_widget_func( $atts ) {

	extract( shortcode_atts( array(
		'mp3_src' => '',
		'ogg_src' => '',
		'title'   => '',
		'artist'  => '',
		'album'   => '',
		'date'    => '',
		'image'   => ''
	), $atts ) );

	$output = '<div class="dynamic-play-button-container">';
	$output .= '<div class="dynamic-play-button"></div>';
	$output .= '<div class="dynamic-single dynamic-single-title">' . $title . '</div>';
	$output .= $artist ? '<div class="dynamic-single dynamic-single-artist"> &nbsp;( ' . $artist . ' ) </div>' : '';
	$output .= '<div class="dynamic-single dynamic-single-mp3-src">' . $mp3_src . '</div>';
	$output .= '<div class="dynamic-single dynamic-single-ogg-src">' . $ogg_src . '</div>';
	$output .= $album ? '<div class="dynamic-single dynamic-single-album"> &nbsp;-&nbsp;Album:&nbsp;' . $album . '</div>' : '';
	$output .= $date ? '<div class="dynamic-single dynamic-single-date"> &nbsp;-&nbsp;Date:&nbsp;' . $date . '</div>' : '';
	$output .= $image ? '<div class="dynamic-single dynamic-single-image">' . $image . '</div>' : '';
	$output .= '</div>';

	return $output;
}

add_shortcode( 'dyn-play-button', 'dyn_play_button_widget_func' );

//Shortcode Add Button
function dyn_add_button_widget_func( $atts ) {

	extract( shortcode_atts( array(
		'mp3_src' => '',
		'ogg_src' => '',
		'title'   => '',
		'artist'  => '',
		'album'   => '',
		'date'    => '',
		'image'   => ''
	), $atts ) );

	$output = '<div class="dynamic-add-button-container">';
	$output .= '<div class="dynamic-add-button"></div>';
	$output .= '<div class="dynamic-single dynamic-single-title">' . $title . '</div>';
	$output .= $artist ? '<div class="dynamic-single dynamic-single-artist"> &nbsp;( ' . $artist . ' ) </div>' : '';
	$output .= '<div class="dynamic-single dynamic-single-mp3-src">' . $mp3_src . '</div>';
	$output .= '<div class="dynamic-single dynamic-single-ogg-src">' . $ogg_src . '</div>';
	$output .= $album ? '<div class="dynamic-single dynamic-single-album"> &nbsp;-&nbsp;Album:&nbsp;' . $album . '</div>' : '';
	$output .= $date ? '<div class="dynamic-single dynamic-single-date"> &nbsp;-&nbsp;Date:&nbsp;' . $date . '</div>' : '';
	$output .= $image ? '<div class="dynamic-single dynamic-single-image">' . $image . '</div>' : '';
	$output .= '</div>';

	return $output;
}

add_shortcode( 'dyn-add-button', 'dyn_add_button_widget_func' );

//Shortcode Play + Add Button
function dyn_play_add_button_widget_func( $atts ) {

	extract( shortcode_atts( array(
		'mp3_src' => '',
		'ogg_src' => '',
		'title'   => '',
		'artist'  => '',
		'album'   => '',
		'date'    => '',
		'image'   => ''
	), $atts ) );

	$output = '<div class="dynamic-play-button-container">';
	$output .= '<div class="dynamic-play-button"></div><div class="dynamic-add-button"></div>';
	$output .= '<div class="dynamic-single dynamic-single-title">' . $title . '</div>';
	$output .= $artist ? '<div class="dynamic-single dynamic-single-artist"> &nbsp;( ' . $artist . ' ) </div>' : '';
	$output .= '<div class="dynamic-single dynamic-single-mp3-src">' . $mp3_src . '</div>';
	$output .= '<div class="dynamic-single dynamic-single-ogg-src">' . $ogg_src . '</div>';
	$output .= $album ? '<div class="dynamic-single dynamic-single-album"> &nbsp;-&nbsp;Album:&nbsp;' . $album . '</div>' : '';
	$output .= $date ? '<div class="dynamic-single dynamic-single-date"> &nbsp;-&nbsp;Date:&nbsp;' . $date . '</div>' : '';
	$output .= $image ? '<div class="dynamic-single dynamic-single-image">' . $image . '</div>' : '';
	$output .= '</div>';

	return $output;
}

add_shortcode( 'dyn-play-add-button', 'dyn_play_add_button_widget_func' );

/**********************************************************/

/*******************ADMIN PANEL****************************/

/******************* Widget *******************************/


function register_dynamic_sidebar3454() {
	global $bp_active;
	//Sidebar for dynamic player
	register_sidebar( array(
		'name'          => __( 'Dynamic Player', 'dynamicPlayer' ),
		'id'            => 'dynamic-player-sidebar',
		'description'   => __( 'Dynamic Player', 'dynamicPlayer' ),
		'before_widget' => '<div class="dynamic-player-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

}

add_action( 'widgets_init', 'register_dynamic_sidebar3454' );

add_action( 'widgets_init', 'dynamic_player_widget3454' );

add_action( 'wp_footer', 'active_dynamic_sidebar6546' );

//Add widgets options
$active_sidebars = get_option( 'sidebars_widgets' );

if ( isset( $active_sidebars['dynamic-player-sidebar'] ) && empty( $active_sidebars['dynamic-player-sidebar'] ) ) {
	$dynamicWidgetOptions = get_option( 'widget_dynamic-player-widget' );

	$dynamicWidgetOptions[1]                   = array(
		'dynTotalWidth'          => 'Regular',
		'dynPosition'            => 'Fixed',
		'dynPlaylistHeight'      => 165,
		'dynPlaylistVisible'     => 'false',
		'dynAutoplayEnabled'     => 'false',
		'dynPlayerMarginFrom'    => 'top',
		'dynPlayerMargin'        => 35,
		'dynPlayerHorMarginFrom' => 'centered',
		'dynPlayerHorMargin'     => 0,
		'dynDoNotAnimateTitle'   => 'false',
		'dynPlayerShowHomePage'  => '',
		'dynPlayerExcludePages'  => '',
		'dynPlayerIncludePages'  => '',
		'dynPlayerExcludePosts'  => '',
		'dynPlayerIncludePosts'  => '',
		'dynPlayerShowShopPage'  => '',
	);
	$active_sidebars['dynamic-player-sidebar'] = array( 'dynamic-player-widget-1' );
	update_option( 'widget_dynamic-player-widget', $dynamicWidgetOptions );
	update_option( 'sidebars_widgets', $active_sidebars );

}

function dynamic_player_widget3454() {
	if ( ! is_active_widget( 'Dynamic_Player_Widget' ) ) {
		register_widget( 'Dynamic_Player_Widget' );
	}

}

function active_dynamic_sidebar6546() {
	if ( is_active_sidebar( 'dynamic-player-sidebar' ) ) {
		echo '<div id="dynamic-player-sidebar"><div id="dynamic-player-sidebar-inner">';
		dynamic_sidebar( 'dynamic-player-sidebar' );
		echo '</div></div>';
	}
}

class Dynamic_Player_Widget extends WP_Widget {

	function Dynamic_Player_Widget() {
		$widget_ops = array( 'classname'   => 'dyn-description',
		                     'description' => __( 'Dynamic Audio Player widget is automatically added. Just type your options. Do not try to add more widgets.', 'dynamicPlayer' )
		);

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'dynamic-player-widget' );

		$this->WP_Widget( 'dynamic-player-widget', __( 'Dynamic Audio Player Widget', 'dynamicPlayer' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		for ( $i = 1; $i <= 40; $i ++ ) {
			$instance['dynTitle'][ $i - 1 ]     = strip_tags( get_option( 'dynamic_title_' . $i ) );
			$instance['dynArtist'][ $i - 1 ]    = strip_tags( get_option( 'dynamic_artist_' . $i ) );
			$instance['dynAlbum'][ $i - 1 ]     = strip_tags( get_option( 'dynamic_album_' . $i ) );
			$instance['dynDate'][ $i - 1 ]      = strip_tags( get_option( 'dynamic_date_' . $i ) );
			$instance['dynOggFile'][ $i - 1 ]   = strip_tags( get_option( 'dynamic_ogg_file_' . $i ) );
			$instance['dynMp3File'][ $i - 1 ]   = strip_tags( get_option( 'dynamic_mp3_file_' . $i ) );
			$instance['dynImageFile'][ $i - 1 ] = strip_tags( get_option( 'dynamic_image_file_' . $i ) );
		}

		wp_register_style( 'dynamicplayer-hide-sidebar', plugins_url( '/css/dynamicplayer-hide-sidebar.css', __FILE__ ) );
		wp_enqueue_style( 'dynamicplayer-hide-sidebar' );
		wp_register_script( 'dynamicplayer-show', plugins_url( '/js/dynamicplayer-show.js', __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'dynamicplayer-show', 'dynamic_options', $instance );
		wp_enqueue_script( 'dynamicplayer-show' );
		?>
		<div class="dynamic-playing-image"><img src="" alt="Song Image"/></div>
		<div id="dynamic-player-container">
			<div class='dynamic-control-panel'>
				<div class='dynamic-image dynamic-previous dynamic-inline'></div>
				<div class='dynamic-image dynamic-play dynamic-inline'></div>
				<div class='dynamic-image dynamic-next dynamic-inline'></div>
				<div class='dynamic-image dynamic-volume-slider-image dynamic-inline'>
					<div class="dynamic-volume-slider"></div>
				</div>
				<div class='dynamic-image dynamic-maximize dynamic-inline'></div>
			</div>
			<div class="dynamic-playlist-container">
				<ul id="dynamic-playlist">
					<li data-mp3="" data-ogg="" data-artist="" data-title=""></li>
				</ul>
			</div>
		</div>

		<?php

	}

	//Update the widget 

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags to remove HTML 
		$instance['dynTotalWidth']          = strip_tags( $new_instance['dynTotalWidth'] );
		$instance['dynPosition']            = strip_tags( $new_instance['dynPosition'] );
		$instance['dynPlaylistVisible']     = strip_tags( $new_instance['dynPlaylistVisible'] );
		$instance['dynPlaylistHeight']      = strip_tags( $new_instance['dynPlaylistHeight'] );
		$instance['dynAutoplayEnabled']     = strip_tags( $new_instance['dynAutoplayEnabled'] );
		$instance['dynPlayerMarginFrom']    = strip_tags( $new_instance['dynPlayerMarginFrom'] );
		$instance['dynPlayerMargin']        = strip_tags( $new_instance['dynPlayerMargin'] );
		$instance['dynPlayerHorMarginFrom'] = strip_tags( $new_instance['dynPlayerHorMarginFrom'] );
		$instance['dynPlayerHorMargin']     = strip_tags( $new_instance['dynPlayerHorMargin'] );
		$instance['dynDoNotAnimateTitle']   = strip_tags( $new_instance['dynDoNotAnimateTitle'] );
		$instance['dynPlayerShowHomePage']  = strip_tags( $new_instance['dynPlayerShowHomePage'] );
		$instance['dynPlayerExcludePages']  = strip_tags( $new_instance['dynPlayerExcludePages'] );
		$instance['dynPlayerIncludePages']  = strip_tags( $new_instance['dynPlayerIncludePages'] );
		$instance['dynPlayerExcludePosts']  = strip_tags( $new_instance['dynPlayerExcludePosts'] );
		$instance['dynPlayerIncludePosts']  = strip_tags( $new_instance['dynPlayerIncludePosts'] );
		$instance['dynPlayerShowShopPage']  = strip_tags( $new_instance['dynPlayerShowShopPage'] );

		return $instance;
	}


	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array(
			'dynTotalWidth'          => 'Regular',
			'dynPosition'            => 'Fixed',
			'dynPlaylistHeight'      => 165,
			'dynPlaylistVisible'     => 'false',
			'dynAutoplayEnabled'     => 'false',
			'dynPlayerMarginFrom'    => 'top',
			'dynPlayerMargin'        => 35,
			'dynPlayerHorMarginFrom' => 'centered',
			'dynPlayerHorMargin'     => 0,
			'dynDoNotAnimateTitle'   => 'false',
			'dynPlayerShowHomePage'  => '',
			'dynPlayerExcludePages'  => '',
			'dynPlayerIncludePages'  => '',
			'dynPlayerExcludePosts'  => '',
			'dynPlayerIncludePosts'  => '',
			'dynPlayerShowShopPage'  => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>


		<p>
			<label for="<?php echo $this->get_field_id( 'dynTotalWidth' ); ?>">
				Total Width
			</label>
			<select id="<?php echo $this->get_field_id( 'dynTotalWidth' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynTotalWidth' ); ?>" class="widefat" style="width:100%;">
				<option <?php selected( $instance['dynTotalWidth'], 'Large' ); ?> value="Large">Large</option>
				<option <?php selected( $instance['dynTotalWidth'], 'Regular' ); ?> value="Regular">Regular</option>
				<option <?php selected( $instance['dynTotalWidth'], 'Small' ); ?> value="Small">Small</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPosition' ); ?>">
				Position
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPosition' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPosition' ); ?>" class="widefat" style="width:100%;">
				<option <?php selected( $instance['dynPosition'], 'Fixed' ); ?> value="Fixed">Fixed</option>
				<option <?php selected( $instance['dynPosition'], 'Absolute' ); ?> value="Absolute">Absolute</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlaylistVisible' ); ?>">
				Playlist Visible
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPlaylistVisible' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPlaylistVisible' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynPlaylistVisible'], 'true' ); ?> value="true">true</option>
				<option <?php selected( $instance['dynPlaylistVisible'], 'false' ); ?> value="false">false</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlaylistHeight' ); ?>">
				Playlist Height
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlaylistHeight' ); ?>" type="number"
			       name="<?php echo $this->get_field_name( 'dynPlaylistHeight' ); ?>"
			       value="<?php echo $instance['dynPlaylistHeight']; ?>" class="widefat" style="width:100%;"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynAutoplayEnabled' ); ?>">
				Auto Play
			</label>
			<select id="<?php echo $this->get_field_id( 'dynAutoplayEnabled' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynAutoplayEnabled' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynAutoplayEnabled'], 'true' ); ?> value="true">true</option>
				<option <?php selected( $instance['dynAutoplayEnabled'], 'false' ); ?> value="false">false</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerMarginFrom' ); ?>">
				Player Vertical Margin From
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPlayerMarginFrom' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPlayerMarginFrom' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynPlayerMarginFrom'], 'top' ); ?> value="top">top</option>
				<option <?php selected( $instance['dynPlayerMarginFrom'], 'bottom' ); ?> value="bottom">bottom</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerMargin' ); ?>">
				Player Vertical Margin (px)
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerMargin' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerMargin' ); ?>" class="widefat" style="width:100%;"
			       type="number" value="<?php echo $instance['dynPlayerMargin'] ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerHorMarginFrom' ); ?>">
				Player Horizontal Margin From
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPlayerHorMarginFrom' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPlayerHorMarginFrom' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynPlayerHorMarginFrom'], 'left' ); ?> value="left">left</option>
				<option <?php selected( $instance['dynPlayerHorMarginFrom'], 'right' ); ?> value="right">right</option>
				<option <?php selected( $instance['dynPlayerHorMarginFrom'], 'centered' ); ?> value="centered">
					centered
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerHorMargin' ); ?>">
				Player Horizontal Margin (px) or leave blank if above option is centered
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerHorMargin' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerHorMargin' ); ?>" class="widefat"
			       style="width:100%;" type="text" value="<?php echo $instance['dynPlayerHorMargin'] ?>"
			       placeholder="leave blank if player is centered">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynDoNotAnimateTitle' ); ?>">
				Do not animate track title
			</label>
			<select id="<?php echo $this->get_field_id( 'dynDoNotAnimateTitle' ); ?>"
					name="<?php echo $this->get_field_name( 'dynDoNotAnimateTitle' ); ?>" class="widefat"
					style="width:100%;">
				<option <?php selected( $instance['dynDoNotAnimateTitle'], 'false' ); ?> value="false">false</option>
				<option <?php selected( $instance['dynDoNotAnimateTitle'], 'true' ); ?> value="true">true</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerShowHomePage' ); ?>">
				Show in home page
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPlayerShowHomePage' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPlayerShowHomePage' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynPlayerShowHomePage'], 'true' ); ?> value="true">true</option>
				<option <?php selected( $instance['dynPlayerShowHomePage'], 'false' ); ?> value="false">false</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerExcludePages' ); ?>">
				Exclude this pages (type a list of page ids (or names) separated by commas to exclude, type "all" to
				exclude all pages )
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerExcludePages' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerExcludePages' ); ?>" class="widefat"
			       style="width:100%;" type="text" value="<?php echo $instance['dynPlayerExcludePages'] ?>"
			       placeholder="leave empty to show in all pages">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerIncludePages' ); ?>">
				Include this pages (type a list of page ids (or names) separated by commas to include, type "all" to
				include all pages )
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerIncludePages' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerIncludePages' ); ?>" class="widefat"
			       style="width:100%;" type="text" value="<?php echo $instance['dynPlayerIncludePages'] ?>"
			       placeholder="leave empty to include all pages">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerExcludePosts' ); ?>">
				Exclude this posts (type a list of post ids (or names) separated by commas to exclude, type "all" to
				exclude all posts )
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerExcludePosts' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerExcludePosts' ); ?>" class="widefat"
			       style="width:100%;" type="text" value="<?php echo $instance['dynPlayerExcludePosts'] ?>"
			       placeholder="leave empty to show in all posts">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerIncludePosts' ); ?>">
				Include this posts (type a list of post ids (or names) separated by commas to include, type "all" to
				include all posts )
			</label>
			<input id="<?php echo $this->get_field_id( 'dynPlayerIncludePosts' ); ?>"
			       name="<?php echo $this->get_field_name( 'dynPlayerIncludePosts' ); ?>" class="widefat"
			       style="width:100%;" type="text" value="<?php echo $instance['dynPlayerIncludePosts'] ?>"
			       placeholder="leave empty to include all posts">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dynPlayerShowShopPage' ); ?>">
				Show in shop page (only for Woocommerce plugin)
			</label>
			<select id="<?php echo $this->get_field_id( 'dynPlayerShowShopPage' ); ?>"
			        name="<?php echo $this->get_field_name( 'dynPlayerShowShopPage' ); ?>" class="widefat"
			        style="width:100%;">
				<option <?php selected( $instance['dynPlayerShowShopPage'], 'true' ); ?> value="true">true</option>
				<option <?php selected( $instance['dynPlayerShowShopPage'], 'false' ); ?> value="false">false</option>
			</select>
		</p>

		<?php
	}
}

/************************************************************/

function dynamic_player_register_settings() {
	for ($i = 1; $i <= 40; $i++) {
		register_setting( 'dynamic_player_settings-group', 'dynamic_title_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_artist_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_album_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_date_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_ogg_file_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_mp3_file_' . $i );
		register_setting( 'dynamic_player_settings-group', 'dynamic_image_file_' . $i );
	}
}

function dynamic_player_settings() {

	add_menu_page( 'Dynamic Player', 'Dynamic Player', 'administrator', 'dynamic_player_register_settings', 'dynamic_player_control_panel', plugins_url( '/images/dynamic-icon.png', __FILE__ ) );

}


function dynamic_player_control_panel() {
	?>
	<p><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/dynamic-audio-player-basic#postform">
			<h3>If you like this plugin please take the time to rate it HERE</h3></a></p>
	<h4>If you are looking for a more advanced version of this plugin with more features <a target="_blank"
	                                                                                        href="http://dynamicaudioplayer.com/contact/">contact
			me</a>.</h4>

	<hr>

	<div class="dynamic-title"><img src="<?php echo plugins_url( '/images/dynamic-icon-large.png', __FILE__ ); ?>"
	                                width="30"/>&nbsp;Dynamic Audio Player Default Playlist
	</div>
	<div class="dynamic-container">
		<div class="dynamic-options-container">


			<form class="dynamic-options" method="post" action="options.php">
				<?php settings_fields( 'dynamic_player_settings-group' ); ?>
				<?php do_settings_sections( 'dynamic_player_settings-group' ); ?>

				<div class="dynamic-options-row">
					<?php for ($i = 1; $i <= 40; $i++) { ?>
						<h3>Default track <?php echo $i ?></h3>
						<div id=".dynamic-title">
							<label for="upload_title">
								<input class="upload_title" type="text" size="36" name="dynamic_title_<?php echo $i ?>"
								       value="<?php echo get_option( 'dynamic_title_' . $i ) ? strip_tags( get_option( 'dynamic_title_' . $i ) ) : "Unknown Title"; ?>"/>
							</label>Enter a title
						</div>
						<div id=".dynamic-artist">
							<label for="upload_artist">
								<input class="upload_artist" type="text" size="36" name="dynamic_artist_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_artist_' . $i ) ); ?>"/>
							</label>Enter an artist
						</div>
						<div id=".dynamic-album">
							<label for="upload_album">
								<input class="upload_album" type="text" size="36" name="dynamic_album_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_album_' . $i ) ); ?>"/>
							</label>Enter an album
						</div>
						<div id=".dynamic-date">
							<label for="upload_date">
								<input class="upload_date" type="text" size="36" name="dynamic_date_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_date_' . $i ) ); ?>"/>
							</label>Enter a date
						</div>
						<div id=".dynamic-ogg">
							<label for="upload_ogg_file">
								<input class="upload_ogg_file" type="url" size="36" name="dynamic_ogg_file_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_ogg_file_' . $i ) ); ?>"/>
								<input class="upload_ogg_file_button button" type="button" value="Upload .ogg Audio File"/>
							</label>Enter a URL or upload an .ogg audio file
						</div>
						<div id=".dynamic-mp3">
							<label for="upload_mp3_file">
								<input class="upload_mp3_file" type="url" size="36" name="dynamic_mp3_file_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_mp3_file_' . $i ) ); ?>"/>
								<input class="upload_mp3_file_button button" type="button" value="Upload .mp3 Audio File"/>
							</label>Enter a URL or upload an .mp3 audio file
						</div>
						<div id=".dynamic-image">
							<label for="upload_image_file">
								<input class="upload_image_file" type="url" size="36" name="dynamic_image_file_<?php echo $i ?>"
								       value="<?php echo strip_tags( get_option( 'dynamic_image_file_' . $i ) ); ?>"/>
								<input class="upload_image_file_button button" type="button" value="Upload Image File"/>
							</label>Enter a URL or upload an image file (88x88px to 116x116px)
						</div>

					<?php } ?>

					<div style="clear:both;"></div>
				</div>


				<?php submit_button(); ?>

			</form>
		</div>
		<div style='clear:both;'></div>
	</div>

	<?php
}

add_action( 'admin_init', 'dynamic_player_register_settings' );
add_action( 'admin_menu', 'dynamic_player_settings' );






