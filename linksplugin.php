<?php

/**

 * Plugin Name: Linksrocker 

 * Description: Customize links via Menu for each page- Provides customized menu on page (better alternate to call links) :) .

 * Version: 3.3

 * Author: t-S-l

 */
 /*  Copyright 2013  Trishul Goel  (email : trishul.goel@gmail.com)

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


/*add custom fields*/
function add_desc_box() {

    $screens = array( 'page');

    foreach ( $screens as $screen ) {

        add_meta_box(
            'tsl_plugins_id',
            __( 'Select Custom Menu Links', 'tsl_plugins' ),
            'description_box',
            $screen,'normal','high'
        );
    }
}
add_action( 'add_meta_boxes', 'add_desc_box' );

/**
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function description_box( $post ) {
  wp_nonce_field( 'description_box', 'description_box_nonce' );
  $value= get_post_meta(get_the_ID(),'tsl_menu_choice',true);
  $menus = get_terms('nav_menu');
  ?>
  <div>
  	<select name="custom_menu_choice">
  	<option value="all">Select Menu</option>	
<?php foreach($menus as $menu){?>

<option value="<?php echo $menu->name;?>" <?php if($value==$menu->name) echo 'selected'; ?>><?php echo $menu->name;?></option>
<?php } ?></select>
<br/>
To Add custom menu in the page kindly use following shortcode:<br/>
[add_paged_menu]

  <?php  }

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function page_desc_save_postdata( $post_id ) {
  if ( ! isset( $_POST['description_box_nonce'] ) )
    return $post_id;

  $nonce = $_POST['description_box_nonce'];
  if ( ! wp_verify_nonce( $nonce, 'description_box' ) )
      return $post_id;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  if ( 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  
  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }
  $mydata = sanitize_text_field( $_POST['tsl_menu_choice'] );
  update_post_meta( $post_id, 'tsl_menu_choice', $mydata );
}
add_action( 'save_post', 'page_desc_save_postdata' );

/*shortcode for generating menu*/
function tsl_shortcode()
{
$custmenu= get_post_meta(get_the_ID(),'custom_menu_choice',true);
if($custmenu!="all")
{wp_nav_menu( array('menu' => $custmenu ));} 
}
add_shortcode('add_paged_menu', 'tsl_shortcode');
