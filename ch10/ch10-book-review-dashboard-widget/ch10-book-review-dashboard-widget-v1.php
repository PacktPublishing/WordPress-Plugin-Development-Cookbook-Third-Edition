<?php
/*
  Plugin Name: Chapter 10 - Book Review Dashboard Widget V1
  Plugin URI:
  Description: Companion to recipe 'Adding a custom dashboard widget'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

add_action( 'wp_dashboard_setup', 'ch10brdw_add_dashboard_widget' );

function ch10brdw_add_dashboard_widget() {
	wp_add_dashboard_widget( 'book_reviews_dashboard_widget',
							 'Book Reviews',
							 'ch10brdw_dashboard_widget' );
}

function ch10brdw_dashboard_widget() { 
	$book_review_count = wp_count_posts( 'book_reviews' );
	if ( !empty( (array) $book_review_count ) ) {
	?>
	<div>
	<a href="<?php echo add_query_arg( array( 'post_status' => 'publish',
											  'post_type' => 'book_reviews' ),
											  admin_url( 'edit.php' ) ); ?>">
		<strong><?php echo $book_review_count->publish; ?></strong> Published</a><br />
	<a href="<?php echo add_query_arg( array( 'post_status' => 'draft',
											  'post_type' => 'book_reviews' ),
											  admin_url( 'edit.php' ) ); ?>">
		<strong><?php echo $book_review_count->draft; ?></strong> Draft
	</a>
	</div><br />
<?php }
}