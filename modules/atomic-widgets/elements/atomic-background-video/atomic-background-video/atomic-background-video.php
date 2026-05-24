<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Toggle_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Video_Control;
use Elementor\Modules\AtomicWidgets\DynamicTags\Dynamic_Prop_Type;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Content\Atomic_Background_Video_Content;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Controls\Atomic_Background_Video_Controls;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\Elements\Loader\Frontend_Assets_Loader;
use Elementor\Utils;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
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
		// TODO: Replace 'eicon-video' with a dedicated background-video icon once it's added to the eicons font.
		return 'eicon-video';
	}

	protected static function define_props_schema(): array {
		return [
			'classes'       => Classes_Prop_Type::make()->default( [] ),
			'source'        => Video_Src_Prop_Type::make()
				->description( 'The video file URL to use as the background.' ),
			'autoplay'      => Boolean_Prop_Type::make()->default( true )
				->description( 'Whether the video starts playing automatically on page load.' ),
			'mute'          => Boolean_Prop_Type::make()->default( true )
				->description( 'Whether the video is muted. Required for autoplay in most browsers.' ),
			'loop'          => Boolean_Prop_Type::make()->default( true )
				->description( 'Whether the video loops continuously.' ),
			'start_time'    => Number_Prop_Type::make()
				->default( null )
				->meta( Dynamic_Prop_Type::ignore() )
				->meta( 'suffix', 'SEC' ),
			'end_time'      => Number_Prop_Type::make()
				->default( null )
				->meta( Dynamic_Prop_Type::ignore() )
				->meta( 'suffix', 'SEC' ),
			'show_controls' => Boolean_Prop_Type::make()->default( true )
				->description( 'Whether to show the play/pause control buttons over the video.' ),
			'video-state'   => String_Prop_Type::make()
				->enum( [ 'default', 'play', 'pause' ] )
				->default( 'default' )
				->meta( 'generates_class', 'video-state-{value}' )
				->set_dependencies(
					Dependency_Manager::make()
						->where( [
							'operator' => 'eq',
							'path'     => [ 'show_controls' ],
							'value'    => true,
							'effect'   => 'hide',
						] )
						->get()
				),
			'attributes'    => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_controls(): array {
		$state_control = Toggle_Control::bind_to( 'video-state' )
			->set_label( __( 'States', 'elementor' ) );

		if ( $state_control instanceof Toggle_Control ) {
			$state_control
				->add_options( [
					'play'  => [ 'title' => __( 'Play', 'elementor' ) ],
					'pause' => [ 'title' => __( 'Pause', 'elementor' ) ],
				] )
				->set_exclusive( true )
				->set_convert_options( true )
				->set_size( 'tiny' )
				->set_full_width( true );
		}

		return [
			Section::make()
				->set_label( __( 'Content', 'elementor' ) )
				->set_id( 'content' )
				->set_items( [
					Video_Control::bind_to( 'source' )
						->set_label( esc_html__( 'Video', 'elementor' ) ),
					Number_Control::bind_to( 'start_time' )
						->set_label( esc_html__( 'Start Time', 'elementor' ) )
						->set_min( 0 )
						->set_max( 10000 ),
					Number_Control::bind_to( 'end_time' )
						->set_label( esc_html__( 'End Time', 'elementor' ) )
						->set_min( 0 )
						->set_max( 10000 ),
					Switch_Control::bind_to( 'autoplay' )
						->set_label( esc_html__( 'Autoplay', 'elementor' ) ),
					Switch_Control::bind_to( 'mute' )
						->set_label( esc_html__( 'Mute', 'elementor' ) ),
					Switch_Control::bind_to( 'loop' )
						->set_label( esc_html__( 'Loop', 'elementor' ) ),
					Switch_Control::bind_to( 'show_controls' )
						->set_label( esc_html__( 'Show Controls', 'elementor' ) ),
					$state_control,
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
			'display'  => String_Prop_Type::generate( 'flex' ),
			'position' => String_Prop_Type::generate( 'relative' ),
			'overflow' => String_Prop_Type::generate( 'hidden' ),
			'width'    => Size_Prop_Type::generate( [
				'size' => 100,
				'unit' => '%',
			] ),
			'padding'  => String_Prop_Type::generate( '0' ),
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
			Atomic_Background_Video_Content::generate()->build(),
			Atomic_Background_Video_Controls::generate()->build(),
		];
	}

	protected function define_allowed_child_types() {
		return [ 'e-background-video-content', 'e-background-video-controls' ];
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

	public function get_script_depends() {
		return array_merge( parent::get_script_depends(), [ 'elementor-background-video-handler' ] );
	}

	public function register_frontend_handlers() {
		$assets_url = ELEMENTOR_ASSETS_URL;
		$min_suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';

		wp_register_script(
			'elementor-background-video-handler',
			"{$assets_url}js/background-video-handler{$min_suffix}.js",
			[ Frontend_Assets_Loader::FRONTEND_HANDLERS_HANDLE ],
			ELEMENTOR_VERSION,
			true
		);
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-background-video' => __DIR__ . '/atomic-background-video.html.twig',
		];
	}

	public function render_markdown(): string {
		$settings = $this->get_atomic_settings();
		$url = $settings['source']['url'] ?? '';

		if ( empty( $url ) ) {
			return '';
		}

		return '[Background Video](' . esc_url( $url ) . ')';
	}
}
