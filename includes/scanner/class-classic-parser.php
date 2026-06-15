<?php
namespace WP_Media_Audit\Scanner;

class Classic_Parser {

	/**
	 * Extract attachment IDs from classic HTML content and shortcodes.
	 *
	 * @param string $post_content
	 * @return int[]
	 */
	public static function extract( string $post_content ): array {
		$ids = array();

		// wp-image-{id} class on <img> tags.
		if ( preg_match_all( '/class=["\'][^"\']*wp-image-(\d+)[^"\']*["\']/', $post_content, $matches ) ) {
			foreach ( $matches[1] as $id ) {
				$ids[] = (int) $id;
			}
		}

		// [gallery ids="1,2,3"] shortcode.
		$ids = array_merge( $ids, self::parse_shortcode( $post_content, 'gallery', 'ids' ) );

		// [caption id="attachment_123"] shortcode.
		if ( preg_match_all( '/\[caption[^\]]*\bid="attachment_(\d+)"/', $post_content, $matches ) ) {
			foreach ( $matches[1] as $id ) {
				$ids[] = (int) $id;
			}
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	private static function parse_shortcode( string $content, string $tag, string $attr ): array {
		$ids     = array();
		$pattern = get_shortcode_regex( array( $tag ) );

		if ( preg_match_all( "/{$pattern}/", $content, $matches ) ) {
			foreach ( $matches[3] as $attr_string ) {
				$attrs = shortcode_parse_atts( $attr_string );
				if ( ! empty( $attrs[ $attr ] ) ) {
					foreach ( explode( ',', $attrs[ $attr ] ) as $id ) {
						$id = (int) trim( $id );
						if ( $id > 0 ) {
							$ids[] = $id;
						}
					}
				}
			}
		}

		return $ids;
	}
}
