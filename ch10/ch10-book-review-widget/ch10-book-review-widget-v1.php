<?php
/*
  Plugin Name: Chapter 10 - Book Review Widget V1
  Plugin URI:
  Description: Companion to recipe 'Creating a new widget in WordPress'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

// Register function to be called when widget initialization occurs
add_action( 'widgets_init', 'ch10brw_create_widgets' );

// Create new widget
function ch10brw_create_widgets() {
	register_widget( 'Book_Reviews' );
}

// Widget implementation class
class Book_Reviews extends WP_Widget {
	// Constructor function
	function __construct() {
		// Widget creation function
		parent::__construct( 'book_reviews',
							 'Book Reviews',
							 array( 'description' =>
									'Displays recent book reviews' ) );
	}
	
		// Function to display widget contents
	function widget( $args, $instance ) {
		// Extract members of args array as individual variables
		extract( $args );

		// Retrieve widget configuration options
		$nb_reviews = ( isset( $instance['nb_reviews'] ) && !empty( $instance['nb_reviews'] ) ? $instance['nb_reviews'] : 5 );
		$widget_title = ( isset( $instance['nb_reviews'] ) && !empty( $instance['widget_title'] ) ? esc_html( $instance['widget_title'] ) : 'Book Reviews' );

		// Preparation of query string to retrieve book reviews
		$query_array = array( 'post_type' => 'book_reviews',
							'post_status' => 'publish',
							'posts_per_page ' => $nb_reviews );

		// Execution of post query
		$book_review_query = new WP_Query();
		$book_review_query->query( $query_array );

		// Display widget title
		echo $before_widget;
		echo $before_title . apply_filters( 'widget_title', $widget_title ) . $after_title; 

		// Check if any posts were returned by query
		if ( $book_review_query->have_posts() ) {
			// Display posts in unordered list layout
			echo '<ul>';

			// Cycle through all items retrieved
			while ( $book_review_query->have_posts() ) {
				$book_review_query->the_post();
				echo '<li><a href="' . get_permalink() . '">';
				echo get_the_title( get_the_ID() );
				if ( isset( $instance['show_author'] ) &&  'true' == $instance['show_author'] ) {
					echo ' - ' . get_post_meta( get_the_ID(), 'book_author', true );
				}
				echo '</a></li>';
			}
			echo '</ul>';
		}
		
		// Reset post data query
		wp_reset_query();
		echo $after_widget;
	}
}