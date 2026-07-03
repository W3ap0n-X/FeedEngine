<?php 
/** @var string $content */
$_content= $content['manual'] . $content['automatic'];
$style = $content['style'];
$output = '';
$output .= <<<HTML
    <div class="qckfe-feed-grid" style="{$style}">
        {$_content}
    </div>
HTML;

return $output;