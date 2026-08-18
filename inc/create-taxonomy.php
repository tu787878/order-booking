<?php 
//create tour taxonomy 
function create_book_booking_cat() {
    $labels = array(
    	'name' => 'Produkte Kategorie',
        'singular' => 'Produkte Kategorie',
        'menu_name' => 'Produkte Kategorie'
    );
    $capabilities = [
        'manage_terms' => 'manage_product-cats',
        'edit_terms' => 'edit_product-cats',
        'delete_terms' => 'delete_product-cats',
        'assign_terms' => 'assign_product-cats'
    ];
    $args = array(
        "label" => __( "Produkte Kategorie", "noo" ),
    	'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        "show_in_menu"               => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        "query_var"                  => true,
        "show_in_rest"               => true,
        "rest_base"                  => "product-cat",
        "rest_controller_class"      => "WP_REST_Terms_Controller",
        "show_in_quick_edit"         => true,
        'supports' => array( 'thumbnail' ),
        'capabilities' => $capabilities,
        // 'map_meta_cap' => true
    );
    register_taxonomy('product-cat', ['product'], $args);
 
}
// Hook into the 'init' action
add_action( 'init', 'create_book_booking_cat', 0 );

function the_term_image_taxonomy( $taxonomy ) {
    // use for tags instead of categories
    return 'product-cat';
}
add_filter( 'taxonomy-term-image-taxonomy', 'the_term_image_taxonomy' );

/**
 * Add a drag handle to the product category table for category managers.
 */
function dsmart_product_cat_order_column( $columns ) {
    if ( ! current_user_can( 'manage_product-cats' ) ) {
        return $columns;
    }

    $ordered_columns = array();
    foreach ( $columns as $key => $label ) {
        $ordered_columns[ $key ] = $label;
        if ( 'cb' === $key ) {
            $ordered_columns['dsmart_product_cat_order'] = '<span class="dashicons dashicons-move" title="' . esc_attr__( 'Reorder', 'order-booking' ) . '"></span>';
        }
    }

    return $ordered_columns;
}
add_filter( 'manage_edit-product-cat_columns', 'dsmart_product_cat_order_column' );

function dsmart_product_cat_order_column_content( $content, $column_name, $term_id ) {
    if ( 'dsmart_product_cat_order' !== $column_name || ! current_user_can( 'manage_product-cats' ) ) {
        return $content;
    }

    return '<span class="dashicons dashicons-move dsmart-product-cat-order-handle" data-term-id="' . esc_attr( $term_id ) . '" title="' . esc_attr__( 'Drag to reorder', 'order-booking' ) . '"></span>';
}
add_filter( 'manage_product-cat_custom_column', 'dsmart_product_cat_order_column_content', 10, 3 );

/**
 * Enable drag-and-drop only on the product-cat term list.
 */
function dsmart_product_cat_order_scripts( $hook ) {
    if ( 'edit-tags.php' !== $hook || ! current_user_can( 'manage_product-cats' ) ) {
        return;
    }

    $taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';
    if ( 'product-cat' !== $taxonomy ) {
        return;
    }

    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_register_style( 'dsmart-product-cat-order', false );
    wp_enqueue_style( 'dsmart-product-cat-order' );

    $config = array(
        'nonce' => wp_create_nonce( 'dsmart-product-cat-order' ),
        'error' => __( 'The product category order could not be saved.', 'order-booking' ),
    );

    wp_add_inline_script(
        'jquery-ui-sortable',
        'jQuery(function($){var table=$("#the-list");if(!table.length){return;}table.sortable({items:"> tr:not(.inline-edit-row)",handle:".dsmart-product-cat-order-handle",axis:"y",cursor:"move",helper:function(e,row){row.children().each(function(){var cell=$(this);cell.width(cell.width());});return row;},update:function(){var rows=table.sortable("toArray");var ids=[];$.each(rows,function(_,rowId){var match=String(rowId).match(/^tag-(\\d+)$/);if(match){ids.push(parseInt(match[1],10));}});table.addClass("dsmart-order-saving");$.post(' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ',{action:"dsmart_update_product_cat_order",nonce:' . wp_json_encode( $config['nonce'] ) . ',term_ids:ids}).fail(function(){window.alert(' . wp_json_encode( $config['error'] ) . ');window.location.reload();}).always(function(){table.removeClass("dsmart-order-saving");});}});});'
    );

    wp_add_inline_style( 'dsmart-product-cat-order', '.column-dsmart_product_cat_order{width:42px;text-align:center}.dsmart-product-cat-order-handle{cursor:move;color:#646970}.dsmart-product-cat-order-handle:hover{color:#2271b1}#the-list.dsmart-order-saving{opacity:.55;pointer-events:none}' );
}
add_action( 'admin_enqueue_scripts', 'dsmart_product_cat_order_scripts' );

/**
 * Persist the visible product category order in the term_order column supplied
 * by the taxonomy ordering plugin.
 */
function dsmart_update_product_cat_order() {
    check_ajax_referer( 'dsmart-product-cat-order', 'nonce' );

    if ( ! current_user_can( 'manage_product-cats' ) ) {
        wp_send_json_error( array( 'message' => __( 'You cannot reorder product categories.', 'order-booking' ) ), 403 );
    }

    $term_ids = isset( $_POST['term_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['term_ids'] ) ) : array();
    $term_ids = array_values( array_unique( array_filter( $term_ids ) ) );
    if ( empty( $term_ids ) ) {
        wp_send_json_error( array( 'message' => __( 'No product categories were supplied.', 'order-booking' ) ), 400 );
    }

    global $wpdb;
    $term_order_column = $wpdb->get_var( "SHOW COLUMNS FROM {$wpdb->terms} LIKE 'term_order'" );
    if ( ! $term_order_column ) {
        wp_send_json_error( array( 'message' => __( 'Category ordering is not available.', 'order-booking' ) ), 500 );
    }

    foreach ( $term_ids as $position => $term_id ) {
        $term = get_term( $term_id, 'product-cat' );
        if ( ! $term || is_wp_error( $term ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product category.', 'order-booking' ) ), 400 );
        }

        $wpdb->update(
            $wpdb->terms,
            array( 'term_order' => $position ),
            array( 'term_id' => $term_id ),
            array( '%d' ),
            array( '%d' )
        );
    }

    clean_term_cache( $term_ids, 'product-cat' );
    wp_send_json_success();
}
add_action( 'wp_ajax_dsmart_update_product_cat_order', 'dsmart_update_product_cat_order' );

