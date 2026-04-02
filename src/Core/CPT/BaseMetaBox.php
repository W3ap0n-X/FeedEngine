<?php
namespace Qck\FeedEngine\Core\CPT;

use Qck\FeedEngine\Core\Pages\Components\Metabox;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;
abstract class BaseMetaBox {

    public $metabox;

    abstract public function get_name(): string;
    abstract public function get_title(): string;
    abstract public function get_screen(): string|array; // e.g., 'qckfe_feed'
    abstract public function get_schema(): array;

    public function register() {
        $this->metabox = new Metabox(
                $this->get_name(), 
                $this->get_screen(), 
                [
                    'title' => $this->get_title(),
                    'context' => 'normal',
                    'priority' => 'high',
                    'callback' => [$this, 'render_wrapper'],
                ]
            );
        add_action('add_meta_boxes', function() {
            $this->metabox->register();
        });
        
        add_action('save_post', [$this, 'save_data']);
    }

    public function render_wrapper($post) {
        // \Qck\FeedEngine\Core\Debug::logDump( $post, __METHOD__);
        // 1. Security Nonce
        wp_nonce_field($this->get_name() . '_action', $this->get_name() . '_nonce');

        // 2. Fetch existing values
        $values = get_post_meta($post->ID, '_qckfe_settings', true) ?: [];

        // 3. Reuse your SettingsBuilder!
        // $builder = new SettingBuilder($this->get_schema(), $values);
        echo '<div class="qckfe-metabox-wrapper">';
        echo '<h1>test</h1>';
        SettingBuilder::build_ui_from_metabox($post->ID, $this->metabox, $this->get_schema());
        echo '<h2>' . $post->ID . '</h2>';
        echo '<h2>' . count($values) . '</h2>';
        echo '<h2>' . count($this->metabox->fields) . '</h2>';
        // echo $content;
        // $metabox->render();
        // \Qck\FeedEngine\Core\Debug::logDump( $content, __METHOD__);
        // $builder->render();
        echo '</div>';
    }

    public function save_data($post_id) {
        // Security checks
        if (!isset($_POST[$this->get_name() . '_nonce'])) return;
        if (!wp_verify_nonce($_POST[$this->get_name() . '_nonce'], $this->get_name() . '_action')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Save the whole array as one row (The "Rhyme" strategy)
        if (isset($_POST['qckfe_settings'])) {
            update_post_meta($post_id, '_qckfe_settings', $_POST['qckfe_settings']);
        }
    }
}