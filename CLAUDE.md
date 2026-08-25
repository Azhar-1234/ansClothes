# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A classic PHP WordPress theme (no build step, no `package.json`, no test suite) for an
e-commerce storefront, built from the `ANSClothes Homepage.dc.html` design mockup and running
on WooCommerce. There is no CLI to build/lint/test — edit PHP/CSS/JS directly and reload the
site (a Local by Flywheel install) to see changes. `style.css` and `assets/js/main.js` are
enqueued directly with no compilation.

## Architecture

**Dual product-type fallback.** The theme was originally built around a custom `ans_product`
post type + `ans_product_cat` taxonomy (see `archive-ans_product.php`, `single-ans_product.php`,
`inc/customizer.php`), but `inc/post-types.php` no longer registers them — the site now runs on
WooCommerce's native `product` post type / `product_cat` taxonomy instead. Code that queries
products branches on `ansclothes_has_woocommerce()` and picks `'product'` when WooCommerce is
active, `'ans_product'` otherwise (see `template-parts/home/products.php` and
`category-products.php`). When touching product-listing code, always go through this helper
rather than hardcoding a post type.

**Homepage is section-by-section.** `front-page.php` assembles the homepage by including one
file per section from `template-parts/home/` (hero, categories, marquee, products, lookbook,
editorial, about, promises, circle-categories). Every section's copy/toggle is driven by
Customizer settings defined in `inc/customizer.php`; `inc/template-tags.php` holds the default
value for every setting plus shared card-rendering helpers, so a section template is typically
just markup + calls into `template-tags.php`.

**WooCommerce is themed via template overrides**, not hooks alone — `woocommerce/` mirrors
WooCommerce's own template paths (`content-product.php`, `checkout/review-order.php`,
`single-product/tabs/tabs.php`) and WooCommerce loads these instead of its defaults.
`woocommerce.php` (theme root) is the wrapper template for all other WooCommerce pages.

**Checkout has two paths**, both cash-on-delivery focused:
- `inc/checkout.php` strips the full `[woocommerce_checkout]` shortcode page down to
  name/mobile/address + coupon (deliberately not the checkout block — its fields can't be
  removed without breaking validation).
- `inc/cod-checkout.php` is a separate quick "Order Now" modal that creates the WC order
  directly via AJAX, bypassing the checkout page entirely.

**Cart drawer** (`inc/cart-drawer.php`) replaces the header cart link with an off-canvas panel,
kept in sync via WooCommerce's `wc-cart-fragments` script; `ansCart` (localized in
`functions.php`) carries the ajax URL/nonce it needs.

**Size filtering** (`inc/product-filters.php`) is custom because size is stored as a local
(non-global) product attribute with inconsistent naming (`size` vs `length` across products) —
WooCommerce's built-in layered nav widget only works with global taxonomy attributes, so this
can't be swapped for a core widget without first normalizing the attribute data.

**Product fields**: `inc/meta-boxes.php` defines `_ans_price`, `_ans_badge`
(New / Featured / Pre-Order), `_ans_sizes` (comma-separated) — these are legacy `ans_product`
meta fields; WooCommerce products use WC's own price/attribute system instead, so check which
post type a code path targets before assuming which fields apply.

**Load order**: `functions.php` requires, in order: `template-tags.php`, `post-types.php`,
`meta-boxes.php`, `customizer.php`, `product-filters.php`, `single-product.php`,
`cart-drawer.php`, `cod-checkout.php`, `checkout.php`.

## Content editing map

| Homepage part | Edited in |
| --- | --- |
| Announcement bar, hero, marquee, lookbook, "The Fit", promises, about, footer text | Appearance → Customize → ANSClothes Theme |
| Category cards | Products → Collections (or `product_cat` terms) |
| New arrivals + shop grid | Products list |
| Header / footer links | Appearance → Menus (Primary / Footer) |
| Footer columns | Appearance → Widgets → Footer |

## Reference files

`ANSClothes Homepage.dc.html`, `support.js`, `image-slot.js` in the theme root are the original
static design mockup — WordPress never loads them; they exist only as a visual reference for
what a section is supposed to look like.
