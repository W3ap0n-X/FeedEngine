<?php

namespace Qck\FeedEngine\Shortcodes;
// use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Shortcodes\Shortcode;
use Qck\FeedEngine\Public\FeedController;

class Feed implements Shortcode {

    private $prefix = Manifest::PREFIX . '_';

    private $atts = array(
        'id'=> null,
        'card' => null,
        'grid' => null,
    );

    public function get_tag(): string { return $this->prefix . 'feed'; }

    public function render( $atts, $content = null ): string {
        $atts = shortcode_atts( $this->atts, $atts );
        $output = '';
        $post_id = isset( $atts['id'] ) ? $atts['id'] : null;
        if ( empty( $post_id ) ) { 
            return $output;
        } 
        $transient = get_transient( 'qckfe_cache_' . $post_id);
        if( $transient === false ) {
            $controller = new FeedController();
            $transient = $controller->build_front( $post_id);
        }
        $settings = $transient;
        if ( !empty( $atts['card'] ) ) { 
            $settings['card'] = $atts['card'];
        } 
        if ( !empty( $atts['grid'] ) ) { 
            $settings['grid']= $atts['grid'];
        } 

        // $output .= \Qck\FeedEngine\Core\Debug::easydump($settings, $post_id . ' settings');

        $output .= apply_filters('qckfe/render', $post_id, $settings);
        // $settings = get_post_meta( $post_id, '_qckfe_feed_settings', true );
        // $content = [];
        // \Qck\FeedEngine\Core\Debug::logDump( $transient , __METHOD__ . ' transient ' );
        // foreach ($transient as $group => $items) {
        //     $content[$group] = '';
        //     switch ($group) {
        //         case 'style':
        //             $content[$group] = $items;
        //             break;

        //         case 'features':
        //             // do nothing
        //             break;
                
        //         default:
        //             foreach ($items as $item) {
        //                 $content[$group] .= $this->get_template_card( $item , $transient['card'] , ['group' => $group , 'features' => ($transient['features'] ?? []) ] );
        //             }
        //             break;
        //     }
            
        // }
        

        // $content = $this->get_template_grid($content, $transient['grid']);

        // $output .= <<<HTML
        //     <div class="qck-feed-container" data-feed-id="{$post_id}">
                
        //             {$content}
                
        //     </div>
        // HTML;
        
        // if(isset($a['message'])){
            
        // }
        return $output ;


    }

    private function is_legit_template(string $template): bool {
        
        return is_file(Manifest::path() . 'templates/' . $template . '.php');
    }

    private function get_template_card( $item, $card , $arguments = array() ) {
        // $template = Manifest::path() . 'templates/bento-card.php';

        $features = $arguments['features'];
        $group = $arguments['group'];

        if (!$this->is_legit_template( 'cards/' . $card )) {
            \Qck\FeedEngine\Core\Debug::logDump( $card . ' is not legit template', __METHOD__ . ' Template Error');
            $card = 'standard';
        }

        if(empty($features)){
            $default = 
            $features = FeedController::_get_card_features( get_option('qckfe_card_settings') );
            if(empty($features)){
                $features = ['heading'];
            }
        }
        // ob_start();
        // This makes $item available inside the included file
        return include Manifest::path() . 'templates/cards/' . $card . '.php';
        // return ob_get_clean();
    }

    private function get_template_grid( $content, $grid ) {
        // $template = Manifest::path() . 'templates/bento-card.php';
        if (!$this->is_legit_template('grid/' . $grid)) {
            \Qck\FeedEngine\Core\Debug::logDump( $grid . ' is not legit template', __METHOD__ . ' Template Error');
            $grid = 'standard';
        }
        // ob_start();
        // This makes $item available inside the included file
        return include Manifest::path() . 'templates/grid/' . $grid . '.php';
        // return ob_get_clean();
    }

    public function get_name(): string {
        return "Feed";
    }
    public function get_description(): string {
        return "Testing";
    }
    public function get_example(): string {
        return "Testing";
    }
}