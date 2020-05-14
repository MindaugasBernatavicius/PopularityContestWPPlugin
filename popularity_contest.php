<?php
    /*
        Plugin Name: Popularity Contest
        Plugin URI: http://popcont.com
        description: A plugin that records post views and contains functions to easily list posts by popularity
        Version: 1.0
        Author: Mindaugas B.
        License: GPL2
    */


    /**
     * Adds a view to the post being viewed
     *
     * Finds the current views of a post and adds one to it by updating
     * the postmeta. The meta key used is "awepop_views".
     *
     * @global object $post The post object
     * @return integer $new_views The number of views the post has
     */
    function awepop_add_view() {
        if(is_single()) {
            global $post;
            $current_views = get_post_meta($post->ID, "awepop_views", true);
            if(!isset($current_views) OR empty($current_views) OR !is_numeric($current_views)){
                $current_views = 0;
            }
            $new_views = $current_views + 1;
            update_post_meta($post->ID, "awepop_views", $new_views);
            return $new_views;
        }
    }

    add_action("wp_head", "awepop_add_view");


    /**
     * Retrieve the number of views for a post
     *
     * Finds the current views for a post, returning 0 if there are none
     *
     * @global object $post The post object
     * @return integer $current_views The number of views the post has
     *
     */
    function awepop_get_view_count() {
        global $post;
        $current_views = get_post_meta($post->ID, "awepop_views", true);
        if(!isset($current_views) 
            OR empty($current_views) 
            OR !is_numeric($current_views) ) {
            $current_views = 0;
        }
        return $current_views;
    }

    /**
     * Shows the number of views for a post
     *
     * Finds the current views of a post and displays it together with some optional text
     *
     * @global object $post The post object
     * @uses awepop_get_view_count()
     *
     * @param string $singular The singular term for the text
     * @param string $plural The plural term for the text
     * @param string $before Text to place before the counter
     *
     * @return string $views_text The views display
     *
     */
    function awepop_show_views($content) {
        if(is_single()){
            $current_views = awepop_get_view_count();
            $views_text = "<h5>This post has: " . $current_views . " ";
            if ($current_views == 1) $views_text .= "view</h5>";
            else $views_text .= "views</h5>";
            echo $content . $views_text;
        } else {
            echo $content;
        }
    }
    
    if(get_option('pc_plugin_setting_disp_location')['rad'] == 'footer'){
        add_filter('the_content', 'awepop_show_views');
    } else {
        add_action('wp_head', 'awepop_show_views');
    }
    
    // Admin area 
    function test_plugin_setup_menu(){
        add_menu_page(
            'Popularity Contest Admin Page',    // $page_title
            'Popularity Contest',               // $menu_title
            'administrator',                    // users capabilities who can see this
            'pc-plugin',                        // slug : A WordPress slug is a text which comes after your domain name as a part of your permalink that is leading to your content.
            'pc_init'                           // callback that will be called
        );
    }
    add_action('admin_menu', 'test_plugin_setup_menu');
 
    function pc_init(){
        ?>
        <form action="options.php" method="post">
            <?php 
                settings_fields('pc_plugin_options');
                do_settings_sections('pc_plugin'); 
            ?>
            <input name="submit" 
                class="button button-primary" 
                type="submit" 
                value="Save"
            />
        </form>
        <?php
    }

    function pc_register_settings() {
        register_setting('pc_plugin_options', 'pc_plugin_setting_disp_location', null);
        add_settings_section('pc_settings', 'Popularity Contest Settings', null, 'pc_plugin');
        add_settings_field('pc_plugin_setting_disp_location', 'Where should the counter be displayed: ', 'pc_plugin_setting_disp_location', 'pc_plugin', 'pc_settings');
    }
    add_action('admin_init', 'pc_register_settings');

    function pc_plugin_setting_disp_location() {
        $option = get_option('pc_plugin_setting_disp_location')['rad'];
        if($option === "" or $option === "header"){
            echo "<input type='radio' name='pc_plugin_setting_disp_location[rad]' value='header' checked='checked'><label for='header_choice'>Header</label><br>";
            echo "<input type='radio' name='pc_plugin_setting_disp_location[rad]' value='footer'><label for='footer_choice'>Footer</label><br>";
        } else {
            echo "<input type='radio' name='pc_plugin_setting_disp_location[rad]' value='header'><label for='header_choice'>Header</label><br>";
            echo "<input type='radio' name='pc_plugin_setting_disp_location[rad]' value='footer' checked='checked'><label for='footer_choice'>Footer</label><br>";
        }

    }
?>