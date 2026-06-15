<?php
namespace WP_Media_Audit\Scanner;

class Block_Parser {

	/** Map of block name → attribute key(s) holding attachment IDs. */
	const BLOCK_MAP = array(
		'core/image'      => array( 'id' ),
		'core/cover'      => array( 'id' ),
		'core/file'       => array( 'id' ),
		'core/video'      => array( 'id' ),
		'core/audio'      => array( 'id' ),
		'core/media-text' => array( 'mediaId' ),
		'core/gallery'    => array( 'ids' ),
	);

	/**
	 * Parse all blocks (including innerBlocks) and return attachment IDs.
	 *
	 * @param string $post_content
	 * @return int[]
	 */
	public static function extract( string $post_content ): array {
		if ( ! function_exists( 'parse_blocks' ) ) {
			return array();
		}

		$blocks = parse_blocks( $post_content );
		$ids    = array();
		self::walk( $blocks, $ids );
		return array_values( array_unique( array_filter( $ids ) ) );
	}

	private static function walk( array $blocks, array &$ids ): void {
		foreach ( $blocks as $block ) {
			$name  = $block['blockName'] ?? '';
			$attrs = $block['attrs'] ?? array();

			if ( isset( self::BLOCK_MAP[ $name ] ) ) {
				foreach ( self::BLOCK_MAP[ $name ] as $key ) {
					if ( ! isset( $attrs[ $key ] ) ) {
						continue;
					}
					$val = $attrs[ $key ];
					if ( is_array( $val ) ) {
						foreach ( $val as $id ) {
							$ids[] = (int) $id;
						}
					} elseif ( is_numeric( $val ) && $val > 0 ) {
						$ids[] = (int) $val;
					}
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				self::walk( $block['innerBlocks'], $ids );
			}
		}
	}
}
