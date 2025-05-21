# Guía de Implementación Técnica
Esta guía detalla la implementación técnica del plugin "Pokémon CPT + Filtro API" para desarrolladores.

# 🏗️ Arquitectura
Patrones: Singleton implícito, Separación de Responsabilidades, Factory, Observer.

# 🚀 Instalación
1. Prerrequisitos: WordPress 5.0+ y PHP 7.0+.
2. Subir Plugin: Copiar andina-pokemones/ a /wp-content/plugins/ o subir ZIP vía Admin.
3. Activar: En "Plugins" de WordPress, activar "Pokémon CPT + Filtro API".
4. Verificar: Aparecerá el menú "Pokémon" en el admin.

# 🔧 Configuración Inicial
1. Importar Pokémon: Ir a "Pokémon" > "Importar desde API" y hacer clic en "Importar Primeros 20 Pokémon".
2. Verificar CPTs: Revisar "Pokémon" > "Todos los Pokémon" y sus taxonomías.

# 📝 Uso del Shortcode
Básico: [buscar_pokemones]
Con Parámetros: [buscar_pokemones show_filters="true" results_per_page="12"]
show_filters: "true" o "false".
results_per_page: número.

# 📱 Funcionalidades
- Implementadas: CPT "Pokémon", Taxonomías, Integración PokéAPI, Búsqueda/Filtro en tiempo real, Diseño responsive, Caché.
- Búsqueda: Por nombre, por tipo, por fortaleza.