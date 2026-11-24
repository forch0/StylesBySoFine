# Prompt: Write the Styles By So Fine User Guide

Use this entire file as the prompt. Paste it into Claude (or similar) and it will produce a complete, accurate user guide.

---

## YOUR TASK

Write a long, thorough, friendly **User Guide** for "Styles By So Fine" — a Nigerian women's fashion e-commerce website at **https://stylesbysofine.ng**. The guide must cover BOTH audiences:

1. **Part 1 — For customers** (shopping the site, mobile-first)
2. **Part 2 — For the store owner/admin** (running the business from the admin panel — she is NOT technical, so write step-by-step, assume zero web experience)

Requirements:

- **Cover page / header block** at the very top:

  > # Styles By So Fine — Complete User & Operations Guide
  > *Prepared by Fortune (fortunegabriel.de) for the CEO, Styles By So Fine*

- Plain, warm, encouraging English. Short sentences. No jargon — explain any term the first time it's used.
- Numbered step-by-step instructions for every task (e.g., "How to add a product").
- Use real URLs: storefront = `https://stylesbysofine.ng`, admin = `https://stylesbysofine.ng/admin`.
- Use callout formats where helpful: `> **Tip:**`, `> **Warning:**`, `> **Note:**`.
- Include a table of contents at the top and a glossary at the end.
- Write it in Markdown so it can be pasted into Google Docs / Notion / a help page.
- Be honest about limits: e.g., the site cannot refund money — only exchanges; delivery is always a separate offline payment.

---

## ABOUT THE BUSINESS (use throughout)

- **Brand:** Styles By So Fine — Lagos-based women's fashion, nationwide delivery in Nigeria.
- **Tagline:** Dress well. Feel confident. Be So Fine.
- **Contact:** WhatsApp **09020501822**, email **sales@stylesbysofine.ng**, Instagram **@stylesby_sofine**. Hours: Mon–Sat, 9am–6pm.
- **Payments:** Paystack (card, transfer, USSD). Product payment online; **delivery fee is separate** — quoted per location via WhatsApp after ordering, paid to the rider on arrival or transferred to the store.
- **Dispatch:** every **Monday, Wednesday, Friday**. Orders before **8:00 AM** on a dispatch day go same day. Lagos 24h after dispatch; elsewhere 48–72h.
- **Returns:** **Exchanges only, NO refunds.** Item must return within 24h, unworn/unwashed/tags on; customer pays return cost.
- **Sizes:** UK 6–18. Colours typically Wine, Black, Ivory.
- **Categories:** Dresses, Tops, Skirts, Sets, Corporate Casuals.

---

## PART 1 — CUSTOMER GUIDE (cover ALL of these)

### Browsing & shopping
- Homepage: hero slider, marquee, category tiles, new arrivals, bestsellers, promo banner, newsletter signup.
- Shop at `/shop` — filter by category, search, sort (newest, price, popular). Category URLs like `/shop/dresses`.
- Product page: image + video gallery, price & sale price, "X people are looking at this" badge, size/colour pickers, stock messages ("Only 3 left"), **Pre-order** state, size-guide link, delivery + returns accordions, Add to Bag, wishlist heart.
- Wishlist: `/wishlist` — saved per browser session (no account needed). NOTE: clearing browser data loses it.
- Bag: `/cart` — change quantities, remove items.
- Checkout `/checkout`: required fields marked `*` (name, email, phone, address, city, state, delivery area); optional fields labelled. Coupon box (e.g. WELCOME10 = 10% off first order). Delivery shows "Quoted separately" — no delivery charge online.
- Payment → Paystack popup/redirect → returns to order confirmation page.
- Order confirmation: order number (SBF-XXXXXXXX), items, total, delivery note.
- **Track order** `/track`: needs order number + the checkout email.
- **No customer accounts** — everything is guest checkout. Orders are identified by order number + email.
- Contact page `/contact`: WhatsApp, email, Instagram cards + contact form.
- Pages: `/pages/about`, `/pages/faq`, `/pages/shipping-delivery`, `/pages/returns-exchanges`, `/pages/size-guide`, `/pages/privacy-policy`, `/pages/terms`.
- Newsletter: footer signup; welcome email; every marketing email has an unsubscribe link (also `/newsletter/unsubscribe/{token}`).
- Pre-orders: products marked "Pre-order" can be bought even at 0 stock — made to order; the product page shows the ETA note; the order line is tagged.
- Reviews can be left on products; they appear after the owner approves them.
- Cookies: essential only (cart/session) — dismissible notice.

---

## PART 2 — ADMIN GUIDE (cover ALL of these)

**Login:** `https://stylesbysofine.ng/admin` — email + password. Sidebar: Dashboard · Orders · Products · Categories · Collections · Inventory · POS · Customers · Reviews · Coupons · Delivery Zones · Pages · Homepage · Navigation · Analytics · Messages · Staff · Audit Log · Settings. Bottom of sidebar: avatar + "View store" and "Log out" buttons.

**General UI pattern:** index pages show a count + a wine "+ Add …" button → opens a modal (or a separate page for products/pages). Edit controls sit inside an expandable "Edit" row. Required fields have a wine `*`. Green flash = success; red = error.

### Dashboard — `/admin`
Stats cards + revenue chart + recent orders + low-stock alerts. Read-only snapshot.

### Orders — `/admin/orders`
- List filters: search (number/name/email), payment status, fulfilment status, channel (online/POS).
- Order detail: customer + delivery info, items (pre-order lines flagged), totals, payment history, **status timeline** (who changed what, when).
- **Fulfilment statuses:** unfulfilled → processing → ready → shipped → delivered; also cancelled. Changing status **emails the customer automatically** (processing/shipped/delivered/cancelled).
- Cancel restores stock for paid orders. Notes = internal only.
- **Delivery fee workflow:** online orders never include delivery — owner reads the delivery area on the order, WhatsApps the customer the delivery cost, collects via rider or transfer. (This is the business's chosen model — explain it clearly.)
- Pre-order orders show a wine banner — contact the customer with an ETA.

### Products — `/admin/products` (+ create/edit pages)
- Fields: name (slug auto-generated once, URL stays stable on rename), category, collections, short/full description, price + compare-at price, SKU, status (draft/active/archived), flags (featured/bestseller/new arrival), **pre-order toggle + note**, SEO title/description, material, care instructions.
- Variants: option grid (e.g. Size × Colour), per-variant price override, stock, low-stock threshold, SKU, active.
- Media: upload images/video (jpg/png/webp/mp4/webm, 50MB max) or paste a URL; mark one primary; reorder; delete.
- Delete = real delete (order history keeps name/price snapshots).

### Categories — `/admin/categories`
Name, description, image, parent, sort order, active. These drive `/shop/{slug}` pages + nav links.

### Collections — `/admin/collections`
Curated groups (e.g. "The Signature Edit") usable by homepage sections.

### Inventory — `/admin/inventory`
Every variant's stock, low-stock flags, manual +/- adjustments with a reason, full movement history per variant. **Negative stock = pre-order backlog** (e.g. −3 means 3 owed). Online paid orders auto-deduct; cancellations restore.

### POS — `/admin/pos`
In-person sales: search products → pick variant + qty → optional customer name/email/phone → discount → payment method (cash / transfer / POS terminal / other) → sale instantly deducted + printable receipt (`/admin/pos/receipt/{order}`). Channel = POS in orders. Requires real stock (no pre-orders).

### Customers — `/admin/customers`
Grouped by email (identity key): order count, total spent (paid only), last order, newsletter badge. Profile page: all orders + saved addresses. Walk-ins without email aren't listed.

### Reviews — `/admin/reviews`
Approve/publish, feature, delete. Unapproved reviews never show publicly.

### Coupons — `/admin/coupons`
Code, type (percentage/fixed), value, min order, usage limit, start/expiry, active. WELCOME10 ships as a demo.

### Delivery Zones — `/admin/delivery-zones`
Area name, covered states, reference fee, ETA text, sort order. The zone name/ETA shows at checkout; the fee is internal reference only (delivery is charged offline).

### Pages — `/admin/pages`
CMS: title, slug, content, meta title/description, published toggle. Ships with about, faq, shipping-delivery, returns-exchanges, size-guide, privacy-policy, terms. New pages live at `/pages/{slug}` — **must be linked manually** (nav or page content) to be findable.

### Homepage — `/admin/homepage`
- 3 switchable designs: **Editorial / Luxury**, **Modern Boutique**, **Social / Trend-led** — activate instantly.
- Each design has 12 sections (hero slider, categories, new arrivals, featured collection, bestsellers, promo banner, marquee strip, brand statement, editorial, reviews, Instagram, newsletter) — enable/disable + drag-reorder per design.
- **Hero slider editor:** slides with image (upload or URL), eyebrow, headline, subcopy, 2 buttons with links; add/remove/reorder (up/down arrows); auto-rotates every ~6.5s, swipeable on mobile.
- Section edits apply per-design — changing Boutique doesn't touch Editorial.

### Navigation — `/admin/navigation`
Header menu items: label, URL (or `route:name` shortcut), order, active. Footer Shop column mirrors the first 5.

### Analytics — `/admin/analytics`
Date ranges (7/30/90 days + all time): revenue trend, pre-orders, channels (online vs POS), top products by revenue, top categories, **most viewed products** (anonymous view counter — no personal tracking), top customers, order pipeline funnel.

### Messages — `/admin/messages`
Contact-form inbox; open → auto-marks read; delete.

### Staff — `/admin/staff`
"+ Add staff member" → name, email, password, role (admin sees everything; staff is for helpers), active toggle. Edit inline; deactivate instead of deleting.

### Audit Log — `/admin/audit-log`
Every admin change: who, what, when — accountability + mistake-tracing.

### Settings — `/admin/settings`
- Store: name, tagline, announcement bar, footer about.
- Contact: email, WhatsApp, Instagram, hours, response time, address.
- SEO: default title + description.
- Payments: **Paystack public + secret keys** — paste and save; secret shows masked afterwards (blank = keep current). Keys drive checkout + webhook.
- Logo upload (optional — ships with brand logo).
- **Demo data card:** "Seed demo products" restores 8 sample products; "Wipe demo products" removes ONLY those 8 (categories/pages/orders safe); "Also clear all orders" is a separate dangerous action.

---

## TECHNICAL TRUTHS TO WEAVE IN (don't expose internals, but be accurate)

- Order emails: confirmation on payment success; status-change emails; owner alert to sales@… on each paid order; newsletter welcome with unsubscribe link.
- Paystack webhook (`/webhooks/paystack`) confirms payments even if the customer closes the tab — the callback also verifies; payment is idempotent (never double-charges stock).
- Checkout re-validates stock server-side; paying deducts stock inside a transaction.
- SEO: sitemap at `/sitemap.xml`, robots.txt, per-product Google rich-result schema, social share cards (og-image) — submit sitemap in Google Search Console.
- Security: admin behind login, CSRF protection, audit log, UUIDs (not enumerable IDs) on public product references.
- Privacy (NDPA): no tracking cookies, minimal data, unsubscribe everywhere, rights explained in privacy policy.

---

## UI / WHAT IT LOOKS LIKE — write this as a dedicated visual tour in the guide

The UI details below must appear in the guide as **its own section ("Screen Guide / Visual Tour")** — walk the reader through what each screen looks like and where every button/field sits, storefront first, then admin, using the descriptions below. Reference real element positions ("top-right", "left sidebar", "wine pill button") so a first-time user can orient themselves.

Design language site-wide: **cream background** (#faf8f5), **deep wine/burgundy accent** (#7d1f3f), **dark ink** (#2b2b2b). Headings are an elegant serif (Playfair Display), body is a clean sans (Inter). Buttons are **rounded pills**. The logo is a portrait illustration of a woman above "STYLES BY SO Fine" text — in a small cream badge on dark areas, directly on the light header.

### Storefront UI
- **Announcement bar** (top, dark wine strip): dispatch schedule text.
- **Sticky header:** hamburger (mobile, left) · centered logo · nav links (desktop) · icons right: search, wishlist heart, bag with item-count badge.
- **Mobile nav:** dark drawer slides from the LEFT — numbered serif links (01 Shop, 02 Dresses…), "Help & info" grid (Track order / Contact / Size guide / FAQ), WhatsApp + Instagram pills at bottom, tagline strip.
- **Hero:** full-bleed photo, dark gradient bottom, eyebrow + big serif headline + subcopy + 1–2 pill buttons; arrows + dots when multiple slides; auto-rotates ~6.5s; swipe on mobile.
- **Product cards:** 3:4 photo, name + price below; badges top-left "Video" or top-right "Sale"/"New"; "Pre-order" tag or "Sold out" veil.
- **Product page:** gallery left (main image + thumbnails, video supported), details right — category link, serif name, price (sale struck through), viewers badge (pulsing dot + eye icon), option buttons (disabled = line-through), "Only X left" / "Pre-order — note" pill, Add to Bag wine pill + "Save to wishlist" link, accordions for details/delivery/returns, reviews below.
- **Cart:** line items with thumbnails, qty +/- steppers, totals box, checkout button.
- **Checkout:** numbered cards — ① Contact details, ② Delivery (area picker as selectable cards), Paystack total; wine "Pay securely with Paystack" button; required `*` marks, "(optional)" tags.
- **Confirmation:** big check, order number, items, "delivery quoted separately" notice, track link.
- **Footer (dark):** logo badge + newsletter form, Shop / Help / Company link columns, mobile "Visit us", copyright line + "Built by fortunegabriel.de".
- **Cookie banner:** small dark card bottom-left ("essential cookies only" + Got it button).
- **404:** split screen — wine photo panel left, cream panel right ("Nothing here" + buttons).

### Admin UI — `/admin`
- **Dark ink sidebar (left, icons + labels):** Dashboard, Orders, Products, Categories, Collections, Inventory, POS, Customers, Reviews, Coupons, Delivery Zones, Pages, Homepage, Navigation, Analytics, Messages, Staff, Audit Log, Settings. Bottom: avatar block + **"View store"** (opens storefront) + **"Log out"** wine button.
- **Login:** split screen — wine editorial photo panel (desktop) + clean form; mobile = cream card.
- **Content area (cream bg):** white rounded cards; page titles with a subtitle; index pages show an item **count** + wine **"+ Add …"** pill top-right.
- **Modals:** centered white card over dark blurred backdrop; ✕ close, backdrop click & Esc close; required `*` marks; auto-reopens on validation errors.
- **Tables:** compact rows; expandable **"Edit"** line per row hides the edit form; status shown as colored pills/badges (green active, gray inactive, wine pre-order, amber low stock).
- **Order detail:** info cards + items table + totals; wine banner if pre-order lines exist; status dropdown + note field; status-history timeline.
- **POS:** left = product search + grid of variant results to tap; right = running sale (lines, qty steppers, discount, payment-method buttons, big complete-sale button); receipt view afterwards.
- **Analytics:** date-range pills top (7/30/90/all); stat cards + simple bar/line charts (pure CSS, no external chart lib); tables for top products/customers.
- **Homepage:** preset cards (3 designs, activate button); sections list = drag/arrow reorder + enable toggle + "Edit" → per-section form; hero editor lists slides with up/down/delete + "+ Add slide".
- **Settings:** stacked cards per group with a save button each; demo-data card at bottom (two buttons + danger link).
- **Flash messages:** green success / red error bar under the header.

---

## CRUD COVERAGE REQUIRED — document every operation explicitly

For EACH admin resource below, the guide must state clearly **what can be Created, Read/viewed, Updated/edited, and Deleted/deactivated** — including any exceptions or safe-guards. Where something can't be deleted (or shouldn't), say why.

| Resource | Create | Read | Update | Delete | Special rules |
|---|---|---|---|---|---|
| Products | ✓ dedicated page | list + filters | ✓ edit page | ✓ hard delete | Slug set once; order history keeps name/price snapshots; variants linked to orders can't be removed (kept hidden instead) |
| Categories | ✓ modal | index table | expandable row | ✓ | Products keep working if a category is removed (they just lose the grouping) |
| Collections | ✓ modal | index table | expandable row | ✓ | Only affects homepage/merchandising groups |
| Orders | online/POS only | list + detail | fulfilment status, internal notes | ✗ no delete | **Cancel** instead (restores stock if paid); delete only via Settings → "clear all orders" |
| Inventory | via products | stock table + history | +/- adjustments w/ reason | ✗ | Negative stock = pre-order backlog |
| Coupons | ✓ modal | index table | expandable row | ✓ | Inactive coupons fail at checkout |
| Delivery zones | ✓ modal | index table | expandable row | ✓ | Fee is reference only — delivery charged offline |
| Pages | ✓ dedicated page | index | ✓ edit (incl. SEO + publish toggle) | ✓ | New pages need manual links to be found |
| Homepage presets | 3 fixed designs | per-preset section list | section enable/disable/reorder + content edit | ✗ presets can't be deleted | Per-design edits are isolated |
| Hero slides | + Add slide | per-slide editor | all fields + reorder arrows | per-slide delete | Auto-rotates ~6.5s on storefront |
| Navigation items | ✓ modal | index table | expandable row | ✓ | Footer Shop column mirrors first 5 |
| Customers | via orders | index + profile | ✗ (read-only profiles) | ✗ | Identity = email; POS walk-ins without email not listed |
| Reviews | via storefront | pending/approved list | approve/feature | ✓ | Unapproved never show publicly |
| Messages | via contact form | inbox | auto-marks read on open | ✓ | Reply happens over email/WhatsApp, not in admin |
| Staff | ✓ hidden form (button reveals) | table w/ roles | inline edit (incl. password reset, role, active) | deactivate, not delete | Admins only section |
| Settings | — | grouped form | ✓ saves per group | — | Paystack secret masked after save; blank keeps current |
| Audit log | auto | chronological feed | ✗ | ✗ | Read-only record of admin actions |
| Wishlist/carts | via shoppers | — | — | wiped by demo wipe | Session-bound |

Also include a **"safe to delete?" note** per resource — e.g., deleting a product is permanent but doesn't corrupt past orders; deleting an order is intentionally hard.

---



1. Welcome + what's in this guide
2. **Screen Guide / Visual Tour** — storefront then admin, element-by-element (use the UI section below)
3. **Part 1 — Shopping on stylesbysofine.ng** (browse → buy → track → returns)
4. **Part 2 — Running your store** (daily workflow first: "a day running the shop" walkthrough, then section-by-section admin reference in sidebar order, each ending with its CRUD operations + "safe to delete?" note — include the full CRUD matrix as a summary table at the end of this part)
5. **Part 3 — Money, delivery & policies** (Paystack, separate delivery fee, exchanges-only, dispatch days)
6. **Part 4 — Going live / maintenance** (change passwords, backups, Search Console, marketing emails need unsubscribe link)
7. Troubleshooting FAQ
8. Glossary

Length: comprehensive — this should read like a real operations manual, not a summary. Aim for thorough coverage of every feature listed above.
