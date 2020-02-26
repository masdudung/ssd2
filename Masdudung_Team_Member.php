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

    function __construct()
    {
        add_action(
            'post_edit_form_tag',
            function() {
                echo ' enctype="multipart/form-data"';
            } 
        );
    }


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

    function native_metabox()
    {
        add_meta_box(
            'untitled',               // Unique ID
            'Native Metabox',         // Box title
            [$this, 'native_metabox_view'],  // Content callback, must be of type callable
            'team_member'             // Post type
        );
        
    }

    function native_metabox_view($post)
    {
        $position = get_post_meta($post->ID, "prefix-position", true);
        $email = get_post_meta($post->ID, "prefix-email", true);
        $phone = get_post_meta($post->ID, "prefix-phone", true);
        $website = get_post_meta($post->ID, "prefix-website", true);
        $image = get_post_meta($post->ID, "prefix-image", true);

?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <p>Position</p>
                    </td>
                    <td>
                        <input type="text" name="prefix-position" value="<?php echo $position; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Email</p>
                    </td>
                    <td>
                        <input type="text" name="prefix-email" value="<?php echo $email; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>phone</p>
                    </td>
                    <td>
                        <input type="text" name="prefix-phone" value="<?php echo $phone; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>website</p>
                    </td>
                    <td>
                        <input type="text" name="prefix-website" value="<?php echo $website; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Image</p>
                    </td>
                    <td>
                        <?php 
                        if($image)
                        {
                            $url = wp_get_attachment_url( $image );
                            echo "<img src='$url' width='150px'><br>";
                        }
                        ?>
						<input name="prefix-image" type="file" required multiple="false" accept="image/x-png,image/gif,image/jpeg" />
                    </td>
                </tr>
            </tbody>
        </table>
<?php
    }

    function native_metabox_save($post_id)
    {
        
        // save textbox
        $fields = array(
            'prefix-position', 'prefix-email', 'prefix-phone', 'prefix-website'
        );

        foreach ($fields as $field) {
            # code...
            if (array_key_exists($field, $_POST)) {
                update_post_meta(
                    $post_id,
                    $field,
                    $_POST[$field]
                );
            }
        }

        // for image 
        if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$image = $_FILES['prefix-image'];
		$upload_overrides = array( 'test_form' => false );
        $image_meta_data = wp_handle_upload($image, $upload_overrides);
        $attachment = array('guid' => $image_meta_data['url'], 'post_mime_type' => $image_meta_data['type'], 'post_title' => preg_replace('/\\.[^.]+$/', '', basename($file['name'])), 'post_content' => '', 'post_status' => 'inherit');
        $id = wp_insert_attachment($attachment, $image_meta_data['file'], $post_id);

        //save image metadata
        update_post_meta(
            $post_id,
            'prefix-image',
            $id
        );

    }

    function get_member($atts)
    {	
        $attributes = shortcode_atts( 
                array(
                    'title' => "true",
                    'position' => "true",
                    'email' => "true",
                    'phone' => "true",
                    'website' => "true",
                    'image' => "true",
                ), $atts
        );
		
        $list_member = $this->_get_member();
        
        foreach ($list_member as $member) {
            # code...
            foreach ($attributes as $key => $attribute) {
                # code...
                if($attribute=="true")
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

        $prefix = $this->prefix;
        $data_post = array();

        $args = array(
            'post_type' => 'team_member',
            'posts_per_page' => 3
        );

        
        $obituary_query = new WP_Query($args);

        $index = 0;
        while ($obituary_query->have_posts()) : $obituary_query->the_post();
            $post_id = get_the_ID();
            $temp_data_post = array();

            $temp_data_post['title']      = get_the_title();
            $temp_data_post['position']   = get_post_meta($post_id, $prefix. 'position', true); // Use myinfo-box1, myinfo-box2, myinfo-box3 for respective fields 
            $temp_data_post['email']      = get_post_meta($post_id, $prefix. 'email', true);
            $temp_data_post['phone']      = get_post_meta($post_id, $prefix. 'phone', true);
            $temp_data_post['website']      = get_post_meta($post_id, $prefix. 'website', true);
            $image_id                     = get_post_meta($post_id, $prefix. 'image', true);
			$temp_data_post['image']      = wp_get_attachment_url( $image_id );

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
add_shortcode('all_members', [$lala, 'get_member']);
// add_filter( 'rwmb_meta_boxes', [$lala, 'metabox'] ); // call metabox.io
add_action('add_meta_boxes', [$lala, 'native_metabox']); // call native metabox
add_action('save_post', [$lala, 'native_metabox_save']);

?>
