<?php
namespace Qck\FeedEngine\Options;
use Qck\FeedEngine\Manifest;
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
        $options = [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'grid-type',
                label: 'Default Grid Type',
                type: 'select',
                default: 'standard',
                options: [
                    'standard'  => 'Standard Grid Layout',
                    'bento'     => 'Asymmetric Bento Box',
                    'editorial' => 'Editorial Row Stream',
                    'magazine'  => 'Magazine Matrix',
                ]
                
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'card-type',
                label: 'Default Card Type',
                type: 'select',
                default: 'standard',
                options: [
                    'standard' => 'Standard',
                    'left' => 'Left',
                    'right' => 'Right',
                    'overlay' => 'Overlay',
                ]
                
            ),
            
            $features = $this->get_features(),
            $colors = $this->get_colors(),

            $sizing = $this->get_sizing(),

            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'custom-css',
                label: 'Custom Css',
                type: 'textarea',
                description: "If all else fails here is a place where you can add custom CSS for all feedengine items.",
            ),
        ];
        return $options;
    }

    public function get_features() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
                

            key: 'features',
            label: 'Standard Elements',
            
            entries: [ 
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'title',
                    label: 'Feed Title',
                    type: 'checkbox',
                    default: false,
                    label_element: 'legend',
                ),

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
                    class: Manifest::PREFIX . '-field-collapsible collapsed',
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
        );
    }

    public function get_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (

            key: 'colors',
            label: 'Colors',
            
            entries: [ 
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

                $this->get_container_colors(),
                $this->get_grid_colors(),
                $this->get_card_colors(),
                $this->get_text_colors(),
                $this->get_link_colors(),
                $this->get_badge_colors(),
                
            ],
        );
    }

    public function get_container_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'container',
            label: 'container Colors',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'background',
                    label: 'container BackGround Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border',
                    label: 'container border Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_grid_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'grid',
            label: 'Grid Colors',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'background',
                    label: 'Grid BackGround Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border',
                    label: 'Grid border Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_card_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'card',
            label: 'Card Colors',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'background',
                    label: 'Card BackGround Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'box-shadow',
                    label: 'Card Box Shadow Color',
                    type: 'text',
                    default: 'rgba(0,0,0,0.12)',
                    label_element: 'legend',
                ),

                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'hover-shadow',
                    label: 'Card Box hover Color',
                    type: 'text',
                    default: 'rgba(0,0,0,0.2)',
                    label_element: 'legend',
                ),

                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'overlay',
                    label: 'Card Overlay Color',
                    type: 'text',
                    default: 'rgba(0,0,0,0.8)',
                    label_element: 'legend',
                ),

                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border',
                    label: 'Card Border Color',
                    type: 'text',
                    default: 'var(--qckfe-color-accent)',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_text_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'text',
            label: 'Font Colors',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'heading',
                    label: 'Card Heading Color',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'default',
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

            ],

        );
    }

    public function get_link_colors() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'link',
            label: 'Read More Link',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'text',
                    label: 'Card link Color',
                    type: 'text',
                    default: 'var(--qckfe-color-feature)',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'background',
                    label: 'Card link bg Color',
                    type: 'text',
                    default: 'transparent',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border',
                    label: 'Card link border Color',
                    type: 'text',
                    default: 'var(--qckfe-color-border)',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_badge_colors() {
        $field = new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'badges',
            label: 'Badges',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
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
        );
        return $field;
    }

    public function get_sizing() {
        return new \Qck\FeedEngine\Core\Options\OptionField (

            key: 'sizing',
            label: 'Sizing',
            
            entries: [ 
                $this->get_container_sizing(),
                $this->get_grid_sizing(),

                $this->get_card_sizing(),
                $this->get_text_sizes(),
                
            ],
        );
    }

    public function get_grid_sizing() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'grid',
            label: 'Grid',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [ 
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'max-width',
                    label: 'Width',
                    type: 'text',
                    default: '100%',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'padding-y',
                    label: 'Vertical Padding',
                    type: 'text',
                    default: '2rem',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'padding-x',
                    label: 'Horizontal Padding',
                    type: 'text',
                    default: '0',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'margin-y',
                    label: 'Vertical margin',
                    type: 'text',
                    default: '0',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'margin-x',
                    label: 'Horizontal margin',
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
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border-width',
                    label: 'Grid Border Width',
                    type: 'text',
                    default: '1px',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border-radius',
                    label: 'Grid Border Radius',
                    type: 'text',
                    default: '12px',
                    label_element: 'legend',
                ),
            ],
        );
    }

    public function get_container_sizing() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'container',
            label: 'Container',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [ 
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'max-width',
                    label: 'Width',
                    type: 'text',
                    default: '100%',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'padding-y',
                    label: 'Vertical Padding',
                    type: 'text',
                    default: '2rem',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'padding-x',
                    label: 'Horizontal Padding',
                    type: 'text',
                    default: '0',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'margin-y',
                    label: 'Vertical margin',
                    type: 'text',
                    default: '0',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'margin-x',
                    label: 'Horizontal margin',
                    type: 'text',
                    default: '0',
                    label_element: 'legend',
                ),
                
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border-width',
                    label: ' Border Width',
                    type: 'text',
                    default: '1px',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'border-radius',
                    label: ' Border Radius',
                    type: 'text',
                    default: '12px',
                    label_element: 'legend',
                ),
            ],
        );
    }

    public function get_card_sizing() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'card',
            label: 'Cards',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
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
                    key: 'box-shadow',
                    label: 'Card box-shadow-size',
                    type: 'text',
                    default: '10px 8px 24px',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'hover-shadow',
                    label: 'Card hover-shadow-size',
                    type: 'text',
                    default: '0 8px 24px',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'overlay',
                    label: 'Card overlay-height',
                    type: 'text',
                    default: '20%',
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
        );
    }

    public function get_text_sizes() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'text',
            label: 'Font Options',
            label_element: 'h4',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                
                
                
                
                $this->get_title_font(),
                $this->get_heading_font(),
                $this->get_text_font(),
                $this->get_muted_font(),
                $this->get_link_font(),
                
            ],

        );
    }

    public function get_title_font() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'title',
            label: 'title',
            label_element: 'h5',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'font',
                    label: 'title Font',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'size',
                    label: 'title Fontsize',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'weight',
                    label: 'title Font-weight',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'alignment',
                    label: 'title alignment',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_heading_font() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'heading',
            label: 'heading',
            label_element: 'h5',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'font',
                    label: 'Card Heading Font',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'size',
                    label: 'Card Heading Fontsize',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'weight',
                    label: 'Card Heading Font-weight',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
            ],

        );
    }
    public function get_text_font() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'text',
            label: 'text',
            label_element: 'h5',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'font',
                    label: 'Card text Font',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'size',
                    label: 'Card Text Fontsize',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'weight',
                    label: 'Card Text Font-weight',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_muted_font() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'muted',
            label: 'muted',
            label_element: 'h5',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'font',
                    label: 'Card muted Font',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'size',
                    label: 'Card Muted Text Fontsize',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'weight',
                    label: 'Card Muted Text Font-weight',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_link_font() {
        return new \Qck\FeedEngine\Core\Options\OptionField (
            key: 'link',
            label: 'link',
            label_element: 'h5',
            class: Manifest::PREFIX . '-field-collapsible collapsed',
            entries: [
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'font',
                    label: 'Card link Font',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'size',
                    label: 'Card link Text Fontsize',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
                new \Qck\FeedEngine\Core\Options\OptionEntry(
                    key: 'weight',
                    label: 'Card link Font-weight',
                    type: 'text',
                    default: 'inherit',
                    label_element: 'legend',
                ),
            ],

        );
    }

    public function get_container_settings(){
        // ? title
            // ? enable
            // ? text
                // ? font
                // ? size
                // ? weight
                // ? color
            // ? alignment
            // ? background
                // ? color
            // ? border
                // ? width
                // ? color
                // ? distance

        // ? border
            // ? width
            // ? color
            // ? distance

        // ? background
            // ? color

        // ? box-shadow
            // ? color
            // ? dimensions

        // ? size
            // ? width
            // ? height

        // ? spacing
            // ? margin
            // ? padding


        
    }

    public function get_grid_settings(){
        // ? gap
        // ? 
    }

    public function get_card_settings(){

        // ? Image
        
        // ? Heading
            // ? enable
            // ? text
                // ? font
                // ? size
                // ? weight
                // ? color
            // ? alignment
            // ? background
                // ? color
            // ? border
                // ? width
                // ? color
                // ? distance
        // ? Excerpt
        // ? enable
            // ? text
                // ? font
                // ? size
                // ? weight
                // ? color
            // ? alignment
            // ? background
                // ? color
            // ? border
                // ? width
                // ? color
                // ? distance
        // ? Link
        // ? Badges
        // ? Overlay
        // ? background
            // ? color
        // ? border
            // ? width
            // ? color
            // ? distance
        // ? box-shadow
            // ? color
            // ? dimensions

    }



}