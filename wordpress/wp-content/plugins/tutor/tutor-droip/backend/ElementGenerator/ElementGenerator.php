<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class ElementGenerator
 *
 * This class is used to define all helper functions.
 */
class ElementGenerator {

	/**
	 * ElementGenerator constructor.
	 */
	public function __construct() {
		add_filter( 'droip_element_generator_' . TDE_APP_PREFIX, array( $this, 'droip_element_generator' ), 10, 2 );
	}

	/**
	 * Droip element generator
	 * This function is used to generate the droip elements
	 *
	 * @param string $string string.
	 * @param array  $props array. //this props contains all the attributes of the droip element.
	 *
	 * @return string html.
	 */
	public function droip_element_generator( $string, $props ) {
		$preview = new Preview( $props );
		return $preview->generate_elements();
	}
}
