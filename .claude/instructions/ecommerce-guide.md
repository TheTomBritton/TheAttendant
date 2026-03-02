# Ecommerce Guide for ProcessWire

## Overview

ProcessWire doesn't have built-in ecommerce, but its flexible page-based architecture makes it excellent for building shops. There are several approaches depending on complexity.

## Approach 1: Snipcart (Recommended for Simple Shops)

**Best for**: Small to medium product catalogues (under 500 products), clients who need quick setup.

Snipcart is an external service that adds cart and checkout to any website via JavaScript. ProcessWire handles the product catalogue; Snipcart handles cart, checkout, and payments.

### Setup

1. Sign up at snipcart.com (transaction-based pricing, no monthly fee for small volume)
2. Add the Snipcart script to `_main.php`:
   ```html
   <link rel="stylesheet" href="https://cdn.snipcart.com/themes/v3.6.1/default/snipcart.css" />
   <script async src="https://cdn.snipcart.com/themes/v3.6.1/default/snipcart.js"></script>
   <div hidden id="snipcart" data-api-key="YOUR_PUBLIC_API_KEY"></div>
   ```
3. Add buy buttons to product templates:
   ```html
   <button class="snipcart-add-item"
       data-item-id="<?= $page->name ?>"
       data-item-price="<?= $page->product_price ?>"
       data-item-url="<?= $page->httpUrl ?>"
       data-item-name="<?= $page->title ?>"
       data-item-description="<?= $page->summary ?>"
       data-item-image="<?= $page->featured_image->httpUrl ?>">
       Add to Cart — &pound;<?= number_format($page->product_price, 2) ?>
   </button>
   ```

### Required Fields for Snipcart
- `product_price` (FieldtypeFloat)
- `product_sku` (FieldtypeText) — unique identifier
- `product_weight` (FieldtypeFloat) — for shipping calculation
- `product_stock` (FieldtypeInteger) — optional stock tracking
- `featured_image` (FieldtypeImage)
- `summary` (FieldtypeTextarea)
- `body` (FieldtypeTextarea with CKEditor)

### Templates Needed
- `shop.php` — product listing page
- `product.php` — single product page
- `product-category.php` — category page (optional)

## Approach 2: Native PW Ecommerce (Custom Build)

**Best for**: When you need full control, complex pricing, or want to avoid external services.

Build the entire cart and checkout in ProcessWire using sessions and a payment gateway API (Stripe recommended).

### Architecture

```
/shop/                          (shop.php — product listing)
├── /shop/category-name/        (product-category.php — filtered listing)
│   ├── /shop/category/product/ (product.php — single product)
/cart/                          (cart.php — session-based cart)
/checkout/                      (checkout.php — Stripe integration)
/order-confirmation/            (order-confirmation.php)
```

### Cart Using Sessions

```php
// Add to cart
$cart = $session->get('cart') ?: [];
$productId = (int) $input->post->product_id;
$quantity = (int) $input->post->quantity ?: 1;

if (isset($cart[$productId])) {
    $cart[$productId]['qty'] += $quantity;
} else {
    $product = $pages->get($productId);
    $cart[$productId] = [
        'id' => $product->id,
        'title' => $product->title,
        'price' => $product->product_price,
        'qty' => $quantity,
    ];
}

$session->set('cart', $cart);

// Cart total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}
```

### Stripe Integration

```bash
composer require stripe/stripe-php
```

```php
\Stripe\Stripe::setApiKey($config->stripeSecretKey);

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $lineItems,
    'mode' => 'payment',
    'success_url' => $pages->get('/order-confirmation/')->httpUrl . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $pages->get('/cart/')->httpUrl,
]);
```

### Additional Fields for Custom Build
- `product_variations` (Repeater) — size, colour options with price adjustments
- `product_gallery` (FieldtypeImage, multiple) — product photos
- `product_category` (Page reference) — link to category pages
- `related_products` (Page reference, multiple) — cross-selling
- `product_in_stock` (FieldtypeToggle) — availability flag

## Approach 3: Padloper 2

**Best for**: When you want a pre-built PW-native ecommerce solution.

Padloper is a ProcessWire module specifically designed for ecommerce. Version 2 is free.

**Note**: Check current availability and maintenance status before recommending. If actively maintained, it's a good middle ground between Snipcart and a full custom build.

## Product Catalogue Fields (All Approaches)

These fields should be created regardless of the checkout approach:

| Field | Type | Purpose |
|---|---|---|
| `product_price` | Float | Base price in GBP |
| `product_sku` | Text | Unique product identifier |
| `product_weight` | Float | Weight in kg (for shipping) |
| `product_stock` | Integer | Stock quantity (0 = unlimited) |
| `product_in_stock` | Toggle | Availability flag |
| `product_gallery` | Image (multiple) | Product photos |
| `product_category` | Page reference | Category assignment |
| `related_products` | Page reference (multiple) | Cross-sell links |
| `product_features` | Textarea | Bullet-point features |
| `product_specifications` | Repeater | Key-value spec pairs |

## Tax & Shipping Considerations

- UK VAT: 20% standard rate. Decide if prices are inclusive or exclusive.
- Display: Always show "inc. VAT" or "ex. VAT" clearly.
- Shipping: Flat rate is simplest. Weight-based if products vary significantly.
- Digital products: Different VAT rules. Flag digital vs physical.

## SEO for Ecommerce

- Product schema (JSON-LD) on every product page
- Category pages should have unique descriptions (not just filtered listings)
- Product URLs should be clean: `/shop/category/product-name/`
- Image alt text should include product name
- See `.claude/instructions/seo-checklist.md` for full details
