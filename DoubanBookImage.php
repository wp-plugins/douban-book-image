<?php
/*
    Plugin Name: Douban Book Image
    Plugin URI: http://nssa.cn/2011/01/douban-book-image/
    Description: Display a Douban Book Image widget using an image from (and linking to) the Douban Books
    Author: minir
    Version: 1.0
    Author URI: http://nssa.cn/
 */

//
//  DoubanBookImage Class
//
class DoubanBookImage extends WP_Widget {
    /** constructor */
    function DoubanBookImage() {
        $widget_ops = array( 'classname' => 'widget_reading', 'description' => __( 'Display a Douban Book Image widget using an image from (and linking to) the Douban Book Website' ), 'internalcss' => true );
        $this->WP_Widget( 'reading', __( 'Douban Book Image', 'reading_widget' ), $widget_ops );
        $this->widget_defaults = array(
            'internalcss' => true,
            'boxshadow' => true,
        );
    }

    //
    //  @see WP_Widget::widget
    //
    function widget($args, $instance) {		
        $args = wp_parse_args( $args, $this->widget_defaults );
        extract( $args );
		//$widget_options = wp_parse_args( $instance, $this->widget_defaults );

        $title = apply_filters('widget_title', $instance['title']);
        $internalcss = $instance["internalcss"] ? true : false;
        $boxshadow = $instance["boxshadow"] ? true : false;

        if ($instance['isbn'] != "") {      

            echo $before_widget;

            if ( $title )
                echo $before_title . $title . $after_title; 

            $spacechars = array(' ', '-', '_');
            $myisbn = str_replace($spacechars, "", $instance['isbn']);
			
            // Douban API
			$api_key = '0d98ccc7b84b89e528ca665728e29daf';
			$book_isbn = $myisbn;
			$image_url_bf = "http://img3.douban.com/";
			$image_type = "mpic";
            $url = 'http://api.douban.com/book/subject/isbn/' . $book_isbn . '?alt=json';
            if (!empty($api_key)){
                $url .= '&apikey=' . $api_key;
            }
            $douban_book = json_decode(file_get_contents($url), true);
			$image_url_get = "" . $douban_book['link'][2]['@href'] . "";
			$image_id = substr($image_url_get,-12,12);
			$image_url = $image_url_bf.$image_type."/".$image_id;
			$book_url = "" . $douban_book['link'][1]['@href'] . "";
			$book_title = "" . $douban_book['title']['$t'] . "";

            print("\n\t<!-- ISBN: " . $instance['isbn'] . " -->\n");
            if ( $internalcss || $boxshadow ) {
                print("\t\t<ul style='margin: 1em;");
                if ( $internalcss ) {
                    print(" list-style: none;");
                }
                print("'>\n");
            } else {
                print("\t\t<ul>\n");
            }
            print( "\t\t\t<li>\n");
            print( "\t\t\t\t<a href='" . $book_url . "'>");
            print( "<img " );
            if ( $boxshadow ) {
                print("style='-moz-box-shadow: #CCC 5px 5px 5px; -webkit-box-shadow: #CCC 5px 5px 5px; -khtml-box-shadow: #CCC 5px 5px 5px; box-shadow: #CCC 5px 5px 5px;' ");
            }
            print( "src='" . $image_url . "' ");
            print( "alt='" . $book_title . "' title='" . $book_title . "'/></a>\n");
            print( "\t\t\t</li>\n\t\t</ul>\n");

            echo $after_widget;
        }
    }

    //
    //  @see WP_Widget::update
    //
    function update($new_instance, $old_instance) {				
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['isbn'] = strip_tags( $new_instance['isbn'] );
        $instance['internalcss'] = $new_instance['internalcss'] ? 1 : 0;
        $instance['boxshadow'] = $new_instance['boxshadow'] ? 1 : 0;
        return $instance;
    }

    //
    //  @see WP_Widget::form
    //
    function form( $instance ) {
        $instance = wp_parse_args( $instance, $this->widget_defaults );
        extract( $instance );

        $title = esc_attr( $instance['title'] );
        $isbn = esc_attr( $instance['isbn'] );
        $internalcss = $instance['internalcss'] ? "checked='checked'" : "";
        $boxshadow = $instance['boxshadow'] ? "checked='checked'" : "";

        print( "\t<p>\n\t\t<label for='" . $this->get_field_id("title") . "'>" ); _e( "Title:" ); 
        print( "\n\t\t\t<input class='widefat' id='" . $this->get_field_id('title') . "' name='" );
        print( $this->get_field_name('title') . "' type='text' value='" . $title );
        print( "' />\n\t\t</label>\n\t\t<em>Leave blank for no title</em>\n\t</p>\n" );

        print( "\t<p>\n\t\t<label for='" . $this->get_field_id("isbn") . "'>" ); _e( "ISBN:" );
        print( "\n\t\t\t<input class='widefat' id='" . $this->get_field_id("isbn") . "' name='" );
        print( $this->get_field_name("isbn") . "' type='text' value='" . $isbn . "' />\n\t\t</label>\n\t</p>\n" );

        print( "\t<p>\n" );
        print( "\t\t<input class='checkbox' type='checkbox' " . $internalcss );
        print( " id='" . $this->get_field_id("internalcss") . "' name='" . $this->get_field_name("internalcss") . "'/>\n" );
        print( "\t\t<label for='" . $this->get_field_id("internalcss") . "'>" ); _e( "Suppress List Marker" );
        print( "\n\t\t<br />\n" );
        print( "\t\t<input class='checkbox' type='checkbox' " . $boxshadow );
        print( " id='" . $this->get_field_id("boxshadow") . "' name='" . $this->get_field_name("boxshadow") . "'/>\n" );
        print( "\t\t<label for='" . $this->get_field_id("boxshadow") . "'>" ); _e( "Display a Box-Shadow" );
        print( "</label>\n\t</p>\n" );

    }
}

//
//  register DoubanBookImage widget
//
add_action('widgets_init', create_function('', 'return register_widget( "DoubanBookImage" );'));

