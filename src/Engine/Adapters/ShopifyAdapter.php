<?php 

namespace Qck\FeedEngine\Core\Adapters;

class ShopifyAdapter {
    public static function map(array $product): FeedItem {
        $item = new FeedItem();
        $item->id        = $product['id'];
        $item->title     = $product['title'];
        $item->url       = "/products/{$product['handle']}";
        $item->type      = 'shopify_product';
        
        // Shopify image structure: images[0] -> src
        $item->image_url = $product['images'][0]['src'] ?? '';
        
        // Shopify price: usually variants[0] -> price
        $item->price     = $product['variants'][0]['price'] ?? '';
        
        return $item;
    }
}