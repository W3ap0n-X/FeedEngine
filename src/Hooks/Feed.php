<?php
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;
use Qck\FeedEngine\Public\FeedController;

class Feed implements HookInterface {

    public function is_filter(): bool { return true ; }
    public function get_hook(): string { return 'qckfe/render'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 2; }

    // public function parse_defaults($properties){
    //     $properties = wp_parse_args(
    //         $properties,
    //         array(
    //             'title'       => __( $this->section_id, Manifest::PREFIX ),
    //             'description' => '',
    //             'meta' => false,
    //             'type' => 'options',
    //             'class' => null,
    //             'style' => null,
    //         )
    //     );
    //     return $properties;
    // }

    public function get_callback(): callable {
        return function($feed_id, $properties) {
            $output = '';
            // $transient = get_transient( 'qckfe_cache_' . $post_id);
            // if( $transient === false ) {
            //     $controller = new FeedController();
            //     $transient = $controller->build_front( $post_id);
            // }
            // if ( empty( $post_id ) ) { 
            //     return $output;
            // } 
            // $settings = get_post_meta( $post_id, '_qckfe_feed_settings', true );
            $content = [];
            $content['style'] = $properties['style'];
            \Qck\FeedEngine\Core\Debug::logDump( $properties , __METHOD__ . ' transient ' );
            foreach ($properties as $group => $items) {
                
                switch ($group) {
                    case 'style':
                        $content[$group] = $items;
                        // do nothing
                        break;

                    case 'features':
                        // do nothing
                        break;

                        case 'card':
                        // do nothing
                        break;

                        case 'grid':
                        // do nothing
                        break;

                        case 'title':
                        if(in_array('title', $properties['features'] ) && (empty($properties['title'] || $properties['title'] !== '')))
                            $properties['title'] = get_the_title( $feed_id );
                        break;
                    
                    default:
                    $content[$group] = '';
                        foreach ($items as $item) {
                            $content[$group] .= $this->get_template_card( $item , $properties['card'] , ['group' => $group , 'features' => ($properties['features'] ?? []) ] );
                        }
                        break;
                }
                
            }
            

            $content = $this->get_template_grid($content, $properties['grid'], ['title' => $properties['title'] ?? get_the_title( $feed_id ),'features' => ($properties['features'] ?? []) ]);
            $style = $properties['style']??'';
            // $output .= \Qck\FeedEngine\Core\Debug::easydump($style, ' $style');

            // $output .= \Qck\FeedEngine\Core\Debug::easydump($properties, ' $properties');
            // $output .= \Qck\FeedEngine\Core\Debug::easydump($feed_id, ' $feed_id');
            $features = $properties['features']??[];
            if(empty($features)){
                //$default = 
                $features = FeedController::_get_card_features( get_option('qckfe_card_settings') );
                if(empty($features)){
                    $features = ['heading'];
                }
            }

            $_title = $properties['title'] ??  get_the_title( $feed_id ) ;
            $title = in_array('title', $features) ? '<span class="qckfe-feed-title">' . $_title . '</span>'  : '';
            // $output .= \Qck\FeedEngine\Core\Debug::easydump($features, ' $features');
            // $output .= \Qck\FeedEngine\Core\Debug::easydump($style, ' $style');
            // $output .= \Qck\FeedEngine\Core\Debug::easydump($title, ' $title');

            
            $output .= <<<HTML
                <div class="qckfe-feed-container" data-feed-id="{$feed_id}" style="{$style}">
                    {$title}
                        {$content}
                    
                </div>
            HTML;
            
            // if(isset($a['message'])){
                
            // }
            return $output ;
            
        };
    }

    private function is_legit_template(string $template): bool {
        
        return is_file(Manifest::path() . 'templates/' . $template . '.php');
    }

    private function get_template_card( $item, $card , $arguments = array() ) {
        // $template = Manifest::path() . 'templates/bento-card.php';

        $features = $arguments['features']??[];
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

    private function get_template_grid( $content, $grid, $arguments = array()  ) {
        $features = $arguments['features'] ?? [];
        $_title = $arguments['title'];
        // $template = Manifest::path() . 'templates/bento-card.php';
        if (!$this->is_legit_template('grid/' . $grid)) {
            \Qck\FeedEngine\Core\Debug::logDump( $grid . ' is not legit template', __METHOD__ . ' Template Error');
            $grid = 'standard';
        }
        if(empty($features)){
            $default = 
            $features = FeedController::_get_card_features( get_option('qckfe_card_settings') );
            if(empty($features)){
                $features = ['heading'];
            }
        }
        // echo \Qck\FeedEngine\Core\Debug::easydump( $arguments, __METHOD__ . ' $arguments');
        // ob_start();
        // This makes $item available inside the included file
        return include Manifest::path() . 'templates/grid/' . $grid . '.php';
        // return ob_get_clean();
    }
}