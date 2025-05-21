<?php
/**
 *
 * @package Andina_Pokemones
 */

class Andina_Pokemon_Shortcode {

    /**
     *
     * @var Andina_Pokemon_API
     */
    private $pokemon_api;

    public function __construct() {
        $this->pokemon_api = new Andina_Pokemon_API();
    }

    /**
     *
     * @param array $atts 
     * @return string 
     */
    public function render_search_form($atts = array()) {
        $atts = shortcode_atts(array(
            'show_filters' => 'true',
            'results_per_page' => '12',
        ), $atts, 'buscar_pokemones');

        ob_start();
        include ANDINA_POKEMON_PATH . 'templates/pokemon-search-form.php';
        return ob_get_clean();
    }

    public function ajax_search_pokemon() {
        if (!wp_verify_nonce($_POST['nonce'], 'pokemon_search_nonce')) {
            wp_die('Seguridad: Nonce inválido');
        }

        $search_term = sanitize_text_field($_POST['search_term'] ?? '');
        $type_filter = sanitize_text_field($_POST['type_filter'] ?? '');
        $strength_filter = sanitize_text_field($_POST['strength_filter'] ?? '');

        $pokemon_results = array();

        if (!empty($search_term)) {
            $pokemon = $this->pokemon_api->get_pokemon_by_name($search_term);
            if ($pokemon) {
                $pokemon_results[] = $pokemon;
            }
        } elseif (!empty($type_filter)) {
            $pokemon_results = $this->pokemon_api->get_pokemon_by_type($type_filter);
        } else {
            $pokemon_results = $this->pokemon_api->get_pokemon_list(12, rand(0, 100));
        }

        if (!empty($strength_filter) && !empty($pokemon_results)) {
            $pokemon_results = array_filter($pokemon_results, function($pokemon) use ($strength_filter) {
                return in_array($strength_filter, $pokemon['fortalezas']);
            });
        }

        $html = '';
        if (!empty($pokemon_results)) {
            foreach ($pokemon_results as $pokemon) {
                $html .= $this->generate_pokemon_card($pokemon);
            }
        } else {
            $html = '<div class="pokemon-no-results">No se encontraron Pokémon con los criterios especificados.</div>';
        }

        wp_send_json_success(array('html' => $html));
    }

    /**
     *
     * @param array $pokemon 
     * @return string 
     */
    private function generate_pokemon_card($pokemon) {
        $sprite_url = !empty($pokemon['sprite_official']) ? $pokemon['sprite_official'] : $pokemon['sprite'];
        
        ob_start();
        include ANDINA_POKEMON_PATH . 'templates/pokemon-card.php';
        return ob_get_clean();
    }

    /**
     *
     * @return array 
     */
    public function get_pokemon_types() {
        return array(
            'normal', 'fighting', 'flying', 'poison', 'ground', 'rock',
            'bug', 'ghost', 'steel', 'fire', 'water', 'grass',
            'electric', 'psychic', 'ice', 'dragon', 'dark', 'fairy'
        );
    }

    /**
     *
     * @return array 
     */
    public function get_pokemon_strengths() {
        return array(
            'Ataque', 'Defensa', 'Ataque Especial', 
            'Defensa Especial', 'Velocidad', 'Resistencia'
        );
    }
}