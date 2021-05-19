<?php
/**
 * Adds and modyfies the admin columns for the post type.
 *
 * @package kebbet-cpt-publication
 */

namespace kebbet\cpt\publication\admincolumns;

use const kebbet\cpt\publication\POSTTYPE;

/**
 * Column orders (set image first)
 *
 * @param array $columns The columns in the table.
 * @return array $columns The columns, in the new order.
 */
function column_order( $columns ) {
	$n_columns = array();
	// Move thumbnail to before title column.
	$before = 'title';

	foreach ( $columns as $key => $value ) {
		if ( $key === $before ) {
			$n_columns['thumbnail'] = '';
		}
		$n_columns[ $key ] = $value;
	}
	return $n_columns;
}
add_filter( 'manage_' . POSTTYPE . '_posts_columns', __NAMESPACE__ . '\column_order' );

/**
 * Add additional admin column.
 *
 * @param array $columns The existing columns.
 */
function set_admin_column_list( $columns ) {
	$columns['modified']  = __( 'Last modified', 'kebbet-cpt-publication' );
	$columns['thumbnail'] = __( 'Featured image', 'kebbet-cpt-publication' );
	return $columns;
}
add_filter( 'manage_' . POSTTYPE . '_posts_columns', __NAMESPACE__ . '\set_admin_column_list' );

/**
 * Add data to each row.
 *
 * @param string $column The column slug.
 * @param int    $post_id The post ID for the row.
 */
function populate_custom_columns( $column, $post_id ) {
	
	if ( 'modified' === $column ) {
		$format   = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$modified = get_the_modified_date( $format );
		if ( $modified ) {
			echo $modified;
		}
	}
	if ( 'thumbnail' === $column ) {
		$thumbnail = get_the_post_thumbnail(
			$post_id,
			'thumbnail',
			array(
				'style' => 'max-width: 80px; height: auto;',
			)
		);
		if ( $thumbnail ) {
			echo $thumbnail;
		} else {
			echo __( 'No image is set.', 'kebbet-cpt-publication' );
		}
	}
}
add_action( 'manage_' . POSTTYPE . '_posts_custom_column', __NAMESPACE__ . '\populate_custom_columns', 10, 2 );

/**
 * Make additional admin column sortable.
 *
 * @param array $columns The existing columns.
 */
function define_admin_sortable_columns( $columns ) {
	$columns['modified'] = 'modified';
	return $columns;
}
add_filter( 'manage_edit-' . POSTTYPE . '_sortable_columns', __NAMESPACE__ . '\define_admin_sortable_columns' );
