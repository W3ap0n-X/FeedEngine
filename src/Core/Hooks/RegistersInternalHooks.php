<?php
namespace Qck\FeedEngine\Core\Hooks;

trait RegistersInternalHooks {
    public function boot_internal_components(array $components) {
        foreach ($components as $key => $component) {
            if (method_exists($component, 'get_actions')) {
                foreach ($component->get_actions() as $hook => $callback) {
                    add_action($hook, [$component, $callback[0]]);
                }
            }
        }
    }
}