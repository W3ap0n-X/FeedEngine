<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\Data\BaseMetaBox;

class CardSettings extends BaseMetaBox {
    public function get_name(): string { return 'feed_card_settings'; }
    public function get_title(): string { return 'Feed Card Settings'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }

    public function get_context(): string {
        return 'side' ;
    }

    public function get_priority(): string {
        return 'low' ;
    }
    

    public function get_schema(): array {
        $default_options = get_option('qckfe_card_settings');
        return [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'card-type',
                label: 'Card Type',
                type: 'select',
                default: $default_options['card-type'] ?? 'default',
                options: [
                    'default' => 'Default',
                ]
                
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'use-global',
                label: 'Use Global Styles',
                type: 'checkbox',
                default: false,
                disabled: true,
                
            ),
            new \Qck\FeedEngine\Core\Options\OptionField (
                

                key: 'features',
                label: 'Standard Elements',
                entries: [ 
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'excerpt',
                        label: 'Excerpt',
                        type: 'checkbox',
                        default: $default_options['features']['excerpt'] ?? false,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'heading',
                        label: 'Card Heading',
                        type: 'checkbox',
                        default: $default_options['features']['heading'] ?? true,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'image',
                        label: 'Card Image',
                        type: 'checkbox',
                        default: $default_options['features']['image'] ?? true,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'box-shadow',
                        label: 'Card Box Shadow',
                        type: 'checkbox',
                        default: $default_options['features']['box-shadow'] ?? true,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'overlay',
                        label: 'Card Overlay',
                        type: 'checkbox',
                        default: $default_options['features']['overlay'] ?? false,
                        label_element: 'legend',
                    ),
                        
                    new \Qck\FeedEngine\Core\Options\OptionField (
                        key: 'badges',
                        label: 'Badges',
                        label_element: 'h4',
                        entries: [
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'category',
                                label: 'Category Badges',
                                type: 'checkbox',
                                default: $default_options['features']['badges']['category'] ?? false,
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag',
                                label: 'Tag Badges',
                                type: 'checkbox',
                                default: $default_options['features']['badges']['category'] ?? false,
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type',
                                label: 'Post Type Badges',
                                type: 'checkbox',
                                default: $default_options['features']['badges']['category'] ?? false,
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group',
                                label: 'Group Badges',
                                type: 'checkbox',
                                default: $default_options['features']['badges']['category'] ?? false,
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source',
                                label: 'Feed Item Source',
                                type: 'checkbox',
                                default: $default_options['features']['badges']['category'] ?? false,
                                label_element: 'legend',
                                description: 'Badge to indicate where a feed item was pulled from (Recommended only use for troubleshooting purposes)',
                            ),
                        ],
                    ),
                            
                    
                    
                ],
            ),
            new \Qck\FeedEngine\Core\Options\OptionField (

                key: 'colors',
                label: 'Colors',
                entries: [ 
                    
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'background',
                        label: 'Card BackGround Color',
                        type: 'text',
                        default: $default_options['colors']['background'] ?? 'transparent',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'accent',
                        label: 'Card Accent Color',
                        type: 'text',
                        default: $default_options['colors']['accent'] ?? 'blue',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'feature',
                        label: 'Card Feature Color',
                        type: 'text',
                        default: $default_options['colors']['feature'] ?? 'var(--qckfe-accent-color)',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'heading',
                        label: 'Card Heading Color',
                        type: 'text',
                        default: $default_options['colors']['feature'] ?? 'inherit',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link',
                        label: 'Card link Color',
                        type: 'text',
                        default: $default_options['colors']['link'] ?? 'var(--qckfe-color-feature)',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link-bg',
                        label: 'Card link bg Color',
                        type: 'text',
                        default: $default_options['colors']['link-bg'] ??'transparent',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link-border',
                        label: 'Card link border Color',
                        type: 'text',
                        default: $default_options['colors']['link-border'] ??'var(--qckfe-color-border)',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'text',
                        label: 'Card Text Color',
                        type: 'text',
                        default: $default_options['colors']['text'] ?? 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'muted',
                        label: 'Card Muted Text Color',
                        type: 'text',
                        default: $default_options['colors']['muted'] ?? 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'box-shadow',
                        label: 'Card Box Shadow Color',
                        type: 'text',
                        default: $default_options['colors']['box-shadow'] ?? 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'overlay',
                        label: 'Card Overlay Color',
                        type: 'text',
                        default: $default_options['colors']['overlay'] ?? 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'border',
                        label: 'Card Border Color',
                        type: 'text',
                        default: $default_options['colors']['border'] ?? 'var(--qckfe-accent-color)',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionField (
                        key: 'badges',
                        label: 'Badges',
                        label_element: 'h4',
                        entries: [
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'category',
                                label: 'Category Badges',
                                type: 'text',
                                default: $default_options['colors']['badges']['category'] ?? 'var(--qckfe-feature-color)',
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'category-text',
                                label: 'Category Badges text',
                                type: 'text',
                                default: $default_options['colors']['badges']['category-text'] ?? 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag',
                                label: 'Tag Badges',
                                type: 'text',
                                default: $default_options['colors']['badges']['tag'] ?? 'var(--qckfe-feature-color)',
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag-text',
                                label: 'Tag Badges text',
                                type: 'text',
                                default: $default_options['colors']['badges']['tag-text'] ?? 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type',
                                label: 'Post Type Badges',
                                type: 'text',
                                default: $default_options['colors']['badges']['post-type'] ?? 'var(--qckfe-accent-color)',
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type-text',
                                label: 'Post Type Badges -text',
                                type: 'text',
                                default: $default_options['colors']['badges']['post-type-text'] ?? 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group',
                                label: 'Group Badges',
                                type: 'text',
                                default: $default_options['colors']['badges']['group'] ?? 'var(--qckfe-feature-color)',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group-text',
                                label: 'Group Badges -text',
                                type: 'text',
                                default: $default_options['colors']['badges']['group-text'] ?? 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source',
                                label: 'Feed Item Source',
                                type: 'text',
                                default: $default_options['colors']['badges']['source'] ?? 'var(--qckfe-accent-color)',
                                label_element: 'legend',
                                description: 'Badge to indicate where a feed item was pulled from (Recommended only use for troubleshooting purposes)',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source-text',
                                label: 'Feed Item Source -text',
                                type: 'text',
                                default: $default_options['colors']['badges']['source-text'] ?? 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate where a feed item was pulled from (Recommended only use for troubleshooting purposes)',
                            ),
                        ],
                    ),
                    
                ],
            ),

            new \Qck\FeedEngine\Core\Options\OptionField (

                key: 'sizing',
                label: 'Sizing',
                
                entries: [ 
                    new \Qck\FeedEngine\Core\Options\OptionField (
                        key: 'container',
                        label: 'Container',
                        label_element: 'h4',
                        entries: [ 
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'max-width',
                                label: 'Container-width',
                                type: 'text',
                                default: $default_options['sizing']['container']['max-width'] ?? '100%',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-y',
                                label: 'Container Vertical Padding',
                                type: 'text',
                                default: $default_options['sizing']['container']['padding-y'] ?? '2rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-x',
                                label: 'Container Horizontal Padding',
                                type: 'text',
                                default: $default_options['sizing']['container']['padding-x'] ?? '0',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'gap',
                                label: 'Gap between cards',
                                type: 'text',
                                default: $default_options['sizing']['container']['gap'] ?? '1rem',
                                label_element: 'legend',
                            ),
                        ],
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionField (
                        key: 'card',
                        label: 'Cards',
                        label_element: 'h4',
                        entries: [ 
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'max-width',
                                label: 'Card max-width',
                                type: 'text',
                                default: $default_options['sizing']['card']['max-width'] ?? '1fr',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'min-width',
                                label: 'Card min-width',
                                type: 'text',
                                default: $default_options['sizing']['card']['min-width'] ?? '240px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'img-height',
                                label: 'Card img-height',
                                type: 'text',
                                default: $default_options['sizing']['card']['img-height'] ?? '200px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-y',
                                label: 'Card Vertical Padding',
                                type: 'text',
                                default: $default_options['sizing']['card']['padding-y'] ?? '1.5rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-x',
                                label: 'Container Horizontal Padding',
                                type: 'text',
                                default: $default_options['sizing']['card']['padding-x'] ?? '1rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-badge',
                                label: 'badge Padding',
                                type: 'text',
                                default: $default_options['sizing']['card']['padding-badge'] ??  '0.4em 0.8em',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-link',
                                label: 'link Padding',
                                type: 'text',
                                default: $default_options['sizing']['card']['padding-link'] ??  '0.4em 0.8em',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'heading',
                                label: 'Card Heading Fontsize',
                                type: 'text',
                                default: $default_options['sizing']['card']['heading'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'text',
                                label: 'Card Text Fontsize',
                                type: 'text',
                                default: $default_options['sizing']['card']['text'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link',
                                label: 'Card link Fontsize',
                                type: 'text',
                                default: $default_options['sizing']['card']['link'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'muted',
                                label: 'Card Muted Text Fontsize',
                                type: 'text',
                                default: $default_options['sizing']['card']['muted'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'heading-weight',
                                label: 'Card Heading Font-weight',
                                type: 'text',
                                default: $default_options['sizing']['card']['heading-weight'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'text-weight',
                                label: 'Card Text Font-weight',
                                type: 'text',
                                default: $default_options['sizing']['card']['text-weight'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-weight',
                                label: 'Card link Font-weight',
                                type: 'text',
                                default: $default_options['sizing']['card']['link-weight'] ?? 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'muted-weight',
                                label: 'Card Muted Text Font-weight',
                                type: 'text',
                                default: $default_options['sizing']['card']['muted-weight'] ?? 'inherit',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'border-width',
                                label: 'Card Border Width',
                                type: 'text',
                                default: $default_options['sizing']['card']['border-width'] ?? '1px',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-border-width',
                                label: 'link Border Width',
                                type: 'text',
                                default: $default_options['sizing']['card']['link-border-width'] ?? 'var(--qckfe-card-border-width)',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'border-radius',
                                label: 'Card Border Radius',
                                type: 'text',
                                default: $default_options['sizing']['card']['border-radius'] ?? '12px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-radius',
                                label: 'link Border Radius',
                                type: 'text',
                                default: $default_options['sizing']['card']['link-radius'] ?? '12px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'badge-radius',
                                label: 'badge Border Radius',
                                type: 'text',
                                default: $default_options['sizing']['card']['badge-radius'] ?? '4px',
                                label_element: 'legend',
                            ),

                        ],
                    ),
                    
                ],
            ),

            // new \Qck\FeedEngine\Core\Options\OptionEntry(
            //     key: 'custom-css',
            //     label: 'Custom Css',
            //     type: 'textarea',
            //     description: "If all else fails here is a place where you can add custom CSS for all feedengine items.",
            // ),
        ];
    }
}