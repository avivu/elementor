import { register } from '@elementor/frontend-handlers';

register( {
	elementType: 'e-background-video',
	id: 'e-background-video-handler',
	callback: ( { element } ) => {
		const video = element.querySelector( 'video' );

		if ( ! video ) {
			return;
		}

		const onPlay = () => element.classList.add( 'is-playing' );
		const onPause = () => element.classList.remove( 'is-playing' );
		const onClick = ( event ) => {
			const playBtn = event.target.closest( '[data-e-type="e-background-video-play-btn"]' );
			const pauseBtn = event.target.closest( '[data-e-type="e-background-video-pause-btn"]' );

			if ( playBtn ) {
				video.play();
			} else if ( pauseBtn ) {
				video.pause();
			}
		};

		video.addEventListener( 'play', onPlay );
		video.addEventListener( 'pause', onPause );
		element.addEventListener( 'click', onClick );

		return () => {
			video.removeEventListener( 'play', onPlay );
			video.removeEventListener( 'pause', onPause );
			element.removeEventListener( 'click', onClick );
		};
	},
} );
