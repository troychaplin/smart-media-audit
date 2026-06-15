<?php
namespace WP_Media_Audit\Scanner;

class Meta_Parser {

	/**
	 * Keys registered for postmeta scanning. Filterable.
	 *
	 * Each entry: [ 'key' => meta_key, 'format' => 'serialized'|'json' ]
	 */
	private static function meta_keys(): array {
		$defaults = array(
			array( 'key' => '_elementor_data',   'format' => 'json' ),
			array( 'key' => '_fl_builder_data',  'format' => 'json' ),
		);
		return apply_filters( 'media_audit_scanned_meta_keys', $defaults );
	}

	/**
	 * Walk all registered postmeta keys for a post and return attachment IDs found.
	 *
	 * @param int   $post_id
	 * @param int[] $known_attachment_ids  All attachment IDs on the site (used to validate candidates).
	 * @return int[]
	 */
	public static function extract( int $post_id, array $known_attachment_ids ): array {
		$ids      = array();
		$known    = array_flip( $known_attachment_ids );

		foreach ( self::meta_keys() as $def ) {
			$raw = get_post_meta( $post_id, $def['key'], true );
			if ( empty( $raw ) ) {
				continue;
			}

			if ( 'json' === $def['format'] ) {
				$data = json_decode( $raw, true );
			} else {
				$data = maybe_unserialize( $raw );
			}

			if ( is_array( $data ) || is_object( $data ) ) {
				self::walk_value( $data, $known, $ids );
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * Recursively walk a decoded value looking for integers that are known attachment IDs.
	 */
	private static function walk_value( $value, array $known, array &$ids ): void {
		if ( is_array( $value ) || is_object( $value ) ) {
			foreach ( (array) $value as $k => $v ) {
				// If the key hints at an image field, check the value directly.
				if ( is_string( $k ) && self::is_image_key( $k ) ) {
					$candidate = (int) $v;
					if ( $candidate > 0 && isset( $known[ $candidate ] ) ) {
						$ids[] = $candidate;
						continue;
					}
				}
				self::walk_value( $v, $known, $ids );
			}
		} elseif ( is_numeric( $value ) ) {
			$candidate = (int) $value;
			if ( $candidate > 0 && isset( $known[ $candidate ] ) ) {
				$ids[] = $candidate;
			}
		}
	}

	private static function is_image_key( string $key ): bool {
		$image_keys = array( 'id', 'image_id', 'mediaId', 'bg_image', 'background_image', 'attachment_id' );
		return in_array( $key, $image_keys, true );
	}
}
