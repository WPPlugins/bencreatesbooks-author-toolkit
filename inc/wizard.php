<?php
/**
 * Author Toolkit setup wizard.
 *
 * Runs after theme activation (or from the dashboard): either imports
 * the demo content or walks the user through a guided setup, creating
 * real books, genres, pages, and menus along the way.
 *
 * @package BenCreatesBooksAuthorToolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Demo image URLs. Kept as a fallback; the importer now uses the copies
 * bundled in the theme's /demo/ folder first.
 *
 * @return array
 */
function bcbat_demo_images() {
	return array(
		'cover_romance' => 'https://www.bencreatesbooks.com/wp-content/uploads/2026/10/demo-cover-romance.webp',
		'cover_cozy'    => 'https://www.bencreatesbooks.com/wp-content/uploads/2026/10/demo-cover-cozy.webp',
		'cover_novel'   => 'https://www.bencreatesbooks.com/wp-content/uploads/2026/10/demo-cover-novel.webp',
		'cover_thriller' => 'https://www.bencreatesbooks.com/wp-content/uploads/2026/10/demo-cover-thriller.webp',
		'author'        => 'https://www.bencreatesbooks.com/wp-content/uploads/2026/10/demo-author-portrait.webp',
	);
}

/**
 * Render the wizard page.
 */
function bcbat_wizard_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<div class="bcbat-wizard">
			<div class="bcbat-wizard-progress">
				<span class="is-done"></span><span></span><span></span><span></span><span></span><span></span>
			</div>

			<!-- Step 0: choice -->
			<div class="bcbat-wizard-step is-active" data-step="0">
				<h2><?php esc_html_e( 'Welcome to AuthorPress', 'bencreatesbooks-author-toolkit' ); ?></h2>
				<p class="step-lead"><?php esc_html_e( 'Get your author website live in minutes. Import the ready-made demo, or build it step by step with your own content.', 'bencreatesbooks-author-toolkit' ); ?></p>
				<div class="bcbat-wizard-choices">
					<div class="bcbat-wizard-choice" id="bcbat-import-demo" role="button" tabindex="0">
						<span class="dashicons dashicons-download"></span>
						<h3><?php esc_html_e( 'Import Demo Content', 'bencreatesbooks-author-toolkit' ); ?></h3>
						<p><?php esc_html_e( 'Sample books, pages, and posts. The fastest way to see the theme in action.', 'bencreatesbooks-author-toolkit' ); ?></p>
					</div>
					<div class="bcbat-wizard-choice" data-goto="1" role="button" tabindex="0">
						<span class="dashicons dashicons-admin-tools"></span>
						<h3><?php esc_html_e( 'Guided Setup', 'bencreatesbooks-author-toolkit' ); ?></h3>
						<p><?php esc_html_e( 'Enter your site name, your books, and your bio. We build the pages for you.', 'bencreatesbooks-author-toolkit' ); ?></p>
					</div>
				</div>
				<div class="bcbat-wipe-warning">
					<p><strong><?php esc_html_e( 'Warning:', 'bencreatesbooks-author-toolkit' ); ?></strong> <?php esc_html_e( 'Importing the demo erases your current site. All pages, posts, books, events, media, menus, widgets, and AuthorPress settings will be permanently deleted and replaced with fresh demo content. This cannot be undone.', 'bencreatesbooks-author-toolkit' ); ?></p>
					<label class="bcbat-wipe-confirm"><input type="checkbox" id="bcbat-wipe-confirm"> <?php esc_html_e( 'I understand my current content will be permanently erased.', 'bencreatesbooks-author-toolkit' ); ?></label>
				</div>
			</div>

			<!-- Step 1: site identity -->
			<div class="bcbat-wizard-step" data-step="1">
				<h2><?php esc_html_e( 'Your site identity', 'bencreatesbooks-author-toolkit' ); ?></h2>
				<p class="step-lead"><?php esc_html_e( 'The basics. You can change all of this later in the dashboard.', 'bencreatesbooks-author-toolkit' ); ?></p>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="wz-blogname"><?php esc_html_e( 'Site title', 'bencreatesbooks-author-toolkit' ); ?></label></th>
						<td><input type="text" id="wz-blogname" class="regular-text" value="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" placeholder="<?php esc_attr_e( 'Elena Marsh', 'bencreatesbooks-author-toolkit' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="wz-blogdescription"><?php esc_html_e( 'Tagline', 'bencreatesbooks-author-toolkit' ); ?></label></th>
						<td><input type="text" id="wz-blogdescription" class="regular-text" value="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" placeholder="<?php esc_attr_e( 'Author & Storyteller', 'bencreatesbooks-author-toolkit' ); ?>"></td>
					</tr>
				</table>
				<div class="bcbat-wizard-nav">
					<button class="button" data-goto="0"><?php esc_html_e( 'Back', 'bencreatesbooks-author-toolkit' ); ?></button>
					<button class="button button-primary" data-goto="2"><?php esc_html_e( 'Continue', 'bencreatesbooks-author-toolkit' ); ?></button>
				</div>
			</div>

			<!-- Step 2: books -->
			<div class="bcbat-wizard-step" data-step="2">
				<h2><?php esc_html_e( 'Your books', 'bencreatesbooks-author-toolkit' ); ?></h2>
				<p class="step-lead"><?php esc_html_e( 'Add each book. Tick "Coming soon" instead of a price for unreleased titles.', 'bencreatesbooks-author-toolkit' ); ?></p>
				<div id="bcbat-books">
					<div class="bcbat-book-row">
						<h4><?php esc_html_e( 'Book', 'bencreatesbooks-author-toolkit' ); ?> <span class="book-num">1</span> <button class="button-link bcbat-remove-book" style="float:right;"><?php esc_html_e( 'Remove', 'bencreatesbooks-author-toolkit' ); ?></button></h4>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Cover', 'bencreatesbooks-author-toolkit' ); ?></th>
								<td>
									<div class="bcbat-media-field">
										<div class="bcbat-media-preview"></div>
										<input type="hidden" name="books[0][cover_id]" class="bcbat-media-id" value="">
										<button type="button" class="button bcbat-media-upload"><?php esc_html_e( 'Upload Cover', 'bencreatesbooks-author-toolkit' ); ?></button>
									</div>
								</td>
							</tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Title', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[0][title]" class="regular-text"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Subtitle', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[0][subtitle]" class="regular-text"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Genre', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[0][genre]" class="regular-text" placeholder="<?php esc_attr_e( 'Romance', 'bencreatesbooks-author-toolkit' ); ?>"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Price', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[0][price]" placeholder="$4.99"> <label><input type="checkbox" name="books[0][coming_soon]" value="1"> <?php esc_html_e( 'Coming soon', 'bencreatesbooks-author-toolkit' ); ?></label></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Buy link', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="url" name="books[0][buy_link]" class="regular-text" placeholder="https://"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Blurb', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><textarea name="books[0][blurb]" rows="4" class="large-text"></textarea></td></tr>
						</table>
					</div>
				</div>
				<!-- Hidden template for JS cloning -->
				<div id="bcbat-book-template" style="display:none;">
					<div class="bcbat-book-row">
						<h4><?php esc_html_e( 'Book', 'bencreatesbooks-author-toolkit' ); ?> <span class="book-num">2</span> <button class="button-link bcbat-remove-book" style="float:right;"><?php esc_html_e( 'Remove', 'bencreatesbooks-author-toolkit' ); ?></button></h4>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Cover', 'bencreatesbooks-author-toolkit' ); ?></th>
								<td>
									<div class="bcbat-media-field">
										<div class="bcbat-media-preview"></div>
										<input type="hidden" name="books[__i__][cover_id]" class="bcbat-media-id" value="">
										<button type="button" class="button bcbat-media-upload"><?php esc_html_e( 'Upload Cover', 'bencreatesbooks-author-toolkit' ); ?></button>
									</div>
								</td>
							</tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Title', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[__i__][title]" class="regular-text"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Subtitle', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[__i__][subtitle]" class="regular-text"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Genre', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[__i__][genre]" class="regular-text"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Price', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="text" name="books[__i__][price]" placeholder="$4.99"> <label><input type="checkbox" name="books[__i__][coming_soon]" value="1"> <?php esc_html_e( 'Coming soon', 'bencreatesbooks-author-toolkit' ); ?></label></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Buy link', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><input type="url" name="books[__i__][buy_link]" class="regular-text" placeholder="https://"></td></tr>
							<tr><th scope="row"><label><?php esc_html_e( 'Blurb', 'bencreatesbooks-author-toolkit' ); ?></label></th><td><textarea name="books[__i__][blurb]" rows="4" class="large-text"></textarea></td></tr>
						</table>
					</div>
				</div>
				<p><button class="button" id="bcbat-add-book"><?php esc_html_e( 'Add Another Book', 'bencreatesbooks-author-toolkit' ); ?></button></p>
				<div class="bcbat-wizard-nav">
					<button class="button" data-goto="1"><?php esc_html_e( 'Back', 'bencreatesbooks-author-toolkit' ); ?></button>
					<button class="button button-primary" data-goto="3"><?php esc_html_e( 'Continue', 'bencreatesbooks-author-toolkit' ); ?></button>
				</div>
			</div>

			<!-- Step 3: about -->
			<div class="bcbat-wizard-step" data-step="3">
				<h2><?php esc_html_e( 'About you', 'bencreatesbooks-author-toolkit' ); ?></h2>
				<p class="step-lead"><?php esc_html_e( 'Your photo and bio become your About page, and a summary appears on the homepage.', 'bencreatesbooks-author-toolkit' ); ?></p>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Photo', 'bencreatesbooks-author-toolkit' ); ?></th>
						<td>
							<div class="bcbat-media-field">
								<div class="bcbat-media-preview"></div>
								<input type="hidden" id="wz-about-photo" class="bcbat-media-id" value="">
								<button type="button" class="button bcbat-media-upload"><?php esc_html_e( 'Upload Photo', 'bencreatesbooks-author-toolkit' ); ?></button>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wz-about-name"><?php esc_html_e( 'Display name', 'bencreatesbooks-author-toolkit' ); ?></label></th>
						<td><input type="text" id="wz-about-name" class="regular-text" placeholder="<?php esc_attr_e( 'Elena Marsh', 'bencreatesbooks-author-toolkit' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="wz-about-bio"><?php esc_html_e( 'Bio', 'bencreatesbooks-author-toolkit' ); ?></label></th>
						<td><textarea id="wz-about-bio" rows="6" class="large-text" placeholder="<?php esc_attr_e( 'A few paragraphs about you, your books, and where readers can find you.', 'bencreatesbooks-author-toolkit' ); ?>"></textarea></td>
					</tr>
				</table>
				<div class="bcbat-wizard-nav">
					<button class="button" data-goto="2"><?php esc_html_e( 'Back', 'bencreatesbooks-author-toolkit' ); ?></button>
					<button class="button button-primary" data-goto="4"><?php esc_html_e( 'Continue', 'bencreatesbooks-author-toolkit' ); ?></button>
				</div>
			</div>

			<!-- Step 4: review -->
			<div class="bcbat-wizard-step" data-step="4">
				<h2><?php esc_html_e( 'Ready to build', 'bencreatesbooks-author-toolkit' ); ?></h2>
				<p class="step-lead"><?php esc_html_e( 'We will create your homepage, About page, books (with genres, prices, and buy links), blog page, and navigation menu.', 'bencreatesbooks-author-toolkit' ); ?></p>
				<div class="bcbat-wizard-nav" style="justify-content:flex-start; gap:12px;">
					<button class="button" data-goto="3"><?php esc_html_e( 'Back', 'bencreatesbooks-author-toolkit' ); ?></button>
					<button class="button button-primary button-hero" id="bcbat-finish-setup"><?php esc_html_e( 'Build My Website', 'bencreatesbooks-author-toolkit' ); ?></button>
				</div>
			</div>

			<!-- Step 5: done -->
			<div class="bcbat-wizard-step" data-step="5">
				<div class="bcbat-wizard-done">
					<span class="dashicons dashicons-yes-alt"></span>
					<h2><?php esc_html_e( 'Your website is ready', 'bencreatesbooks-author-toolkit' ); ?></h2>
					<p class="step-lead"><?php esc_html_e( 'Everything is in place. Fine-tune colors and fonts in the dashboard whenever you like.', 'bencreatesbooks-author-toolkit' ); ?></p>
					<p>
						<a class="button button-primary button-hero" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View Your Site', 'bencreatesbooks-author-toolkit' ); ?></a>
						<a class="button button-hero" href="<?php echo esc_url( admin_url( 'edit.php?post_type=bcbat_book' ) ); ?>"><?php esc_html_e( 'Manage Your Books', 'bencreatesbooks-author-toolkit' ); ?></a>
					</p>
				</div>
			</div>

		</div>
	</div>
	<?php
}

/**
 * Localize the wizard script.
 *
 * @param string $hook Current admin page hook.
 */
function bcbat_wizard_assets( $hook ) {
	if ( 'bcbat_book_page_bcbat-wizard' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'bencreatesbooks-author-toolkit-admin', plugin_dir_url( __FILE__ ) . '../assets/admin.css', array(), BCBAT_VERSION );
	wp_enqueue_script( 'bcbat-admin', plugin_dir_url( __FILE__ ) . '../assets/admin.js', array( 'jquery' ), BCBAT_VERSION, true );
	wp_localize_script(
		'bcbat-admin',
		'bcbatWizard',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bcbat_wizard' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'bcbat_wizard_assets', 20 );

/**
 * Create a page if it does not exist.
 *
 * @param string $slug    Page slug.
 * @param string $title   Page title.
 * @param string $content Page content.
 * @return int Page ID.
 */
function bcbat_maybe_create_page( $slug, $title, $content ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	return wp_insert_post(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
}

/**
 * Sideload an image from a URL and attach it to a post.
 *
 * Tries the theme's bundled /demo/ folder first so the import never
 * depends on the theme author's server, then falls back to the remote URL.
 *
 * @param string $url       Image URL (remote fallback).
 * @param int    $post_id   Attach to this post (0 for unattached).
 * @param string $fallback  Filename in the theme's /demo/ folder.
 * @return int Attachment ID, or 0 on failure.
 */
function bcbat_sideload_image( $url, $post_id = 0, $fallback = '' ) {
	if ( ! function_exists( 'media_sideload_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	// Primary: sideload from the theme's bundled /demo/ folder.
	if ( '' !== $fallback ) {
		$local = get_template_directory() . '/demo/' . $fallback;
		if ( file_exists( $local ) ) {
			$tmp = wp_tempnam( $fallback );
			if ( $tmp && copy( $local, $tmp ) ) {
				$file_array = array(
					'name'     => $fallback,
					'tmp_name' => $tmp,
				);
				$local_id   = media_handle_sideload( $file_array, $post_id );
				if ( ! is_wp_error( $local_id ) && $local_id ) {
					return absint( $local_id );
				}
				wp_delete_file( $tmp );
			}
		}
	}

	// Fallback: sideload from the remote URL.
	$attachment_id = media_sideload_image( $url, $post_id, null, 'id' );
	if ( ! is_wp_error( $attachment_id ) && $attachment_id ) {
		return absint( $attachment_id );
	}

	return 0;
}

/**
 * Create a book with its genre, meta, and cover.
 *
 * @param array $book Book data.
 * @return int Post ID.
 */
function bcbat_create_book( $book ) {
	$post_id = wp_insert_post(
		array(
			'post_title'   => sanitize_text_field( $book['title'] ),
			'post_content' => wp_kses_post( $book['blurb'] ),
			'post_excerpt' => sanitize_text_field( $book['blurb'] ),
			'post_status'  => 'publish',
			'post_type'    => 'bcbat_book',
		)
	);
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 0;
	}

	if ( ! empty( $book['genre'] ) ) {
		wp_set_object_terms( $post_id, sanitize_text_field( $book['genre'] ), 'bcbat_genre' );
	}
	if ( isset( $book['subtitle'] ) ) {
		update_post_meta( $post_id, '_bcbat_subtitle', sanitize_text_field( $book['subtitle'] ) );
	}
	if ( ! empty( $book['coming_soon'] ) ) {
		update_post_meta( $post_id, '_bcbat_price', __( 'Coming soon', 'bencreatesbooks-author-toolkit' ) );
	} elseif ( isset( $book['price'] ) ) {
		update_post_meta( $post_id, '_bcbat_price', sanitize_text_field( $book['price'] ) );
	}
	if ( isset( $book['buy_link'] ) ) {
		update_post_meta( $post_id, '_bcbat_buy_link', esc_url_raw( $book['buy_link'] ) );
	}
	if ( ! empty( $book['cover_id'] ) ) {
		set_post_thumbnail( $post_id, absint( $book['cover_id'] ) );
	} elseif ( ! empty( $book['cover_url'] ) ) {
		$fallback      = isset( $book['cover_fallback'] ) ? $book['cover_fallback'] : '';
		$attachment_id = bcbat_sideload_image( esc_url_raw( $book['cover_url'] ), $post_id, $fallback );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}
	bcbat_save_book_extras( $post_id, $book );
	return $post_id;
}

/**
 * Build the primary navigation menu.
 */
function bcbat_build_menu() {
	$menu_name = __( 'Primary', 'bencreatesbooks-author-toolkit' );
	$menu      = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
	} else {
		$menu_id = $menu->term_id;
	}

	$items = array(
		array( 'title' => __( 'Home', 'bencreatesbooks-author-toolkit' ), 'url' => home_url( '/' ) ),
		array( 'title' => __( 'Books', 'bencreatesbooks-author-toolkit' ), 'url' => get_post_type_archive_link( 'bcbat_book' ) ),
		array( 'title' => __( 'Events', 'bencreatesbooks-author-toolkit' ), 'url' => get_post_type_archive_link( 'bcbat_event' ) ),
	);
	foreach ( array( 'about', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$items[] = array( 'title' => $page->post_title, 'object_id' => $page->ID, 'object' => 'page', 'type' => 'post_type' );
		}
	}
	$blog_id = get_option( 'page_for_posts' );
	if ( $blog_id ) {
		$items[] = array( 'title' => get_the_title( $blog_id ), 'object_id' => $blog_id, 'object' => 'page', 'type' => 'post_type' );
	}

	foreach ( $items as $order => $item ) {
		$args = array(
			'menu-item-title'  => $item['title'],
			'menu-item-status' => 'publish',
			'menu-item-position' => $order + 1,
		);
		if ( isset( $item['object_id'] ) ) {
			$args['menu-item-object-id'] = $item['object_id'];
			$args['menu-item-object']    = $item['object'];
			$args['menu-item-type']      = $item['type'];
		} else {
			$args['menu-item-url']  = $item['url'];
			$args['menu-item-type'] = 'custom';
		}
		wp_update_nav_menu_item( $menu_id, 0, $args );
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary']   = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Shared finish: homepage, posts page, menu.
 */
function bcbat_wizard_finish() {
	$home_id = bcbat_maybe_create_page(
		'home',
		__( 'Home', 'bencreatesbooks-author-toolkit' ),
		__( 'Welcome to my website.', 'bencreatesbooks-author-toolkit' )
	);
	$blog_id = bcbat_maybe_create_page(
		'blog',
		__( 'Blog', 'bencreatesbooks-author-toolkit' ),
		''
	);
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $blog_id );
	bcbat_build_menu();
}

/**
 * AJAX: import the demo content.
 */
/**
 * Erase existing content so the demo installs on a fresh site.
 */
function bcbat_wipe_for_demo() {
	// Delete all posts, pages, books, events, and media.
	$post_ids = get_posts( array(
		'post_type'      => array( 'post', 'page', 'bcbat_book', 'bcbat_event', 'attachment' ),
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	foreach ( $post_ids as $pid ) {
		wp_delete_post( $pid, true );
	}

	// Delete all comments.
	$comment_ids = get_comments( array( 'fields' => 'ids' ) );
	foreach ( $comment_ids as $cid ) {
		wp_delete_comment( $cid, true );
	}

	// Delete all nav menus.
	foreach ( wp_get_nav_menus() as $menu ) {
		wp_delete_nav_menu( $menu );
	}

	// Reset theme settings, customizer values, and widget areas.
	delete_option( 'authorpress_settings' );
	remove_theme_mods();
	update_option( 'sidebars_widgets', array( 'wp_inactive_widgets' => array() ) );
}

function bcbat_ajax_import_demo() {
	check_ajax_referer( 'bcbat_wizard', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bencreatesbooks-author-toolkit' ) ) );
	}
	if ( empty( $_POST['confirmed'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Please confirm the demo import first.', 'bencreatesbooks-author-toolkit' ) ) );
	}

	bcbat_wipe_for_demo();

	$images = bcbat_demo_images();

	bcbat_maybe_create_page(
		'home',
		__( 'Home', 'bencreatesbooks-author-toolkit' ),
		__( 'Welcome to my corner of the internet. I write novels about second chances, small towns, and the people who almost got away.', 'bencreatesbooks-author-toolkit' )
	);
	$about_id = bcbat_maybe_create_page(
		'about',
		__( 'Elena Marsh', 'bencreatesbooks-author-toolkit' ),
		__( 'Elena Marsh writes contemporary romance and cozy mystery from a sunlit desk in Portland, Oregon. Her debut novel was a Kindle bestseller in 2024. When she is not writing, she is hiking with her rescue dog, Biscuit, or hunting for the perfect cinnamon roll.', 'bencreatesbooks-author-toolkit' )
	);
	bcbat_maybe_create_page(
		'contact',
		__( 'Contact', 'bencreatesbooks-author-toolkit' ),
		__( 'For rights inquiries, speaking, or just to say hello, email hello@example.com.', 'bencreatesbooks-author-toolkit' )
	);

	// Author photo on the About page.
	$author_photo = bcbat_sideload_image( $images['author'], $about_id, 'demo-author-portrait.jpg' );
	if ( $author_photo ) {
		set_post_thumbnail( $about_id, $author_photo );
	}

	$demo_books = array(
		array(
			'title'     => __( 'The Summer We Stayed', 'bencreatesbooks-author-toolkit' ),
			'subtitle'  => __( 'A Beach House Romance', 'bencreatesbooks-author-toolkit' ),
			'genre'     => __( 'Romance', 'bencreatesbooks-author-toolkit' ),
			'price'     => '$4.99',
			'buy_link'  => 'https://www.amazon.com/dp/B0DEMOSUMMER',
			'cover_url' => $images['cover_romance'],
			'cover_fallback' => 'demo-cover-romance.jpg',
			'blurb'     => __( 'When Nora Bennett inherits her grandmother’s crumbling beach house, she plans to sell it in a week. Then the roof caves in, the contractor is infuriatingly charming, and the town she swore she would never return to starts feeling like home.', 'bencreatesbooks-author-toolkit' ),
			'retailers' => array(
				array( 'name' => 'Amazon', 'url' => 'https://www.amazon.com/dp/B0DEMOSUMMER' ),
				array( 'name' => 'Barnes & Noble', 'url' => 'https://www.barnesandnoble.com/w/B0DEMOSUMMER' ),
			),
			'reviews' => array(
				array( 'quote' => 'A sun-drenched debut that reads like a long exhale. Marsh writes longing better than anyone in the genre right now.', 'name' => 'Kirkus Reviews', 'source' => 'Kirkus', 'rating' => 5 ),
				array( 'quote' => 'I finished it in one sitting and immediately bought two copies for my sisters.', 'name' => 'Hannah Lindqvist', 'source' => 'Goodreads', 'rating' => 5 ),
			),
			'quotes' => array(
				array( 'quote' => 'Some houses hold you the way some people do: loosely, until you realize you never want to leave.', 'attr' => 'Chapter 12' ),
				array( 'quote' => 'She had come to sell the past and stayed to renovate it.', 'attr' => 'Chapter 3' ),
			),
		),
		array(
			'title'     => __( 'The Cinnamon Shop Mystery', 'bencreatesbooks-author-toolkit' ),
			'subtitle'  => __( 'Sweet Crimes, Book 1', 'bencreatesbooks-author-toolkit' ),
			'genre'     => __( 'Mystery', 'bencreatesbooks-author-toolkit' ),
			'price'     => '$3.99',
			'buy_link'  => 'https://www.amazon.com/dp/B0DEMOCINNAMON',
			'cover_url' => $images['cover_cozy'],
			'cover_fallback' => 'demo-cover-cozy.jpg',
			'blurb'     => __( 'Baker-turned-amateur-sleuth Poppy Hart just wanted to perfect her cinnamon roll recipe. She did not expect to find her rival face-down in a vat of frosting.', 'bencreatesbooks-author-toolkit' ),
			'retailers' => array(
				array( 'name' => 'Amazon', 'url' => 'https://www.amazon.com/dp/B0DEMOCINNAMON' ),
				array( 'name' => 'Kobo', 'url' => 'https://www.kobo.com/ebook/B0DEMOCINNAMON' ),
				array( 'name' => 'Apple Books', 'url' => 'https://books.apple.com/book/B0DEMOCINNAMON' ),
			),
			'reviews' => array(
				array( 'quote' => 'Cozy mystery at its most delicious. The frosting scene alone is worth the price.', 'name' => 'Mystery Scene Magazine', 'source' => 'Mystery Scene', 'rating' => 4 ),
				array( 'quote' => 'Poppy Hart is the amateur sleuth I have been waiting for.', 'name' => 'Dana Whitfield', 'source' => 'BookBub', 'rating' => 5 ),
			),
			'quotes' => array(
				array( 'quote' => 'Murder, she decided, pairs badly with cinnamon. Everything else in her shop, though, was to die for.', 'attr' => 'Chapter 1' ),
			),
		),
		array(
			'title'     => __( 'The Glasshouse Secret', 'bencreatesbooks-author-toolkit' ),
			'subtitle'  => __( 'A Thriller', 'bencreatesbooks-author-toolkit' ),
			'genre'     => __( 'Thriller', 'bencreatesbooks-author-toolkit' ),
			'price'     => '$5.99',
			'buy_link'  => 'https://www.amazon.com/dp/B0DEMOGLASS',
			'cover_url' => $images['cover_thriller'],
			'cover_fallback' => 'demo-cover-thriller.jpg',
			'blurb'     => __( 'Botanist Wren Calloway inherits a glasshouse and the encrypted journals hidden inside it. Someone has been watching the house for thirty years, and they know she found the key.', 'bencreatesbooks-author-toolkit' ),
			'retailers' => array(
				array( 'name' => 'Amazon', 'url' => 'https://www.amazon.com/dp/B0DEMOGLASS' ),
				array( 'name' => 'Google Play', 'url' => 'https://play.google.com/store/books/details?id=B0DEMOGLASS' ),
				array( 'name' => 'Bookshop.org', 'url' => 'https://bookshop.org/books/B0DEMOGLASS' ),
			),
			'reviews' => array(
				array( 'quote' => 'Atmospheric and relentless. The glasshouse chapters are masterclasses in slow dread.', 'name' => 'Publishers Weekly', 'source' => 'Publishers Weekly', 'rating' => 5 ),
				array( 'quote' => 'I guessed the ending three times and was wrong three times.', 'name' => 'Marcus Bell', 'source' => 'Goodreads', 'rating' => 4 ),
			),
			'quotes' => array(
				array( 'quote' => 'Glass lets the light in. It also lets everything else watch.', 'attr' => 'Chapter 7' ),
			),
		),
		array(
			'title'     => __( 'Where the Light Gets In', 'bencreatesbooks-author-toolkit' ),
			'subtitle'  => __( 'A Novel', 'bencreatesbooks-author-toolkit' ),
			'genre'     => __( 'Romance', 'bencreatesbooks-author-toolkit' ),
			'price'     => '',
			'buy_link'  => '',
			'cover_url' => $images['cover_novel'],
			'cover_fallback' => 'demo-cover-novel.jpg',
			'blurb'     => __( 'A decade after their band broke up on the eve of stardom, four former friends are forced back into the same recording studio. Coming soon.', 'bencreatesbooks-author-toolkit' ),
			'quotes' => array(
				array( 'quote' => 'We were never a band. We were a weather system with guitars.', 'attr' => 'Chapter 2' ),
			),
		),
	);
	foreach ( $demo_books as $book ) {
		if ( '' === $book['price'] ) {
			$book['coming_soon'] = 1;
		}
		bcbat_create_book( $book );
	}

	$demo_posts = array(
		array(
			'title'   => __( 'How I Write 2,000 Words Before Breakfast', 'bencreatesbooks-author-toolkit' ),
			'content' => __( 'Everyone wants the morning routine. Here is the unglamorous truth: I write badly, early, and without my phone in the room. Protect the first hour. Everything else in your day will try to take it.', 'bencreatesbooks-author-toolkit' ),
			'cat'     => __( 'Writing', 'bencreatesbooks-author-toolkit' ),
		),
		array(
			'title'   => __( 'What Launch Week Actually Looks Like', 'bencreatesbooks-author-toolkit' ),
			'content' => __( 'Launch week is not a single day of glory. It is ten days of refreshing dashboards and learning that your best marketing is a reader telling a friend. Build the list before you need it.', 'bencreatesbooks-author-toolkit' ),
			'cat'     => __( 'Publishing', 'bencreatesbooks-author-toolkit' ),
		),
		array(
			'title'   => __( 'Why I Switched to Wide (and What It Cost Me)', 'bencreatesbooks-author-toolkit' ),
			'content' => __( 'After two years in Kindle Unlimited, I went wide. Month one hurt. Month four beat my best KU month. Exclusivity is a business decision, not a loyalty oath.', 'bencreatesbooks-author-toolkit' ),
			'cat'     => __( 'Publishing', 'bencreatesbooks-author-toolkit' ),
		),
	);
	foreach ( $demo_posts as $post ) {
		$post_id = wp_insert_post(
			array(
				'post_title'   => $post['title'],
				'post_content' => '<p>' . $post['content'] . '</p>',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			wp_set_post_terms( $post_id, $post['cat'], 'category' );
		}
	}

	$demo_events = array(
		array(
			'title'   => __( 'Spring Signing at The Book Nook', 'bencreatesbooks-author-toolkit' ),
			'desc'    => __( 'Meet Elena, get your books signed, and hear the first chapter of The Glasshouse Secret read aloud. Free entry, refreshments provided.', 'bencreatesbooks-author-toolkit' ),
			'date'    => '2027-04-10',
			'time'    => '2:00 PM',
			'venue'   => __( 'The Book Nook', 'bencreatesbooks-author-toolkit' ),
			'city'    => __( 'Portland, OR', 'bencreatesbooks-author-toolkit' ),
			'link'    => '',
		),
		array(
			'title'   => __( 'Virtual Launch Party: The Glasshouse Secret', 'bencreatesbooks-author-toolkit' ),
			'desc'    => __( 'Join the online launch party: a live reading, a Q&A about the research behind the book, and a giveaway of five signed paperbacks.', 'bencreatesbooks-author-toolkit' ),
			'date'    => '2027-06-18',
			'time'    => '7:00 PM ET',
			'venue'   => __( 'Online event', 'bencreatesbooks-author-toolkit' ),
			'city'    => '',
			'link'    => 'https://bencreatesbooks.com/',
		),
		array(
			'title'   => __( 'Coastal Readers Festival', 'bencreatesbooks-author-toolkit' ),
			'desc'    => __( 'Elena joins twenty authors for a weekend of panels, signings, and salty air. Find her at the mystery panel on Saturday and the signing tent all weekend.', 'bencreatesbooks-author-toolkit' ),
			'date'    => '2028-09-16',
			'time'    => '10:00 AM',
			'venue'   => __( 'Cannon Beach Community Hall', 'bencreatesbooks-author-toolkit' ),
			'city'    => __( 'Cannon Beach, OR', 'bencreatesbooks-author-toolkit' ),
			'link'    => '',
		),
	);
	foreach ( $demo_events as $event ) {
		$event_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $event['title'] ),
				'post_content' => wp_kses_post( $event['desc'] ),
				'post_status'  => 'publish',
				'post_type'    => 'bcbat_event',
			)
		);
		if ( $event_id && ! is_wp_error( $event_id ) ) {
			update_post_meta( $event_id, '_bcbat_event_date', sanitize_text_field( $event['date'] ) );
			update_post_meta( $event_id, '_bcbat_event_time', sanitize_text_field( $event['time'] ) );
			update_post_meta( $event_id, '_bcbat_event_venue', sanitize_text_field( $event['venue'] ) );
			update_post_meta( $event_id, '_bcbat_event_city', sanitize_text_field( $event['city'] ) );
			if ( '' !== $event['link'] ) {
				update_post_meta( $event_id, '_bcbat_event_link', esc_url_raw( $event['link'] ) );
			}
		}
	}

	// Demo showcase defaults: socials, header button, press logos.
	// Neutral placeholder accounts: the site owner replaces them with their own.
	set_theme_mod( 'social_instagram', 'https://www.instagram.com/authorname' );
	set_theme_mod( 'social_threads', 'https://www.threads.net/@authorname' );
	set_theme_mod( 'social_linkedin', 'https://www.linkedin.com/in/authorname' );
	set_theme_mod( 'social_x', 'https://x.com/authorname' );
	set_theme_mod( 'header_cta_show', 1 );
	set_theme_mod( 'header_cta_text', __( 'Get the book', 'bencreatesbooks-author-toolkit' ) );
	set_theme_mod( 'header_cta_url', get_post_type_archive_link( 'bcbat_book' ) );
	$demo_press = array( 'Kirkus Reviews', 'Publishers Weekly', 'BookBub', 'Mystery Scene', 'Goodreads' );
	foreach ( $demo_press as $i => $name ) {
		set_theme_mod( 'press_logo_' . ( $i + 1 ) . '_text', $name );
	}

	bcbat_wizard_finish();
	update_option( 'bcbat_wizard_done', 1 );

	wp_send_json_success();
}
add_action( 'wp_ajax_bcbat_import_demo', 'bcbat_ajax_import_demo' );

/**
 * AJAX: guided setup.
 */
function bcbat_ajax_guided_setup() {
	check_ajax_referer( 'bcbat_wizard', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bencreatesbooks-author-toolkit' ) ) );
	}

	if ( isset( $_POST['blogname'] ) ) {
		update_option( 'blogname', sanitize_text_field( wp_unslash( $_POST['blogname'] ) ) );
	}
	if ( isset( $_POST['blogdescription'] ) ) {
		update_option( 'blogdescription', sanitize_text_field( wp_unslash( $_POST['blogdescription'] ) ) );
	}

	$books = isset( $_POST['books'] ) ? wp_unslash( $_POST['books'] ) : array();
	if ( is_array( $books ) ) {
		foreach ( $books as $book ) {
			if ( empty( $book['title'] ) ) {
				continue;
			}
			bcbat_create_book(
				array(
					'title'      => isset( $book['title'] ) ? $book['title'] : '',
					'subtitle'   => isset( $book['subtitle'] ) ? $book['subtitle'] : '',
					'genre'      => isset( $book['genre'] ) ? $book['genre'] : '',
					'price'      => isset( $book['price'] ) ? $book['price'] : '',
					'coming_soon' => ! empty( $book['coming_soon'] ),
					'buy_link'   => isset( $book['buy_link'] ) ? $book['buy_link'] : '',
					'cover_id'   => isset( $book['cover_id'] ) ? $book['cover_id'] : 0,
					'blurb'      => isset( $book['blurb'] ) ? $book['blurb'] : '',
				)
			);
		}
	}

	$about_bio = isset( $_POST['about_bio'] ) ? wp_kses_post( wp_unslash( $_POST['about_bio'] ) ) : '';
	$about_name = isset( $_POST['about_name'] ) ? sanitize_text_field( wp_unslash( $_POST['about_name'] ) ) : __( 'About', 'bencreatesbooks-author-toolkit' );
	if ( '' !== $about_bio || '' !== $about_name ) {
		$about_id = bcbat_maybe_create_page( 'about', $about_name, wpautop( $about_bio ) );
		if ( isset( $_POST['about_photo'] ) && '' !== $_POST['about_photo'] ) {
			set_post_thumbnail( $about_id, absint( $_POST['about_photo'] ) );
		}
	}
	bcbat_maybe_create_page( 'contact', __( 'Contact', 'bencreatesbooks-author-toolkit' ), '' );

	bcbat_wizard_finish();
	update_option( 'bcbat_wizard_done', 1 );

	wp_send_json_success();
}
add_action( 'wp_ajax_bcbat_guided_setup', 'bcbat_ajax_guided_setup' );

/**
 * Flag a pending redirect after theme activation.
 *
 * We only set a transient here. The actual redirect happens on the next
 * admin page load (see bcbat_maybe_activation_redirect). Redirecting
 * inside after_switch_theme breaks on hosts that wrap activation in their
 * own controller and causes redirect loops.
 */
function bcbat_activation_flag() {
	if ( get_option( 'bcbat_wizard_done' ) ) {
		return;
	}
	set_transient( 'bcbat_activation_redirect', 1, 60 );
}

/**
 * Redirect to the wizard once, on the first admin page load after activation.
 */
function bcbat_maybe_activation_redirect() {
	if ( ! get_transient( 'bcbat_activation_redirect' ) ) {
		return;
	}
	delete_transient( 'bcbat_activation_redirect' );

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( wp_doing_ajax() || is_network_admin() ) {
		return;
	}
	// Never interrupt bulk activation.
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}
	// Never redirect away from our own pages (loop guard).
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( in_array( $page, array( 'bcbat-wizard' ), true ) ) {
		return;
	}
	if ( headers_sent() ) {
		return;
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=bcbat_book&page=bcbat-wizard' ) );
	exit;
}
add_action( 'admin_init', 'bcbat_maybe_activation_redirect' );

/**
 * Register the setup wizard under the Books menu.
 */
function bcbat_companion_wizard_menu() {
	add_submenu_page(
		'edit.php?post_type=bcbat_book',
		__( 'Author Toolkit Setup', 'bencreatesbooks-author-toolkit' ),
		__( 'Setup Wizard', 'bencreatesbooks-author-toolkit' ),
		'manage_options',
		'bcbat-wizard',
		'bcbat_wizard_page'
	);
}
add_action( 'admin_menu', 'bcbat_companion_wizard_menu' );
