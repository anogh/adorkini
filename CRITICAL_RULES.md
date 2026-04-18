# CRITICAL RULES for AI Edits

## 1. Icon System (SVG)
**DO NOT REVERT TO FONT ICONS.** We have migrated to a custom SVG system.

- **Usage:**
  ```html
  <!-- CORRECT -->
  <span class="material-symbols-outlined" data-icon="icon_name"></span>
  
  <!-- WRONG (Old Font Method) -->
  <span class="material-symbols-outlined">icon_name</span>
  ```
- **PHP Dynamic Icons:**
  ```php
  <span class="material-symbols-outlined" data-icon="<?php echo esc_attr($icon_variable); ?>"></span>
  ```
- **JavaScript:**
  Use `.dataset.icon` to change icons, NOT `.textContent`.
  ```javascript
  element.dataset.icon = 'new_icon_name'; // Correct
  element.textContent = 'new_icon_name';  // Wrong
  ```
- **CSS:**
  Icons are rendered as `background-image` via `modern-svg-icons.css`.
  Do NOT add `font-family` or ligature styles.

## 2. Mobile Visibility
- Ensure `lg:hidden` and `hidden lg:block` classes are preserved for responsive design.
- Do not break the Mobile Categories loop in `front-page.php`.

## 3. Code Integrity
- Always check for closed PHP tags `?>` and HTML tags.
- Do not place PHP logic inside HTML attributes without proper escaping (`esc_attr`).
- Avoid nesting double quotes in HTML attributes (use single quotes for inner strings or escape).

## 4. Git Pushes
- Use the local SOCKS5 proxy when pushing if direct GitHub access fails: `git -c http.proxy=socks5h://127.0.0.1:10808 push origin main`.
- Keep homepage layout tweaks in `front-page.php` when the change is specific to the front page.
