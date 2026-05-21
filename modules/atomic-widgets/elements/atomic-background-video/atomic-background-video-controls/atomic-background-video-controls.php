<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Controls;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video\Atomic_Background_Video;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Pause_Btn\Atomic_Background_Video_Pause_Btn;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Play_Btn\Atomic_Background_Video_Play_Btn;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\Elements\Base\Render_Context;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Background_Video_Controls extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'Container for the video play/pause controls. Absolutely positioned and centred over the video. Shown or hidden based on the "Show Controls" setting on the parent e-background-video element.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'permanently_locked', true );
		$this->meta( 'is_container', true );
	}

	public static function get_type() {
		return 'e-background-video-controls';
	}

	public static function get_element_type(): string {
		return 'e-background-video-controls';
	}

	public function get_title() {
		return esc_html__( 'Video Controls', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic', 'video', 'controls', 'play', 'pause' ];
	}

	public function get_icon() {
		return '';
	}

	public function should_show_in_panel() {
		return false;
	}

	protected static function define_props_schema(): array {
		return [
			'classes'    => Classes_Prop_Type::make()->default( [] ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
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
			'position'         => String_Prop_Type::generate( 'absolute' ),
			'bottom'           => Size_Prop_Type::generate( [
				'size' => 16,
				'unit' => 'px',
			] ),
			'right'            => Size_Prop_Type::generate( [
				'size' => 16,
				'unit' => 'px',
			] ),
			'z-index'          => String_Prop_Type::generate( '2' ),
			'display'          => String_Prop_Type::generate( 'flex' ),
			'align-items'      => String_Prop_Type::generate( 'center' ),
			'gap'              => Size_Prop_Type::generate( [
				'size' => 8,
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
			Atomic_Background_Video_Play_Btn::generate()->build(),
			Atomic_Background_Video_Pause_Btn::generate()->build(),
		];
	}

	protected function define_allowed_child_types() {
		return [ 'e-background-video-play-btn', 'e-background-video-pause-btn', 'container' ];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-background-video-controls' => __DIR__ . '/atomic-background-video-controls.html.twig',
		];
	}

	protected function build_template_context(): array {
		$context = Render_Context::get( Atomic_Background_Video::class );
		$show_controls = $context['show_controls'] ?? true;

		return array_merge( $this->build_base_template_context(), [
			'show_controls' => $show_controls,
		] );
	}
}
