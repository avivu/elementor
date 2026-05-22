<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item\Atomic_Accordion_Item;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Accordion extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'An accordion element with collapsible items. Contains e-accordion-item children, each with an e-accordion-item-title (the clickable header) and e-accordion-item-content (the collapsible body). CSS-only toggle via native <details>/<summary>. Use default_open_index to control which item starts open (-1 = all closed).';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'is_container', true );
	}

	public static function get_type() {
		return 'e-accordion';
	}

	public static function get_element_type(): string {
		return 'e-accordion';
	}

	public function get_title() {
		return esc_html__( 'Accordion', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic', 'accordion', 'collapse', 'toggle', 'faq' ];
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'default_open_index' => Number_Prop_Type::make()
				->default( 0 )
				->description( 'The 0-based index of the accordion item open by default. Use -1 to start with all items closed.' )
				->meta( Overridable_Prop_Type::ignore() ),
			'attributes' => Attributes_Prop_Type::make()
				->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( [
					Text_Control::bind_to( '_cssid' )
						->set_label( __( 'ID', 'elementor' ) )
						->set_meta( [
							'layout' => 'two-columns',
						] ),
				] ),
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
		return [ 'e-accordion-item' ];
	}

	protected function define_default_children() {
		$default_item_count = 3;
		$items = [];

		foreach ( range( 1, $default_item_count ) as $i ) {
			$items[] = Atomic_Accordion_Item::generate()
				->editor_settings( [
					'title' => "Accordion Item {$i}",
					'initial_position' => $i,
				] )
				->build();
		}

		return $items;
	}

	private function get_item_index( string $item_id ): ?int {
		$item_ids = Collection::make( $this->get_children() )
			->filter( fn( $child ) => $child->get_type() === 'e-accordion-item' )
			->map( fn( $child ) => $child->get_id() )
			->flip()
			->all();

		return $item_ids[ $item_id ] ?? null;
	}

	protected function define_render_context(): array {
		$default_open_index = $this->get_atomic_setting( 'default_open_index' );

		return [
			[
				'context' => [
					'accordion_id' => $this->get_id(),
					'default_open_index' => $default_open_index,
					'get_item_index' => fn( $item_id ) => $this->get_item_index( $item_id ),
				],
			],
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-accordion' => __DIR__ . '/atomic-accordion.html.twig',
		];
	}

	public function render_markdown(): string {
		return '';
	}
}
