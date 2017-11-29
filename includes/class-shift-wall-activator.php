<?php

/**
 * Fired during plugin activation
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Shift_Wall
 * @subpackage Shift_Wall/includes
 * @author     Marvin Ayaay <marvz73@gmail.com>
 */
class Shift_Wall_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		//Capability
	    $capability = get_role( 'subscriber' );
	    
	    
		$capability->add_cap( 'publish_shift_walls' ); 
	    $capability->add_cap( 'edit_shift_walls' ); 
	    $capability->add_cap( 'edit_published_shift_walls' ); 
	    $capability->add_cap( 'delete_shift_walls' ); 
	    $capability->add_cap( 'delete_published_shift_walls' ); 



	}

}
