<?php
/**
 * Plugin Name:       Project Portfolio Grid
 * Plugin URI:        https://github.com/72hourwebsites/project-portfolio-grid
 * Description:       A small portfolio custom post type with an accessible shortcode grid.
 * Version:           1.1.1
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Angel Lopez
 * Author URI:        https://github.com/72hourwebsites
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       project-portfolio-grid
 *
 * @package HW72_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HW72_SHOWCASE_VERSION', '1.1.1' );
define( 'HW72_SHOWCASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the project post type.
 */
function hw72_showcase_register_post_type() {
	$labels = array(
		'name'               => _x( 'Showcase Items', 'post type general name', 'project-portfolio-grid' ),
		'singular_name'      => _x( 'Showcase Item', 'post type singular name', 'project-portfolio-grid' ),
		'add_new_item'       => __( 'Add New Showcase Item', 'project-portfolio-grid' ),
		'edit_item'          => __( 'Edit Showcase Item', 'project-portfolio-grid' ),
		'new_item'           => __( 'New Showcase Item', 'project-portfolio-grid' ),
		'view_item'          => __( 'View Showcase Item', 'project-portfolio-grid' ),
		'search_items'       => __( 'Search Showcase Items', 'project-portfolio-grid' ),
		'not_found'          => __( 'No showcase items found.', 'project-portfolio-grid' ),
		'not_found_in_trash' => __( 'No showcase items found in Trash.', 'project-portfolio-grid' ),
		'menu_name'          => _x( 'Showcase', 'admin menu', 'project-portfolio-grid' ),
	);

	register_post_type(
		'hw72_project',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-portfolio',
			'rewrite'       => array( 'slug' => 'showcase' ),
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
		)
	);
}
add_action( 'init', 'hw72_showcase_register_post_type' );

/**
 * Allow project meta only for users who can edit that project.
 *
 * @param bool   $allowed  Whether the user can edit the meta.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function hw72_showcase_can_edit_meta( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Register public-safe project fields.
 */
function hw72_showcase_register_meta() {
	register_post_meta(
		'hw72_project',
		'_hw72_project_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => 'hw72_showcase_can_edit_meta',
		)
	);

	register_post_meta(
		'hw72_project',
		'_hw72_stack',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => 'hw72_showcase_can_edit_meta',
		)
	);
}
add_action( 'init', 'hw72_showcase_register_meta' );

/**
 * Register front-end assets without loading them on every page.
 */
function hw72_showcase_register_assets() {
	wp_register_style(
		'hw72-showcase',
		HW72_SHOWCASE_URL . 'assets/showcase.css',
		array(),
		HW72_SHOWCASE_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'hw72_showcase_register_assets' );

/**
 * Add the project details box.
 */
function hw72_showcase_add_meta_box() {
	add_meta_box(
		'hw72-showcase-details',
		__( 'Project details', 'project-portfolio-grid' ),
		'hw72_showcase_render_meta_box',
		'hw72_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_hw72_project', 'hw72_showcase_add_meta_box' );

/**
 * Render the project details box.
 *
 * @param WP_Post $post Current post.
 */
function hw72_showcase_render_meta_box( $post ) {
	wp_nonce_field( 'hw72_showcase_save_meta', 'hw72_showcase_nonce' );
	$project_url = get_post_meta( $post->ID, '_hw72_project_url', true );
	$stack       = get_post_meta( $post->ID, '_hw72_stack', true );
	?>
	<p>
		<label for="hw72-project-url"><strong><?php esc_html_e( 'Project URL', 'project-portfolio-grid' ); ?></strong></label>
		<input type="url" class="widefat" id="hw72-project-url" name="hw72_project_url" value="<?php echo esc_url( $project_url ); ?>" placeholder="https://example.com/">
	</p>
	<p>
		<label for="hw72-stack"><strong><?php esc_html_e( 'Stack or services', 'project-portfolio-grid' ); ?></strong></label>
		<input type="text" class="widefat" id="hw72-stack" name="hw72_stack" value="<?php echo esc_attr( $stack ); ?>" maxlength="160">
		<span class="description"><?php esc_html_e( 'For example: WordPress, PHP, WooCommerce, REST API.', 'project-portfolio-grid' ); ?></span>
	</p>
	<?php
}

/**
 * Save project details after validating the request.
 *
 * @param int $post_id Post ID.
 */
function hw72_showcase_save_meta( $post_id ) {
	if ( ! isset( $_POST['hw72_showcase_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['hw72_showcase_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'hw72_showcase_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$project_url = isset( $_POST['hw72_project_url'] ) ? esc_url_raw( wp_unslash( $_POST['hw72_project_url'] ) ) : '';
	$stack       = isset( $_POST['hw72_stack'] ) ? sanitize_text_field( wp_unslash( $_POST['hw72_stack'] ) ) : '';

	update_post_meta( $post_id, '_hw72_project_url', $project_url );
	update_post_meta( $post_id, '_hw72_stack', $stack );
}
add_action( 'save_post_hw72_project', 'hw72_showcase_save_meta' );

/**
 * Render a grid of published showcase items.
 *
 * @param array|string $atts Shortcode attributes.
 * @return string
 */
function hw72_showcase_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'limit' => 6 ), $atts, 'hw72_showcase' );
	$limit = min( 48, max( 1, absint( $atts['limit'] ) ) );

	wp_enqueue_style( 'hw72-showcase' );

	$query = new WP_Query(
		array(
			'post_type'           => 'hw72_project',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	ob_start();
	if ( $query->have_posts() ) {
		?>
		<div class="hw72-showcase">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$project_url = get_post_meta( get_the_ID(), '_hw72_project_url', true );
				$stack       = get_post_meta( get_the_ID(), '_hw72_stack', true );
				$link        = $project_url ? $project_url : get_permalink();
				?>
				<article <?php post_class( 'hw72-showcase__item' ); ?>>
					<?php if ( has_post_thumbnail() ) { ?>
						<div class="hw72-showcase__image"><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></div>
					<?php } ?>
					<div class="hw72-showcase__body">
						<h3 class="hw72-showcase__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
						<?php if ( $stack ) { ?>
							<p class="hw72-showcase__stack"><?php echo esc_html( $stack ); ?></p>
						<?php } ?>
						<div class="hw72-showcase__excerpt"><?php the_excerpt(); ?></div>
					</div>
				</article>
				<?php
			}
			?>
		</div>
		<?php
	} else {
		?>
		<p class="hw72-showcase__empty"><?php esc_html_e( 'Showcase items are being prepared.', 'project-portfolio-grid' ); ?></p>
		<?php
	}
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'hw72_showcase', 'hw72_showcase_shortcode' );

/**
 * Register rewrite rules on activation.
 */
function hw72_showcase_activate() {
	hw72_showcase_register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'hw72_showcase_activate' );

/**
 * Flush project rewrite rules on deactivation without deleting content.
 */
function hw72_showcase_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hw72_showcase_deactivate' );
