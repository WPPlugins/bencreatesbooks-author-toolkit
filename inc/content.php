<?php
/**
 * Author Toolkit: content types and book/event data.
 *
 * Registers the Books and Events post types, the Genre taxonomy, and all
 * book and event meta fields (subtitle, buy link, price, retailers,
 * reviews, quotes, event date and venue details).
 *
 * @package BenCreatesBooksAuthorToolkit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Books custom post type */
function bcbat_register_book_cpt() {
    $labels = array(
        'name'          => __( 'Books', 'bencreatesbooks-author-toolkit' ),
        'singular_name' => __( 'Book', 'bencreatesbooks-author-toolkit' ),
        'add_new_item'  => __( 'Add New Book', 'bencreatesbooks-author-toolkit' ),
        'edit_item'     => __( 'Edit Book', 'bencreatesbooks-author-toolkit' ),
    );
    register_post_type( 'bcbat_book', array(
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'books' ),
        'menu_icon'    => 'dashicons-book',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest' => true,
    ) );

    register_taxonomy( 'bcbat_genre', 'bcbat_book', array(
        'label'        => __( 'Genres', 'bencreatesbooks-author-toolkit' ),
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'genre' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'bcbat_register_book_cpt' );


function bcbat_book_meta_boxes() {
    add_meta_box( 'bcbat-book-details', __( 'Book Details', 'bencreatesbooks-author-toolkit' ),
        'bcbat_book_meta_html', 'bcbat_book', 'side' );
}
add_action( 'add_meta_boxes', 'bcbat_book_meta_boxes' );

function bcbat_book_meta_html( $post ) {
    wp_nonce_field( 'bcbat_book_meta', 'bcbat_book_meta_nonce' );
    $subtitle  = get_post_meta( $post->ID, '_bcbat_subtitle', true );
    $buy_link  = get_post_meta( $post->ID, '_bcbat_buy_link', true );
    $price     = get_post_meta( $post->ID, '_bcbat_price', true );
    ?>
    <p><label for="bcbat_subtitle"><strong><?php esc_html_e( 'Subtitle', 'bencreatesbooks-author-toolkit' ); ?></strong></label>
    <input type="text" id="bcbat_subtitle" name="bcbat_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat"></p>
    <p><label for="bcbat_buy_link"><strong><?php esc_html_e( 'Buy Link', 'bencreatesbooks-author-toolkit' ); ?></strong></label>
    <input type="url" id="bcbat_buy_link" name="bcbat_buy_link" value="<?php echo esc_attr( $buy_link ); ?>" class="widefat"></p>
    <p><label for="bcbat_price"><strong><?php esc_html_e( 'Price', 'bencreatesbooks-author-toolkit' ); ?></strong></label>
    <input type="text" id="bcbat_price" name="bcbat_price" value="<?php echo esc_attr( $price ); ?>" class="widefat" placeholder="$4.99"></p>
    <?php
}

function bcbat_save_book_meta( $post_id ) {
    if ( ! isset( $_POST['bcbat_book_meta_nonce'] )
        || ! wp_verify_nonce( $_POST['bcbat_book_meta_nonce'], 'bcbat_book_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
    foreach ( array( 'bcbat_subtitle' => '_bcbat_subtitle', 'bcbat_buy_link' => '_bcbat_buy_link', 'bcbat_price' => '_bcbat_price' ) as $field => $key ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'save_post_bcbat_book', 'bcbat_save_book_meta' );

/**
 * Save the extended book meta: retailers, reviews, and book quotes.
 *
 * Shared by the setup wizard and the dashboard book editor.
 *
 * @param int   $post_id Book post ID.
 * @param array $book    Book data array.
 */
function bcbat_save_book_extras( $post_id, $book ) {
	// Retailers: array of [name, url].
	if ( isset( $book['retailers'] ) && is_array( $book['retailers'] ) ) {
		$presets = bcbat_retailer_presets();
		$clean   = array();
		foreach ( wp_unslash( $book['retailers'] ) as $r ) {
			if ( ! is_array( $r ) ) { continue; }
			$name = isset( $r['name'] ) ? sanitize_text_field( $r['name'] ) : '';
			$url  = isset( $r['url'] ) ? esc_url_raw( $r['url'] ) : '';
			if ( '' !== $name && '' !== $url ) {
				$clean[] = array(
					'name'  => $name,
					'url'   => $url,
					'color' => isset( $presets[ $name ] ) ? $presets[ $name ] : '#6b1f2a',
				);
			}
		}
		update_post_meta( $post_id, '_bcbat_retailers', $clean );
	}

	// Reviews: array of [quote, name, source, rating].
	if ( isset( $book['reviews'] ) && is_array( $book['reviews'] ) ) {
		$clean = array();
		foreach ( wp_unslash( $book['reviews'] ) as $r ) {
			if ( ! is_array( $r ) ) { continue; }
			$quote = isset( $r['quote'] ) ? sanitize_textarea_field( $r['quote'] ) : '';
			if ( '' === $quote ) {
				continue;
			}
			$clean[] = array(
				'quote'  => $quote,
				'name'   => isset( $r['name'] ) ? sanitize_text_field( $r['name'] ) : '',
				'source' => isset( $r['source'] ) ? sanitize_text_field( $r['source'] ) : '',
				'rating' => isset( $r['rating'] ) ? max( 1, min( 5, absint( $r['rating'] ) ) ) : 5,
			);
		}
		update_post_meta( $post_id, '_bcbat_reviews', $clean );
	}

	// Book quotes: array of [quote, attr].
	if ( isset( $book['quotes'] ) && is_array( $book['quotes'] ) ) {
		$clean = array();
		foreach ( wp_unslash( $book['quotes'] ) as $q ) {
			if ( ! is_array( $q ) ) { continue; }
			$quote = isset( $q['quote'] ) ? sanitize_textarea_field( $q['quote'] ) : '';
			if ( '' === $quote ) {
				continue;
			}
			$clean[] = array(
				'quote' => $quote,
				'attr'  => isset( $q['attr'] ) ? sanitize_text_field( $q['attr'] ) : '',
			);
		}
		update_post_meta( $post_id, '_bcbat_quotes', $clean );
	}
}


/**
 * Retailer presets for the per-book buy links.
 *
 * @return array Retailer name => brand color.
 */
function bcbat_retailer_presets() {
	return array(
		'Amazon'         => '#ff9900',
		'Barnes & Noble' => '#2e6b34',
		'Kobo'           => '#c2185b',
		'Apple Books'    => '#1d1d1f',
		'Google Play'    => '#01875f',
		'Bookshop.org'   => '#1d4ed8',
		'Smashwords'     => '#b3541e',
		'Audible'        => '#f7991c',
	);
}

/* Events custom post type (managed from the AuthorPress dashboard) */
function bcbat_register_event_cpt() {
	$labels = array(
		'name'          => __( 'Events', 'bencreatesbooks-author-toolkit' ),
		'singular_name' => __( 'Event', 'bencreatesbooks-author-toolkit' ),
		'add_new_item'  => __( 'Add New Event', 'bencreatesbooks-author-toolkit' ),
		'edit_item'     => __( 'Edit Event', 'bencreatesbooks-author-toolkit' ),
	);
	register_post_type( 'bcbat_event', array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'events' ),
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => array( 'title', 'editor' ),
			'show_in_rest' => true,
	) );
}
add_action( 'init', 'bcbat_register_event_cpt' );
/* Event meta: date, time, venue, city, link */
function bcbat_event_meta_boxes() {
	add_meta_box( 'bcbat-event-details', __( 'Event Details', 'bencreatesbooks-author-toolkit' ),
		'bcbat_event_meta_html', 'bcbat_event', 'normal' );
}
add_action( 'add_meta_boxes', 'bcbat_event_meta_boxes' );

function bcbat_event_meta_html( $post ) {
	wp_nonce_field( 'bcbat_event_meta', 'bcbat_event_meta_nonce' );
	$fields = array(
		'_bcbat_event_date'  => __( 'Date', 'bencreatesbooks-author-toolkit' ),
		'_bcbat_event_time'  => __( 'Time', 'bencreatesbooks-author-toolkit' ),
		'_bcbat_event_venue' => __( 'Venue', 'bencreatesbooks-author-toolkit' ),
		'_bcbat_event_city'  => __( 'City', 'bencreatesbooks-author-toolkit' ),
		'_bcbat_event_link'  => __( 'Link', 'bencreatesbooks-author-toolkit' ),
	);
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		$type  = ( '_bcbat_event_date' === $key ) ? 'date' : ( ( '_bcbat_event_link' === $key ) ? 'url' : 'text' );
		$name  = 'bcbat_' . substr( $key, 7 );
		?>
		<p><label for="<?php echo esc_attr( $name ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat"></p>
		<?php
	}
}

function bcbat_save_event_meta( $post_id ) {
	if ( ! isset( $_POST['bcbat_event_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bcbat_event_meta_nonce'] ) ), 'bcbat_event_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	$map = array(
		'bcbat_event_date'  => '_bcbat_event_date',
		'bcbat_event_time'  => '_bcbat_event_time',
		'bcbat_event_venue' => '_bcbat_event_venue',
		'bcbat_event_city'  => '_bcbat_event_city',
		'bcbat_event_link'  => '_bcbat_event_link',
	);
	foreach ( $map as $field => $key ) {
		if ( isset( $_POST[ $field ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
			if ( '_bcbat_event_link' === $key ) {
				$value = esc_url_raw( $value );
			}
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_bcbat_event', 'bcbat_save_event_meta' );

/* Book extras editor: retailers, reviews, quotes (repeatable rows) */
function bcbat_book_extras_box() {
	add_meta_box( 'bcbat-book-extras', __( 'Retailers, Reviews & Quotes', 'bencreatesbooks-author-toolkit' ),
		'bcbat_book_extras_html', 'bcbat_book', 'normal' );
}
add_action( 'add_meta_boxes', 'bcbat_book_extras_box' );

function bcbat_book_extras_html( $post ) {
	wp_nonce_field( 'bcbat_book_extras', 'bcbat_book_extras_nonce' );
	$retailers = get_post_meta( $post->ID, '_bcbat_retailers', true );
	$reviews   = get_post_meta( $post->ID, '_bcbat_reviews', true );
	$quotes    = get_post_meta( $post->ID, '_bcbat_quotes', true );
	if ( ! is_array( $retailers ) ) { $retailers = array(); }
	if ( ! is_array( $reviews ) )   { $reviews = array(); }
	if ( ! is_array( $quotes ) )    { $quotes = array(); }
	$retailers[] = array( 'name' => '', 'url' => '' );
	$reviews[]   = array( 'quote' => '', 'name' => '', 'source' => '', 'rating' => 5 );
	$quotes[]    = array( 'quote' => '', 'attr' => '' );
	?>
	<h4><?php esc_html_e( 'Retailers', 'bencreatesbooks-author-toolkit' ); ?></h4>
	<div class="ap-repeat" data-group="bcbat_retailers">
		<?php foreach ( $retailers as $r ) : ?>
		<p class="ap-row">
			<input type="text" name="bcbat_retailers[][name]" value="<?php echo esc_attr( isset( $r['name'] ) ? $r['name'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Retailer (e.g. Amazon)', 'bencreatesbooks-author-toolkit' ); ?>">
			<input type="url" name="bcbat_retailers[][url]" value="<?php echo esc_attr( isset( $r['url'] ) ? $r['url'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Buy URL', 'bencreatesbooks-author-toolkit' ); ?>" class="widefat">
		</p>
		<?php endforeach; ?>
	</div>
	<h4><?php esc_html_e( 'Reviews', 'bencreatesbooks-author-toolkit' ); ?></h4>
	<div class="ap-repeat" data-group="bcbat_reviews">
		<?php foreach ( $reviews as $r ) : ?>
		<div class="ap-row">
			<textarea name="bcbat_reviews[][quote]" rows="2" class="widefat" placeholder="<?php esc_attr_e( 'Review quote', 'bencreatesbooks-author-toolkit' ); ?>"><?php echo esc_textarea( isset( $r['quote'] ) ? $r['quote'] : '' ); ?></textarea>
			<input type="text" name="bcbat_reviews[][name]" value="<?php echo esc_attr( isset( $r['name'] ) ? $r['name'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Reviewer name', 'bencreatesbooks-author-toolkit' ); ?>">
			<input type="text" name="bcbat_reviews[][source]" value="<?php echo esc_attr( isset( $r['source'] ) ? $r['source'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Source', 'bencreatesbooks-author-toolkit' ); ?>">
			<input type="number" name="bcbat_reviews[][rating]" min="1" max="5" value="<?php echo esc_attr( isset( $r['rating'] ) ? $r['rating'] : 5 ); ?>">
		</div>
		<?php endforeach; ?>
	</div>
	<h4><?php esc_html_e( 'Book Quotes', 'bencreatesbooks-author-toolkit' ); ?></h4>
	<div class="ap-repeat" data-group="bcbat_quotes">
		<?php foreach ( $quotes as $q ) : ?>
		<div class="ap-row">
			<textarea name="bcbat_quotes[][quote]" rows="2" class="widefat" placeholder="<?php esc_attr_e( 'Quote from the book', 'bencreatesbooks-author-toolkit' ); ?>"><?php echo esc_textarea( isset( $q['quote'] ) ? $q['quote'] : '' ); ?></textarea>
			<input type="text" name="bcbat_quotes[][attr]" value="<?php echo esc_attr( isset( $q['attr'] ) ? $q['attr'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Attribution', 'bencreatesbooks-author-toolkit' ); ?>" class="widefat">
		</div>
		<?php endforeach; ?>
	</div>
	<p class="description"><?php esc_html_e( 'Leave a row empty to remove it. Add more rows by saving: a fresh empty row appears for each group.', 'bencreatesbooks-author-toolkit' ); ?></p>
	<?php
}

function bcbat_save_book_extras_box( $post_id ) {
	if ( ! isset( $_POST['bcbat_book_extras_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bcbat_book_extras_nonce'] ) ), 'bcbat_book_extras' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	// Regroup the flat name[][field] submission into row arrays.
	$group = function( $field ) {
		if ( ! isset( $_POST[ $field ] ) || ! is_array( $_POST[ $field ] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- values are sanitized inside bcbat_save_book_extras().
			return array();
		}
		$raw  = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- values are sanitized inside bcbat_save_book_extras().
		$rows = array();
		foreach ( $raw as $piece ) {
			if ( ! is_array( $piece ) ) { continue; }
			foreach ( $piece as $k => $v ) {
				$last = count( $rows ) - 1;
				if ( $last < 0 || array_key_exists( $k, $rows[ $last ] ) ) {
					$rows[] = array();
					$last++;
				}
				$rows[ $last ][ $k ] = $v;
			}
		}
		return $rows;
	};

	$book = array(
		'retailers' => $group( 'bcbat_retailers' ),
		'reviews'   => $group( 'bcbat_reviews' ),
		'quotes'    => $group( 'bcbat_quotes' ),
	);
	bcbat_save_book_extras( $post_id, $book );
}
add_action( 'save_post_bcbat_book', 'bcbat_save_book_extras_box' );
