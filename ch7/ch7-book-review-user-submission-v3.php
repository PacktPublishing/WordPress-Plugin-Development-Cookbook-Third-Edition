<?php

/*
  Plugin Name: Chapter 7 - Book Review User Submission v3
  Plugin URI:
  Description: Companion to recipe 'Sending e-mail notifications upon new submissions'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Declare shortcode and specify function to be called when found
add_shortcode( 'submit-book-review', 'ch7_brus_book_review_form' );

// Function to replace shortcode with content when found
function ch7_brus_book_review_form() { 
	// make sure user is logged in
	if ( !is_user_logged_in() ) {
		echo '<p>You need to be a website member to submit book reviews.</p>';
		return;
	}
	?>

	<form method="post" id="add_book_review" action="">
		<!-- Nonce fields to verify visitor provenance -->
		<?php wp_nonce_field( 'add_review_form', 'br_user_form' ); ?>
		
		<!-- Display confirmation message to users who submit a book review -->
		<?php if ( !empty( $_GET['add_review_message'] ) ) { ?>
		<div style="margin: 8px;border: 1px solid #ddd;background-color: #ff0;">
			Thank for your submission!
		</div>
		<?php } ?>
		
	    <!-- Post variable to indicate user-submitted items -->
		<input type="hidden" name="ch7_brus_user_book_review" value="1" />
		
		<table><tr>
			<td>Book Title</td>
			<td><input type="text" name="book_title" /></td>
		</tr><tr>
			<td>Book Author</td>
			<td><input type="text" name="book_author" /></td>
		</tr><tr>
			<td>Review</td>
			<td><textarea name="book_review_text"></textarea></td>
		</tr><tr>
			<td>Rating</td>
			<td>
				<select name="book_review_rating">
				<?php for ( $r = 5; $r >= 1; $r-- ) { ?>
					<option value="<?php echo $r; ?>"><?php echo $r; ?> stars
				<?php } ?>
				</select>
			</td>
		</tr><tr>
			<td>Book Type</td>
			<td>
				<?php 
					$book_types = get_terms( 'book_reviews_book_type', array( 'orderby' => 'name', 'hide_empty' => 0 ) );

					if ( !is_wp_error( $book_types ) && !empty( $book_types ) ) {
						echo '<select name="book_review_book_type">';

						foreach ( $book_types as $book_type ) {				
							echo '<option value="' . $book_type->term_id . '">' . $book_type->name . '</option>';
						}		
						echo '</select>';
					} ?>
			</td>
		</tr></table>

		<input type="submit" name="submit" value="Submit Review" />
	</form>

<?php $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

add_action( 'template_redirect', 'ch7_brus_match_new_book_reviews' );

function ch7_brus_match_new_book_reviews( $template ) {	
	if ( !empty( $_POST['ch7_brus_user_book_review'] ) ) {
		ch7_brus_process_user_book_reviews();
	} else {
		return $template;
	}		
}

function ch7_brus_process_user_book_reviews() {

	// Check that all required fields are present and non-empty
	if ( wp_verify_nonce( $_POST['br_user_form'], 'add_review_form' ) && 
		 !empty( $_POST['book_title'] ) && 
		 !empty( $_POST['book_author'] ) &&
		 !empty( $_POST['book_review_text'] ) && 
		 !empty( $_POST['book_review_book_type'] ) &&
		 !empty( $_POST['book_review_rating'] ) ) {
		// Display error message if any required fields are missing
		// or if form did not have valid nonce fields.		
		$abort_message = 'Some fields were left empty. Please '; 
        $abort_message .= 'go back and complete form.'; 
        wp_die( $abort_message ); 
		exit;	
	}

	// Create array with received data
	$new_br_data = array(
			'post_status' => 'draft', 
			'post_title' => sanitize_text_field( $_POST['book_title'] ),
			'post_type' => 'book_reviews',
			'post_content' => sanitize_text_field( $_POST['book_review_text'] )
		);

	// Insert new post in site database
	// Store new post ID from return value in variable
	$new_book_review_id = wp_insert_post( $new_br_data );

	// Store book author and rating
	add_post_meta( $new_book_review_id, 'book_author', sanitize_text_field( $_POST['book_author'] ) );
	add_post_meta( $new_book_review_id, 'book_rating', (int) $_POST['book_review_rating'] );

	// Set book type on post
	$type = sanitize_text_field( $_POST['book_review_book_type'] );
	if ( term_exists( $type, 'book_reviews_book_type' ) ) {
		wp_set_post_terms( $new_book_review_id, $type, 'book_reviews_book_type' );
	}

	// Redirect browser to book review submission page
	$redirect_address = ( empty( $_POST['_wp_http_referer'] ) ? site_url() : $_POST['_wp_http_referer'] );
	wp_redirect( add_query_arg( 'add_review_message', '1', $redirect_address ) );
	exit;
}

add_action( 'wp_insert_post', 'ch7_brus_send_email', 10, 2 );

function ch7_brus_send_email( $post_id, $post ) {
	// Only send e-mails for user-submitted book reviews
	if ( !isset( $_POST['ch7_brus_user_book_review'] ) || 'book_reviews' != $post->post_type ) {
		return;
	}

	$headers = 'Content-type: text/html';

	// Prepare e-mail message to notify site admin of new submission
	$admin_mail = get_option( 'admin_email' );

	$message = 'A user submitted a new book review.<br />';
	$message .= 'Book Title: ' . esc_html( $post->post_title ) . '<br />';

	$message .= '<a href="' . add_query_arg( array( 'post_status' => 'draft', 'post_type' => 'book_reviews' ), admin_url( 'edit.php' ) ) . '">Moderate new book reviews</a>';

	$email_title = esc_html( get_bloginfo(), ENT_QUOTES );
	$email_title .= ' - New Book Review Added: ';
	$email_title .= esc_html( $post->post_title );

	// Send e-mail
	wp_mail( $admin_mail, $email_title, $message, $headers );
}