<?php

/**
 * Class LP_Helper
 */
defined( 'ABSPATH' ) || exit;

class LP_Helper {
	/**
	 * Shuffle array and keep the keys
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function shuffle_assoc( &$array ) {
		$keys = array_keys( $array );
		shuffle( $keys );
		$new = array();
		foreach ( $keys as $key ) {
			$new[ $key ] = $array[ $key ];
		}
		$array = $new;

		return true;
	}

	/**
	 * Sanitize array by removing empty and/or duplicating values.
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function sanitize_array( $array ) {
		$array = array_filter( $array );
		$array = array_unique( $array );

		return $array;
	}

	/**
	 * MD5 an array.
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public static function array_to_md5( $array ) {
		settype( $array, 'array' );
		ksort( $array );

		return md5( serialize( $array ) );
	}

	public static function cache_posts( $ids ) {
		settype( $ids, 'array' );
		global $wpdb;
		$format = array_fill( 0, sizeof( $ids ), '%d' );
		$query  = $wpdb->prepare( "
			SELECT * FROM {$wpdb->posts} WHERE ID IN(" . join( ',', $format ) . ")
		", $ids );
		if ( $posts = $wpdb->get_results( $query ) ) {
			foreach ( $posts as $post ) {
				wp_cache_set( $post->ID, $post, 'posts' );
			}
		}
	}

	/**
	 * Sort an array by a field.
	 * Having some issue with default PHP usort function.
	 *
	 * @param array  $array
	 * @param string $field
	 * @param int    $default
	 */
	public static function sort_by_priority( &$array, $field = 'priority', $default = 10 ) {
		foreach ( $array as $k => $item ) {
			if ( ! array_key_exists( $field, $item ) ) {
				$array[ $k ][ $field ] = $default;
			}
		}

		$priority = array_unique( wp_list_pluck( $array, $field ) );
		sort( $priority );
		$priority = array_fill_keys( $priority, array() );

		foreach ( $array as $k => $item ) {
			$priority[ $item[ $field ] ][] = $item;
		}

		$sorted = array();
		foreach ( $priority as $item ) {
			$sorted = array_merge( $sorted, $item );
		}
		$array = $sorted;
	}

	/**
	 * Merge two or more classes into one.
	 *
	 * @return array
	 */
	public static function merge_class() {
		if ( func_num_args() == 1 ) {
			return func_get_arg( 0 );
		} elseif ( func_num_args() == 0 ) {
			return null;
		}
		$classes = array();
		foreach ( func_get_args() as $class ) {
			if ( is_string( $class ) ) {
				$cls     = explode( ' ', $class );
				$classes = array_merge( $classes, $cls );
			} else {
				$classes = array_merge( $classes, $class );
			}
		}
		$classes = array_filter( $classes );
		$classes = array_unique( $classes );

		return $classes;
	}

	/**
	 * Sanitize order statuses.
	 * Add prefix lp- into each status if it is not exists.
	 *
	 * @param $statuses
	 *
	 * @return array|mixed
	 */
	public static function sanitize_order_status( &$statuses ) {
		if ( is_array( $statuses ) ) {
			foreach ( $statuses as $k => $status ) {
				if ( false === strpos( $status, 'lp-' ) ) {
					$statuses[ $k ] = "lp-{$status}";
				}
			}
		} else {
			$statuses = preg_split( '#\s+#', $statuses );
			self::sanitize_order_status( $statuses );
			if ( sizeof( $statuses ) == 1 ) {
				$statuses = reset( $statuses );
			}
		}

		return $statuses;
	}

	/**
	 * Merge two arrays recursive.
	 *
	 * @param array $array1
	 * @param array $array2
	 *
	 * @return array
	 */
	public static function array_merge_recursive( & $array1, & $array2 ) {
		$merged = $array1;

		if ( is_array( $array1 ) && is_array( $array2 ) ) {
			foreach ( $array2 as $key => & $value ) {
				if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
					$merged[ $key ] = self::array_merge_recursive( $merged[ $key ], $value );
				} else if ( is_numeric( $key ) ) {
					if ( ! in_array( $value, $merged ) ) {
						$merged[] = $value;
					}
				} else {
					$merged[ $key ] = $value;
				}
			}
		} elseif ( is_array( $array2 ) ) {
			$merged = $array2;
		}

		return $merged;
	}
}