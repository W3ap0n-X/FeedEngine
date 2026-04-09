<?php 
namespace Qck\FeedEngine\Engine\Data;

class FeedItem {
    public string $id;
    public string $title;
    public string $url;
    public string $image_url;
    public string $price;      // Empty for posts, filled for products
    public string $type;       // 'wp_post', 'shopify_product', etc.
    public array  $raw;   // For the "just in case" moments
    public string $source;
}