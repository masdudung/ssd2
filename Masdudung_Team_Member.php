<?php

/*
    Plugin Name: Masdudung Team Members
    Plugin URI: http://jadipesan.com/
    Description: Declares a plugin that will create a custom post type displaying Team Members.
    Version: 1.0
    Author: Moch Mufiddin
    Author URI: http://jadipesan.com/
    License: GPLv2
*/

// Our custom post type function
class Masdudung_Team_Member{

	private $prefix = "prefix-";
	
    private $labels = array(
        'name'               => 'Team Member',
        'singular_name'      => 'Team Member',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Team Member',
        'edit_item'          => 'Edit Team Member',
        'new_item'           => 'New Team Member',
        'all_items'          => 'All Team Members',
        'view_item'          => 'View Product',
        'search_items'       => 'Search Team Members',
        'not_found'          => 'No Team Members found',
        'not_found_in_trash' => 'No Team Members found in the Trash', 
        'parent_item_colon'  => ’,
        'menu_name'          => 'Team Members'
    );
    
    private $args = array(
        'description'   => 'Holds our member specific data',
        'public'        => true,
        'menu_position' => 5,
        'supports'      => array( 'title' ),
        'has_archive'   => true,
    );


    function custom_team_member() {
        $this->args['labels'] = $this->labels;
        register_post_type( 'team_member', $this->args ); 
    }

    function metabox( $meta_boxes ) {
        $prefix = $this->prefix;
    
        $meta_boxes[] = array(
            'id' => 'untitled',
            'title' => esc_html__( 'Untitled Metabox', 'metabox-online-generator' ),
            'post_types' => array('team_member' ), //'post', 'page'
            'context' => 'advanced',
            'priority' => 'default',
            'autosave' => 'false',
            'fields' => array(
                array(
                    'id' => $prefix . 'position',
                    'type' => 'text',
                    'name' => esc_html__( 'position', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'email',
                    'type' => 'text',
                    'name' => esc_html__( 'email', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'phone',
                    'type' => 'text',
                    'name' => esc_html__( 'phone', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'website',
                    'type' => 'text',
                    'name' => esc_html__( 'website', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'image',
                    'type' => 'image_advanced',
                    'name' => esc_html__( 'image', 'metabox-online-generator' ),
                ),
            ),
        );
    
        return $meta_boxes;
    }

    function get_member($atts)
    {	
        $attributes = shortcode_atts( 
                array(
                    'title' => true,
                    'position' => true,
                    'email' => true,
                    'phone' => true,
                    'image' => true,
                ), $atts
        );
		
        $list_member = $this->_get_member();
        foreach ($list_member as $member) {
            # code...
            foreach ($attributes as $key => $attribute) {
                # code...
                if($attribute===true)
                {
                    echo $member[$key];
                    echo "<br>";
                }
            }
            echo "<br>";
        }
    }

    private function _get_member()
    {
        global $post;

        $prefix = $this->prefix;
        $data_post = array();

        $args = array(
            'post_type' => 'team_member',
            'posts_per_page' => 3
        );

        
        $obituary_query = new WP_Query($args);

        $index = 0;
        while ($obituary_query->have_posts()) : $obituary_query->the_post();
            $temp_data_post = array();

            $temp_data_post['title']      = get_the_title();
            $temp_data_post['position']   = get_post_meta($post->ID, $prefix. 'position', true); // Use myinfo-box1, myinfo-box2, myinfo-box3 for respective fields 
            $temp_data_post['email']      = get_post_meta($post->ID, $prefix. 'email', true);
            $temp_data_post['phone']      = get_post_meta($post->ID, $prefix. 'phone', true);
            $image_id                        = get_post_meta($post->ID, $prefix. 'image', true);
			$images                          = rwmb_meta( $prefix. 'image', array( 'size' => 'thumbnail' ) );
            $temp_data_post['image']      = $images[$image_id]['url'];

            $data_post[$index] = $temp_data_post; 

            $index++;
        endwhile;

        // Reset Post Data
        wp_reset_postdata();
        
        return $data_post;
    }
}


$lala = new Masdudung_Team_Member();
add_action( 'init', [$lala, 'custom_team_member'] );
add_filter( 'rwmb_meta_boxes', [$lala, 'metabox'] );
add_shortcode('all_members', [$lala, 'get_member']);


?>