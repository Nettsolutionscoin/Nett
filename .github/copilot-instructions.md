# Copilot Instructions for Nett Solutions Website

## Project Overview

**Nett** is a Bootstrap-based business/portfolio website (BootstrapMade "NewBiz" template) with a single-page design featuring sections for intro, about, services, portfolio, team, testimonials, and contact forms.

**Tech Stack:**

- HTML5 with semantic structure
- Bootstrap 4 (minified)
- jQuery + plugin ecosystem
- Custom CSS (1413 lines)
- PHP backend for contact form (optional)

## Architecture & Key Patterns

### Single-Page Application (SPA) Structure

- **`index.html`** is the only page; navigation uses anchor links (`#intro`, `#about`, etc.)
- All content is organized in semantic `<section>` elements with matching IDs
- Smooth scrolling is handled by `js/main.js` with easing
- Active navigation state updates dynamically on scroll via section offset detection

### JavaScript Plugin Integration (`js/main.js`)

The project uses a **jQuery IIFE pattern** (Immediately Invoked Function Expression) to avoid global scope pollution:

```javascript
(function ($) {
  "use strict";
  // All code runs here, scoped to this function
})(jQuery);
```

**Key libraries and their usage:**

- **WOW.js** – Scroll-triggered animations (`.wow` classes with delays like `data-wow-delay="0.1s"`)
- **Isotope** – Portfolio filtering (`.portfolio-container`, `#portfolio-flters`)
- **Owl Carousel** – Testimonials slider (`.testimonials-carousel`)
- **CounterUp** – Animated counters in "Why Us" section (`[data-toggle="counter-up"]`)
- **Lightbox** – Portfolio image preview (`data-lightbox="portfolio"`)

### Asset Organization

```
lib/                    # Third-party minified libraries (do NOT edit)
├── bootstrap/          # Bootstrap CSS/JS
├── font-awesome/       # Icon fonts
├── owlcarousel/        # Carousel plugin
├── isotope/            # Portfolio filtering
├── wow/                # Scroll animations
├── lightbox/           # Image lightbox
├── easing/             # jQuery easing functions
└── [other libraries]   # jQuery, waypoints, counterup, ionicons, animate.css

css/                    # Custom styles
├── style.css           # Main stylesheet (edit here for styling)
└── scss-files.txt      # Reference file (SCSS sources, not compiled)

js/                     # Custom JavaScript
├── main.js             # Core functionality (124 lines)
└── [no other custom JS files currently]

contactform/            # Contact form backend
├── contactform.js      # Client-side validation & AJAX
└── [PHP backend expected but not committed]

img/                    # Static images
├── portfolio/          # Portfolio images
└── clients/            # Client logos
```

## Critical Conventions & Patterns

### Form Validation (`contactform/contactform.js`)

Uses **data attributes** for declarative validation:

```html
<input
  type="email"
  name="email"
  data-rule="email"
  data-msg="Please enter a valid email"
/>
<textarea
  name="message"
  data-rule="required"
  data-msg="Please write something for us"
></textarea>
```

**Supported rules:** `required`, `minlen:N`, `email`, `checked`, `regexp:PATTERN`

- Validation messages display in adjacent `.validation` div
- Form submits via AJAX to `contactform.php` (or action attribute)
- Success shows `#sendmessage`, errors populate `#errormessage`

### Animation & Effects

- **WOW.js classes:** Add `.wow fadeInUp` (or other animate.css effects) to elements
  - Control timing with `data-wow-delay="0.2s"` and `data-wow-duration="1.4s"`
  - Effects trigger on scroll when element enters viewport
- **Scroll-triggered classes:** Header gets `.header-scrolled` class after 100px scroll
- **Portfolio isotope:** Clicking filter list items toggles class `filter-active` and filters items

### CSS Architecture (`css/style.css`)

- **Monolithic file** – No CSS pre-processing/modules currently
- **Color scheme:** Primary blue `#007bff` (hover: `#0b6bd3`), secondary colors for service icons
- **Typography:** "Open Sans" (body), "Montserrat" (headings) – loaded from Google Fonts
- **Bootstrap grid:** Uses 12-column grid extensively (`col-lg-6`, `col-md-4`, etc.)
- **Responsive design:** Mobile-first with breakpoints: `lg` (≥992px), `md` (≥768px), `sm` (≥576px)

### Navigation Pattern

- Header has `.main-nav` (desktop) and `.mobile-nav` (hidden on mobile)
- Smooth scroll links use `.scrollto` class
- Current section is highlighted via `.active` class on nav items
- Mobile nav is toggled with `.mobile-nav-toggle` and fade via `.mobile-nav-overly`

## Common Development Tasks

### Adding a New Section

1. Add semantic `<section id="new-section">` in `index.html` between sections
2. Add nav item in header: `<li><a href="#new-section">New Section</a></li>`
3. Apply animations with `.wow fadeInUp` classes
4. Style in `css/style.css` (follow existing section patterns)
5. Navigation highlighting auto-updates (no JS changes needed)

### Adding Portfolio Items

Add to `.portfolio-container` with class pattern:

```html
<div class="col-lg-4 col-md-6 portfolio-item filter-web">
  <div class="portfolio-wrap">
    <img src="img/portfolio/[name].jpg" class="img-fluid" alt="" />
    <div class="portfolio-info">
      <h4><a href="#">Item Title</a></h4>
      <p>Category</p>
      <div>
        <a
          href="img/portfolio/[name].jpg"
          class="link-preview"
          data-lightbox="portfolio"
          data-title="Item Title"
          ><i class="ion ion-eye"></i
        ></a>
      </div>
    </div>
  </div>
</div>
```

Update filter buttons (`#portfolio-flters`) to match categories (`.filter-web`, `.filter-app`, `.filter-card`)

### Modifying Contact Form

- Update form fields in `index.html` (`id`, `name`, `data-rule`, `data-msg`)
- **Server-side:** Form posts to `contactform/contactform.php`; verify backend processes new fields
- **Client-side validation:** Already handles all rules; no JS changes usually needed

### Adding Animations to New Elements

- Use `.wow [animate.css-class]` (e.g., `fadeInUp`, `bounceInUp`, `slideInLeft`)
- Add timing: `data-wow-delay="0.1s"` (increments: 0, 0.1s, 0.2s, etc.)
- Duration: `data-wow-duration="1.4s"` (default is fine for consistency)
- WOW.js initializes automatically in `main.js`

## Known Quirks & Edge Cases

1. **Portfolio filtering:** Isotope requires `.portfolio-container` to load images before layout calculation; initialized on `window.load` (not `ready`) to ensure image dimensions are known
2. **Contact form action:** If form `action` is empty, defaults to `contactform/contactform.php`; verify backend exists
3. **Header sticky behavior:** Only adds `.header-scrolled` after 100px scroll; keep space budget ≤100px at top
4. **Mobile nav toggle:** Uses `.fa-bars` / `.fa-times` icons from Font Awesome; ensure correct icon library is loaded
5. **Smooth scroll offset:** Subtracts header height from scroll target; adjust `top_space` logic if header layout changes

## External Dependencies to Preserve

- **Bootstrap 4** – Core grid and utilities (minified)
- **jQuery** + jQuery Migrate – Required by all plugins
- **Google Fonts** – "Open Sans", "Montserrat" (link in `<head>`)
- **Font Awesome** – Icon font (CSS + font files)
- **Animate.css** – Animation library (included as minified CSS)
- **Plugin Libraries:** WOW, Isotope, Owl Carousel, Lightbox, CounterUp, Waypoints, Easing, Ionicons

All are in `lib/` and should NOT be modified. Use as-is or upgrade versions carefully (test all animations/plugins).

## When in Doubt

- **Content changes?** Edit `index.html` only
- **Styling changes?** Edit `css/style.css`
- **Functionality changes?** Edit `js/main.js` (within the jQuery IIFE)
- **New libraries?** Add to `lib/[library-name]/` and include script/link tags in `index.html` `<head>` or before closing `</body>`
- **Validation rules?** Modify data attributes in form inputs; no JS changes needed
