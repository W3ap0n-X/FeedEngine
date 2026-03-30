<?php
namespace Qck\FeedEngine\Core\Hooks;

interface HookInterface {
    public function get_hook(): string;      // e.g., 'wp_enqueue_scripts'
    public function get_callback(): callable;
    public function get_priority(): int;
    public function get_args_count(): int;
}