/**
 *
 * @package Andina_Pokemones
 */

jQuery(document).ready(function($) {
    
    const $searchForm = $('#pokemon-search-form');
    const $searchInput = $('#pokemon-search-input');
    const $typeFilter = $('#pokemon-type-filter');
    const $strengthFilter = $('#pokemon-strength-filter');
    const $clearBtn = $('#pokemon-clear-btn');
    const $loading = $('#pokemon-loading');
    const $results = $('#pokemon-results');
    
    $searchForm.on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    let searchTimeout;
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if ($searchInput.val().length >= 3 || $searchInput.val().length === 0) {
                performSearch();
            }
        }, 500);
    });
    
    $typeFilter.on('change', performSearch);
    $strengthFilter.on('change', performSearch);
    
    $clearBtn.on('click', function() {
        $searchInput.val('');
        $typeFilter.val('');
        $strengthFilter.val('');
        $results.html('');
    });
    
    function performSearch() {
        const searchTerm = $searchInput.val().trim();
        const typeFilter = $typeFilter.val();
        const strengthFilter = $strengthFilter.val();
        
        // No buscar si no hay criterios
        if (!searchTerm && !typeFilter && !strengthFilter) {
            $results.html('');
            return;
        }
        
        $loading.show();
        $results.html('');
        
        const ajaxData = {
            action: 'search_pokemon',
            search_term: searchTerm,
            type_filter: typeFilter,
            strength_filter: strengthFilter,
            nonce: pokemon_ajax.nonce
        };
        
        $.ajax({
            url: pokemon_ajax.ajax_url,
            type: 'POST',
            data: ajaxData,
            timeout: 30000,
            success: function(response) {
                $loading.hide();
                
                if (response.success && response.data.html) {
                    $results.html(response.data.html);
                    
                    $results.find('.pokemon-card').each(function(index) {
                        $(this).delay(index * 100).fadeIn(300);
                    });
                } else {
                    $results.html('<div class="pokemon-no-results">❌ No se encontraron Pokémon con los criterios especificados.</div>');
                }
            },
            error: function(xhr, status, error) {
                $loading.hide();
                
                let errorMessage = 'Error de conexión';
                if (status === 'timeout') {
                    errorMessage = 'La búsqueda tardó demasiado tiempo. Inténtalo de nuevo.';
                } else if (xhr.status === 0) {
                    errorMessage = 'Sin conexión a internet';
                } else {
                    errorMessage = 'Error del servidor: ' + xhr.status;
                }
                
                $results.html('<div class="pokemon-no-results">⚠️ ' + errorMessage + '</div>');
                console.error('Error AJAX:', error, xhr);
            }
        });
    }
    

    function loadInitialPokemon() {
        $loading.show();
        
        $.ajax({
            url: pokemon_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'search_pokemon',
                search_term: '',
                type_filter: '',
                strength_filter: '',
                nonce: pokemon_ajax.nonce
            },
            success: function(response) {
                $loading.hide();
                if (response.success && response.data.html) {
                    $results.html(response.data.html);
                    
                    $results.find('.pokemon-card').each(function(index) {
                        $(this).delay(index * 100).fadeIn(300);
                    });
                }
            },
            error: function() {
                $loading.hide();
                $results.html('<div class="pokemon-no-results">🎯 Usa el buscador para encontrar Pokémon</div>');
            }
        });
    }
    
    loadInitialPokemon();
    

    $(document).on('click', '.pokemon-details-btn', function(e) {
        e.preventDefault();
        const pokemonName = $(this).data('pokemon');
        
        if (pokemonName) {
            alert('Detalles de ' + pokemonName + ' (funcionalidad pendiente)');
        }
    });
    
    function scrollToResults() {
        if ($results.find('.pokemon-card').length > 6) {
            $('html, body').animate({
                scrollTop: $results.offset().top - 100
            }, 300);
        }
    }
    
    const originalPerformSearch = performSearch;
    performSearch = function() {
        originalPerformSearch();
        setTimeout(scrollToResults, 1000);
    };

    $searchForm.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
        
        if (e.key === 'Escape') {
            $clearBtn.click();
        }
    });

    const $searchBtn = $('.pokemon-search-btn');
    const originalSearchBtnText = $searchBtn.text();
    
    function setSearchBtnLoading(loading) {
        if (loading) {
            $searchBtn.prop('disabled', true).text('🔄 Buscando...');
        } else {
            $searchBtn.prop('disabled', false).text(originalSearchBtnText);
        }
    }
    
    const originalAjax = $.ajax;
    const pokemonAjax = function(options) {
        if (options.data && options.data.action === 'search_pokemon') {
            setSearchBtnLoading(true);
            
            const originalSuccess = options.success || function() {};
            const originalError = options.error || function() {};
            const originalComplete = options.complete || function() {};
            
            options.success = function(response) {
                originalSuccess(response);
            };
            
            options.error = function(xhr, status, error) {
                originalError(xhr, status, error);
            };
            
            options.complete = function(xhr, status) {
                setSearchBtnLoading(false);
                originalComplete(xhr, status);
            };
        }
        
        return originalAjax(options);
    };
    
    window.pokemonAjax = pokemonAjax;
});


window.PokemonUtils = {
    

    capitalize: function(str) {
        return str.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    },
    

    slugify: function(str) {
        return str.toLowerCase()
                  .replace(/[^a-z0-9 -]/g, '')
                  .replace(/\s+/g, '-')
                  .replace(/-+/g, '-');
    },
    

    getTypeColor: function(type) {
        const colors = {
            normal: '#A8A878',
            fighting: '#C03028',
            flying: '#A890F0',
            poison: '#A040A0',
            ground: '#E0C068',
            rock: '#B8A038',
            bug: '#A8B820',
            ghost: '#705898',
            steel: '#B8B8D0',
            fire: '#F08030',
            water: '#6890F0',
            grass: '#78C850',
            electric: '#F8D030',
            psychic: '#F85888',
            ice: '#98D8D8',
            dragon: '#7038F8',
            dark: '#705848',
            fairy: '#EE99AC'
        };
        
        return colors[type] || '#68A090';
    }
};