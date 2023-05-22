<?php

const GTM_CONTAINER_ID = 'GTM-P6ZSBHC';

add_action('wp_head', 'gtm_config_head');
add_action('wp_body_open', 'gtm_config_body');
add_action('woocommerce_before_shop_loop', 'gtm_view_item_list');
add_action('woocommerce_before_single_product', 'gtm_view_item');
add_action('woocommerce_add_to_cart', 'gtm_add_to_cart', 10, 3);
add_action('woocommerce_before_cart', 'gtm_view_cart');
add_action('woocommerce_before_checkout_form', 'gtm_begin_checkout');
add_action('woocommerce_thankyou', 'gtm_purchase', 10, 1);

function gtm_config_head()
{
    ?>
<!-- Google Tag Manager -->
<script>
    window.dataLayer = window.dataLayer || [];
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?= GTM_CONTAINER_ID ?>');
</script>
<!-- End Google Tag Manager -->
<?php
}

function gtm_config_body()
{
    ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe
        src="https://www.googletagmanager.com/ns.html?id=<?= GTM_CONTAINER_ID ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
}

function gtm_view_item_list()
{
    $product_query = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    ));
    $products = $product_query->get_posts(); ?>
<script>
    dataLayer.push({
        event: 'view_item_list',
        ecommerce: {
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
        }
    });
</script>
<?php
}


function gtm_view_item()
{
    global $product;
    if (!$product) {
        return;
    }
    ?>
<script>
    dataLayer.push({
        event: 'view_item',
        ecommerce: {
            value: <?= $product->get_price() ?> ,
            currency: '<?= get_woocommerce_currency() ?>',
            items: [{
                item_id: '<?= $product->get_id() ?>',
                item_name: '<?= $product->get_name() ?>',
                price: <?= $product->get_price() ?> ,
            }],
        },
    });
</script>
<?php
}

function gtm_add_to_cart($cart_item_key, $product_id, $quantity)
{
    $product = wc_get_product($product_id);
    $price = $product->get_price();
    ?>
<script>
    dataLayer.push({
        event: 'add_to_cart',
        currency: '<?= get_woocommerce_currency() ?>',
        value: <?= $price * $quantity ?> ,
        items: [{
            item_id: '<?= $product->get_id() ?>',
            item_name: '<?= $product->get_name() ?>',
            price: <?= $price ?> ,
            quantity: <?= $quantity ?> ,
        }]
    });
</script>
<?php
}

function gtm_view_cart()
{
    $cart_items = WC()->cart->get_cart();
    ?>
<script>
    dataLayer.push({
        event: 'view_cart',
        ecommerce: {
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
        },
    });
</script>
<?php
}


function gtm_begin_checkout()
{
    $cart_items = WC()->cart->get_cart();
    ?>
<script>
    dataLayer.push({
        event: 'begin_checkout',
        ecommerce: {
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
        },
    });
</script>
<?php
}

function gtm_purchase($order_id)
{
    $order = wc_get_order($order_id);
    ?>
<script>
    dataLayer.push({
        event: 'purchase',
        ecommerce: {
            transaction_id: '<?= $order->get_order_number() ?>',
            value: <?= $order->get_subtotal() ?> ,
            currency: '<?= $order->get_currency() ?>',
            items: [
                <?php foreach ($order->get_items() as $item): ?>
                {
                    item_id: '<?= $item->get_id() ?>',
                    item_name: '<?= $item->get_name() ?>',
                    item_quantity: <?= $item->get_quantity() ?> ,
                },
                <?php endforeach ?>
            ],
        },
    });
</script>
<?php
}

?>