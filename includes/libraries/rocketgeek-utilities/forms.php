<?php

if ( ! function_exists( 'rktgk_get' ) ):
/**
 * Utility function to validate $_POST, $_GET, and $_REQUEST.
 *
 * While this function retrieves data, remember that the data should generally be
 * sanitized or escaped depending on how it is used.
 *
 * @since 1.0.0
 *
 * @param  string $tag     The form field or query string.
 * @param  string $default The default value (optional).
 * @param  string $type    post|get|request (optional).
 * @return string 
 */
function rktgk_get( $tag, $default = '', $type = 'post' ) {
	switch ( $type ) {
		case 'get':
			return ( isset( $_GET[ $tag ] ) ) ? $_GET[ $tag ] : $default;
			break;
		case 'request':
			return ( isset( $_REQUEST[ $tag ] ) ) ? $_REQUEST[ $tag ] : $default;
			break;
		default: // case 'post':
			return ( isset( $_POST[ $tag ] ) ) ? $_POST[ $tag ] : $default;
			break;
	}
}
endif;

if ( ! function_exists( 'rktgk_sanitize_class' ) ):
/**
 * Sanitizes classes passed to the WP-Members form building functions.
 *
 * This generally uses just sanitize_html_class() but allows for 
 * whitespace so multiple classes can be passed (such as "regular-text code").
 *
 * @since 1.0.0
 *
 * @param	string $class
 * @return	string sanitized_class
 */
function rktgk_sanitize_class( $class ) {
	// If no whitespace, just return WP sanitized class.
	if ( ! strpos( $class, ' ' ) ) {
		return sanitize_html_class( $class );
	} else {
		// Break string by whitespace, sanitize individual class names.
		$class_array = explode( ' ', $class );
		$len = count( $class_array ); $i = 0;
		$sanitized_class = '';
		foreach ( $class_array as $single_class ) {
			$sanitized_class .= sanitize_html_class( $single_class );
			$sanitized_class .= ( $i == $len - 1 ) ? '' : ' ';
			$i++;
		}
		return $sanitized_class;
	}
}
endif;

if ( ! function_exists( 'rktgk_sanitize_array' ) ):
/**
 * Sanitizes the text in an array.
 *
 * @since 1.0.0
 *
 * @param  array  $data
 * @param  string $type The data type integer|int (default: false)
 * @return array  $data
 */
function rktgk_sanitize_array( $data, $type = false ) {
	if ( is_array( $data ) ) {
		foreach( $data as $key => $val ) {
			$data[ $key ] = ( 'integer' == $type || 'int' == $type ) ? intval( $val ) : sanitize_text_field( $val );
		}
	}
	return $data;
}
endif;

if ( ! function_exists( 'rktgk_sanitize_field' ) ):
/**
 * Sanitizes field based on field type.
 *
 * Obviously, this isn't an all inclusive function of every WordPress
 * sanitization function. It is intended to handle sanitization of 
 * WP-Members form input and therefore includes the necessary methods
 * that would relate to the WP-Members custom field types and can thus
 * be used by looping through form data when the WP-Members fields are
 * handled and validated.
 *
 * @since 1.0.0
 *
 * @param  string $data
 * @param  string $type
 * @return string $sanitized_data
 */
function rktgk_sanitize_field( $data, $type = '' ) {

	switch ( $type ) {

		case 'multiselect':
		case 'multicheckbox':
		case 'multipleselect':
		case 'multiplecheckbox':
			$sanitized_data = rktgk_sanitize_array( $data );
			break;

		case 'textarea':
			$sanitized_data = sanitize_textarea_field( $data );
			break;

		case 'email':
			$sanitized_data = sanitize_email( $data );
			break;

		case 'file':
		case 'image':
			$sanitized_data = sanitize_file_name( $data );
			break;

		case 'int':
		case 'integer':
		case 'number':
			$sanitized_data = intval( $data );
			break;

		default:
			$sanitized_data = sanitize_text_field( $data );
			break;	
	}

	return $sanitized_data;
}
endif;