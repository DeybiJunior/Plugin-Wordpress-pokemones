<?php
/**
 *
 * @package Andina_Pokemones
 */

class Andina_Pokemon_CPT {

    public function register_post_type() {
        $labels = array(
            'name'                  => 'Pokémon',
            'singular_name'         => 'Pokémon',
            'menu_name'             => 'Pokémon',
            'name_admin_bar'        => 'Pokémon',
            'archives'              => 'Archivo de Pokémon',
            'attributes'            => 'Atributos del Pokémon',
            'parent_item_colon'     => 'Pokémon padre:',
            'all_items'             => 'Todos los Pokémon',
            'add_new_item'          => 'Agregar nuevo Pokémon',
            'add_new'               => 'Agregar nuevo',
            'new_item'              => 'Nuevo Pokémon',
            'edit_item'             => 'Editar Pokémon',
            'update_item'           => 'Actualizar Pokémon',
            'view_item'             => 'Ver Pokémon',
            'view_items'            => 'Ver Pokémon',
            'search_items'          => 'Buscar Pokémon',
            'not_found'             => 'No se encontraron Pokémon',
            'not_found_in_trash'    => 'No se encontraron Pokémon en la papelera',
            'featured_image'        => 'Imagen destacada',
            'set_featured_image'    => 'Establecer imagen destacada',
            'remove_featured_image' => 'Remover imagen destacada',
            'use_featured_image'    => 'Usar como imagen destacada',
            'insert_into_item'      => 'Insertar en Pokémon',
            'uploaded_to_this_item' => 'Subido a este Pokémon',
            'items_list'            => 'Lista de Pokémon',
            'items_list_navigation' => 'Navegación de lista de Pokémon',
            'filter_items_list'     => 'Filtrar lista de Pokémon',
        );

        $args = array(
            'label'                 => 'Pokémon',
            'description'           => 'Custom Post Type para Pokémon',
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies'            => array('tipo', 'fortaleza'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-pets',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type('pokemones', $args);
    }

    public function register_taxonomies() {
        $tipo_labels = array(
            'name'                       => 'Tipos',
            'singular_name'              => 'Tipo',
            'menu_name'                  => 'Tipos',
            'all_items'                  => 'Todos los tipos',
            'parent_item'                => 'Tipo padre',
            'parent_item_colon'          => 'Tipo padre:',
            'new_item_name'              => 'Nuevo nombre de tipo',
            'add_new_item'               => 'Agregar nuevo tipo',
            'edit_item'                  => 'Editar tipo',
            'update_item'                => 'Actualizar tipo',
            'view_item'                  => 'Ver tipo',
            'separate_items_with_commas' => 'Separar tipos con comas',
            'add_or_remove_items'        => 'Agregar o remover tipos',
            'choose_from_most_used'      => 'Elegir de los más usados',
            'popular_items'              => 'Tipos populares',
            'search_items'               => 'Buscar tipos',
            'not_found'                  => 'No encontrado',
            'no_terms'                   => 'No hay tipos',
            'items_list'                 => 'Lista de tipos',
            'items_list_navigation'      => 'Navegación de lista de tipos',
        );

        $tipo_args = array(
            'labels'                     => $tipo_labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );

        register_taxonomy('tipo', array('pokemones'), $tipo_args);

        $fortaleza_labels = array(
            'name'                       => 'Fortalezas',
            'singular_name'              => 'Fortaleza',
            'menu_name'                  => 'Fortalezas',
            'all_items'                  => 'Todas las fortalezas',
            'parent_item'                => 'Fortaleza padre',
            'parent_item_colon'          => 'Fortaleza padre:',
            'new_item_name'              => 'Nuevo nombre de fortaleza',
            'add_new_item'               => 'Agregar nueva fortaleza',
            'edit_item'                  => 'Editar fortaleza',
            'update_item'                => 'Actualizar fortaleza',
            'view_item'                  => 'Ver fortaleza',
            'separate_items_with_commas' => 'Separar fortalezas con comas',
            'add_or_remove_items'        => 'Agregar o remover fortalezas',
            'choose_from_most_used'      => 'Elegir de los más usados',
            'popular_items'              => 'Fortalezas populares',
            'search_items'               => 'Buscar fortalezas',
            'not_found'                  => 'No encontrado',
            'no_terms'                   => 'No hay fortalezas',
            'items_list'                 => 'Lista de fortalezas',
            'items_list_navigation'      => 'Navegación de lista de fortalezas',
        );

        $fortaleza_args = array(
            'labels'                     => $fortaleza_labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );

        register_taxonomy('fortaleza', array('pokemones'), $fortaleza_args);
    }
}