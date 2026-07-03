<?php
namespace Qck\FeedEngine\Core\Pages;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\TabPage;
use Qck\FeedEngine\Core\CPT\BaseCommentType;
use Qck\FeedEngine\Core\Pages\Components\CustomCommentTypeSettings;
class CommentPage extends Admin {
    use \Qck\FeedEngine\Core\Hooks\RegistersInternalHooks;
	protected $comment_type;

	public function __construct($comment_type){
        // \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' ## ' . $this::class );
		$this->comment_type = $comment_type;
		
	}

    public function get_actions(): array {
        return array(
            'admin_menu'            => array( 'add_page' ),
            'admin_init'            => array( 'register_sections' ),
            'admin_notices'         => array( 'display_admin_notices' ),
            'admin_enqueue_scripts' => array( 'maybe_enqueue_stylesheets' ),
        );
    }

    protected $hook_suffix; // We'll store the screen ID here

    public function get_slug(){
        return $this->comment_type->get_slug();
    }

    public function get_label(){
        return $this->comment_type->get_label();
    }

    public function get_menu_title(){
        return $this->comment_type->get_menu_title();
    }

    public function get_capability(){
        return $this->comment_type->get_capability();
    }

    public function description(){
        return $this->comment_type->description();
    }

    public function get_page_title(){
        return $this->comment_type->get_label() . ' Management';
    }

        // public function get_menu_title(){
    //     return 'menu-tabpage';
    // }

    // public function get_page_title(){
    //     return 'title-tabpage';
    // }

    // public function get_slug(){
    //     return 'slug-tabpage';
    // }

    public function register_sections(){

    }

	/**
     * Add this page as a subpage
     */
    public function add_page() {
        // \Qck\FeedEngine\Core\Debug::logDump('get_slug: ' . $this->get_slug(), __METHOD__);
        $this->hook_suffix = add_comments_page(
            $this->get_label(),
            $this->get_menu_title(),
            $this->get_capability(), 
            $this->get_slug(), 
            [ $this, 'render' ]
        );
        // \Qck\FeedEngine\Core\Debug::logDump('$this->hook_suffix: ' . $this->hook_suffix, __METHOD__);
        $this->add_tab( new CustomCommentTypeSettings($this->get_slug(), 'settings'));
        $this->boot_internal_components($this->tabs);
        
        add_action( "load-{$this->hook_suffix}", [ $this, 'prepare_list_table_hooks' ] );
    }

    protected function get_page_prefix() {
        return 'comments_page_';
    }

    /**
     * This runs only when our specific page is loaded.
     */
    public function prepare_list_table_hooks() {
        // This is where you will experiment with adding columns later
        // manage_{screen_id}_columns
        
        add_filter( "manage_{$this->hook_suffix}_columns", [ $this, 'add_custom_columns' ] );
        add_action( "manage_comments_custom_column", [ $this, 'render_custom_column_data' ], 10, 2 );
    }

    protected $tabs = [];

    public function add_tab( TabPage $tab ) {
        $this->tabs[ $tab->get_tab_slug() ] = $tab;
        
        return $tab;
    }
	
	public function render() {
        
        
        // 1. Determine which tab we are on (default to 'list')
        $active_tab = $_GET['tab'] ?? 'list';
        $base_url = admin_url("edit-comments.php?page={$this->get_slug()}");

        echo '<div class="wrap">';
        echo "<h1>{$this->get_label()} Management</h1>";
        if( !empty( $this->description() ) ) {
            echo $this->description();
        }
        // 2. Render the Tab Navigation
        echo '<nav class="nav-tab-wrapper">';
        echo sprintf(
            '<a href="%s" class="nav-tab %s">Activity Log</a>',
            $base_url . '',
            $active_tab === 'list' ? 'nav-tab-active' : ''
        );
        foreach ( $this->tabs as $slug => $tab ) {
            $active_class = ( $active_tab === $slug ) ? 'nav-tab-active' : '';
            $url = admin_url("edit-comments.php?page={$this->get_slug()}&tab={$slug}");
            echo "<a href='{$url}' class='nav-tab {$active_class}'>{$tab->get_menu_title()}</a>";
        }
        echo '</nav>';

        // 3. Render the Content based on the Tab
        echo '<div class="tab-content" style="margin-top: 20px;">';
        if ( isset( $this->tabs[ $active_tab ] ) ) {
            $this->tabs[ $active_tab ]->render();
        } else {
            $this->render_list_tab();
        }
		
        echo '</div>';

        echo '</div>'; // End wrap
    }
	
	protected function render_list_tab() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-comments-list-table.php';

		// 1. Create a local Proxy Class to force columns
		// We do this because the base class won't find columns for a custom screen
		$cols = [
			'cb'       => '<input type="checkbox" />', // Bulk selection
			'author'   => __( 'Author' ),
			'comment'  => __( 'Comment' ),
			'date'     => __( 'Submitted On' ),
		];
// 		comtd($cols);
		// We use an anonymous-like class or a local definition
		$list_table = new class( $cols, $this->get_slug() ) extends \WP_Comments_List_Table {
			private $forced_cols;
			private $type_slug;

			public function __construct( $cols, $slug ) {
				$this->forced_cols = $cols;
				$this->type_slug = $slug;
				parent::__construct([ 'screen' => get_current_screen() ]);
			}

			public function get_columns() {
				return $this->forced_cols;
			}

			// 2. THE CRITICAL OVERRIDE
			public function prepare_items() {
				// A. Manually set the headers (solves the empty _column_headers)
				$this->_column_headers = [ $this->get_columns(), [], [], 'comment' ];

				// B. Manually fetch the items (solves the empty [items])
				$this->items = get_comments([
					'type'   => $this->type_slug,
					'status' => 'all', // Ensure we see everything
					'number' => 20,    // Hardcode for the experiment
				]);

				// C. Tell WP how many items we have for pagination
				$this->set_pagination_args([
					'total_items' => count($this->items),
					'per_page'    => 20,
				]);
			}
		};
		
// 		comtd($list_table);

		// 2. Apply the filtering logic
		add_filter( 'comments_clauses', function( $clauses ) {
			global $wpdb;
			// Make sure we only get comments for this specific type
			$clauses['where'] .= $wpdb->prepare( " AND comment_type = %s", $this->get_slug() );
			return $clauses;
		});

		// 3. Prepare and Display
		$list_table->prepare_items();
		if ( empty( $list_table->items ) ) {
			echo '<div class="notice notice-warning"><p>Database found 0 records for type: ' . $this->get_slug() . '</p></div>';
		}
// 		comtd($list_table->items);
		echo '<form id="comments-filter" method="get">';
		echo '<input type="hidden" name="page" value="' . esc_attr($this->get_slug()) . '" />';
		$list_table->display();
		echo '</form>';
	}
	public function add_custom_columns( $columns ) { return $columns; }
    public function render_custom_column_data( $column_name, $comment_id ) { }

}