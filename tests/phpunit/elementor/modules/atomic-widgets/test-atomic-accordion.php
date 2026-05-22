<?php

use Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion\Atomic_Accordion;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item\Atomic_Accordion_Item;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Content\Atomic_Accordion_Item_Content;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Accordion\Atomic_Accordion_Item_Title\Atomic_Accordion_Item_Title;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Plugin;
use ElementorEditorTesting\Elementor_Test_Base;
use Spatie\Snapshots\MatchesSnapshots;

class Test_Atomic_Accordion extends Elementor_Test_Base {
	use MatchesSnapshots;

	const ACCORDION_ID = 'accordion-1';
	const ITEM_1_ID = 'accordion-item-1';
	const ITEM_2_ID = 'accordion-item-2';
	const ITEM_3_ID = 'accordion-item-3';
	const TITLE_1_ID = 'accordion-title-1';
	const TITLE_2_ID = 'accordion-title-2';
	const TITLE_3_ID = 'accordion-title-3';
	const CONTENT_1_ID = 'accordion-content-1';
	const CONTENT_2_ID = 'accordion-content-2';
	const CONTENT_3_ID = 'accordion-content-3';

	// --- Snapshot Tests ---

	public function test__render_accordion_default(): void {
		// Arrange.
		$instance = $this->create_accordion_instance( [] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
	}

	public function test__render_accordion_first_item_open(): void {
		// Arrange.
		$instance = $this->create_accordion_instance( [ 'default_open_index' => 0 ] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
		$this->assertStringContainsString( 'open', $rendered_output );
	}

	public function test__render_accordion_all_items_closed(): void {
		// Arrange.
		$instance = $this->create_accordion_instance( [ 'default_open_index' => -1 ] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
	}

	public function test__render_accordion_second_item_open(): void {
		// Arrange.
		$instance = $this->create_accordion_instance( [ 'default_open_index' => 1 ] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
	}

	// --- Schema Tests ---

	public function test__props_schema_includes_default_open_index(): void {
		$schema = Atomic_Accordion::get_props_schema();

		$this->assertArrayHasKey( 'default_open_index', $schema );
		$this->assertInstanceOf( Prop_Type::class, $schema['default_open_index'] );
	}

	public function test__props_schema_includes_classes_and_attributes(): void {
		$schema = Atomic_Accordion::get_props_schema();

		$this->assertArrayHasKey( 'classes', $schema );
		$this->assertArrayHasKey( 'attributes', $schema );
	}

	public function test__accordion_item_props_schema_includes_classes(): void {
		$schema = Atomic_Accordion_Item::get_props_schema();

		$this->assertArrayHasKey( 'classes', $schema );
		$this->assertArrayHasKey( 'attributes', $schema );
	}

	public function test__accordion_item_title_props_schema_includes_classes(): void {
		$schema = Atomic_Accordion_Item_Title::get_props_schema();

		$this->assertArrayHasKey( 'classes', $schema );
		$this->assertArrayHasKey( 'attributes', $schema );
	}

	public function test__accordion_item_content_props_schema_includes_classes(): void {
		$schema = Atomic_Accordion_Item_Content::get_props_schema();

		$this->assertArrayHasKey( 'classes', $schema );
		$this->assertArrayHasKey( 'attributes', $schema );
	}

	// --- Element Type Tests ---

	public function test__accordion_element_types_are_correct(): void {
		$this->assertSame( 'e-accordion', Atomic_Accordion::get_element_type() );
		$this->assertSame( 'e-accordion-item', Atomic_Accordion_Item::get_element_type() );
		$this->assertSame( 'e-accordion-item-title', Atomic_Accordion_Item_Title::get_element_type() );
		$this->assertSame( 'e-accordion-item-content', Atomic_Accordion_Item_Content::get_element_type() );
	}

	// --- Helpers ---

	private function create_accordion_instance( array $settings ): object {
		$title_1 = [
			'id' => self::TITLE_1_ID,
			'elType' => Atomic_Accordion_Item_Title::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Title::get_element_type(),
			'settings' => [],
		];

		$content_1 = [
			'id' => self::CONTENT_1_ID,
			'elType' => Atomic_Accordion_Item_Content::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Content::get_element_type(),
			'settings' => [],
		];

		$title_2 = [
			'id' => self::TITLE_2_ID,
			'elType' => Atomic_Accordion_Item_Title::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Title::get_element_type(),
			'settings' => [],
		];

		$content_2 = [
			'id' => self::CONTENT_2_ID,
			'elType' => Atomic_Accordion_Item_Content::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Content::get_element_type(),
			'settings' => [],
		];

		$title_3 = [
			'id' => self::TITLE_3_ID,
			'elType' => Atomic_Accordion_Item_Title::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Title::get_element_type(),
			'settings' => [],
		];

		$content_3 = [
			'id' => self::CONTENT_3_ID,
			'elType' => Atomic_Accordion_Item_Content::get_element_type(),
			'widgetType' => Atomic_Accordion_Item_Content::get_element_type(),
			'settings' => [],
		];

		$item_1 = [
			'id' => self::ITEM_1_ID,
			'elType' => Atomic_Accordion_Item::get_element_type(),
			'widgetType' => Atomic_Accordion_Item::get_element_type(),
			'settings' => [],
			'elements' => [ $title_1, $content_1 ],
		];

		$item_2 = [
			'id' => self::ITEM_2_ID,
			'elType' => Atomic_Accordion_Item::get_element_type(),
			'widgetType' => Atomic_Accordion_Item::get_element_type(),
			'settings' => [],
			'elements' => [ $title_2, $content_2 ],
		];

		$item_3 = [
			'id' => self::ITEM_3_ID,
			'elType' => Atomic_Accordion_Item::get_element_type(),
			'widgetType' => Atomic_Accordion_Item::get_element_type(),
			'settings' => [],
			'elements' => [ $title_3, $content_3 ],
		];

		$mock = [
			'id' => self::ACCORDION_ID,
			'elType' => Atomic_Accordion::get_element_type(),
			'widgetType' => Atomic_Accordion::get_element_type(),
			'settings' => $settings,
			'elements' => [ $item_1, $item_2, $item_3 ],
		];

		return Plugin::$instance->elements_manager->create_element_instance( $mock );
	}

	private function find_control_by_bind( array $controls, string $bind ): ?Atomic_Control_Base {
		foreach ( $controls as $control ) {
			if ( $control instanceof Section ) {
				$found = $this->find_control_by_bind( $control->get_items(), $bind );
				if ( null !== $found ) {
					return $found;
				}
				continue;
			}

			if ( $control instanceof Atomic_Control_Base && $control->get_bind() === $bind ) {
				return $control;
			}
		}

		return null;
	}
}
