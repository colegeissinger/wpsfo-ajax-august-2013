<?php
	/*
		Plugin Name: WCSF Example
		Plugin URI: https://slid.es/colegeissinger/wpsfo-08-2013-ajax/
		Description: This is an example of an Ajax script in WordPress. This was built for the WordPress San Francisco Meetup and is by NO MEANS useable except for seeing Ajax in action.
		Author: Cole Geissinger
		Version: 0.1
		Author URI: http://www.colegeissinger.com
	
		License: GPLv2 or later

		Copyright 2013 Cole Geissinger (cole@colegeissinger.com)

		This program is free software; you can redistribute it and/or
		modify it under the terms of the GNU General Public License
		as published by the Free Software Foundation; either version 2
		of the License, or (at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

		References for the WordPress functions used below: #edumacated
		add_action()		 - http://codex.wordpress.org/Function_Reference/add_action
		add_filter()		 - http://codex.wordpress.org/Function_Reference/add_filter
		wp_enqueue_script()  - http://codex.wordpress.org/Function_Reference/wp_enqueue_script
		wp_localize_script() - http://codex.wordpress.org/Function_Reference/wp_localize_script
		wp_verify_nonce()	 - http://codex.wordpress.org/Function_Reference/wp_verify_nonce
		wp_kses_post()		 - http://codex.wordpress.org/Function_Reference/wp_kses_post
		wp_nonce_field()	 - http://codex.wordpress.org/Function_Reference/wp_nonce_field
		the_content()		 - http://codex.wordpress.org/Function_Reference/the_content
	*/


	/**
	 * While OOP can be confusing, it's good practice (in my optinion) for plugin development.
	 * This allows us to code within a special name space and reduce any name conflicts. YAY!
	 *
	 * This code below will add a text field with a submit button at the end of every section using the_content().
	 * The text entered will then be added to the content area found within Twenty Fourteen.
	 * No very practical, nor does the adjustments actually get saved, but the example is there. #shazam
	 */
	class WCSF_Example {

		/**
		 * The construct method. This is where we hook all of our scripts outlined below.
		 */
		public function __construct() {
			// Enqueue our ajax with WordPress
			add_action( 'wp_enqueue_scripts', array( $this, 'add_javascript' ) );

			// Hook our Ajax script with the action we specified in the ajax request
			add_action( 'wp_ajax_wcsf_ajax', array( $this, 'wcsf_ajax' ) );

			// Add our input/button at the end of the_content() function
			add_filter( 'the_content', array( $this, 'add_button' ) );
		}

		/**
		 * Enqueue our JavaScriptssssss
		 */
		public function add_javascript() {
			// Enqueue our jQuery. You never know if an install is loading it!
			wp_enqueue_script( 'jquery' );

			// Call our script that contains the Ajaxy goodness.
			wp_enqueue_script( 'wcsf_exmaple_ajax', plugins_url( 'js/script.js', __FILE__ ) );

			// Although used for translation, this function allows us to load arbitrary JS built with PHP into the head of our WP theme
			// Without modifying the themes header.php :) #magix
			// We only need the admin ajax file in WordPress, so that's all we'll do.
			wp_localize_script( 'wcsf_exmaple_ajax', 'wcsf_ajax', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
		}

		/**
		 * The actual script to process the Ajax request.
		 * This function is called when we pass the "action" key through $_POST and WordPress will map that to the proper add_action().
		 * We'll process and then return the data in JSON form. Make sure you sanitize yo data! Safty first.
		 */
		public function wcsf_ajax() {

			// Check that we requested this from the input field and the nonce is valid. Prevents malicious users.
			if ( isset( $_POST['submission'] ) && $_POST['submission'] && wp_verify_nonce( $_POST['nonce'], 'wcsf-ajax' ) )
				continue;

			echo json_encode( array(
				'body' => wp_kses_post( $_POST['data'] ),
			) );
			die(); // This funciton is REQUIRED within WordPress or else you'll get 'parse' errors because there's a zero at the end of your JSON
		}

		/**
		 * Add our input/button at the end of the_content() function
		 */
		public function add_button() {
			echo '<form id="wcsf-example">';
				echo '<input type="text" class="wcsf-text-field" placeholder="Add your text" value="">';
				echo '<input type="submit" class="wcsf-submit-field" value="Add yo text!" />';
				echo '<input type="hidden" name="wcsf-submitted" value="true" />';
				wp_nonce_field( 'wcsf-ajax', 'wcsf-nonce' ); // Adds our nonce and creates a unique key automatically! #moremagix
			echo '</form>';
		}
	}

	// Initiate our class.
	$wcsf_example = New WCSF_Example();