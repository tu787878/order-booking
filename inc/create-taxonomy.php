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

