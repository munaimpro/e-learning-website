<?php
/**
 * QnA view generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

use Droip\HelperFunctions as DroipHelperFunctions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class QnAGenerator
 * This class is used to define all QnA functions.
 */
trait QnAGenerator {

	/**
	 * Generate QnA Editor
	 *
	 * @return string
	 */
	private function generate_qna_editor() {
		$comment_id = 0;
		if ( ! empty( $this->options ) && ! empty( $this->options['comment']->comment_ID ) ) {
			$comment_id = $this->options['comment']->comment_ID;
		}

		ob_start();
		wp_editor(
			'',
			'tutor_qna_reply_editor_' . $comment_id,
			tutor_utils()->text_editor_config(
				array(
					'plugins' => 'codesample',
					'tinymce' => array(
						'toolbar1' => 'bold,italic,underline,link,unlink,removeformat,image,bullist,codesample',
						'toolbar2' => '',
						'toolbar3' => '',
					),
				)
			)
		);
		$editor_markup = ob_get_contents();
		ob_end_clean();

		$more_attrs_array = array(
			'name' => $this->element['name'],
		);

		if ( ! empty( $this->options['comment'] ) ) {
			$more_attrs_array['comment_parent'] = $this->options['comment']->comment_ID;
		}

		$more_attrs = '';

		foreach ( $more_attrs_array as $key => $value ) {
			$more_attrs .= "$key=\"$value\"";
		}

		return <<<EOL
            <div $this->attributes $more_attrs>
                $editor_markup
            </div>
        EOL;
	}

	/**
	 * Generate QnA Reply
	 *
	 * @return string
	 */
	private function generate_qna_reply() {
		$templates = $this->detectReplyTemplateByUser();

		$non_current_user_reply_template_id = $templates[0];
		$current_user_reply_template_id     = $templates[1];

		$child = '';

		if ( (int) $this->options['comment']->user_id === get_current_user_ID() ) {
			$child .= call_user_func(
				$this->generate_child_element,
				$current_user_reply_template_id,
				$this->options
			);
		} else {
			$child .= call_user_func(
				$this->generate_child_element,
				$non_current_user_reply_template_id,
				$this->options
			);
		}

		$name = $this->element['name'];

		return <<<EOL
            <div $this->attributes data-ele_name='$name'>
                $child
            </div>
        EOL;
	}

	/**
	 * Generate QnA Reply Button
	 *
	 * @return string
	 */
	private function generate_qna_reply_button() {
		$child = '';

		foreach ( $this->element['children'] as $child_id ) {
			$child .= call_user_func(
				$this->generate_child_element,
				$child_id,
				$this->options
			);
		}

		$name           = $this->element['name'];
		$comment_parent = $this->options['comment']->comment_ID;
		$commentItemId  = $this->options['commentItemId'];

		return <<<EOL
            <a 
                $this->attributes 
                data-ele_name='$name' 
                data-comment_parent='$comment_parent' 
                data-comment_item_id='$commentItemId'
            >
                $child
            </a>
        EOL;
	}

	private function detectReplyTemplateByUser() {

		return array(
			$this->element['children'][0],
			$this->element['children'][1],
		);
	}

	/**
	 * Generate QnA Element
	 *
	 * @param object $comment (new comment)
	 * @param string $elementName (new element name to be generated, this could be comment-item or -qna-reply)
	 * @param array  $collection_data (every collection data which comes with request)
	 *  $collection_data = array(
	 *      'blocks' => array(),
	 *      'styles' => array(),
	 *  );
	 *
	 * @return string as html preview element
	 */
	public static function generateQnAElement( $comment, $elementName, $collection_data ) {
		$blocks = $collection_data['blocks'];
		$styles = $collection_data['styles'];

		$element_id = '';

		foreach ( $blocks as $key => $block ) {
			if ( $elementName === $block['name'] ) {
				$element_id = $key;
				break;
			}
		}

		$preview = DroipHelperFunctions::get_html_using_preview_script(
			$blocks,
			$styles,
			$element_id,
			null,
			array(
				'comment'       => $comment,
				'componentType' => 'comments',
			)
		);

		return $preview;
	}
}
