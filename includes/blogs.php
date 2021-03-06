<?php
/**
 * Blogs component.
 *
 * @package HC_Provisional_Content
 */

/**
 * Filter blog options before save to enforce visibility depending on user status.
 *
 * @uses hcpc_is_user_vetted()
 * @param BP_Blogs_Blog $blog Blog.
 * @return BP_Blogs_Blog
 */
function hcpc_filter_bp_blogs_blog_before_save( BP_Blogs_Blog $blog ) {
	$required_blog_public_option_value = -3; // Required value of blog_public option for non-vetted users (assumes MPO).
	$blog_details = get_blog_details( $blog->blog_id );

	$vetted_user = hcpc_is_user_vetted();

	if ( ! $vetted_user && $blog_details->public !== $required_blog_public_option_value ) {
		update_blog_details(
			$blog_details->blog_id, [
				'public' => $required_blog_public_option_value,
			]
		);
	}

	return $blog;
}
add_action( 'bp_blogs_blog_before_save', 'hcpc_filter_bp_blogs_blog_before_save' );

/**
 * Disable relevant visibility radio buttons and add explanatory notice to label using inline js.
 * This only works if hooked to actions before wp_head.
 */
function hcpc_preset_blog_settings() {
	echo '<script>' . file_get_contents( plugin_dir_path( __DIR__ ) . 'js/hcpc-blog-settings.js' ) . '</script>';
}
add_action( 'bp_after_create_group', 'hcpc_preset_blog_settings' ); // Site creation during group creation.
add_action( 'bp_after_create_blog_content', 'hcpc_preset_blog_settings' ); // Direct frontend site creation.
add_action( 'admin_head-options-reading.php', 'hcpc_preset_blog_settings' ); // Admin site creation.
