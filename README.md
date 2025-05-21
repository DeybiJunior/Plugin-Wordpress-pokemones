# Pokémon CPT + Filtro API
Plugin de WordPress para gestionar y buscar Pokémon, integrado con la PokéAPI.

## 🚀 Características

### ✅ Custom Post Type y Taxonomías
- CPT y Taxonomías: CPT "pokemones", taxonomías "tipo" y "fortaleza".
- Integración PokéAPI: Consumo dinámico de https://pokeapi.co para búsqueda, filtrado y datos de Pokémon.
- Frontend Interactivo: Shortcode [buscar_pokemones] con búsqueda en tiempo real, filtros y resultados en tarjetas con AJAX.
- Panel Administrativo: Página para importar automáticamente los primeros 20 Pokémon.

## 📦 Estructura del Plugin

```
andina-pokemones/
├── andina-pokemones.php          # Archivo principal
├── inc/
│   ├── class-pokemon-cpt.php     # Registro CPT y taxonomías
│   ├── class-pokemon-api.php     # Consumo PokéAPI
│   ├── class-pokemon-shortcode.php # Shortcode y AJAX
│   └── class-pokemon-admin.php   # Panel administrativo
├── templates/
│   ├── pokemon-search-form.php   # Formulario de búsqueda
│   └── pokemon-card.php          # Tarjeta de Pokémon
├── assets/
│   ├── css/pokemon-style.css     # Estilos CSS
│   └── js/pokemon-script.js      # JavaScript interactivo
└── README.md                     # Esta documentación
```

# 👨‍💻 Autor
Deybi Junior Ruiz Marquina
Website: https://deybijunior.github.io
Versión: 1.0.0

# 📄 Licencia
Este plugin está licenciado bajo GPL v2 o posterior.