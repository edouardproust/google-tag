<?php

const GA4_MEASUREMENT_ID = 'G-PZ5MNKZ88B';

add_action('wp_head', 'ga4_config');
add_action('woocommerce_before_shop_loop', 'ga4_view_item_list');
add_action('woocommerce_before_single_product', 'ga4_view_item');
add_action('woocommerce_add_to_cart', 'ga4_add_to_cart', 10, 3);
add_action('woocommerce_before_cart', 'ga4_view_cart');
add_action('woocommerce_before_checkout_form', 'ga4_begin_checkout');
add_action('woocommerce_thankyou', 'ga4_purchase', 10, 1);

function ga4_config()
{
    ?>
<script async
    src="https://www.googletagmanager.com/gtag/js?id=<?= GA4_MEASUREMENT_ID ?>">
</script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '<?= GA4_MEASUREMENT_ID ?>');
</script>
<?php
}

function ga4_view_item_list()
{
    $product_query = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    ));
    $products = $product_query->get_posts(); ?>
<script>
    gtag('event', 'view_item_list', {
        currency: '<?= get_woocommerce_currency() ?>',
        items: [
            <?php foreach ($products as $product): ?>
            <?php $wc_product = wc_get_product($product->ID); ?>
            {
                item_id: '<?= $product->ID ?>',
                item_name: '<?= $product->post_title ?>',
                price: <?= $wc_product->get_price() ?> ,
                quantity: 1,
            },
            <?php endforeach ?>
        ],
    });
</script>
<?php
}


function ga4_view_item()
{
    global $product;
    if (!$product) {
        return;
    }
    ?>
<script>
    gtag('event', 'view_item', {
        value: <?= $product->get_price() ?> ,
        currency: '<?= get_woocommerce_currency() ?>',
        items: [{
            item_id: '<?= $product->get_id() ?>',
            item_name: '<?= $product->get_name() ?>',
            price: <?= $product->get_price() ?> ,
        }],
    });
</script>
<?php
}

function ga4_add_to_cart($cart_item_key, $product_id, $quantity)
{
    $product = wc_get_product($product_id);
    $price = $product->get_price();
    ?>
<script>
    const waitForGtag = setInterval(function() {
        if (typeof gtag === 'function') {
            gtag('event', 'add_to_cart', {
                currency: '<?= get_woocommerce_currency() ?>',
                value: <?= $price * $quantity ?> ,
                items: [{
                    item_id: '<?= $product->get_id() ?>',
                    item_name: '<?= $product->get_name() ?>',
                    price: <?= $price ?> ,
                    quantity: <?= $quantity ?> ,
                }]
            });
            clearInterval(waitForGtag);
        }
    }, 100);
</script>
<?php
}

function ga4_view_cart()
{
    $cart_items = WC()->cart->get_cart();
    ?>
<script>
    gtag('event', 'view_cart', {
        currency: '<?= get_woocommerce_currency() ?>',
        value: <?= WC()->cart->subtotal ?> ,
        items: [
            <?php foreach ($cart_items as $cart_item): ?>
            <?php $product = $cart_item['data'] ?>
            {
                item_id: '<?= $product->get_id() ?>',
                item_name: '<?= $product->get_name() ?>',
                price: <?= $product->get_price() ?> ,
                quantity: <?= $cart_item['quantity'] ?> ,
            },
            <?php endforeach ?>
        ],
    });
</script>
<?php
}


function ga4_begin_checkout()
{
    $cart_items = WC()->cart->get_cart();
    ?>
<script>
    gtag('event', 'begin_checkout', {
        currency: '<?= get_woocommerce_currency() ?>',
        value: <?= WC()->cart->subtotal ?> ,
        items: [
            <?php foreach ($cart_items as $cart_item): ?>
            <?php $product = $cart_item['data'] ?>
            {
                item_id: '<?= $product->get_id() ?>',
                item_name: '<?= $product->get_name() ?>',
                price: <?= $product->get_price() ?> ,
                quantity: <?= $cart_item['quantity'] ?> ,
            },
            <?php endforeach ?>
        ],
    });
</script>
<?php
}

function ga4_purchase($order_id)
{
    $order = wc_get_order($order_id);
    ?>
<script>
    gtag('event', 'purchase', {
        transaction_id: '<?= $order->get_order_number() ?>',
        value: <?= $order->get_subtotal() ?> ,
        currency: '<?= $order->get_currency() ?>',
        items: [
            <?php foreach ($order->get_items() as $item): ?>
            {
                $item_id: <?= $item->get_id() ?> ,
                $item_name: <?= $item->get_name() ?> ,
                $item_quantity: <?= $item->get_quantity() ?> ,
            },
            <?php endforeach ?>
        ],
    });
</script>
<?php
}

?>