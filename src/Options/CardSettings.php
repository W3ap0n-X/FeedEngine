<?php
namespace Qck\FeedEngine\Options;

use Qck\FeedEngine\Core\Options\OptionSection;

class CardSettings extends OptionSection {

    public function get_name(): string {
        return 'card_settings'; // DB Row: qckfe_bento_settings
    }

    public function get_title(): string {
        return 'Default Card Configuration';
    }

    public function get_description(): string {
        return 'These will act as the default settings for card styles';
    }

    public function get_schema(): array {
        return [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'card-type',
                label: 'Default Card Type',
                type: 'select',
                default: 'default',
                options: [
                    'default' => 'Default',
                ]
                
            ),
            new \Qck\FeedEngine\Core\Options\OptionField (
                

                key: 'features',
                label: 'Standard Elements',
                entries: [ 
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'excerpt',
                        label: 'Excerpt',
                        type: 'checkbox',
                        default: false,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'heading',
                        label: 'Card Heading',
                        type: 'checkbox',
                        default: true,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'image',
                        label: 'Card Image',
                        type: 'checkbox',
                        default: true,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'box-shadow',
                        label: 'Card Box Shadow',
                        type: 'checkbox',
                        default: false,
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'overlay',
                        label: 'Card Overlay',
                        type: 'checkbox',
                        default: false,
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
                                default: false,
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag',
                                label: 'Tag Badges',
                                type: 'checkbox',
                                default: false,
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type',
                                label: 'Post Type Badges',
                                type: 'checkbox',
                                default: false,
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group',
                                label: 'Group Badges',
                                type: 'checkbox',
                                default: false,
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source',
                                label: 'Feed Item Source',
                                type: 'checkbox',
                                default: false,
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
                        default: 'transparent',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'accent',
                        label: 'Card Accent Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'feature',
                        label: 'Card Feature Color',
                        type: 'text',
                        default: 'var(--qckfe-color-accent)',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'heading',
                        label: 'Card Heading Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link',
                        label: 'Card link Color',
                        type: 'text',
                        default: 'var(--qckfe-color-feature)',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link-bg',
                        label: 'Card link bg Color',
                        type: 'text',
                        default: 'transparent',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'link-border',
                        label: 'Card link border Color',
                        type: 'text',
                        default: 'var(--qckfe-color-border)',
                        label_element: 'legend',
                    ),
                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'text',
                        label: 'Card Text Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'muted',
                        label: 'Card Muted Text Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'box-shadow',
                        label: 'Card Box Shadow Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'overlay',
                        label: 'Card Overlay Color',
                        type: 'text',
                        default: 'inherit',
                        label_element: 'legend',
                    ),

                    new \Qck\FeedEngine\Core\Options\OptionEntry(
                        key: 'border',
                        label: 'Card Border Color',
                        type: 'text',
                        default: 'var(--qckfe-color-accent)',
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
                                default: 'var(--qckfe-color-feature)',
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'category-text',
                                label: 'Category Badges text',
                                type: 'text',
                                default: 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate category',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag',
                                label: 'Tag Badges',
                                type: 'text',
                                default: 'var(--qckfe-color-feature)',
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'tag-text',
                                label: 'Tag Badges text',
                                type: 'text',
                                default: 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate tag',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type',
                                label: 'Post Type Badges',
                                type: 'text',
                                default: 'var(--qckfe-color-accent)',
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'post-type-text',
                                label: 'Post Type Badges -text',
                                type: 'text',
                                default: 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                                description: 'Badge to indicate post type on frontend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group',
                                label: 'Group Badges',
                                type: 'text',
                                default: 'var(--qckfe-color-feature)',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'group-text',
                                label: 'Group Badges -text',
                                type: 'text',
                                default: 'var(--qckfe-color-muted)',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source',
                                label: 'Feed Item Source',
                                type: 'text',
                                default: 'var(--qckfe-color-accent)',
                                label_element: 'legend',
                                description: 'Badge to indicate where a feed item was pulled from (Recommended only use for troubleshooting purposes)',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'source-text',
                                label: 'Feed Item Source -text',
                                type: 'text',
                                default: 'var(--qckfe-color-muted)',
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
                                default: '100%',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-y',
                                label: 'Container Vertical Padding',
                                type: 'text',
                                default: '2rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-x',
                                label: 'Container Horizontal Padding',
                                type: 'text',
                                default: '0',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'gap',
                                label: 'Gap between cards',
                                type: 'text',
                                default: '1rem',
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
                                default: '1fr',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'min-width',
                                label: 'Card min-width',
                                type: 'text',
                                default: '240px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'img-height',
                                label: 'Card img-height',
                                type: 'text',
                                default: '200px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-y',
                                label: 'Card Vertical Padding',
                                type: 'text',
                                default: '1.5rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-x',
                                label: 'Card Horizontal Padding',
                                type: 'text',
                                default: '1rem',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-badge',
                                label: 'badge Padding',
                                type: 'text',
                                default: '0.4em 0.8em',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'padding-link',
                                label: 'link Padding',
                                type: 'text',
                                default: '0.4em 0.8em',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'heading',
                                label: 'Card Heading Fontsize',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'text',
                                label: 'Card Text Fontsize',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link',
                                label: 'Card link Fontsize',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'muted',
                                label: 'Card Muted Text Fontsize',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'heading-weight',
                                label: 'Card Heading Font-weight',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'text-weight',
                                label: 'Card Text Font-weight',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-weight',
                                label: 'Card link Font-weight',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'muted-weight',
                                label: 'Card Muted Text Font-weight',
                                type: 'text',
                                default: 'inherit',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'border-width',
                                label: 'Card Border Width',
                                type: 'text',
                                default: '1px',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-border-width',
                                label: 'link Border Width',
                                type: 'text',
                                default: 'var(--qckfe-card-border-width)',
                                label_element: 'legend',
                            ),

                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'border-radius',
                                label: 'Card Border Radius',
                                type: 'text',
                                default: '12px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'link-radius',
                                label: 'link Border Radius',
                                type: 'text',
                                default: '12px',
                                label_element: 'legend',
                            ),
                            new \Qck\FeedEngine\Core\Options\OptionEntry(
                                key: 'badge-radius',
                                label: 'badge Border Radius',
                                type: 'text',
                                default: '4px',
                                label_element: 'legend',
                            ),

                        ],
                    ),
                    
                ],
            ),

            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'custom-css',
                label: 'Custom Css',
                type: 'textarea',
                description: "If all else fails here is a place where you can add custom CSS for all feedengine items.",
            ),
        ];
    }
}