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
 * Class Rsource genrator
 *
 * @package TutorLMSDroip\ElementGenerator
 */
trait ResourcesGenerator {

	/**
	 * Generate Resources elements
	 *
	 * @return string
	 */
	private function generate_resources_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-resources':
				$resources = tutor_utils()->get_attachments();
				$child     = count( $resources ) > 0 ?
					call_user_func( $this->generate_child_element, $this->element['children'][1], $this->options ) :
					call_user_func( $this->generate_child_element, $this->element['children'][0], $this->options );

				return "<div $this->attributes>$child</div>";

			case TDE_APP_PREFIX . '-has-resources':
				$resources = tutor_utils()->get_attachments();
				$html      = '';
				$child     = $this->element['children'][0];
				foreach ( $resources as $key => $value ) {
					$html .= call_user_func(
						$this->generate_child_element_with_new_id,
						$this->elements,
						$this->style_blocks,
						$child,
						array_merge(
							$this->options,
							array(
								'tutor_resource_name' => $value->name,
								'tutor_resource_size_bytes' => round( ( $value->size_bytes / 1024 ), 2 ),
								'tutor_resource_url'  => $value->url,
							)
						)
					);
				}
				return $this->generate_common_element( false, $html );

			case TDE_APP_PREFIX . '-resource-download':
				$children_html = $this->generate_child_elements();
				$url           = $this->options['tutor_resource_url'];
				$name          = $this->options['tutor_resource_name'];

				return "<a href='$url' download='$name' $this->attributes>$children_html</a>";

			case TDE_APP_PREFIX . '-resource-title':
				return $this->generate_common_element( false, $this->options['tutor_resource_name'] );

			case TDE_APP_PREFIX . '-resource-size':
				if ( isset( $this->options['tutor_resource_size_bytes'] ) ) {
					$size_bytes = $this->options['tutor_resource_size_bytes'];
					$settings   = isset( $this->properties['settings'] ) ? $this->properties['settings'] : array();
					$template   = $settings['template'];
					$size       = str_replace( '{{size}}', $this->options['tutor_resource_size_bytes'], $template );

					return "<span $this->attributes>$size</span>";
				}
				return '';

			case TDE_APP_PREFIX . '-no-resources':
				return $this->generate_common_element();

		}
	}
}
