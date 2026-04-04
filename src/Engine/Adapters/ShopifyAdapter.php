<?php 

namespace Qck\FeedEngine\Engine\Adapters;

use Qck\FeedEngine\Engine\Data\FeedItem;

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

    public static function create_item_from_shopify_data(array $raw_data): FeedItem {

        $new_item = new FeedItem();

        $new_item->id   = (string) ($raw_data['id'] ?? '');

        $new_item->title       = (string) ($raw_data['title'] ?? 'Untitled Product');

        $new_item->url       = self::build_shopify_url($raw_data);

        $new_item->feature_image_url   = self::extract_primary_image($raw_data);

        $new_item->price_display_value = self::format_price($raw_data);

        $new_item->source  = 'shopify';

        $new_item->raw = $raw_data;

        return $new_item;

    }

    private static function build_shopify_url(array $data): string {

        $handle = $data['handle'] ?? '';

        return "/products/" . $handle;

    }

    private static function extract_primary_image(array $data): string {

        $images = $data['images'] ?? [];

        return $images[0]['src'] ?? '';

    }

    private static function format_price(array $data): string {

        $variants = $data['variants'] ?? [];

        $price = $variants[0]['price'] ?? '0.00';

        return "$" . number_format((float) $price, 2);

    }
}