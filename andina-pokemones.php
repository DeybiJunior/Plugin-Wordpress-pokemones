<?php
/**
 * Plugin Name: Pokémon CPT + Filtro API
 * Plugin URI: https://deybijunior.github.io
 * Description: Plugin que registra un Custom Post Type para Pokémon con integración a PokéAPI y funcionalidad de búsqueda y filtros.
 * Version: 1.0.0
 * Author: Deybi Junior Ruiz Marquina
 * Author URI: https://deybijunior.github.io
 * Text Domain: andina-pokemones
 */

if (!defined('WPINC')) {
    die;
}

define('ANDINA_POKEMON_VERSION', '1.0.0');
define('ANDINA_POKEMON_PATH', plugin_dir_path(__FILE__));
define('ANDINA_POKEMON_URL', plugin_dir_url(__FILE__));

function activate_andina_pokemones() {
    flush_rewrite_rules();
}

function deactivate_andina_pokemones() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'activate_andina_pokemones');
register_deactivation_hook(__FILE__, 'deactivate_andina_pokemones');

require_once ANDINA_POKEMON_PATH . 'inc/class-pokemon-cpt.php';
require_once ANDINA_POKEMON_PATH . 'inc/class-pokemon-api.php';
require_once ANDINA_POKEMON_PATH . 'inc/class-pokemon-shortcode.php';
require_once ANDINA_POKEMON_PATH . 'inc/class-pokemon-admin.php';

function andina_pokemon_enqueue_scripts() {
    wp_enqueue_style(
        'andina-pokemon-style',
        ANDINA_POKEMON_URL . 'assets/css/pokemon-style.css',
        array(),
        ANDINA_POKEMON_VERSION
    );
    
    wp_enqueue_script(
        'andina-pokemon-script',
        ANDINA_POKEMON_URL . 'assets/js/pokemon-script.js',
        array('jquery'),
        ANDINA_POKEMON_VERSION,
        true
    );
    
    wp_localize_script('andina-pokemon-script', 'pokemon_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pokemon_search_nonce')
    ));
}

function run_andina_pokemones() {
    $pokemon_cpt = new Andina_Pokemon_CPT();
    $pokemon_api = new Andina_Pokemon_API();
    $pokemon_shortcode = new Andina_Pokemon_Shortcode();
    $pokemon_admin = new Andina_Pokemon_Admin();
    
    add_action('init', array($pokemon_cpt, 'register_post_type'));
    add_action('init', array($pokemon_cpt, 'register_taxonomies'));
    
    add_action('wp_enqueue_scripts', 'andina_pokemon_enqueue_scripts');
    
    add_shortcode('buscar_pokemones', array($pokemon_shortcode, 'render_search_form'));
    
    add_action('wp_ajax_search_pokemon', array($pokemon_shortcode, 'ajax_search_pokemon'));
    add_action('wp_ajax_nopriv_search_pokemon', array($pokemon_shortcode, 'ajax_search_pokemon'));
    
    add_action('admin_menu', array($pokemon_admin, 'add_admin_menu'));
    add_action('wp_ajax_import_pokemon', array($pokemon_admin, 'import_pokemon'));
}

run_andina_pokemones();