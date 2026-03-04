<?php
namespace ProcessWire;

/**
 * Populate Maid of Threads with sample content
 *
 * Run via Docker CLI:
 * docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml \
 *   exec web php /var/www/html/sites/maid-of-threads/scripts/populate-content.php
 *
 * Idempotent — safe to re-run without duplicating content.
 */

// Bootstrap ProcessWire
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_FILENAME'] = '/var/www/html/index.php';

require_once '/var/www/html/index.php';

$pages = wire('pages');
$sanitizer = wire('sanitizer');
$fields = wire('fields');

// Run as superuser
$su = wire('users')->get('admin');
wire()->wire('user', $su);

// Counters
$stats = ['updated' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

function out($msg) { echo "$msg\n"; }

function findOrCreate($template, $parent, $title) {
    $pages = wire('pages');
    $sanitizer = wire('sanitizer');
    $name = $sanitizer->pageName($title);
    $existing = $pages->get("template=$template, parent=$parent, name=$name");
    if ($existing && $existing->id) {
        return ['page' => $existing, 'exists' => true];
    }
    $p = new Page();
    $p->template = $template;
    $p->parent = $parent;
    $p->title = $title;
    $p->name = $name;
    return ['page' => $p, 'exists' => false];
}

function savePage($page, $label, &$stats) {
    try {
        $page->of(false);
        $page->save();
        // Publish if unpublished
        if ($page->hasStatus(Page::statusUnpublished)) {
            $page->removeStatus(Page::statusUnpublished);
            $page->save();
        }
        return true;
    } catch (\Exception $e) {
        out("  [ERROR] $label: " . $e->getMessage());
        $stats['errors']++;
        return false;
    }
}


// =====================================================================
// PHASE 1: Update existing static pages
// =====================================================================
out("\n=== PHASE 1: Static page content ===\n");

// --- Homepage ---
$home = $pages->get('/');
$home->of(false);
$home->title = 'Handcrafted Textiles, Thoughtfully Made';
$home->summary = 'Beautiful handmade textiles, accessories, and homeware from independent British makers. Ethically sourced, lovingly crafted, delivered to your door.';
$home->body = '<p>Welcome to Maid of Threads — a curated collection of handcrafted textiles from the best independent makers across the British Isles. Every piece in our shop has been chosen for its quality, character, and the story behind it.</p>
<p>From luxurious wool throws spun in the Scottish Highlands to hand-dyed linen runners crafted in a Cotswolds studio, we believe beautiful things should be made to last. Browse our collections, discover the makers, and find something truly special.</p>';
if (savePage($home, 'Homepage', $stats)) {
    out("  [OK] Updated homepage");
    $stats['updated']++;
}

// --- About ---
$about = $pages->get('/about/');
if ($about->id) {
    $about->of(false);
    $about->body = '<h2>Our Story</h2>
<p>Maid of Threads began in 2019 in a small studio overlooking the market square in Cirencester. What started as a love of collecting beautiful textiles from local craft fairs quickly grew into something bigger — a mission to connect people with the makers behind the fabric.</p>

<h2>What We Believe</h2>
<p>We believe that the things you surround yourself with should have stories. That a cushion cover is more than just a cushion cover when you know it was woven by hand in a Welsh mill. That a scarf carries more warmth when you understand the care that went into dyeing and finishing it.</p>

<h2>Our Makers</h2>
<p>Every product in our shop comes from an independent maker or small workshop, mostly based in the UK. We visit each maker personally, see their process first-hand, and build relationships that last. We pay fair prices and never ask for exclusivity — our makers are free to sell wherever they choose.</p>

<h2>Sustainability</h2>
<p>Handmade textiles are inherently more sustainable than mass-produced alternatives. Our makers use natural fibres — wool, linen, cotton, silk — sourced responsibly. We ship in recycled and recyclable packaging, and we encourage our customers to buy fewer, better things that will last for years.</p>

<h2>Visit Us</h2>
<p>Our studio and shop in Cirencester is open Thursday to Saturday, 10am–5pm. Pop in for a browse, a chat, and a cup of tea. We love meeting the people who share our passion for beautiful textiles.</p>';
    if (savePage($about, 'About', $stats)) {
        out("  [OK] Updated about page");
        $stats['updated']++;
    }
}

// --- Contact ---
$contact = $pages->get('/contact/');
if ($contact->id) {
    $contact->of(false);
    $contact->body = '<p>Have a question about an order, or want to discuss a custom commission? Drop us a message using the form below and we\'ll get back to you within one working day.</p>
<p>You can also find us at our studio in Cirencester, open Thursday to Saturday, 10am–5pm. We\'re always happy to welcome visitors.</p>
<p><strong>Maid of Threads Studio</strong><br>
14 Market Place<br>
Cirencester, GL7 2NW<br>
<a href="mailto:hello@maidofthreads.co.uk">hello@maidofthreads.co.uk</a><br>
01285 640 123</p>';
    if (savePage($contact, 'Contact', $stats)) {
        out("  [OK] Updated contact page");
        $stats['updated']++;
    }
}

// --- Privacy Policy ---
$privacy = $pages->get('/privacy-policy/');
if ($privacy->id) {
    $privacy->of(false);
    $privacy->body = '<h2>Introduction</h2>
<p>Maid of Threads Ltd ("we", "our", "us") is committed to protecting your personal information. This privacy policy explains how we collect, use, and safeguard your data when you use our website and services.</p>

<h2>What Data We Collect</h2>
<p>We may collect the following information:</p>
<ul>
<li><strong>Contact details</strong> — name, email address, phone number, delivery address</li>
<li><strong>Order information</strong> — products purchased, order history, payment references</li>
<li><strong>Technical data</strong> — IP address, browser type, pages visited, referring site</li>
<li><strong>Communication records</strong> — emails and messages you send us via our contact form</li>
</ul>

<h2>How We Use Your Data</h2>
<p>We use your information to:</p>
<ul>
<li>Process and fulfil your orders</li>
<li>Communicate with you about your orders and enquiries</li>
<li>Improve our website and services</li>
<li>Comply with legal and regulatory obligations</li>
</ul>

<h2>Payment Processing</h2>
<p>All payments are processed securely by Stripe. We never see, store, or have access to your full card details. Stripe\'s privacy policy can be found at <a href="https://stripe.com/privacy">stripe.com/privacy</a>.</p>

<h2>Cookies</h2>
<p>Our website uses essential cookies to manage your shopping basket and session. We do not use tracking cookies or third-party advertising cookies.</p>

<h2>Data Retention</h2>
<p>We retain order records for six years to comply with HMRC requirements. Contact form messages are deleted after 12 months unless they relate to an ongoing matter.</p>

<h2>Your Rights</h2>
<p>Under the UK GDPR, you have the right to:</p>
<ul>
<li>Access the personal data we hold about you</li>
<li>Request correction of inaccurate data</li>
<li>Request deletion of your data (where legally permissible)</li>
<li>Object to processing of your data</li>
<li>Request a copy of your data in a portable format</li>
</ul>

<h2>Contact Us</h2>
<p>For any privacy-related queries, please contact us at <a href="mailto:privacy@maidofthreads.co.uk">privacy@maidofthreads.co.uk</a> or write to: Maid of Threads Ltd, 14 Market Place, Cirencester, GL7 2NW.</p>

<p><em>Last updated: March 2026</em></p>';
    if (savePage($privacy, 'Privacy Policy', $stats)) {
        out("  [OK] Updated privacy policy");
        $stats['updated']++;
    }
}

// --- Terms & Conditions ---
$terms = $pages->get('/terms-and-conditions/');
if ($terms->id) {
    $terms->of(false);
    $terms->body = '<h2>1. About These Terms</h2>
<p>These terms and conditions apply to all orders placed through the Maid of Threads website (maidofthreads.co.uk). By placing an order, you agree to be bound by these terms. Maid of Threads Ltd is registered in England and Wales.</p>

<h2>2. Ordering</h2>
<p>All orders are subject to availability. We will confirm your order by email once payment has been processed. If we are unable to fulfil your order, we will contact you and issue a full refund.</p>

<h2>3. Pricing</h2>
<p>All prices are shown in pounds sterling (GBP) and include VAT where applicable. We reserve the right to change prices at any time, but changes will not affect orders already placed.</p>

<h2>4. Payment</h2>
<p>Payment is taken at the time of ordering via Stripe. We accept all major credit and debit cards. All transactions are processed securely and we never store your card details.</p>

<h2>5. Delivery</h2>
<p>We aim to dispatch orders within 1–3 working days. Standard delivery within the UK is charged at a flat rate, with free delivery on orders over the threshold shown at checkout. Delivery typically takes 3–5 working days via Royal Mail.</p>

<h2>6. Returns &amp; Refunds</h2>
<p>You have the right to cancel your order within 14 days of receiving your goods, in accordance with the Consumer Contracts Regulations 2013. Items must be returned in their original condition, unused and with all tags attached. Please contact us before returning any items.</p>
<p>Refunds will be processed within 14 days of receiving the returned items, using the same payment method as the original purchase.</p>

<h2>7. Faulty or Damaged Goods</h2>
<p>If you receive a faulty or damaged item, please contact us within 48 hours of delivery with photographs. We will arrange a replacement or full refund at no extra cost to you.</p>

<h2>8. Handmade Products</h2>
<p>Many of our products are handmade by independent makers. Slight variations in colour, texture, and size are inherent to handcrafted items and are not considered faults. These variations are part of what makes each piece unique.</p>

<h2>9. Intellectual Property</h2>
<p>All content on this website — including text, images, logos, and design — is the property of Maid of Threads Ltd and may not be reproduced without written permission.</p>

<h2>10. Governing Law</h2>
<p>These terms are governed by the laws of England and Wales. Any disputes will be subject to the exclusive jurisdiction of the courts of England and Wales.</p>

<p><em>Last updated: March 2026</em></p>';
    if (savePage($terms, 'Terms & Conditions', $stats)) {
        out("  [OK] Updated terms & conditions");
        $stats['updated']++;
    }
}

// --- Shop Category Descriptions ---
$categories_data = [
    '/shop/new-arrivals/' => [
        'summary' => 'The latest additions to our collection — fresh from the makers\' studios.',
        'body' => '<p>Our newest arrivals showcase the very best of what British makers are creating right now. From seasonal colourways to limited-edition collaborations, this is where you\'ll find pieces that are fresh, exciting, and won\'t be around forever.</p>',
    ],
    '/shop/accessories/' => [
        'summary' => 'Handmade scarves, bags, and accessories crafted from natural fibres.',
        'body' => '<p>Our accessories are designed to be worn and loved every day. Each piece is made by hand using natural fibres — wool, silk, linen, and cotton — chosen for their quality and feel. From statement scarves to everyday tote bags, these are the finishing touches that make an outfit.</p>',
    ],
    '/shop/homeware/' => [
        'summary' => 'Beautiful throws, cushions, and kitchen textiles to transform your living space.',
        'body' => '<p>Bring warmth and character to your home with our collection of handcrafted homeware. Every throw, cushion cover, and kitchen towel has been made with care by skilled makers who take pride in their craft. These are pieces that get better with age.</p>',
    ],
    '/shop/gifts/' => [
        'summary' => 'Thoughtful, handmade gifts for the people who appreciate beautiful things.',
        'body' => '<p>Looking for a gift that feels personal and special? Our curated gift collection includes everything from lavender sachets to craft starter kits — all beautifully made by independent British makers. Each piece comes wrapped in our signature recycled packaging.</p>',
    ],
];

foreach ($categories_data as $path => $data) {
    $cat = $pages->get($path);
    if ($cat->id) {
        $cat->of(false);
        $cat->summary = $data['summary'];
        $cat->body = $data['body'];
        if (savePage($cat, $cat->title, $stats)) {
            out("  [OK] Updated category: {$cat->title}");
            $stats['updated']++;
        }
    } else {
        out("  [SKIP] Category not found: $path");
        $stats['skipped']++;
    }
}


// =====================================================================
// PHASE 2: Create blog tags
// =====================================================================
out("\n=== PHASE 2: Blog tags ===\n");

$tags_parent = $pages->get('/blog-tags/');
if (!$tags_parent->id) {
    out("  [ERROR] /blog-tags/ parent page not found");
    $stats['errors']++;
} else {
    $tag_names = [
        'Sustainability',
        'Handmade',
        'Textile Care',
        'Gift Guide',
        'Natural Fibres',
        'Local Makers',
        'Studio Life',
        'Seasonal',
    ];

    foreach ($tag_names as $tag_title) {
        $result = findOrCreate('blog-tag', $tags_parent, $tag_title);
        if ($result['exists']) {
            out("  [SKIP] Tag already exists: $tag_title");
            $stats['skipped']++;
        } else {
            if (savePage($result['page'], $tag_title, $stats)) {
                out("  [OK] Created tag: $tag_title");
                $stats['created']++;
            }
        }
    }
}


// =====================================================================
// PHASE 3: Create products
// =====================================================================
out("\n=== PHASE 3: Products ===\n");

$products_data = [
    // --- New Arrivals ---
    [
        'parent' => '/shop/new-arrivals/',
        'title' => 'Merino Wool Wrap Scarf',
        'summary' => 'Luxuriously soft merino wool wrap in a versatile neutral palette. Perfect for layering through every season.',
        'body' => '<p>This generous wrap scarf is knitted from 100% merino wool by a small mill in the Scottish Borders. The neutral oatmeal tone works beautifully with everything in your wardrobe, from a winter coat to a summer dress on a cool evening.</p>
<p>Merino wool is naturally temperature-regulating, moisture-wicking, and wonderfully soft against the skin. Unlike synthetic alternatives, it breathes with you — keeping you warm when it\'s cold and cool when it\'s not.</p>
<p>The hand-finished rolled edges give this scarf a refined look that elevates any outfit. It\'s the kind of piece you\'ll reach for every day.</p>',
        'price' => 48.00,
        'sku' => 'MOT-NW-001',
        'weight' => 180,
        'stock' => 25,
        'features' => "100% merino wool\nMeasures 200cm × 70cm\nLightweight yet warm\nHand-finished rolled edges\nMade in Scotland",
    ],
    [
        'parent' => '/shop/new-arrivals/',
        'title' => 'Hand-Dyed Linen Table Runner',
        'summary' => 'A statement table runner in hand-dyed linen with raw edge detail. Each piece is unique.',
        'body' => '<p>Every one of these table runners is individually hand-dyed by textile artist Sarah Hadley in her Cotswolds studio. Using plant-based dyes extracted from local botanicals — weld, woad, and madder root — no two pieces are exactly alike.</p>
<p>The European linen base is pre-washed for a beautiful, lived-in softness from day one. Raw edges are left deliberately unfinished, adding an organic, artisan feel that works equally well on a farmhouse kitchen table or a modern dining setting.</p>
<p>Linen is one of the most sustainable textiles available — it requires far less water to produce than cotton and the flax plant enriches the soil it grows in.</p>',
        'price' => 36.00,
        'sku' => 'MOT-NW-002',
        'weight' => 250,
        'stock' => 15,
        'features' => "100% European linen\nHand-dyed with plant-based dyes\nMeasures 180cm × 40cm\nRaw edge finish\nPre-washed for softness",
    ],
    [
        'parent' => '/shop/new-arrivals/',
        'title' => 'Botanical Print Tote Bag',
        'summary' => 'Sturdy cotton canvas tote with original botanical screen print. A practical everyday bag with artisan charm.',
        'body' => '<p>This roomy tote bag is screen-printed by hand in a Brighton studio using water-based inks on heavy-duty organic cotton canvas. The botanical design features native British wildflowers — foxglove, cow parsley, and red campion — illustrated by printmaker Ellie Macdonald.</p>
<p>With reinforced handles and a generous interior, it\'s built to carry everything from your weekly shop to your library books. The natural cotton will soften and develop character with use.</p>',
        'price' => 24.00,
        'sku' => 'MOT-NW-003',
        'weight' => 150,
        'stock' => 40,
        'features' => "Organic cotton canvas\nHand screen-printed\nOriginal botanical design\nReinforced handles\nMeasures 40cm × 45cm × 12cm",
    ],

    // --- Accessories ---
    [
        'parent' => '/shop/accessories/',
        'title' => 'Silk-Blend Evening Scarf',
        'summary' => 'A lightweight silk and wool blend scarf with a subtle jacquard pattern. Elegant enough for evening, soft enough for every day.',
        'body' => '<p>Woven on a traditional Jacquard loom in Norwich, this scarf blends the lustre of silk with the warmth of fine wool. The subtle geometric pattern catches the light beautifully, making it equally suited to a night out or a weekend stroll.</p>
<p>The silk-wool blend gives the fabric a gorgeous drape and a luxurious hand-feel that\'s impossible to replicate with synthetic fibres. It\'s finished with a delicate hand-rolled fringe.</p>
<p>Available in deep midnight blue with a tonal pattern that adds texture without overwhelming.</p>',
        'price' => 62.00,
        'sku' => 'MOT-AC-001',
        'weight' => 100,
        'stock' => 12,
        'features' => "60% wool, 40% silk\nJacquard woven pattern\nMeasures 180cm × 55cm\nHand-rolled fringe\nMade in Norwich",
    ],
    [
        'parent' => '/shop/accessories/',
        'title' => 'Woven Cotton Market Bag',
        'summary' => 'A colourful hand-woven market bag in sturdy cotton. Folds flat when not in use.',
        'body' => '<p>These cheerful market bags are hand-woven on traditional frame looms by a women\'s cooperative in Bristol. Each bag uses remnant yarns from larger weaving projects, so the colour combinations vary — no two are identical.</p>
<p>The open-weave construction stretches to hold a surprising amount while remaining lightweight enough to tuck into a pocket or handbag when not needed. It\'s the perfect replacement for single-use plastic bags.</p>',
        'price' => 28.00,
        'sku' => 'MOT-AC-002',
        'weight' => 200,
        'stock' => 30,
        'features' => "100% cotton\nHand-woven\nStretches to hold heavy loads\nFolds flat for storage\nUnique colour combinations",
    ],
    [
        'parent' => '/shop/accessories/',
        'title' => 'Hand-Stitched Leather & Linen Purse',
        'summary' => 'A compact purse combining vegetable-tanned leather with natural linen. Beautifully made to last.',
        'body' => '<p>Crafted by leatherworker James Hartley in his Devon workshop, this compact purse pairs vegetable-tanned English leather with a panel of handwoven linen. Every stitch is done by hand using traditional saddle-stitching technique, which is stronger and more durable than machine stitching.</p>
<p>The leather will develop a rich patina over time, becoming more beautiful with age. Inside, you\'ll find a zip coin compartment and space for six cards. It\'s small enough for a pocket but big enough for the essentials.</p>',
        'price' => 34.00,
        'sku' => 'MOT-AC-003',
        'weight' => 120,
        'stock' => 18,
        'features' => "Vegetable-tanned English leather\nHandwoven linen panel\nHand saddle-stitched\nZip coin compartment\nFits 6 cards",
    ],

    // --- Homeware ---
    [
        'parent' => '/shop/homeware/',
        'title' => 'Herringbone Wool Throw',
        'summary' => 'A substantial herringbone throw in pure new wool. Woven in Wales, built to last a lifetime.',
        'body' => '<p>This generous throw is woven from pure new wool at one of the last remaining woollen mills in mid-Wales. The classic herringbone pattern in charcoal and cream is timeless — it suits a contemporary sofa as well as a traditional armchair.</p>
<p>At over 150cm × 200cm, it\'s large enough to wrap up in on a winter evening or drape across a bed for an extra layer of warmth. Pure new wool is naturally flame-retardant, hypoallergenic, and temperature-regulating.</p>
<p>This is the kind of throw that gets handed down. Buy it once, love it for decades.</p>',
        'price' => 85.00,
        'sku' => 'MOT-HW-001',
        'weight' => 900,
        'stock' => 10,
        'features' => "100% pure new wool\nClassic herringbone weave\nMeasures 150cm × 200cm\nWoven in Wales\nNaturally flame-retardant",
    ],
    [
        'parent' => '/shop/homeware/',
        'title' => 'Linen Cushion Cover Pair',
        'summary' => 'A pair of stonewashed linen cushion covers in a soft sage green. Simple, elegant, endlessly versatile.',
        'body' => '<p>These cushion covers are cut from premium stonewashed Belgian linen in a beautiful muted sage green. The stonewashing process gives the fabric a gorgeously soft, relaxed drape from the moment you open the packet — no breaking-in period required.</p>
<p>Each cover has a hidden zip closure for a clean, seamless look. They fit standard 45cm × 45cm cushion pads (not included).</p>
<p>Linen is one of those miraculous fabrics that actually gets softer with every wash. These covers will look and feel better in five years than they do today.</p>',
        'price' => 42.00,
        'sku' => 'MOT-HW-002',
        'weight' => 300,
        'stock' => 22,
        'features' => "100% Belgian linen\nStonewashed finish\nHidden zip closure\nFits 45cm × 45cm pads\nSold as a pair",
    ],
    [
        'parent' => '/shop/homeware/',
        'title' => 'Cotton Waffle Kitchen Towels (Set of 3)',
        'summary' => 'A set of three waffle-weave kitchen towels in organic cotton. Absorbent, quick-drying, and built for daily use.',
        'body' => '<p>These kitchen towels are woven from GOTS-certified organic cotton in a classic waffle weave that\'s both highly absorbent and quick-drying. The set includes three towels in complementary natural tones — undyed cream, soft grey, and clay.</p>
<p>Unlike terry cloth, waffle weave dries quickly between uses, which means less mustiness and fewer washes needed. They\'re generous at 50cm × 70cm and finished with a hanging loop.</p>
<p>We go through a lot of kitchen towels. These are the ones we use at the studio — they last, they work, and they look good doing it.</p>',
        'price' => 18.00,
        'sku' => 'MOT-HW-003',
        'weight' => 250,
        'stock' => 45,
        'features' => "GOTS-certified organic cotton\nWaffle weave texture\nSet of 3 complementary tones\nMeasures 50cm × 70cm each\nHanging loop",
    ],

    // --- Gifts ---
    [
        'parent' => '/shop/gifts/',
        'title' => 'Lavender Sachets Gift Set',
        'summary' => 'A set of three hand-sewn lavender sachets in vintage-inspired floral cotton. The perfect small gift.',
        'body' => '<p>These little sachets are hand-sewn from a beautiful Liberty-style floral cotton and filled with dried English lavender from a small farm in the Cotswolds. Tuck them into drawers, hang them in wardrobes, or simply keep one on your desk — the scent is calming and long-lasting.</p>
<p>Each set of three comes in a recycled kraft gift box, ready to give. The sachets can be refreshed by gently squeezing them, and the lavender scent typically lasts 6–12 months.</p>',
        'price' => 16.00,
        'sku' => 'MOT-GF-001',
        'weight' => 80,
        'stock' => 50,
        'features' => "Set of 3 sachets\nHand-sewn cotton covers\nEnglish dried lavender filling\nRecycled kraft gift box\nScent lasts 6–12 months",
    ],
    [
        'parent' => '/shop/gifts/',
        'title' => 'Embroidered Handkerchief Set',
        'summary' => 'A boxed set of three cotton handkerchiefs with delicate hand-embroidered initials. A timeless, personal gift.',
        'body' => '<p>There\'s something wonderfully old-fashioned about a proper cotton handkerchief, and these are the real thing. Made from fine cotton lawn — the same fabric used for the best handkerchiefs since the Victorian era — each one is finished with a narrow rolled hem and a hand-embroidered floral motif in the corner.</p>
<p>The set comes beautifully presented in a recycled card box with tissue paper. It\'s the kind of gift that makes people smile — thoughtful, useful, and unmistakably handmade.</p>',
        'price' => 22.00,
        'sku' => 'MOT-GF-002',
        'weight' => 60,
        'stock' => 35,
        'features' => "Fine cotton lawn\nHand-embroidered floral motif\nNarrow rolled hem\nSet of 3 in gift box\nMeasures 30cm × 30cm",
    ],
    [
        'parent' => '/shop/gifts/',
        'title' => 'Craft Lover\'s Starter Kit',
        'summary' => 'Everything a beginner needs to start hand-embroidery. A beautiful gift for creative minds.',
        'body' => '<p>This starter kit contains everything you need to try your hand at embroidery: a beechwood hoop, pre-printed linen fabric with a floral design, a selection of DMC embroidery threads in complementary colours, a set of needles, and a clear illustrated guide to basic stitches.</p>
<p>The kit was designed by textile artist and teacher Helen Frost, who has been running embroidery workshops in Bath for over fifteen years. The pre-printed design means no tracing or transferring — just pick up the needle and start stitching.</p>
<p>Suitable for complete beginners aged 12 and up. Makes a wonderful birthday, Christmas, or "just because" gift for anyone who\'s ever said they\'d like to try embroidery.</p>',
        'price' => 55.00,
        'sku' => 'MOT-GF-003',
        'weight' => 500,
        'stock' => 8,
        'features' => "Beechwood embroidery hoop (15cm)\nPre-printed linen fabric\n8 DMC embroidery threads\nNeedle set and threader\nIllustrated stitch guide",
    ],
];

// Create all products and store references for Phase 5
$product_pages = [];

foreach ($products_data as $pdata) {
    $parent = $pages->get($pdata['parent']);
    if (!$parent->id) {
        out("  [ERROR] Parent not found: {$pdata['parent']}");
        $stats['errors']++;
        continue;
    }

    $result = findOrCreate('product', $parent, $pdata['title']);
    $p = $result['page'];

    if ($result['exists']) {
        out("  [SKIP] Product already exists: {$pdata['title']}");
        $stats['skipped']++;
        $product_pages[$pdata['sku']] = $p;
        continue;
    }

    $p->of(false);
    $p->body = $pdata['body'];
    $p->summary = $pdata['summary'];
    $p->product_price = $pdata['price'];
    $p->product_sku = $pdata['sku'];
    $p->product_weight = $pdata['weight'];
    $p->product_stock = $pdata['stock'];
    $p->product_in_stock = 1;
    $p->product_features = $pdata['features'];

    if (savePage($p, $pdata['title'], $stats)) {
        // Set product_category (page reference field) — needs save first to get ID
        $p->product_category->add($parent);
        $p->save();
        out("  [OK] Created product: {$pdata['title']} ({$pdata['sku']})");
        $stats['created']++;
        $product_pages[$pdata['sku']] = $p;
    }
}


// =====================================================================
// PHASE 4: Create blog posts
// =====================================================================
out("\n=== PHASE 4: Blog posts ===\n");

$blog_parent = $pages->get('/blog/');
if (!$blog_parent->id) {
    out("  [ERROR] /blog/ parent page not found");
    $stats['errors']++;
} else {

    // Helper to get tag pages
    $getTag = function($name) use ($pages) {
        $sanitizer = wire('sanitizer');
        return $pages->get("template=blog-tag, name=" . $sanitizer->pageName($name));
    };

    $posts_data = [
        // --- News ---
        [
            'title' => 'Our New Spring Collection Is Here',
            'date' => '2026-02-15',
            'category' => '/blog-categories/news/',
            'tags' => ['Seasonal', 'Handmade'],
            'summary' => 'Fresh colours, new makers, and plenty to love — our spring collection has landed in the shop.',
            'body' => '<p>After months of planning, visiting makers, and choosing just the right pieces, we\'re thrilled to announce that our spring 2026 collection is now live in the shop.</p>

<h2>What\'s New</h2>
<p>This season we\'ve welcomed three new makers to the Maid of Threads family. Sarah Hadley\'s hand-dyed linen table runners bring a splash of botanical colour to any dining table, while Ellie Macdonald\'s screen-printed tote bags capture the spirit of the British countryside in every stitch.</p>

<p>We\'ve also expanded our popular merino wool scarf range with two new colourways for spring — a soft heather and a warm sandstone — both designed to take you from the last chilly mornings of March right through to September evenings.</p>

<h2>Small Batches, Big Heart</h2>
<p>As always, everything in the collection is made in small batches by independent British makers. When a piece sells out, it may not come back in exactly the same form — our makers work with seasonal dyes, limited yarn runs, and whatever inspiration strikes. That\'s part of what makes each purchase special.</p>

<p>Head over to <a href="/shop/new-arrivals/">New Arrivals</a> to see the full collection. And if you spot something you love, don\'t wait too long — small batches don\'t hang around.</p>',
        ],
        [
            'title' => 'Maid of Threads at the Cirencester Craft Fair',
            'date' => '2025-12-02',
            'category' => '/blog-categories/news/',
            'tags' => ['Local Makers', 'Studio Life'],
            'summary' => 'We had the most wonderful weekend at the Cirencester Christmas Craft Fair. Here\'s what happened.',
            'body' => '<p>What a weekend! The annual Cirencester Christmas Craft Fair is always one of our favourite events of the year, and this year was no exception. Over two days, we met hundreds of people, reconnected with loyal customers, and introduced plenty of new faces to the world of handmade textiles.</p>

<h2>Meeting the Makers</h2>
<p>For the first time, we shared our stall with three of our makers — James Hartley (leather goods), Helen Frost (embroidery kits), and the team from the Bristol weaving cooperative. It was wonderful to see customers chatting directly with the people who made their purchases. That connection between maker and buyer is exactly what Maid of Threads is all about.</p>

<h2>Best Sellers</h2>
<p>The runaway hit of the fair was our Lavender Sachets Gift Set — we sold out by Saturday lunchtime and had to dash back to the studio for reinforcements. The Herringbone Wool Throws were also incredibly popular, with several customers buying two or three as Christmas gifts.</p>

<h2>Thank You</h2>
<p>A huge thank you to everyone who visited, bought, chatted, and supported independent makers this Christmas. Events like these remind us why we do what we do. See you next year!</p>',
        ],
        [
            'title' => 'Free Delivery This Weekend Only',
            'date' => '2025-11-10',
            'category' => '/blog-categories/news/',
            'tags' => ['Seasonal'],
            'summary' => 'This weekend only: free standard delivery on all orders, no minimum spend. Stock up for Christmas!',
            'body' => '<p>We don\'t do sales very often — our makers set fair prices and we respect that. But we do like to treat our customers from time to time, so this weekend we\'re covering the postage.</p>

<h2>The Details</h2>
<p>From Friday 10th November at midnight through to Sunday 12th at 11:59pm, all orders qualify for free standard Royal Mail delivery. No minimum spend, no code needed — it\'ll be applied automatically at checkout.</p>

<h2>Christmas Shopping Sorted</h2>
<p>With Christmas just around the corner, this is the perfect time to tick a few names off your list. Our gift collection has something for everyone — from the <a href="/shop/gifts/">Craft Lover\'s Starter Kit</a> for the creative one to the <a href="/shop/gifts/">Embroidered Handkerchief Set</a> for someone who appreciates the classics.</p>

<p>Every order is wrapped in our signature recycled packaging, so it\'ll look as good under the tree as it does in use.</p>

<p>Happy shopping — and happy posting, on us!</p>',
        ],

        // --- Inspiration ---
        [
            'title' => '5 Ways to Style a Wool Throw This Winter',
            'date' => '2026-01-20',
            'category' => '/blog-categories/inspiration/',
            'tags' => ['Seasonal', 'Textile Care'],
            'summary' => 'A good wool throw is the hardest-working piece in your home. Here are five ways to make the most of yours.',
            'body' => '<p>A wool throw is one of those rare things that\'s both beautiful and genuinely useful. Ours get used every single day at the studio — and at home, they\'re practically family members. Here are five ways to get the most from yours.</p>

<h2>1. The Classic Sofa Drape</h2>
<p>The obvious one, but worth doing well. Fold your throw in thirds lengthways and drape it over one arm of the sofa, letting it cascade naturally. This gives your living room an instant "styled" look and keeps the throw within arm\'s reach for evening telly.</p>

<h2>2. The Bed Layer</h2>
<p>Fold the throw widthways and lay it across the bottom third of your bed. It adds visual texture, extra warmth on cold nights, and saves you reaching for the duvet when you\'re reading in bed.</p>

<h2>3. The Reading Chair Companion</h2>
<p>If you have a favourite armchair, drape the throw over the back so it falls behind the cushion. When you sit down, it\'s right there — pull it over your lap and you\'re set for the afternoon.</p>

<h2>4. The Picnic Essential</h2>
<p>Wool throws are naturally water-resistant (the lanolin in wool repels moisture). Fold yours up, tuck it under your arm, and take it to the park. It\'ll keep the damp grass at bay far better than a cotton blanket.</p>

<h2>5. The Travel Wrap</h2>
<p>Our Herringbone Wool Throw is just the right size to use as a travel blanket. It\'s warmer than an airline blanket, nicer to touch, and makes any journey feel a little more luxurious. Roll it tightly and secure with a ribbon or leather strap.</p>

<h2>Caring for Your Throw</h2>
<p>Wool throws rarely need washing — just air them regularly and spot-clean as needed. When they do need a proper clean, hand-wash in cool water with a gentle wool detergent. Never tumble dry; lay flat to dry instead.</p>',
        ],
        [
            'title' => 'The Perfect Handmade Gift Guide for 2025',
            'date' => '2025-11-25',
            'category' => '/blog-categories/inspiration/',
            'tags' => ['Gift Guide', 'Handmade'],
            'summary' => 'Stuck for gift ideas? Here\'s our guide to choosing handmade presents that people will actually love.',
            'body' => '<p>Finding the right gift is an art. Finding a handmade gift that someone will genuinely use and treasure? That\'s the dream. Here\'s our guide to getting it right this year.</p>

<h2>For the Homebody</h2>
<p>If they love nothing more than a quiet evening in, a <strong>Herringbone Wool Throw</strong> is the ultimate gift. It\'s the textile equivalent of a warm hug — and at this quality, it\'ll last decades. Pair it with our <strong>Linen Cushion Covers</strong> for the complete cosy living room upgrade.</p>

<h2>For the Eco-Conscious Friend</h2>
<p>Skip the fast fashion and go for a <strong>Woven Cotton Market Bag</strong> — handmade, unique, and a genuine replacement for plastic bags. Add a set of our <strong>Cotton Waffle Kitchen Towels</strong> for a practical gift that aligns with their values.</p>

<h2>For the Creative One</h2>
<p>Our <strong>Craft Lover\'s Starter Kit</strong> is designed for complete beginners and comes with everything they need to try embroidery. It\'s the gift that keeps giving — once they\'re hooked, they\'ll be stitching for years.</p>

<h2>For the Person Who Has Everything</h2>
<p>A set of <strong>Lavender Sachets</strong> is small, beautiful, and the kind of thing no one buys for themselves. It\'s a thoughtful touch that says "I know you appreciate the little things."</p>

<h2>For the Style-Conscious</h2>
<p>The <strong>Silk-Blend Evening Scarf</strong> is a piece they\'ll wear again and again. It\'s elegant enough for an evening out but versatile enough for everyday. The kind of accessory that gets compliments.</p>

<p>All our products come beautifully wrapped in recycled packaging — no need for extra wrapping paper. <a href="/shop/gifts/">Browse the full gift collection here.</a></p>',
        ],
        [
            'title' => 'Mixing Textures: Linen, Cotton, and Wool in Your Home',
            'date' => '2025-10-15',
            'category' => '/blog-categories/inspiration/',
            'tags' => ['Natural Fibres'],
            'summary' => 'How to combine different natural fabrics for a home that feels layered, warm, and effortlessly put together.',
            'body' => '<p>One of the simplest ways to make a room feel interesting is to mix textures. A space filled with a single fabric — all smooth cotton, or all chunky wool — can feel flat. But layer different natural fibres together and suddenly everything comes alive.</p>

<h2>The Three Essential Textures</h2>
<p><strong>Linen</strong> brings a relaxed, slightly rumpled elegance. It\'s the fabric that says "I care about quality but I don\'t iron." Use it for cushion covers, table runners, and curtains.</p>
<p><strong>Cotton</strong> is the workhorse — clean, crisp, and versatile. It works everywhere, from kitchen towels to bedding. Choose waffle or herringbone weaves to add visual interest.</p>
<p><strong>Wool</strong> adds warmth and weight. A wool throw on a linen sofa or a wool cushion on a cotton-dressed bed creates an instant sense of cosiness.</p>

<h2>Rules of Thumb</h2>
<ul>
<li><strong>Stick to a palette</strong> — mixing textures works best when the colours are harmonious. Neutrals (cream, stone, sage, charcoal) let the textures do the talking.</li>
<li><strong>Vary the scale</strong> — combine a chunky knitted throw with a fine linen cushion cover. The contrast is what makes it work.</li>
<li><strong>Touch everything</strong> — shopping online is convenient, but if you can, visit a shop and feel the fabrics. Texture is a tactile experience.</li>
<li><strong>Don\'t overthink it</strong> — there are no real rules. If it feels good, it is good.</li>
</ul>

<h2>Getting Started</h2>
<p>If you\'re new to texture-mixing, start with one room and one change. Swap out a synthetic cushion cover for a linen one. Add a wool throw to the sofa. Replace paper napkins with cotton ones. Small changes, big difference.</p>',
        ],

        // --- Behind the Scenes ---
        [
            'title' => 'Meet the Maker: Sarah\'s Hand-Dyed Linens',
            'date' => '2026-02-01',
            'category' => '/blog-categories/behind-the-scenes/',
            'tags' => ['Local Makers', 'Handmade', 'Natural Fibres'],
            'summary' => 'We visited textile artist Sarah Hadley in her Cotswolds studio to learn about her plant-based dyeing process.',
            'body' => '<p>Sarah Hadley\'s studio is tucked behind a stone wall in a small village about fifteen minutes from our shop in Cirencester. When I arrived on a Tuesday morning, she was already elbow-deep in a vat of weld dye — the kitchen-window light turning everything a warm gold.</p>

<h2>The Dyeing Process</h2>
<p>"I start with the plants," Sarah explains, gesturing to bunches of dried weld, woad, and madder root hanging from the studio ceiling. "Most of my dyes come from plants I grow myself or forage locally. The weld gives me yellows and golds, woad gives blues, madder gives reds and pinks. Everything in between is about layering."</p>

<p>Each piece of linen is dipped and dried multiple times to build up colour. The process takes several days — sometimes a week for the deeper tones. "You can\'t rush plant dyes," Sarah says. "The colour develops slowly. It\'s more like cooking than painting."</p>

<h2>Why Linen?</h2>
<p>"Linen takes plant dyes beautifully. It has a natural lustre that makes the colours glow. And it\'s incredibly durable — a linen table runner will outlast you if you treat it well."</p>

<h2>Sustainability</h2>
<p>Sarah\'s process uses no synthetic chemicals. The spent dye baths go on her garden (the plants love it), and any linen offcuts become stuffing for her cat\'s bed. "There\'s virtually no waste," she says with a smile. "The whole process is circular."</p>

<p>You can find Sarah\'s work in our <a href="/shop/new-arrivals/">New Arrivals</a> section — including the Hand-Dyed Linen Table Runner, which is one of our most popular new additions.</p>',
        ],
        [
            'title' => 'From Fleece to Fabric: How Our Wool Products Are Made',
            'date' => '2025-12-18',
            'category' => '/blog-categories/behind-the-scenes/',
            'tags' => ['Natural Fibres', 'Sustainability'],
            'summary' => 'The journey of British wool — from sheep in the hills to the throws and scarves in your home.',
            'body' => '<p>Every wool product we sell starts the same way: with a sheep, a hillside, and a farmer. But the journey from fleece to finished fabric involves more skill, tradition, and care than most people realise.</p>

<h2>Shearing and Sorting</h2>
<p>Shearing happens once a year, usually in early summer. The raw fleece is then sorted by hand — different parts of the sheep produce different qualities of wool. The finest, softest fibres come from the shoulders and sides; the coarser ones from the legs and belly.</p>

<h2>Scouring and Carding</h2>
<p>Raw wool contains lanolin, dirt, and vegetable matter. Scouring washes all this away (the lanolin is often saved and sold separately — it\'s the main ingredient in many hand creams). Carding aligns the clean fibres and blends them into a consistent yarn-ready preparation.</p>

<h2>Spinning and Weaving</h2>
<p>The prepared fibre is spun into yarn on industrial spinning frames — or, in some smaller mills, on traditional mule spinners. The yarn is then warped onto looms and woven into fabric. Our Herringbone Wool Throw, for example, is woven on shuttle looms in a Welsh mill that has been operating since 1809.</p>

<h2>Finishing</h2>
<p>After weaving, the fabric is washed again to tighten the weave, then "raised" — brushed to bring up a soft nap on the surface. Finally, it\'s pressed and checked for defects. The whole process, from fleece to finished fabric, takes several weeks.</p>

<h2>Why It Matters</h2>
<p>British wool is one of the most sustainable fibres on earth. The sheep graze on land that\'s unsuitable for crops, the wool is entirely renewable, and the finished product is naturally fire-resistant, moisture-wicking, and biodegradable. When you choose wool, you\'re choosing a fibre that works with nature, not against it.</p>',
        ],
        [
            'title' => 'A Day in the Maid of Threads Studio',
            'date' => '2025-10-28',
            'category' => '/blog-categories/behind-the-scenes/',
            'tags' => ['Studio Life'],
            'summary' => 'Ever wondered what goes on behind the scenes at Maid of Threads? Here\'s a peek at a typical Tuesday.',
            'body' => '<p>People sometimes ask what we actually do all day. Fair question — we\'re not exactly a factory. But running a small shop that works directly with independent makers involves more variety than you might think. Here\'s what a typical Tuesday looks like.</p>

<h2>8:30am — Arriving</h2>
<p>The studio is above the shop on Market Place. I unlock the back door, put the kettle on, and check overnight orders. On a good morning there are five or six; on a quiet one, maybe two. Every order gets a little moment of celebration — someone, somewhere, chose to buy something handmade instead of mass-produced.</p>

<h2>9:00am — Packing Orders</h2>
<p>Packing is meditative. Each item gets wrapped in tissue paper, tucked into a recycled kraft mailer, and sealed with a branded sticker. I include a hand-written thank-you card with every order. It takes longer than a machine could do it, but that\'s the point.</p>

<h2>10:00am — Shop Opens</h2>
<p>Thursday to Saturday we\'re open to walk-ins, but on Tuesdays the shop is closed to the public. This is when the behind-the-scenes work happens.</p>

<h2>10:30am — Maker Calls</h2>
<p>Today I\'m catching up with James in Devon about a new leather design, and checking in with the Bristol weavers about a custom colour run. These relationships are the heart of the business — we don\'t just buy wholesale, we collaborate.</p>

<h2>12:00pm — Photography</h2>
<p>New stock means new photos. I use natural light from the studio\'s big north-facing window and a simple linen backdrop. No fancy equipment — just a decent camera, good light, and patience.</p>

<h2>2:00pm — Website Updates</h2>
<p>Adding new products, writing descriptions, and updating the blog. I try to write product descriptions that tell the story of each piece — who made it, how, and why it\'s worth caring about.</p>

<h2>4:00pm — Planning</h2>
<p>The last hour is for thinking ahead. Upcoming markets, seasonal collections, new maker visits. I keep a notebook full of ideas, fabric swatches, and scribbled plans. It\'s chaotic but it works.</p>

<h2>5:00pm — Closing Up</h2>
<p>Lock the door, walk home through the market square. Tomorrow we do it all again — and I wouldn\'t have it any other way.</p>',
        ],
    ];

    foreach ($posts_data as $post_data) {
        $result = findOrCreate('blog-post', $blog_parent, $post_data['title']);
        $p = $result['page'];

        if ($result['exists']) {
            out("  [SKIP] Post already exists: {$post_data['title']}");
            $stats['skipped']++;
            continue;
        }

        $p->of(false);
        $p->body = $post_data['body'];
        $p->summary = $post_data['summary'];
        $p->date = $post_data['date'];

        if (savePage($p, $post_data['title'], $stats)) {
            // Add category
            $cat = $pages->get($post_data['category']);
            if ($cat->id) {
                $p->blog_categories->add($cat);
            }

            // Add tags
            foreach ($post_data['tags'] as $tag_title) {
                $tag = $getTag($tag_title);
                if ($tag->id) {
                    $p->blog_tags->add($tag);
                }
            }
            $p->save();

            out("  [OK] Created post: {$post_data['title']}");
            $stats['created']++;
        }
    }
}


// =====================================================================
// PHASE 5: Set related products
// =====================================================================
out("\n=== PHASE 5: Related products ===\n");

$related_map = [
    'MOT-NW-001' => ['MOT-AC-001', 'MOT-HW-001'],        // Merino Scarf → Silk Scarf, Wool Throw
    'MOT-NW-002' => ['MOT-HW-002', 'MOT-HW-003'],        // Linen Runner → Cushion Covers, Kitchen Towels
    'MOT-NW-003' => ['MOT-AC-002', 'MOT-GF-001'],         // Tote Bag → Market Bag, Lavender Sachets
    'MOT-AC-001' => ['MOT-NW-001', 'MOT-AC-003'],         // Silk Scarf → Merino Scarf, Leather Purse
    'MOT-AC-002' => ['MOT-NW-003', 'MOT-GF-003'],         // Market Bag → Tote Bag, Starter Kit
    'MOT-AC-003' => ['MOT-AC-001', 'MOT-GF-002'],         // Leather Purse → Silk Scarf, Handkerchiefs
    'MOT-HW-001' => ['MOT-HW-002', 'MOT-NW-001'],        // Wool Throw → Cushion Covers, Merino Scarf
    'MOT-HW-002' => ['MOT-HW-001', 'MOT-NW-002'],        // Cushion Covers → Wool Throw, Linen Runner
    'MOT-HW-003' => ['MOT-NW-002', 'MOT-GF-001'],        // Kitchen Towels → Linen Runner, Lavender Sachets
    'MOT-GF-001' => ['MOT-GF-002', 'MOT-HW-002'],        // Lavender Sachets → Handkerchiefs, Cushion Covers
    'MOT-GF-002' => ['MOT-GF-001', 'MOT-AC-003'],        // Handkerchiefs → Lavender Sachets, Leather Purse
    'MOT-GF-003' => ['MOT-AC-002', 'MOT-NW-003'],        // Starter Kit → Market Bag, Tote Bag
];

foreach ($related_map as $sku => $related_skus) {
    if (!isset($product_pages[$sku])) {
        out("  [SKIP] Product not found for SKU: $sku");
        $stats['skipped']++;
        continue;
    }

    $p = $product_pages[$sku];
    $p->of(false);

    // Check if related products already set
    if ($p->related_products && $p->related_products->count) {
        out("  [SKIP] Related products already set for: {$p->title}");
        $stats['skipped']++;
        continue;
    }

    foreach ($related_skus as $rel_sku) {
        if (isset($product_pages[$rel_sku])) {
            $p->related_products->add($product_pages[$rel_sku]);
        }
    }

    try {
        $p->save();
        out("  [OK] Set related products for: {$p->title}");
        $stats['updated']++;
    } catch (\Exception $e) {
        out("  [ERROR] Related products for {$p->title}: " . $e->getMessage());
        $stats['errors']++;
    }
}


// =====================================================================
// SUMMARY
// =====================================================================
out("\n=== COMPLETE ===\n");
out("  Created: {$stats['created']}");
out("  Updated: {$stats['updated']}");
out("  Skipped: {$stats['skipped']}");
out("  Errors:  {$stats['errors']}");
out("");

exit($stats['errors'] > 0 ? 1 : 0);
