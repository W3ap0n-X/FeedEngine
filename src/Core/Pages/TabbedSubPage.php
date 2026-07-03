<?php
namespace Qck\FeedEngine\Core\Pages;

use Qck\FeedEngine\Core\Pages\TabPage;
abstract class TabbedSubPage extends SubPage {
    
    protected $tabs = [];

    public function add_tab( TabPage $tab ) {
        $this->tabs[ $tab->get_tab_slug() ] = $tab;
        return $tab;
    }

    public function render() {
        $active_tab = $_GET['tab'] ?? array_key_first($this->tabs);
        
        echo '<div class="wrap">';
        echo '<h1>' . $this->get_page_title() . '</h1>';
        
        // 1. Render the Tab Headers
        echo '<nav class="nav-tab-wrapper">';
        foreach ( $this->tabs as $slug => $tab ) {
            $active_class = ( $active_tab === $slug ) ? 'nav-tab-active' : '';
            $url = admin_url("admin.php?page={$this->get_slug()}&tab={$slug}");
            echo "<a href='{$url}' class='nav-tab {$active_class}'>{$tab->get_menu_title()}</a>";
        }
        echo '</nav>';

        // 2. Delegate Rendering to the Active Tab Object
        if ( isset( $this->tabs[ $active_tab ] ) ) {
            $this->tabs[ $active_tab ]->render();
        }
        
        echo '</div>';
    }
}