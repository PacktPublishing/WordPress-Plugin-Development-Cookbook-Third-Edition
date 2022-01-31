<?php
/*
Plugin Name: Chapter 11 - Transit Feed V2
Plugin URI:
Description: Declares a plugin that will be visible in the WordPress admin interface
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://ylefebvre.ca
License: GPLv2
*/

function is_multi_array_or_convert( $array ) {
	if ( isset( $array[0] ) && is_array( $array[0] ) ) {
		return $array;
	} else {
		return array( $array );
	}
} 

function get_transit_data() {
	$stations = array( '12th', '16th', '19th', '24th' );
	$transit_data = array();
	
	foreach ( $stations as $station ) {
		if ( false === ( $transit_data[$station] = get_transient( 'transit_feed_' . $station ) ) ) {
			$next_departures_xml = simplexml_load_file( 'http://api.bart.gov/api/etd.aspx?cmd=etd&orig=' . $station . '&key=MW9S-E7SL-26DU-VV8V' );
			$next_departures_json = json_encode( $next_departures_xml );
			$transit_data[$station] = json_decode( $next_departures_json, TRUE );
			
			set_transient( 'transit_feed_' . $station, $transit_data[$station], 300 );
		}
	}
	return $transit_data;    
}

add_shortcode( 'transit-feed', 'ch11tf_transit_feed' );

function ch11tf_transit_feed() {
	$output = '';    
	$transit_data = get_transit_data();
	
	if ( empty( $transit_data ) ) { return; }
	foreach ( $transit_data as $station_data ) {
		$output .= '<h3>' . $station_data['station']['name'];
		$output .= '</h3>';
	
		if ( empty( $station_data['station']['etd'] ) ) { continue; }
		$etd_array = is_multi_array_or_convert( $station_data['station']['etd'] );
	
		$output .= '<table><tr><th>Destination</th>';
		$output .= '<th>Direction</th>';
		$output .= '<th>Number of minutes</th></tr>';
		foreach ( $etd_array as $etd ) {
			if ( empty( $etd['estimate'] ) ) { continue; }
			$etd_estimate_array = is_multi_array_or_convert( $etd['estimate'] );
	
			foreach ( $etd_estimate_array as $eta ) {
				$output .= '<tr><td>';
				$output .= $etd['destination'];
				$output .= '</td><td>' . $eta['direction'];
				$output .= '</td><td>' . $eta['minutes'];
				$output .= '</td></tr>';
			}   				             
		}
		$output .= '</table>';
	}
	return $output;
}

add_action( 'init', 'ch11tf_init' );

function ch11tf_init() {
	add_action( 'ch11tf_transit_data_update', 'get_transit_data' );
	register_deactivation_hook( __FILE__, 'ch11tf_deactivation' );
	
	if ( !wp_next_scheduled( 'ch11tf_transit_data_update' ) ) { 
		wp_schedule_event( time(), 'five_minutes', 'ch11tf_transit_data_update' );
	}
};

function ch11tf_deactivation() {
	wp_clear_scheduled_hook( 'ch11tf_transit_data_update' );
}

add_filter( 'cron_schedules', 'ch11tf_add_cron_period' );

function ch11tf_add_cron_period( $schedules ) {
	$schedules['five_minutes'] = array(
		'interval' => 300,
		'display' => 'Five Minutes'
	);
	return $schedules;
};