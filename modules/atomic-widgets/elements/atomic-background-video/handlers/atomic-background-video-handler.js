import { register } from '@elementor/frontend-handlers';
import { Alpine } from '@elementor/alpinejs';

register( {
	elementType: 'e-background-video',
	id: 'e-background-video-handler',
	callback: ( { element } ) => {
		const videoId = element.dataset.id;

		Alpine.data( `eBackgroundVideo${ videoId }`, () => ( {
			init() {
				const video = this.$el.querySelector( 'video' );

				if ( ! video ) {
					return;
				}

				video.addEventListener( 'play', () => {
					this.$el.classList.add( 'is-playing' );
				} );

				video.addEventListener( 'pause', () => {
					this.$el.classList.remove( 'is-playing' );
				} );
			},

			playVideo() {
				const video = this.$el.querySelector( 'video' );

				if ( video ) {
					video.play();
				}
			},

			pauseVideo() {
				const video = this.$el.querySelector( 'video' );

				if ( video ) {
					video.pause();
				}
			},
		} ) );
	},
} );
