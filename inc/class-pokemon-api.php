<?php
/**
 *
 * @package Andina_Pokemones
 */

class Andina_Pokemon_API {

    /**
     *
     * @var string
     */
    private $base_url = 'https://pokeapi.co/api/v2/';

    /**
     *
     * @var array
     */
    private $cache = array();

    /**
     *
     * @param string $name 
     * @return array|false 
     */
    public function get_pokemon_by_name($name) {
        if (empty($name)) {
            return false;
        }

        $name = sanitize_text_field(strtolower($name));
        
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $url = $this->base_url . 'pokemon/' . $name;
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'WordPress-Pokemon-Plugin/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['name'])) {
            return false;
        }

        $pokemon_data = $this->format_pokemon_data($data);
        $this->cache[$name] = $pokemon_data;
        
        return $pokemon_data;
    }

    /**
     *
     * @param int $limit 
     * @param int $offset
     * @return array 
     */
    public function get_pokemon_list($limit = 20, $offset = 0) {
        $url = $this->base_url . 'pokemon?limit=' . intval($limit) . '&offset=' . intval($offset);
        
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'WordPress-Pokemon-Plugin/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['results'])) {
            return array();
        }

        $pokemon_list = array();
        foreach ($data['results'] as $pokemon) {
            $pokemon_data = $this->get_pokemon_by_name($pokemon['name']);
            if ($pokemon_data) {
                $pokemon_list[] = $pokemon_data;
            }
        }

        return $pokemon_list;
    }

    /**
     *
     * @param array $raw_data 
     * @return array
     */
    private function format_pokemon_data($raw_data) {
        $types = array();
        if (isset($raw_data['types'])) {
            foreach ($raw_data['types'] as $type) {
                $types[] = $type['type']['name'];
            }
        }

        $abilities = array();
        if (isset($raw_data['abilities'])) {
            foreach ($raw_data['abilities'] as $ability) {
                $abilities[] = $ability['ability']['name'];
            }
        }

        $stats = array();
        if (isset($raw_data['stats'])) {
            foreach ($raw_data['stats'] as $stat) {
                $stats[$stat['stat']['name']] = $stat['base_stat'];
            }
        }
        $fortalezas = $this->determine_strengths($stats);

        return array(
            'id' => $raw_data['id'],
            'name' => ucfirst($raw_data['name']),
            'types' => $types,
            'abilities' => $abilities,
            'stats' => $stats,
            'fortalezas' => $fortalezas,
            'height' => $raw_data['height'],
            'weight' => $raw_data['weight'],
            'sprite' => $raw_data['sprites']['front_default'] ?? '',
            'sprite_official' => $raw_data['sprites']['other']['official-artwork']['front_default'] ?? ''
        );
    }

    /**
     *
     * @param array $stats 
     * @return array 
     */
    private function determine_strengths($stats) {
        $fortalezas = array();
        
        if (empty($stats)) {
            return $fortalezas;
        }
        arsort($stats);
        $top_stats = array_slice($stats, 0, 2, true);

        foreach ($top_stats as $stat_name => $value) {
            switch ($stat_name) {
                case 'attack':
                    $fortalezas[] = 'Ataque';
                    break;
                case 'defense':
                    $fortalezas[] = 'Defensa';
                    break;
                case 'special-attack':
                    $fortalezas[] = 'Ataque Especial';
                    break;
                case 'special-defense':
                    $fortalezas[] = 'Defensa Especial';
                    break;
                case 'speed':
                    $fortalezas[] = 'Velocidad';
                    break;
                case 'hp':
                    $fortalezas[] = 'Resistencia';
                    break;
            }
        }

        return array_unique($fortalezas);
    }

    /**
     *
     * @param string $type 
     * @return array 
     */
    public function get_pokemon_by_type($type) {
        $type = sanitize_text_field(strtolower($type));
        $url = $this->base_url . 'type/' . $type;
        
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'WordPress-Pokemon-Plugin/1.0'
            )
        ));

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['pokemon'])) {
            return array();
        }

        $pokemon_list = array();
        $count = 0;
        foreach ($data['pokemon'] as $pokemon_data) {
            if ($count >= 10) break;
            
            $pokemon = $this->get_pokemon_by_name($pokemon_data['pokemon']['name']);
            if ($pokemon) {
                $pokemon_list[] = $pokemon;
                $count++;
            }
        }

        return $pokemon_list;
    }
}