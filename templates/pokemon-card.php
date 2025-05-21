<?php
/**
 *
 * @package Andina_Pokemones
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="pokemon-card">
    <div class="pokemon-card-header">
        <h3 class="pokemon-name"><?php echo esc_html($pokemon['name']); ?></h3>
        <span class="pokemon-id">#<?php echo esc_html(str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT)); ?></span>
    </div>
    
    <div class="pokemon-image">
        <?php if (!empty($sprite_url)): ?>
            <img src="<?php echo esc_url($sprite_url); ?>" alt="<?php echo esc_attr($pokemon['name']); ?>" />
        <?php else: ?>
            <div class="pokemon-no-image">Sin imagen</div>
        <?php endif; ?>
    </div>
    
    <div class="pokemon-info">
        <div>
            <strong>Tipos:</strong>
            <div class="pokemon-types">
                <?php if (!empty($pokemon['types'])): ?>
                    <?php foreach ($pokemon['types'] as $type): ?>
                        <span class="pokemon-type type-<?php echo esc_attr($type); ?>">
                            <?php echo esc_html(ucfirst($type)); ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="pokemon-type type-normal">Normal</span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($pokemon['fortalezas'])): ?>
        <div>
            <strong>Fortalezas:</strong>
            <div class="pokemon-strengths">
                <?php foreach ($pokemon['fortalezas'] as $fortaleza): ?>
                    <span class="pokemon-strength">
                        <?php echo esc_html($fortaleza); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="pokemon-physical">
            <span><strong>Altura:</strong> <?php echo esc_html(number_format($pokemon['height'] / 10, 1)); ?> m</span>
            <span><strong>Peso:</strong> <?php echo esc_html(number_format($pokemon['weight'] / 10, 1)); ?> kg</span>
        </div>
        
        <?php if (!empty($pokemon['abilities'])): ?>
        <div>
            <strong>Habilidades:</strong>
            <ul class="abilities-list">
                <?php foreach (array_slice($pokemon['abilities'], 0, 3) as $ability): ?>
                    <li><?php echo esc_html(ucfirst(str_replace('-', ' ', $ability))); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($pokemon['stats'])): ?>
        <div>
            <strong>Stats destacadas:</strong>
            <div style="font-size: 12px; color: #666; margin-top: 5px;">
                <?php 
                $top_stats = array_slice($pokemon['stats'], 0, 3, true);
                foreach ($top_stats as $stat_name => $value): 
                    $stat_display = ucfirst(str_replace('-', ' ', $stat_name));
                ?>
                    <span style="margin-right: 10px;">
                        <strong><?php echo esc_html($stat_display); ?>:</strong> <?php echo esc_html($value); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>