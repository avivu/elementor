<?php

namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video_Embed\Atomic_Background_Video_Embed;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\DynamicTags\Dynamic_Prop_Type;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Element_Template;
use Elementor\Modules\AtomicWidgets\Elements\Loader\Frontend_Assets_Loader;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Background_Video_Embed extends Atomic_Element_Base {
	use Has_Element_Template;

	public static $widget_description = 'Embed a YouTube or Vimeo video as a looping background, with any Elementor content layered on top.';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->meta( 'is_container', true );
	}

	public static function get_type() {
		return 'e-background-video-embed';
	}

	public static function get_element_type(): string {
		return 'e-background-video-embed';
	}

	public function get_title() {
		return esc_html__( 'Background Video Embed', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic', 'video', 'background', 'youtube', 'vimeo', 'embed' ];
	}

	public function get_icon() {
		return 'eicon-video';
	}

	protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => false,
		];
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'source' => String_Prop_Type::make(),
			'start_time' => Number_Prop_Type::make()
				->default( null )
				->meta( Dynamic_Prop_Type::ignore() )
				->meta( 'suffix', 'SEC' ),
			'end_time' => Number_Prop_Type::make()
				->default( null )
				->meta( Dynamic_Prop_Type::ignore() )
				->meta( 'suffix', 'SEC' ),
			'mute' => Boolean_Prop_Type::make()->default( true ),
			'loop' => Boolean_Prop_Type::make()->default( true ),
			'play_on_mobile' => Boolean_Prop_Type::make()->default( false ),
			'privacy_mode' => Boolean_Prop_Type::make()->default( false ),
			'fallback_image' => Image_Prop_Type::make()->default_size( 'full' ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor' ) )
				->set_items( [
					Text_Control::bind_to( 'source' )
						->set_placeholder( esc_html__( 'Paste a YouTube or Vimeo URL', 'elementor' ) )
						->set_label( esc_html__( 'Video URL', 'elementor' ) ),
					Number_Control::bind_to( 'start_time' )
						->set_label( esc_html__( 'Start Time', 'elementor' ) )
						->set_min( 0 )
						->set_max( 10000 ),
					Number_Control::bind_to( 'end_time' )
						->set_label( esc_html__( 'End Time', 'elementor' ) )
						->set_min( 0 )
						->set_max( 10000 ),
					Switch_Control::bind_to( 'mute' )->set_label( esc_html__( 'Mute', 'elementor' ) ),
					Switch_Control::bind_to( 'loop' )->set_label( esc_html__( 'Loop', 'elementor' ) ),
					Switch_Control::bind_to( 'play_on_mobile' )->set_label( esc_html__( 'Play on Mobile', 'elementor' ) ),
					Switch_Control::bind_to( 'privacy_mode' )->set_label( esc_html__( 'Privacy Mode', 'elementor' ) ),
					Image_Control::bind_to( 'fallback_image' )->set_label( esc_html__( 'Fallback Image', 'elementor' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
	}

	protected function get_settings_controls(): array {
		return [
			Text_Control::bind_to( '_cssid' )
				->set_label( __( 'ID', 'elementor' ) )
				->set_meta( $this->get_css_id_control_meta() ),
		];
	}

	protected function define_base_styles(): array {
		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'display', String_Prop_Type::generate( 'flex' ) )
						->add_prop( 'flex-direction', String_Prop_Type::generate( 'column' ) )
						->add_prop( 'position', String_Prop_Type::generate( 'relative' ) )
						->add_prop( 'overflow', String_Prop_Type::generate( 'hidden' ) )
						->add_prop( 'isolation', String_Prop_Type::generate( 'isolate' ) )
						->add_prop( 'width', Size_Prop_Type::generate( [
							'size' => 100,
							'unit' => '%',
						] ) )
				),
		];
	}

	public function get_script_depends() {
		return array_merge(
			parent::get_script_depends(),
			[ 'elementor-background-video-embed-handler' ]
		);
	}

	public function register_frontend_handlers() {
		$assets_url = ELEMENTOR_ASSETS_URL;
		$min_suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';

		wp_register_script(
			'elementor-background-video-embed-handler',
			"{$assets_url}js/background-video-embed-handler{$min_suffix}.js",
			[ Frontend_Assets_Loader::FRONTEND_HANDLERS_HANDLE ],
			ELEMENTOR_VERSION,
			true
		);
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-background-video-embed' => __DIR__ . '/atomic-background-video-embed.html.twig',
		];
	}

	public function render_markdown(): string {
		$settings = $this->get_atomic_settings();
		$url = $settings['source'] ?? '';

		if ( empty( $url ) ) {
			return '';
		}

		return '[Background Video](' . esc_url( $url ) . ')';
	}
}
