# P5 FAQ

Plugin de WordPress para gestionar FAQs mediante un Custom Post Type y un shortcode con soporte de Schema.org (FAQPage).

## Instalación

1. Copia la carpeta `p5faq` en `wp-content/plugins/`.
2. Activa el plugin desde el panel de administración de WordPress.

Opcional (WP-CLI):

```zsh
wp plugin activate p5faq
```

## Uso

1. En el admin, crea un nuevo post del tipo **FAQ**.
2. Añade grupos de **Pregunta** y **Respuesta** usando el botón "Agregar Pregunta".
3. Inserta el FAQ en cualquier contenido con el shortcode:

```text
[faq id="123"]
```

Reemplaza `123` por el ID del post FAQ.

## Atributos del Shortcode

- `id` (obligatorio): ID del post FAQ.
- `schema` (opcional, por defecto `true`): activa/desactiva la impresión del JSON-LD FAQPage.
- `title` (opcional, por defecto `true`): incluye el título del post como `name` en el JSON-LD.

Ejemplos:

```text
[faq id="123"]
[faq id="123" schema="false"]
[faq id="123" schema="true" title="false"]
```

## Esquema (JSON-LD)

- Se genera un objeto `FAQPage` con cada pregunta como `Question` y su `acceptedAnswer`.
- El JSON-LD se imprime en `wp_footer` y se deduplica por ID para evitar duplicados si insertas el mismo FAQ varias veces.

## Estilos y comportamiento

- `assets/style.css`: estilos del listado y acordeón.
- `assets/script.js`: lógica de toggle para mostrar/ocultar respuestas.
- `assets/admin.js`: administración de grupos en el editor (agregar/eliminar, reindexar).

## Recomendaciones

- Valida el marcado con la herramienta de resultados enriquecidos de Google.
- Evita preguntas vacías; el shortcode las omite automáticamente.

## Roadmap (opcional)

- Ordenar preguntas por drag & drop.
- Taxonomías para categorizar FAQs.
- Parámetros de shortcode para filtrar o limitar.
