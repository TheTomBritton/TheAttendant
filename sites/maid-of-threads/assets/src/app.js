/**
 * Maid of Threads — Main JavaScript entry point
 *
 * Alpine.js handles reactive UI (cart, product selectors, dropdowns).
 * HTMX handles server-driven dynamic updates (filtering, load more).
 */

import Alpine from 'alpinejs';
import htmx from 'htmx.org';
import './app.css';

// ──────────────────────────────────────────────
// Alpine.js — Cart Store
// ──────────────────────────────────────────────
Alpine.store('cart', {
    items: JSON.parse(localStorage.getItem('mot_cart_preview') || '[]'),
    count: parseInt(localStorage.getItem('mot_cart_count') || '0'),

    /**
     * Update the client-side cart preview after server mutation.
     * Called via HTMX afterRequest or on page load.
     */
    async refresh() {
        try {
            const res = await fetch('/cart/?json=1');
            if (res.ok) {
                const data = await res.json();
                this.items = data.items || [];
                this.count = data.count || 0;
                localStorage.setItem('mot_cart_preview', JSON.stringify(this.items));
                localStorage.setItem('mot_cart_count', String(this.count));
            }
        } catch (e) {
            // Silent fail — cart icon just won't update
        }
    },
});

// ──────────────────────────────────────────────
// Alpine.js — Product Gallery
// ──────────────────────────────────────────────
Alpine.data('productGallery', () => ({
    activeIndex: 0,
    setActive(index) {
        this.activeIndex = index;
    },
}));

// ──────────────────────────────────────────────
// Alpine.js — Mobile Menu
// ──────────────────────────────────────────────
Alpine.data('mobileMenu', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

// Start Alpine
Alpine.start();

// Expose HTMX globally for inline attributes
window.htmx = htmx;

// Refresh cart on page load
document.addEventListener('DOMContentLoaded', () => {
    Alpine.store('cart').refresh();
});

// Refresh cart after HTMX requests that modify it
document.addEventListener('htmx:afterRequest', (event) => {
    const target = event.detail.target;
    if (target && target.id === 'cart-content') {
        Alpine.store('cart').refresh();
    }
});
