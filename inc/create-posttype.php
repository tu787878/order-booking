<?php 
//create posttype Tour
function book_custom_post_type() {
	//register Product post type
    $labels = array(
        'name'                => _x( 'Produkte', 'Post Type General Name', 'order-booking' ),
        'singular_name'       => _x( 'Produkte', 'Post Type Singular Name', 'order-booking' ),
        'menu_name'           => __( 'TCG Restaurant SHOP', 'order-booking' ),
        'parent_item_colon'   => __( '', 'order-booking' ),
        'all_items'           => __( 'Produkte', 'order-booking' ),
        'view_item'           => __( 'View Produkte', 'order-booking' ),
        'add_new_item'        => __( 'Add Produkte', 'order-booking' ),
        'add_new'             => __( 'Neues Produkt hinzufügen', 'order-booking' ),
        'edit_item'           => __( 'Edit', 'order-booking' ),
        'update_item'         => __( 'Update', 'order-booking' ),
        'search_items'        => __( 'Search', 'order-booking' ),
        'not_found'           => __( 'Not found', 'order-booking' ),
        'not_found_in_trash'  => __( 'Not found', 'order-booking' ),
    );
    $capabilities = [
        'read_post'                 => 'read_product',
        'edit_post'                 => 'edit_product',
        'delete_post'               => 'delete_product',        
        'edit_posts'                => 'edit_products',
        'edit_others_posts'         => 'edit_others_products',
        'publish_posts'             => 'publish_products',
        'delete_posts'              => 'delete_products',
        'delete_published_posts'    => 'delete_published_products', 
        'delete_others_posts'       => 'delete_others_products', 
        'edit_published_posts'      => 'edit_published_products', 
    ];
    $args = array(
        'label'                 => __( 'Produkte', 'order-booking' ),
        'description'           => __( 'Neues Produkt hinzufügen', 'order-booking' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
        'taxonomies'            => array( 'product-cat' ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 5,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        "query_var"             => true,
        'capability_type'       => 'post',
        "capabilities"          => $capabilities, 
    );

    register_post_type( 'product', $args );
    //register Coupon post type
    $labels1 = array(
        'name'                => _x( 'Coupon', 'Post Type General Name', 'order-booking' ),
        'singular_name'       => _x( 'Coupon', 'Post Type Singular Name', 'order-booking' ),
        'menu_name'           => __( 'Coupon', 'order-booking' ),
        'parent_item_colon'   => __( '', 'order-booking' ),
        'all_items'           => __( 'Coupon', 'order-booking' ),
        'view_item'           => __( 'View coupon', 'order-booking' ),
        'add_new_item'        => __( 'Add new coupon', 'order-booking' ),
        'add_new'             => __( 'Add new', 'order-booking' ),
        'edit_item'           => __( 'Edit', 'order-booking' ),
        'update_item'         => __( 'Update', 'order-booking' ),
        'search_items'        => __( 'Search', 'order-booking' ),
        'not_found'           => __( 'Not found', 'order-booking' ),
        'not_found_in_trash'  => __( 'Not found', 'order-booking' ),
    );
    $args1 = array(
        'label'               => __( 'coupon', 'order-booking' ),
        'description'         => __( 'Add new coupon', 'order-booking' ),
        'labels'              => $labels1,
        'supports'            => array( 'title', 'thumbnail' ),
        'taxonomies'          => array( '' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=product',
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        "capabilities"        => $capabilities, 
    );
    register_post_type( 'coupon', $args1 );
    //register Order post type
    $labels2 = array(
        'name'                => _x( 'Bestellungen', 'Post Type General Name', 'order-booking' ),
        'singular_name'       => _x( 'Bestellungen', 'Post Type Singular Name', 'order-booking' ),
        'menu_name'           => __( 'Bestellungen', 'order-booking' ),
        'parent_item_colon'   => __( '', 'order-booking' ),
        'all_items'           => __( 'Bestellungen', 'order-booking' ),
        'view_item'           => __( 'View order', 'order-booking' ),
        'add_new_item'        => __( 'Add new order', 'order-booking' ),
        'add_new'             => __( 'Add new', 'order-booking' ),
        'edit_item'           => __( 'Edit', 'order-booking' ),
        'update_item'         => __( 'Update', 'order-booking' ),
        'search_items'        => __( 'Search', 'order-booking' ),
        'not_found'           => __( 'Not found', 'order-booking' ),
        'not_found_in_trash'  => __( 'Not found', 'order-booking' ),
    );
    $args2 = array(
        'label'               => __( 'orders', 'order-booking' ),
        'description'         => __( 'Add new order', 'order-booking' ),
        'labels'              => $labels2,
        'supports'            => array( 'title', 'thumbnail' ),
        'taxonomies'          => array(),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=product',
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        "capabilities"        => $capabilities, 
    );
    register_post_type( 'orders', $args2 );
}
add_action( 'init', 'book_custom_post_type', 0 );

//add file single tours
add_filter('single_template', 'book_tour_template');
function book_tour_template($single) {
    global $post;
    if ( $post->post_type == 'product' ) {
        $plugin_dir = ABSPATH . 'wp-content/plugins/order-booking/';
        if ( file_exists( $plugin_dir . 'templates/single-product.php' ) ) {
            return $plugin_dir . 'templates/single-product.php';
        }
    }
    if ( $post->post_type == 'orders' ) {
        $plugin_dir = ABSPATH . 'wp-content/plugins/order-booking/';
        if ( file_exists( $plugin_dir . 'templates/single-orders.php' ) ) {
            return $plugin_dir . 'templates/single-orders.php';
        }
    }
    return $single;
}


function my_author_filter(){
        $screen = get_current_screen();
		global $wp_query;
		if ($screen->post_type == 'product') {			
			$terms = get_terms( array(
			'taxonomy' => 'product-cat',
			'hide_empty' => false,
			) );

			?>

			<select name="category_filter">
				<option value="">Alle Kategorien</option>
				<?php foreach ($terms as $obj): ?>
					<option value="<?php echo $obj->slug  ?>" <?php echo ( isset($_GET['category_filter']) ? ($_GET['category_filter'] == $obj->slug  ? 'selected' : '') : '' ) ?>><?php echo $obj->name; ?></option>
				<?php endforeach; ?>
			</select>	
			<?php
		}
}
add_action('restrict_manage_posts','my_author_filter');

function my_author_filter_results($query){
    $screen = get_current_screen();
    global $post_type; 
    if ($screen->post_type == 'product') {

        if(isset($_GET['category_filter']) && $_GET['category_filter'] !== ""){

           $taxquery = array(
                array(
                    'taxonomy' => 'product-cat',
                    'terms' => $_GET['category_filter'],
                    'field' => 'slug',
                )
            );

            $query->set( 'tax_query', $taxquery );
        }
    }
}
add_action('pre_get_posts','my_author_filter_results');