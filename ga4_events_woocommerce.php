<?php

add_action('head', 'ga4_config');
add_action('woocommerce_before_single_product', 'ga4_view_item', 10, 1);
add_action('woocommerce_add_to_cart', 'ga4_add_to_cart');
add_action('woocommerce_before_checkout_form', 'ga4_begin_checkout');
add_action('woocommerce_thankyou', 'ga4_purchase');
add_action('woocommerce_before_cart', 'ga4_view_cart');

function ga4_config() {
?>
<script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PZ5MNKZ88B"></script>
<script>
 window.dataLayer = window.dataLayer || [];
 function gtag(){dataLayer.push(arguments);}
 gtag('js', new Date());
 gtag('config', 'G-PZ5MNKZ88B');
</script><!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P6ZSBHC');</script>
<!-- End Google Tag Manager -->
<?php
}

function ga4_view_item() {
global $product;
if (!$product) {
        return;
    }
?>
<script>
gtag('event', 'view_item', {
value: <?= $product->get_price() ?>,
currency: '<?= get_woocommerce_currency() ?>',
items: [
{
item_id: '<?= $product->get_id() ?>',
item_name: '<?= $product->get_name() ?>',
price: <?= $product->get_price() ?>,
}
],
});
</script>
<?php
}

function ga4_add_to_cart() {
?>
<script>
const wait_for_config = setInterval(function(){
if(typeof gtag === 'function') {
gtag('event', 'add_to_cart', {

});
clearInterval(wait_for_config);
}
}, 20);
</script>
<?php
}

function ga4_view_cart() {
?>
<script>
gtag('event', 'view_cart', {

});
</script>
<?php
}

function ga4_begin_checkout() {
?>
<script>
gtag('event', 'begin_checkout', {

});
</script>
<?php
}

function ga4_purchase() {
?>
<script>
gtag('event', 'purchase', {

});
</script>
<?php
}
