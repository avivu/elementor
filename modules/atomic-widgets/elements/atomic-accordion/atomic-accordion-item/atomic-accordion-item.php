<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion\Atomic_Accordion;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Content\Atomic_Accordion_Item_Content;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Title\Atomic_Accordion_Item_Title;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\Elements\Base\Render_Context;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Accordion_Item extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'A single accordion item. Contains an e-accordion-item-title (the always-visible clickable header) and e-accordion-item-content (the collapsible body). Renders as a native <details> element.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'permanently_locked', true );
	}

	public static function get_type() {
		return 'e-accordion-item';
	}

	public static function get_element_type(): string {
		return 'e-accordion-item';
	}

	public function get_title() {
		return esc_html__( 'Accordion item', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atomic', 'accordion', 'item' ];
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function should_show_in_panel() {
		return false;
	}

	protected function define_default_html_tag() {
		return 'details';
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
		$styles = [
			'display' => String_Prop_Type::generate( 'block' ),
		];

		return [
			static::BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_props( $styles )
				),
		];
	}

	protected function define_allowed_child_types() {
		return [ 'e-accordion-item-title', 'e-accordion-item-content' ];
	}

	protected function define_default_children() {
		return [
			Atomic_Accordion_Item_Title::generate()->build(),
			Atomic_Accordion_Item_Content::generate()->build(),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-accordion-item' => __DIR__ . '/atomic-accordion-item.html.twig',
		];
	}

	protected function build_template_context(): array {
		$accordion_context = Render_Context::get( Atomic_Accordion::class );
		$accordion_id = $accordion_context['accordion_id'];
		$default_open_index = $accordion_context['default_open_index'];
		$get_item_index = $accordion_context['get_item_index'];

		$index = $get_item_index( $this->get_id() );
		$is_open = null !== $index && $default_open_index >= 0 && $default_open_index === $index;

		return array_merge( $this->build_base_template_context(), [
			'accordion_id' => $accordion_id,
			'is_open' => $is_open,
		] );
	}
}
