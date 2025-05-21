<?php
/**
 * @package Andina_Pokemones
 */

class Andina_Pokemon_Admin {

    /**
     * @var Andina_Pokemon_API
     */
    private $pokemon_api;

    public function __construct() {
        $this->pokemon_api = new Andina_Pokemon_API();
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=pokemones',
            'Importar Pokémon',
            'Importar desde API',
            'manage_options',
            'pokemon-import',
            array($this, 'import_page')
        );
    }

    public function import_page() {
        ?>
        <div class="wrap">
            <h1>Importar Pokémon desde PokéAPI</h1>
            <p>Esta herramienta importará los primeros 20 Pokémon desde la PokéAPI y los creará como entradas del CPT.</p>
            
            <div id="import-status" style="margin: 20px 0;"></div>
            
            <button id="import-pokemon-btn" class="button button-primary">
                Importar Primeros 20 Pokémon
            </button>
            
            <div id="import-progress" style="display: none; margin-top: 20px;">
                <div class="progress-bar" style="width: 100%; background-color: #f0f0f0; height: 20px; border-radius: 10px;">
                    <div id="progress-fill" style="width: 0%; background-color: #0073aa; height: 100%; border-radius: 10px; transition: width 0.3s;"></div>
                </div>
                <p id="progress-text">Importando...</p>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#import-pokemon-btn').click(function() {
                var button = $(this);
                var statusDiv = $('#import-status');
                var progressDiv = $('#import-progress');
                var progressFill = $('#progress-fill');
                var progressText = $('#progress-text');
                
                button.prop('disabled', true).text('Importando...');
                statusDiv.html('');
                progressDiv.show();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'import_pokemon',
                        nonce: '<?php echo wp_create_nonce('import_pokemon_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            statusDiv.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                            progressFill.css('width', '100%');
                            progressText.text('¡Importación completada!');
                        } else {
                            statusDiv.html('<div class="notice notice-error"><p>Error: ' + response.data.message + '</p></div>');
                        }
                    },
                    error: function() {
                        statusDiv.html('<div class="notice notice-error"><p>Error de conexión</p></div>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Importar Primeros 20 Pokémon');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function import_pokemon() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'import_pokemon_nonce')) {
            wp_send_json_error(array('message' => 'Seguridad: Nonce inválido'));
        }

        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'No tienes permisos para realizar esta acción'));
        }

        $imported_count = 0;
        $errors = array();

        $pokemon_list = $this->pokemon_api->get_pokemon_list(20, 0);

        if (empty($pokemon_list)) {
            wp_send_json_error(array('message' => 'No se pudieron obtener los datos de la API'));
        }

        foreach ($pokemon_list as $pokemon_data) {
            $result = $this->create_pokemon_post($pokemon_data);
            if ($result) {
                $imported_count++;
            } else {
                $errors[] = 'Error al importar: ' . $pokemon_data['name'];
            }
        }

        $message = "Se importaron $imported_count Pokémon correctamente.";
        if (!empty($errors)) {
            $message .= ' Errores: ' . implode(', ', $errors);
        }

        wp_send_json_success(array('message' => $message, 'count' => $imported_count));
    }

    /**
     * @param array $pokemon_data 
     * @return int|false 
     */
    private function create_pokemon_post($pokemon_data) {
        $existing_post = get_posts(array(
            'post_type' => 'pokemones',
            'meta_query' => array(
                array(
                    'key' => '_pokemon_id',
                    'value' => $pokemon_data['id'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));

        if (!empty($existing_post)) {
            return false; 
        }

        $post_data = array(
            'post_title' => $pokemon_data['name'],
            'post_content' => $this->generate_pokemon_content($pokemon_data),
            'post_status' => 'publish',
            'post_type' => 'pokemones',
            'meta_input' => array(
                '_pokemon_id' => $pokemon_data['id'],
                '_pokemon_height' => $pokemon_data['height'],
                '_pokemon_weight' => $pokemon_data['weight'],
                '_pokemon_sprite' => $pokemon_data['sprite'],
                '_pokemon_sprite_official' => $pokemon_data['sprite_official'],
                '_pokemon_stats' => json_encode($pokemon_data['stats']),
                '_pokemon_abilities' => json_encode($pokemon_data['abilities'])
            )
        );

        $post_id = wp_insert_post($post_data);

        if (!$post_id || is_wp_error($post_id)) {
            return false;
        }

        $this->assign_pokemon_taxonomies($post_id, $pokemon_data);

        return $post_id;
    }

    /**
     *
     * @param array $pokemon_data 
     * @return string 
     */
    private function generate_pokemon_content($pokemon_data) {
        $content = '<h3>Información Básica</h3>';
        $content .= '<p><strong>ID:</strong> ' . $pokemon_data['id'] . '</p>';
        $content .= '<p><strong>Altura:</strong> ' . ($pokemon_data['height'] / 10) . ' m</p>';
        $content .= '<p><strong>Peso:</strong> ' . ($pokemon_data['weight'] / 10) . ' kg</p>';
        
        if (!empty($pokemon_data['sprite_official'])) {
            $content .= '<p><img src="' . esc_url($pokemon_data['sprite_official']) . '" alt="' . esc_attr($pokemon_data['name']) . '" style="max-width: 200px;"></p>';
        }

        $content .= '<h3>Habilidades</h3>';
        $content .= '<ul>';
        foreach ($pokemon_data['abilities'] as $ability) {
            $content .= '<li>' . ucfirst(str_replace('-', ' ', $ability)) . '</li>';
        }
        $content .= '</ul>';

        $content .= '<h3>Estadísticas</h3>';
        $content .= '<ul>';
        foreach ($pokemon_data['stats'] as $stat_name => $value) {
            $stat_display = ucfirst(str_replace('-', ' ', $stat_name));
            $content .= '<li><strong>' . $stat_display . ':</strong> ' . $value . '</li>';
        }
        $content .= '</ul>';

        return $content;
    }

    /**
     *
     * @param int $post_id 
     * @param array $pokemon_data 
     */
    private function assign_pokemon_taxonomies($post_id, $pokemon_data) {
        if (!empty($pokemon_data['types'])) {
            $type_terms = array();
            foreach ($pokemon_data['types'] as $type) {
                $term = get_term_by('slug', $type, 'tipo');
                if (!$term) {
                    $term_data = wp_insert_term(
                        ucfirst($type),
                        'tipo',
                        array('slug' => $type)
                    );
                    if (!is_wp_error($term_data)) {
                        $type_terms[] = $term_data['term_id'];
                    }
                } else {
                    $type_terms[] = $term->term_id;
                }
            }
            if (!empty($type_terms)) {
                wp_set_object_terms($post_id, $type_terms, 'tipo');
            }
        }

        if (!empty($pokemon_data['fortalezas'])) {
            $strength_terms = array();
            foreach ($pokemon_data['fortalezas'] as $fortaleza) {
                $term = get_term_by('name', $fortaleza, 'fortaleza');
                if (!$term) {
                    $term_data = wp_insert_term($fortaleza, 'fortaleza');
                    if (!is_wp_error($term_data)) {
                        $strength_terms[] = $term_data['term_id'];
                    }
                } else {
                    $strength_terms[] = $term->term_id;
                }
            }
            if (!empty($strength_terms)) {
                wp_set_object_terms($post_id, $strength_terms, 'fortaleza');
            }
        }
    }
}