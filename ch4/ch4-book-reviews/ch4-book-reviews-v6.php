<?php

/*
  Plugin Name: Chapter 4 - Book Reviews V6
  Plugin URI: 
  Description: Companion to recipe 'Adding custom categories for custom post types'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

/****************************************************************************
 * Code from recipe 'Creating a custom post type'
 ****************************************************************************/

add_action( 'init', 'ch4_br_create_book_post_type' );

function ch4_br_create_book_post_type() {
	register_post_type( 'book_reviews',
		array(
				'labels' => array(
				'name' => 'Book Reviews',
				'singular_name' => 'Book Review',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Book Review',
				'edit' => 'Edit',
				'edit_item' => 'Edit Book Review',
				'new_item' => 'New Book Review',
				'view' => 'View',
				'view_item' => 'View Book Review',
				'search_items' => 'Search Book Reviews',
				'not_found' => 'No Book Reviews found',
				'not_found_in_trash' => 'No Book Reviews found in Trash',
				'parent' => 'Parent Book Review',
			),
		'public' => true,
		'menu_position' => 20,
		'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
		'taxonomies' => array( '' ),
		'menu_icon' => 'dashicons-book-alt',
		'has_archive' => false,
		'exclude_from_search' => false,
		)
	);
	
	/* Code from recipe 'Adding custom taxonomies for custom post types */    
	register_taxonomy(
		'book_reviews_book_type',
		'book_reviews',
		array(
			'labels' => array(
				'name' => 'Book Type',
				'add_new_item' => 'Add New Book Type',
				'new_item_name' => 'New Book Type Name',
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,
		)
	);
}

/****************************************************************************
 * Code from recipe 'Adding a new section to the custom post type editor'
 ****************************************************************************/

// Register function to be called when admin interface is visited
add_action( 'admin_init', 'ch4_br_admin_init' );

// Function to register new meta box for book review post editor
function ch4_br_admin_init() {
	add_meta_box( 'ch4_br_review_details_meta_box', 'Book Review Details', 'ch4_br_display_review_details_mb', 'book_reviews', 'normal', 'high' );
}

// Function to display meta box contents
function ch4_br_display_review_details_mb( $book_review ) { 
	// Retrieve current author and rating based on book review ID
	$book_author = get_post_meta( $book_review->ID, 'book_author', true );
	$book_rating = get_post_meta( $book_review->ID, 'book_rating', true );
	?>
	<table>
		<tr>
			<td style="width: 150px">Book Author</td>
			<td><input type="text" style="width:100%" name="book_review_author_name" value="<?php echo esc_html( $book_author ); ?>" /></td>
		</tr>
		<tr>
			<td style="width: 150px">Book Rating</td>
			<td>
				<select style="width: 130px" name="book_review_rating">
					<option value="">Select rating</option>
					<!-- Loop to generate all items in dropdown list -->
					<?php for ( $rating = 5; $rating >= 1; $rating -- ) { ?>
					<option value="<?php echo intval( $rating ); ?>" <?php echo selected( $rating, $book_rating ); ?>><?php echo intval( $rating ); ?> stars
					<?php } ?>
				</select>
			</td>
		</tr>
	</table>

<?php }

// Register function to be called when posts are saved
// The function will receive 2 arguments
add_action( 'save_post', 'ch4_br_add_book_review_fields', 10, 2 );

function ch4_br_add_book_review_fields( $book_review_id, $book_review ) {
	if ( 'book_reviews' != $book_review->post_type ) {
		return;
	}

	if ( isset( $_POST['book_review_author_name'] ) ) {
		update_post_meta( $book_review_id, 'book_author', sanitize_text_field( $_POST['book_review_author_name'] ) );
	}
	if ( isset( $_POST['book_review_rating'] ) && !empty( $_POST['book_review_rating'] ) ) {
		update_post_meta( $book_review_id, 'book_rating', intval( $_POST['book_review_rating'] ) );
	}
}

/************************************************************************************
 * Code from recipe 'Displaying single custom post type items using a custom layout'
 ************************************************************************************/

add_filter( 'template_include', 'ch4_br_template_include', 1 );

function ch4_br_template_include( $template_path ) {	
	if ( is_single() && 'book_reviews' == get_post_type()) {
		// checks if the file exists in theme first,
		// otherwise install content filter
		if ( $theme_file = locate_template( array( 'single-book_reviews.php' ) ) ) {
			return $theme_file;
		} else {
			add_filter( 'the_content', 'ch4_br_display_single_book_review',  20 );			
		}
	}
	return $template_path;
}

function ch4_br_display_single_book_review( $content ) {
    if ( empty( get_the_ID() ) ) {
		return;
	}

	// Display featured image in right-aligned floating div
	$content = '<div style="float: right; margin: 10px">';
	$content .= get_the_post_thumbnail( get_the_ID(), 'medium' );
	$content .= '</div>';
	
	$content .= '<div class="entry-content">';

	// Display Author Name
	$content .= '<strong>Author: </strong>';
	$content .= esc_html( get_post_meta( get_the_ID(), 'book_author', true ) );
	$content .= '<br />';

	// Display yellow stars based on rating -->
	$content .= '<strong>Rating: </strong>';

	$nb_stars = intval( get_post_meta( get_the_ID(), 'book_rating', true ) );

	$content .= str_repeat( '<img style="margin: 0" src="' . plugins_url( 'star-icon.png', __FILE__ ) . '" />', $nb_stars );
	$content .= str_repeat( '<img style="margin: 0" src="' . plugins_url( 'star-icon-grey.png', __FILE__ ) . '" />', 5 - $nb_stars );
	
	$book_types = wp_get_post_terms( get_the_ID(), 'book_reviews_book_type' ); 
 
	$content .= '<br /><strong>Type: </strong>';

	if ( $book_types ) {
		$type_array = array();
		foreach ( $book_types as $book_type ) {
			$type_array[] = $book_type->name;
		}
		$content .= esc_html( implode( ',', $type_array ) );
	} else {
		$content .= 'None Assigned';
	}

	// Display book review contents
	$content .= '<br /><br />' . get_the_content( get_the_ID() ) . '</div>';

	return $content;
}

/****************************************************************************
 * Code from recipe 'Tailoring search output for Custom Post Type items'
 ****************************************************************************/

add_filter( 'get_the_excerpt', 'ch4_br_search_display' );
add_filter( 'the_excerpt', 'ch4_br_search_display' );
add_filter( 'the_content', 'ch4_br_search_display' );

function ch4_br_search_display( $content ) {
	if ( !is_search() && 'book_reviews' != get_post_type() ) {
		return $content;
	}

	$content = '<div style="float: right; margin: 10px">';
	$content .= get_the_post_thumbnail( get_the_ID(), 'medium' );
	$content .= '</div><div class="entry-content">';

	$content .= '<strong>Author: </strong>';
	$content .= esc_html( get_post_meta( get_the_ID(), 'book_author', true ) );
	$content .= '<br />';
        
	$content .= '<strong>Rating: </strong>';
	$nb_stars = intval( get_post_meta( get_the_ID(), 'book_rating', true ) );
	$content .= str_repeat( '<img style="margin: 0" src="' . plugins_url( 'star-icon.png', __FILE__ ) . '" />', $nb_stars );
	$content .= str_repeat( '<img style="margin: 0" src="' . plugins_url( 'star-icon-grey.png', __FILE__ ) . '" />', 5 - $nb_stars );

	$content .= '<br /><br />';
	$content .= wp_trim_words( get_the_content( get_the_ID() ), 20 );
	$content .= '</div>';
	return $content;
}

add_filter( 'the_title', 'ch4_br_review_title', 10, 2 );

function ch4_br_review_title( $title, $id = null ) {
    if ( !is_admin() && is_search() && !empty( $id ) ) { 
        $post = get_post( $id );
        if ( !empty( $post ) && $post->post_type == 'book_reviews' ) {            
            return 'Book review: ' . $title;
        }
    }
    return $title;
}

/****************************************************************************
 * Code from recipe 'Displaying custom post type data in shortcodes'
 ****************************************************************************/

add_shortcode( 'book-review-list', 'ch4_br_book_review_list' );

// Implementation of short code function
function ch4_br_book_review_list() {
	// Preparation of query array to retrieve 5 book reviews
	$query_params = array( 'post_type' => 'book_reviews',
                           'post_status' => 'publish',
                           'posts_per_page' => 5 );
	
	// Retrieve page query variable, if present
	$page_num = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	// If page number is higher than 1, add to query array
	if ( $page_num != 1 ) {
		$query_params['paged'] = $page_num;
	}

	// Execution of post query
	$book_review_query = new WP_Query;
    $book_review_query->query( $query_params );
	
	// Check if any posts were returned by query
	if ( $book_review_query->have_posts() ) {
		// Display posts in table layout
		$output = '<table>';
		$output .= '<tr><th><strong>Title</strong></th>';
		$output .= '<th><strong>Author</strong></th></tr>';

		// Cycle through all items retrieved
		while ( $book_review_query->have_posts() ) {
			$book_review_query->the_post();
			$output .= '<tr><td><a href="' . get_permalink() . '">';
			$output .= get_the_title( get_the_ID() ) . '</a></td>';
			$output .= '<td>' . esc_html( get_post_meta( get_the_ID(), 'book_author', true ) );
			$output .= '</td></tr>';
		}

		$output .= '</table>';

		// Display page navigation links
		if ( $book_review_query->max_num_pages > 1 ) {
			$output .= '<nav id="nav-below">';
			$output .= '<div class="nav-previous">';
			$output .= get_next_posts_link( '<span class="meta-nav">&larr;</span> Older reviews', $book_review_query->max_num_pages );
			$output .= '</div>';
			$output .= "<div class='nav-next'>";
			$output .= get_previous_posts_link( 'Newer reviews <span class="meta-nav">&rarr;</span>', $book_review_query->max_num_pages );
			$output .= '</div>';
			$output .= '</nav>';
		}

		// Reset post data query
		wp_reset_postdata();
	}

	return $output;
}