<?php
/**
 * The WP_Members Forms Class.
 *
 * @package WP-Members
 * @subpackage WP_Members Forms Object Class
 * @since 3.1.0
 */

class WP_Members_Forms {

	/**
	 * Plugin initialization function.
	 *
	 * @since 3.1.0
	 */
	function __construct() {

	}
	
	
	/**
	 * Creates form fields
	 *
	 * Creates various form fields and returns them as a string.
	 *
	 * @since 3.1.0
	 *
	 * @param array  $args {
	 *     @type string $name
	 *     @type string $type
	 *     @type string $value
	 *     @type string $valtochk
	 *     @type string $class
	 * }
	 * @return string $str The field returned as a string.
	 */
	function create_form_field( $args ) {
		
		$name     = $args['name'];
		$type     = $args['type'];
		$value    = $args['value'];
		$valtochk = $args['valtochk'];
		$class    = ( isset( $args['class'] ) ) ? $args['class'] : 'textbox';
		$required = ( isset( $args['required'] ) ) ? $args['required'] : false;
	
		switch ( $type ) {
			
		case "url":
		case "email":
			$class = ( $class == 'textbox' ) ? "textbox" : $class;
			$str = "<input name=\"$name\" type=\"$type\" id=\"$name\" value=\"$value\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . " />";
			break;
		
		case "file":
			$class = ( $class == 'textbox' ) ? "file" : $class;
			$str = "<input name=\"$name\" type=\"$type\" id=\"$name\" value=\"$value\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "checkbox":
			$class = ( $class == 'textbox' ) ? "checkbox" : $class;
			$str = "<input name=\"$name\" type=\"$type\" id=\"$name\" value=\"$value\"" . checked( $value, $valtochk, false ) . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "text":
			$value = stripslashes( esc_attr( $value ) );
			$str = "<input name=\"$name\" type=\"$type\" id=\"$name\" value=\"$value\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "textarea":
			$value = stripslashes( esc_textarea( $value ) );
			$class = ( $class == 'textbox' ) ? "textarea" : $class;
			$str = "<textarea cols=\"20\" rows=\"5\" name=\"$name\" id=\"$name\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . ">$value</textarea>";
			break;
	
		case "password":
			$str = "<input name=\"$name\" type=\"$type\" id=\"$name\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . " />";
			break;
	
		case "hidden":
			$str = "<input name=\"$name\" type=\"$type\" value=\"$value\" />";
			break;
	
		case "option":
			$str = "<option value=\"$value\" " . selected( $value, $valtochk, false ) . " >$name</option>";
			break;
	
		case "select":
			$class = ( $class == 'textbox' ) ? "dropdown" : $class;
			$str = "<select name=\"$name\" id=\"$name\" class=\"$class\"" . ( ( $required ) ? " required " : "" ) . ">\n";
			foreach ( $value as $option ) {
				$pieces = explode( '|', $option );
				$str = $str . "<option value=\"$pieces[1]\"" . selected( $pieces[1], $valtochk, false ) . ">" . __( $pieces[0], 'wp-members' ) . "</option>\n";
			}
			$str = $str . "</select>";
			break;
			
		case "radio":
			$class = ( $class == 'textbox' ) ? "radio" : $class;
			$str = '';
			$num = 1;
			foreach ( $value as $option ) {
				$pieces = explode( '|', $option );
				$id = $name . '_' . $num;
				$str = $str . "<input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$pieces[1]\"" . checked( $pieces[1], $valtochk, false ) . ( ( $required ) ? " required " : " " ) . ">" . __( $pieces[0], 'wp-members' ) . "<br />\n";
				$num++;
			}
			break;		
	
		} 
	
		return $str;
	} // End create_form_field()
	
	
	/**
	 * Uploads file from the user.
	 *
	 * @since 3.1.0
	 *
	 * @param  array    $file
	 * @return int|bool
	 */
	function do_file_upload( $file = array() ) {
	
		// Get WordPress file upload processing scripts.
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		$file_return = wp_handle_upload( $file, array( 'test_form' => false ) );
	
		if ( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
			return false;
		} else {
	
			$filename = $file_return['file'];
	
			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'guid'           => $file_return['url'],
			);
	
			$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
	
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
	
			if ( 0 < intval( $attachment_id ) ) {
				// Returns an array with file information.
				return $attachment_id;
			}
		}
	
		return false;
	} // End upload_file()
	
	
	/**
	 * Function to validate uploaded files.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args
	 */
	function upload_file_validate( $args ) {
		
		global $wpmem_themsg;

		$required_file_fields = array( 
			'upload_1' => 'Field Display Name',
		);
		
		foreach ( $required_file_fields as $key => $val ) {
			if ( empty( $_FILES[ $key ]['name'] ) ) {
				$wpmem_themsg = "Sorry, $val is a required field.";
			}
		}
		return;
	} // End upload_file_validate()
	
} // End of WP_Members_Utilties class.