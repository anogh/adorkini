# CSS Development Guide for Ador Kini Theme

> **IMPORTANT:** This document explains the exact CSS architecture and override patterns for this WooCommerce theme. Always follow these rules when making style changes.

---

## ⚠️ CRITICAL: CSS Override Hierarchy

### 1. Inline Styles in PHP Files Override External Stylesheet

**The theme uses inline `<style>` blocks in PHP files that take precedence over `style.css`.**

**Location of key inline styles:**
- `front-page.php` (lines ~398-475): Mobile product card styles, price styling, add-to-cart button styles
- `header.php` (lines ~46-127): Preloader, visibility overrides, webview detection
- Various page templates have their own inline `<style>` blocks

**Rule:** When making mobile or product-specific CSS changes, **update BOTH locations:**
1. `style.css` (external stylesheet)
2. `front-page.php` inline `<style>` block (for product grid styles)

---

## 📱 Mobile Breakpoint: 1023px (NOT 768px)

**The primary mobile/desktop split point is `1023px`, not the standard `768px`.**

```css
/* CORRECT - Use this */
@media (max-width: 1023px) {
    /* Mobile styles */
}

/* ALSO USED - For smaller phones */
@media (max-width: 768px) {
    /* Extra small mobile styles */
}
```

**Why:** The theme uses Tailwind's `lg:` breakpoint (1024px) as the desktop threshold.

---

## 🎨 WooCommerce Price HTML Structure

When `$product->get_price_html()` is called, it outputs:

```html
<div class="mobile-compact-price flex items-center flex-wrap flex-1 min-w-0 pr-1">
    <!-- Regular price (original) - when on sale -->
    <del aria-hidden="true">
        <span class="woocommerce-Price-amount amount">
            <bdi><span class="woocommerce-Price-currencySymbol">৳</span>1,250.00</bdi>
        </span>
    </del>
    
    <!-- Sale price (discounted) -->
    <ins>
        <span class="woocommerce-Price-amount amount">
            <bdi><span class="woocommerce-Price-currencySymbol">৳</span>990.00</bdi>
        </span>
    </ins>
</div>
```

**Key elements:**
- `<del>` = Original/regular price (should have strikethrough)
- `<ins>` = Sale/discounted price (should be prominent)
- `.woocommerce-Price-amount amount` = Wrapper for price value
- `<bdi>` = Bidirectional isolation wrapper

**Selectors to target:**
```css
/* Target both del and ins */
.mobile-compact-price del,
.mobile-compact-price .woocommerce-Price-amount del {
    text-decoration: line-through !important;
    opacity: 0.6 !important;
}

.mobile-compact-price ins,
.mobile-compact-price .woocommerce-Price-amount ins {
    font-weight: 800 !important;
    text-decoration: none !important;
}
```

---

## 🛒 Add to Cart Button Structure

```html
<button class="add-to-cart-btn bg-[#FFB800] hover:bg-[#e6a600] ..." data-product-id="123">
    <span class="add-text">Add to cart</span>
    <span class="added-text hidden text-white">Added</span>
</button>
```

**Two text spans:**
- `.add-text` = Visible text (target this for styling)
- `.added-text` = Hidden success state (shown after clicking)

**Mobile button styling example:**
```css
@media (max-width: 1023px) {
    .add-to-cart-btn .add-text {
        font-size: 2em !important;
        font-weight: 700 !important;
        line-height: 1.5 !important;
    }
}
```

---

## 📦 Product Card Classes

### Mobile Product Card
- Container: `.warafy-mobile-product-card`
- Actions wrapper: `.warafy-mobile-product-actions`
- Price wrapper: `.mobile-compact-price`
- Button: `.add-to-cart-btn`

### Desktop Product Card
- Container: `.warafy-desktop-product-card`
- Price wrapper: `.mobile-compact-price` (yes, same class name - confusing but true)
- Button: `.add-to-cart-btn`

---

## 🔧 CSS Loading Order

1. Google Fonts (Noto Sans Bengali)
2. SVG Icons (`/assets/css/modern-svg-icons.css`)
3. **Tailwind CDN** (`https://cdn.tailwindcss.com`) - Runtime compiler, generates CSS client-side
4. **Main stylesheet** (`style.css`) - Versioned by theme version in header

**Important:** Tailwind CDN generates a `<style>` tag in `<head>`. The external `style.css` loads after, but **inline styles in PHP files load in the body and override both**.

---

## 🚫 What NOT to Do

1. **Don't only modify `style.css`** - Always check if inline styles in PHP files override it
2. **Don't use 768px breakpoint for mobile** - Use 1023px as primary mobile breakpoint
3. **Don't assume WooCommerce classes** - Inspect actual HTML output, it may have extra wrappers
4. **Don't forget `!important`** - This theme uses `!important` extensively due to Tailwind utility conflicts

---

## ✅ What TO Do

1. **Check both locations when modifying CSS:**
   - `style.css` for global styles
   - `front-page.php` inline `<style>` block for product grid styles
   
2. **Use high-specificity selectors:**
   ```css
   /* Good */
   .warafy-mobile-product-card .add-to-cart-btn .add-text { }
   
   /* Better */
   button.add-to-cart-btn span.add-text { }
   
   /* Best for inline style blocks */
   .warafy-mobile-product-card .add-to-cart-btn .add-text,
   .add-to-cart-btn .add-text { }
   ```

3. **Version CSS changes:**
   - Update `Version:` in `style.css` header (forces cache refresh)
   - Add version comment below shipping banner in `front-page.php`

4. **Use `!important` when needed:**
   - Tailwind utilities may conflict with custom CSS
   - WooCommerce default styles may override

---

## 📝 File Locations Reference

| Purpose | File |
|---------|------|
| Global styles | `style.css` |
| Homepage inline styles | `front-page.php` (lines ~398-475) |
| Header/preloader styles | `header.php` (lines ~46-127) |
| Script enqueuing | `functions.php` (lines 2-50) |
| Theme version | `style.css` line 7 |
| Product rendering | `front-page.php` (functions `warafy_render_mobile_compact_product`, `warafy_render_desktop_compact_product`) |

---

## 🔄 Cache Busting

The theme uses WordPress's `wp_enqueue_style()` with theme version as cache buster:

```php
$theme_version = wp_get_theme()->get('Version');
wp_enqueue_style('warafy-style', get_stylesheet_uri(), array(), $theme_version);
```

**Result:** `style.css?ver=3.4.3`

**To force CSS refresh:**
1. Update `Version:` in `style.css` header
2. Commit and push
3. Deploy via cPanel
4. Users must hard refresh (Ctrl+Shift+R)

---

## 🎯 Example: Making Mobile Add-to-Cart Text Larger

**Step 1:** Update `front-page.php` inline styles:
```php
<style>
    @media (max-width: 1023px) {
        .warafy-mobile-product-card .add-to-cart-btn .add-text,
        .add-to-cart-btn .add-text {
            font-size: 2em !important;
            font-weight: 700 !important;
        }
    }
</style>
```

**Step 2:** Update `style.css` for consistency:
```css
@media (max-width: 1023px) {
    .add-to-cart-btn .add-text {
        font-size: 2em !important;
    }
}
```

**Step 3:** Bump version in `style.css`:
```css
Version: 3.4.3  /* Increment this */
```

**Step 4:** Update version comment in `front-page.php`:
```php
<!-- CSS Version: 3.4.3 - Description of changes -->
```

---

## ⚡ Quick Troubleshooting Checklist

If CSS changes don't appear:

- [ ] Did I update inline styles in `front-page.php`?
- [ ] Am I using `@media (max-width: 1023px)` for mobile?
- [ ] Did I add `!important` to override Tailwind?
- [ ] Did I bump the version number in `style.css`?
- [ ] Did I commit and push to GitHub?
- [ ] Did I deploy via cPanel Git Version Control?
- [ ] Did I do a hard refresh (Ctrl+Shift+R) in browser?
- [ ] Are my selectors specific enough? (`.warafy-mobile-product-card .add-to-cart-btn .add-text`)
- [ ] Is there a conflicting inline `<style>` block overriding my changes?

---

## 📚 Additional Notes

- **No build process** - All CSS is served raw, no minification or compilation
- **Tailwind CDN** - Runtime compiler, not pre-compiled. Adds load time but allows dynamic class usage
- **WooCommerce compatibility** - Theme inherits WooCommerce default styles that may need overriding
- **Dark mode support** - Use `.dark` prefix for dark theme variants
- **Bengali font support** - Noto Sans Bengali loaded via Google Fonts

---

*Last updated: April 15, 2026 - After discovering inline styles override external stylesheet*
