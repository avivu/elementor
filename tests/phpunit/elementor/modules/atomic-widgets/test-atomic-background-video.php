<?php

use Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video\Atomic_Background_Video;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Content\Atomic_Background_Video_Content;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Controls\Atomic_Background_Video_Controls;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Pause_Btn\Atomic_Background_Video_Pause_Btn;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Background_Video\Atomic_Background_Video_Play_Btn\Atomic_Background_Video_Play_Btn;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Plugin;
use ElementorEditorTesting\Elementor_Test_Base;
use Spatie\Snapshots\MatchesSnapshots;

class Test_Atomic_Background_Video extends Elementor_Test_Base {
	use MatchesSnapshots;

	const VIDEO_ID = 'bg-video-1';
	const CONTENT_ID = 'bg-video-content-1';
	const CONTROLS_ID = 'bg-video-controls-1';
	const PLAY_BTN_ID = 'bg-video-play-btn-1';
	const PAUSE_BTN_ID = 'bg-video-pause-btn-1';

	// --- Snapshot Tests ---

	public function test__render_background_video_without_source(): void {
		// Arrange.
		$instance = $this->create_background_video_instance( [] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
	}

	public function test__render_background_video_with_source(): void {
		// Arrange.
		$instance = $this->create_background_video_instance( [
			'source' => [
				'url' => 'https://example.com/video.mp4',
				'id' => 0,
			],
		] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
	}

	public function test__render_background_video_with_start_and_end_time(): void {
		// Arrange.
		$instance = $this->create_background_video_instance( [
			'source' => [
				'url' => 'https://example.com/video.mp4',
				'id' => 0,
			],
			'start_time' => 10,
			'end_time' => 30,
		] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
		$this->assertStringContainsString( '#t=10,30', $rendered_output );
	}

	public function test__render_background_video_with_controls_hidden(): void {
		// Arrange.
		$instance = $this->create_background_video_instance( [
			'show_controls' => false,
		] );

		// Act.
		ob_start();
		$instance->print_element();
		$rendered_output = ob_get_clean();

		// Assert.
		$this->assertMatchesSnapshot( $rendered_output );
		$this->assertStringContainsString( 'e-show-controls-false', $rendered_output );
	}

	// --- Schema Tests ---

	public function test__props_schema_includes_source_prop(): void {
		$schema = Atomic_Background_Video::get_props_schema();

		$this->assertArrayHasKey( 'source', $schema );
		$this->assertInstanceOf( Prop_Type::class, $schema['source'] );
	}

	public function test__props_schema_includes_start_and_end_time_props(): void {
		$schema = Atomic_Background_Video::get_props_schema();

		$this->assertArrayHasKey( 'start_time', $schema );
		$this->assertInstanceOf( Prop_Type::class, $schema['start_time'] );

		$this->assertArrayHasKey( 'end_time', $schema );
		$this->assertInstanceOf( Prop_Type::class, $schema['end_time'] );
	}

	public function test__props_schema_includes_autoplay_mute_loop_show_controls(): void {
		$schema = Atomic_Background_Video::get_props_schema();

		$this->assertArrayHasKey( 'autoplay', $schema );
		$this->assertArrayHasKey( 'mute', $schema );
		$this->assertArrayHasKey( 'loop', $schema );
		$this->assertArrayHasKey( 'show_controls', $schema );
	}

	// --- Controls Tests ---

	public function test__start_time_number_control_is_registered(): void {
		$instance = $this->make_background_video_instance();

		$control = $this->find_control_by_bind( $instance->get_atomic_controls(), 'start_time' );

		$this->assertInstanceOf( Number_Control::class, $control );
	}

	public function test__end_time_number_control_is_registered(): void {
		$instance = $this->make_background_video_instance();

		$control = $this->find_control_by_bind( $instance->get_atomic_controls(), 'end_time' );

		$this->assertInstanceOf( Number_Control::class, $control );
	}

	public function test__show_controls_switch_control_is_registered(): void {
		$instance = $this->make_background_video_instance();

		$control = $this->find_control_by_bind( $instance->get_atomic_controls(), 'show_controls' );

		$this->assertInstanceOf( Switch_Control::class, $control );
	}

	// --- Helpers ---

	private function create_background_video_instance( array $settings ): object {
		$play_btn = [
			'id' => self::PLAY_BTN_ID,
			'elType' => Atomic_Background_Video_Play_Btn::get_element_type(),
			'widgetType' => Atomic_Background_Video_Play_Btn::get_element_type(),
			'settings' => [],
		];

		$pause_btn = [
			'id' => self::PAUSE_BTN_ID,
			'elType' => Atomic_Background_Video_Pause_Btn::get_element_type(),
			'widgetType' => Atomic_Background_Video_Pause_Btn::get_element_type(),
			'settings' => [],
		];

		$controls = [
			'id' => self::CONTROLS_ID,
			'elType' => Atomic_Background_Video_Controls::get_element_type(),
			'widgetType' => Atomic_Background_Video_Controls::get_element_type(),
			'settings' => [],
			'elements' => [ $play_btn, $pause_btn ],
		];

		$content = [
			'id' => self::CONTENT_ID,
			'elType' => Atomic_Background_Video_Content::get_element_type(),
			'widgetType' => Atomic_Background_Video_Content::get_element_type(),
			'settings' => [],
		];

		$mock = [
			'id' => self::VIDEO_ID,
			'elType' => Atomic_Background_Video::get_element_type(),
			'widgetType' => Atomic_Background_Video::get_element_type(),
			'settings' => $settings,
			'elements' => [ $content, $controls ],
		];

		return Plugin::$instance->elements_manager->create_element_instance( $mock );
	}

	private function make_background_video_instance(): Atomic_Background_Video {
		return new Atomic_Background_Video(
			[
				'id' => 'test-bg-video',
				'elType' => Atomic_Background_Video::get_element_type(),
				'widgetType' => Atomic_Background_Video::get_element_type(),
				'settings' => [],
			],
			null
		);
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
