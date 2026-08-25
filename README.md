# ANSClothes — WordPress Theme

A classic (PHP) WordPress theme built from the `ANSClothes Homepage.dc.html` design mockup.
No page builder and no plugin dependency — everything is native WordPress.

## Activate

1. WP Admin → **Appearance → Themes → ANSClothes → Activate**.
2. **Settings → Reading → Your homepage displays**: pick *A static page* and choose a page
   (e.g. "Home"). The storefront layout renders through `front-page.php` either way, so
   this step is only needed if you also want editable content under the sections.
3. **Settings → Permalinks → Save** (once) so `/shop/` and `/product/…` URLs resolve.

## Where content comes from

| Homepage part | Edited in |
| --- | --- |
| Announcement bar, hero, marquee, lookbook, "The Fit", promises, about, footer text | **Appearance → Customize → ANSClothes Theme** |
| Category cards | **Products → Collections** (name, *Count Label*, *Card Image*) |
| New arrivals + shop grid | **Products** (title, featured image, *Price*, *Badge*, *Sizes*) |
| Logo | **Appearance → Customize → Site Identity → Logo** (falls back to `assets/logo.png`) |
| Header / footer links | **Appearance → Menus** → *Primary Menu* / *Footer Menu* |
| Footer columns | **Appearance → Widgets → Footer** |

Until you publish real products and collections, the homepage and `/shop/` fall back to the
sample items from the original design, so the site never looks empty.

## Products

- Post type: `ans_product` — archive at `/shop/`, single at `/product/<slug>/`.
- Taxonomy: `ans_product_cat` ("Collections") — archive at `/collection/<slug>/`.
- Post meta: `_ans_price` (number only), `_ans_badge` (New / Featured / Pre-Order), `_ans_sizes`
  (comma separated). The currency symbol is a single Customizer setting (`৳` by default).
- Ordering uses **Page Attributes → Order** first, then newest date.

## File map

```
style.css                    Theme header + all styles (design tokens at the top)
functions.php                Setup, menus, widgets, enqueues
inc/template-tags.php        Defaults for every setting + card renderers
inc/post-types.php           Product post type & Collections taxonomy
inc/meta-boxes.php           Product fields + collection term fields
inc/customizer.php           Customizer panel (field map at the top)
front-page.php               Storefront homepage
template-parts/home/*.php    One file per homepage section
archive-ans_product.php      Shop grid (also used for collections)
single-ans_product.php       Product page + related products
index/archive/search/…       Blog, pages, search, 404, comments
assets/js/main.js            Mobile nav + scroll reveal
assets/js/admin.js           Media picker for collection images
```

`ANSClothes Homepage.dc.html`, `support.js` and `image-slot.js` are the original design
source files. WordPress ignores them; keep them for reference or delete them.

## Customizing the look

Colors, spacing and fonts are CSS custom properties in the `:root` block at the top of
`style.css` — change `--ans-ink`, `--ans-surface`, `--ans-gutter`, `--ans-section` etc. and the
whole theme follows.
