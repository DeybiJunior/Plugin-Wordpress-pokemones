<?php
/**
 *
 * @package Andina_Pokemones
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="pokemon-search-container">
    <form class="pokemon-search-form" id="pokemon-search-form">
        <div class="search-row">
            <div class="search-field">
                <label for="pokemon-search-input">Buscar Pokémon por nombre:</label>
                <input type="text" id="pokemon-search-input" name="search_term" placeholder="Ej: pikachu, charizard, bulbasaur..." />
            </div>
            
            <?php if ($atts['show_filters'] === 'true'): ?>
            <div class="search-field">
                <label for="pokemon-type-filter">Filtrar por tipo:</label>
                <select id="pokemon-type-filter" name="type_filter">
                    <option value="">Todos los tipos</option>
                    <?php 
                    $types = $this->get_pokemon_types();
                    foreach ($types as $type): 
                    ?>
                        <option value="<?php echo esc_attr($type); ?>">
                            <?php echo esc_html(ucfirst($type)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="search-field">
                <label for="pokemon-strength-filter">Filtrar por fortaleza:</label>
                <select id="pokemon-strength-filter" name="strength_filter">
                    <option value="">Todas las fortalezas</option>
                    <?php 
                    $strengths = $this->get_pokemon_strengths();
                    foreach ($strengths as $strength): 
                    ?>
                        <option value="<?php echo esc_attr($strength); ?>">
                            <?php echo esc_html($strength); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="search-field">
                <button type="submit" class="pokemon-search-btn">
                    Buscar
                </button>
                <button type="button" class="pokemon-clear-btn" id="pokemon-clear-btn">
                    Limpiar
                </button>
            </div>
        </div>
    </form>
    
    <div id="pokemon-loading" class="pokemon-loading" style="display: none;">
        🔄 Buscando Pokémon...
    </div>
    
    <div id="pokemon-results" class="pokemon-results-grid">
    </div>
</div>