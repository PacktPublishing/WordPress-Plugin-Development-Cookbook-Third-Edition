<?php

/*
  Plugin Name: Chapter 7 - Book Review User Submission v1
  Plugin URI:
  Description: Companion to recipe 'Creating a client-side content submission form'
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
	ob_start();
	?>

	<form method="post" id="add_book_review" action="">
		<!-- Nonce fields to verify visitor provenance -->
		<?php wp_nonce_field( 'add_review_form', 'br_user_form' ); ?>	
		
	    <!-- Post variable to indicate user-submitted items -->
		<input type="hidden" name="ch7_brus_user_book_review" value="1" />
		
		<table style="width:100%"><tr>
			<td style="width:20%">Book Title</td>
			<td><input type="text" name="book_title" style="width:100%" /></td>
		</tr><tr>
			<td>Book Author</td>
			<td><input type="text" name="book_author" style="width:100%" /></td>
		</tr><tr>
			<td>Review</td>
			<td><textarea name="book_review_text" style="width:100%" rows="4"></textarea></td>
		</tr><tr>
			<td>Rating</td>
			<td>
				<select name="book_review_rating" style="width:200px">
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

					if ( !is_wp_error( $book_types ) && !empty( $book_types ) ) { ?>
						<select name="book_review_book_type" style="width:200px">

						<?php foreach ( $book_types as $book_type ) {
							echo '<option value="' . $book_type->term_id . '">' . $book_type->name . '</option>';
						} ?>
						</select>
					<?php } ?>
			</td>
		</tr></table>

		<input type="submit" name="submit" value="Submit Review" />
	</form>
	
	<style>
		#add_book_review table {
			border-collapse: collapse;
		}
		#add_book_review tr {
			border-bottom: 1px solid #eee !important;		
		}
		#add_book_review td {
			padding: 6px;
		}
	</style>

<?php $content = ob_get_contents();
    ob_end_clean();
    return $content;
}