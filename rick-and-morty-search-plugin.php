<?php
/**
 * Plugin Name: Rick and Morty Search Plugin
 * Description: A Rick and Morty search bar that shows data from the characters of the show.
 * Version: 1.0
 * Author: Gabriel Mavarez
 * Author URI: http://gabrielmavarez.com/
 */


function rick_and_morty() {
    ob_start();
    ?>
    <form id="rick-and-morty-form" class="rick-and-morty-form">
        <input type="text" id="rick-and-morty-query" class="rick-and-morty-query" placeholder="Wubba Lubba dub-dub!">
        <button type="submit" class="rick-and-morty-submit">
            <img src="<?php echo plugins_url( 'search-icon.png', __FILE__ ); ?>" alt="Search">
        </button>
    </form>
    <div id="rick-and-morty-query-results"></div>
    <?php
    return ob_get_clean();
}

add_shortcode('rick-and-morty-search', 'rick_and_morty');

function enqueue_scripts() {
    wp_enqueue_script(
        'script-js',
        plugins_url( 'script.js', __FILE__ ),
        array( 'jquery' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'script.js' ),
        true
    );

    wp_enqueue_style(
        'styles-css',
        plugins_url( 'styles.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'styles.css' )
    );

    wp_localize_script( 'script-js', 'queryParams', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'rick-and-morty-nonce' )
    ) );
}

add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

function ajax_handler() {
    if ( !isset($_GET['nonce']) || !wp_verify_nonce( $_GET['nonce'], 'rick-and-morty-nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
        exit;
    }

    $query = sanitize_text_field( $_GET['query'] );

    $response = wp_remote_get( 'https://rickandmortyapi.com/api/character/?name=' . urlencode( $query ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Error fetching data: ' . $response->get_error_message() );
    } else {
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            wp_send_json_error( 'Error decoding JSON: ' . json_last_error_msg() );
        } else {
            wp_send_json_success( $data );
        }
    }
}

add_action( 'wp_ajax_script', 'ajax_handler' );
add_action( 'wp_ajax_nopriv_script', 'ajax_handler' );
