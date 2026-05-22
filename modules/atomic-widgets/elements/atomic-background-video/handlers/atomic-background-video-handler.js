import { register } from '@elementor/frontend-handlers';

register( {
	elementType: 'e-background-video',
	id: 'e-background-video-handler',
	callback: ( { element } ) => {
		const video = element.querySelector( 'video' );

		if ( ! video ) {
			return;
		}

		video.addEventListener( 'play', () => {
			element.classList.add( 'is-playing' );
		} );

		video.addEventListener( 'pause', () => {
			element.classList.remove( 'is-playing' );
		} );

		element.addEventListener( 'click', ( event ) => {
			const playBtn = event.target.closest( '[data-e-type="e-background-video-play-btn"]' );
			const pauseBtn = event.target.closest( '[data-e-type="e-background-video-pause-btn"]' );

			if ( playBtn ) {
				video.play();
			} else if ( pauseBtn ) {
				video.pause();
			}
		} );
	},
} );
