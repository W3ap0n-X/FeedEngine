<?php
namespace Qck\FeedEngine\Core\Pages\Components\Standalone;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Submit_Button {

	public $slug;
	public $text;
	public $styles;

	public function __construct($slug , $text = 'Save Options', $styles = 'primary large'){
		$this->slug = $slug;
		$this->text = $text;
		$this->styles = $styles;
		$this->render();
	}

	public function render() {
		$button = get_submit_button( __( $this->text, $this->slug ) , $this->styles , $this->slug );
		echo $button;
	}
}