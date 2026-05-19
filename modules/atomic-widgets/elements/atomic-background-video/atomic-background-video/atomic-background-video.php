<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Video_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Controls\Atomic_Background_Video_Controls;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Overlay\Atomic_Background_Video_Overlay;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\Elements\Loader\Frontend_Assets_Loader;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Video_Src_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Background_Video extends Atomic_Element_Base {
	use Has_Element_Template;

	const BASE_STYLE_KEY = 'base';

	public static $widget_description = 'A container that renders a looping background video with a content overlay slot and optional play/pause controls. Drop content into the overlay child to layer it over the video.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'is_container', true );
	}

	public static function get_type() {
		return 'e-background-video';
	}

	public static function get_element_type(): string {
		return 'e-background-video';
	}

	public function get_title() {
		return esc_html__( 'Background Video', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic', 'video', 'background', 'media' ];
	}

	public function get_icon() {
		return 'eicon-video';
	}

	protected static function define_props_schema(): array {
		return [
			'classes'       => Classes_Prop_Type::make()->default( [] ),
			'source'        => Video_Src_Prop_Type::make(),
			'autoplay'      => Boolean_Prop_Type::make()->default( true ),
			'mute'          => Boolean_Prop_Type::make()->default( true ),
			'loop'          => Boolean_Prop_Type::make()->default( true ),
			'show_controls' => Boolean_Prop_Type::make()->default( true ),
			'attributes'    => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor' ) )
				->set_id( 'content' )
				->set_items( [
					Video_Control::bind_to( 'source' )
						->set_label( esc_html__( 'Video', 'elementor' ) ),
					Switch_Control::bind_to( 'autoplay' )
						->set_label( esc_html__( 'Autoplay', 'elementor' ) ),
					Switch_Control::bind_to( 'mute' )
						->set_label( esc_html__( 'Mute', 'elementor' ) ),
					Switch_Control::bind_to( 'loop' )
						->set_label( esc_html__( 'Loop', 'elementor' ) ),
					Switch_Control::bind_to( 'show_controls' )
						->set_label( esc_html__( 'Show Controls', 'elementor' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( [
					Text_Control::bind_to( '_cssid' )
						->set_label( __( 'ID', 'elementor' ) )
						->set_meta( [ 'layout' => 'two-columns' ] ),
				] ),
		];
	}

	protected function define_base_styles(): array {
		$styles = [
			'position'   => String_Prop_Type::generate( 'relative' ),
			'overflow'   => String_Prop_Type::generate( 'hidden' ),
			'width'      => Size_Prop_Type::generate( [ 'size' => 100, 'unit' => '%' ] ),
			'min-height' => Size_Prop_Type::generate( [ 'size' => 300, 'unit' => 'px' ] ),
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
			Atomic_Background_Video_Overlay::generate()->build(),
			Atomic_Background_Video_Controls::generate()->build(),
		];
	}

	protected function define_allowed_child_types() {
		return [ 'e-background-video-overlay', 'e-background-video-controls', 'container' ];
	}

	protected function define_render_context(): array {
		return [
			[
				'context' => [
					'show_controls' => $this->get_atomic_setting( 'show_controls' ),
				],
			],
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-background-video' => __DIR__ . '/atomic-background-video.html.twig',
		];
	}

	public function get_script_depends() {
		return array_merge( parent::get_script_depends(), [ Frontend_Assets_Loader::ALPINEJS_HANDLE ] );
	}
}
