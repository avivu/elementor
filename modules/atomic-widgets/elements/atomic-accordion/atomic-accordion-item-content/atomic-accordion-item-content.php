<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Content;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Accordion_Item_Content extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'The collapsible body of an accordion item. Shown when the item is open, hidden when closed. Drop any elements here. Uses CSS Grid for smooth height animation on open.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'permanently_locked', true );
	}

	public static function get_type() {
		return 'e-accordion-item-content';
	}

	public static function get_element_type(): string {
		return 'e-accordion-item-content';
	}

	public function get_title() {
		return esc_html__( 'Accordion item content', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atomic', 'accordion', 'content', 'body' ];
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function should_show_in_panel() {
		return false;
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'attributes' => Attributes_Prop_Type::make()
				->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( [] ),
		];
	}

	protected function define_base_styles(): array {
		// display:grid + grid-template-rows:0fr collapses the content to zero height.
		// The open state (grid-template-rows:1fr) is applied via module.php inline CSS
		// using the [data-e-type="e-accordion-item"][open] selector.
		$styles = [
			'display' => String_Prop_Type::generate( 'grid' ),
			'grid-template-rows' => String_Prop_Type::generate( '0fr' ),
			'overflow' => String_Prop_Type::generate( 'hidden' ),
		];

		return [
			static::BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_props( $styles )
				),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-accordion-item-content' => __DIR__ . '/atomic-accordion-item-content.html.twig',
		];
	}
}
