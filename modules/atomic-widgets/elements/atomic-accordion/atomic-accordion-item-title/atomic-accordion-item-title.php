<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Title;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Icon\Atomic_Accordion_Item_Icon;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Icon_Expanded\Atomic_Accordion_Item_Icon_Expanded;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Paragraph\Atomic_Paragraph;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Html_V3_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_States;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Accordion_Item_Title extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'The clickable header of an accordion item. Always visible. Clicking it toggles the item open or closed. Drop text or icon elements here. Renders as a native <summary> element.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'permanently_locked', true );
	}

	public static function get_type() {
		return 'e-accordion-item-title';
	}

	public static function get_element_type(): string {
		return 'e-accordion-item-title';
	}

	public function get_title() {
		return esc_html__( 'Accordion item title', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atomic', 'accordion', 'title', 'summary', 'header' ];
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function should_show_in_panel() {
		return false;
	}

	protected function define_default_html_tag() {
		return 'summary';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'attributes' => Attributes_Prop_Type::make()
				->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_style_states(): array {
		return [ Style_States::get_class_states_map()['selected'] ];
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
			'display'          => String_Prop_Type::generate( 'flex' ),
			'align-items'      => String_Prop_Type::generate( 'center' ),
			'justify-content'  => String_Prop_Type::generate( 'space-between' ),
			'cursor'           => String_Prop_Type::generate( 'pointer' ),
			'padding'          => Size_Prop_Type::generate( [
				'size' => 16,
				'unit' => 'px',
			] ),
		];

		return [
			static::BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_props( $styles )
				),
		];
	}

	protected function define_default_children() {
		return [
			Atomic_Paragraph::generate()
				->settings( [
					'paragraph' => Html_V3_Prop_Type::generate( [
						'content'  => String_Prop_Type::generate( 'Accordion Item' ),
						'children' => [],
					] ),
					'tag' => String_Prop_Type::generate( 'span' ),
				] )
				->build(),
			Atomic_Accordion_Item_Icon::generate()->build(),
			Atomic_Accordion_Item_Icon_Expanded::generate()->build(),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-accordion-item-title' => __DIR__ . '/atomic-accordion-item-title.html.twig',
		];
	}
}
