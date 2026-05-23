import { register } from '@elementor/frontend-handlers';

// --- Provider detection ---
const detectProvider = ( url ) => {
	if ( /vimeo\.com/.test( url ) ) {
		return 'vimeo';
	}
	if ( /youtu\.be|youtube\.com/.test( url ) ) {
		return 'youtube';
	}
	return null;
};

// --- YouTube video ID extraction ---
const getYouTubeVideoId = ( url ) => {
	const regex = /^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user|shorts)\/))([^?&"'>]+)/;
	const match = url.match( regex );
	return match ? match[ 1 ] : null;
};

// --- Vimeo video ID extraction ---
const getVimeoVideoId = ( url ) => {
	const match = url.match( /vimeo\.com\/(?:video\/)?(\d+)/ );
	return match ? match[ 1 ] : null;
};

// --- YouTube API loader ---
const loadYouTubeAPI = () => {
	return new Promise( ( resolve ) => {
		if ( window.YT && window.YT.loaded ) {
			resolve( window.YT );
			return;
		}

		const YOUTUBE_IFRAME_API_URL = 'https://www.youtube.com/iframe_api';
		if ( ! document.querySelector( `script[src="${ YOUTUBE_IFRAME_API_URL }"]` ) ) {
			const tag = document.createElement( 'script' );
			tag.src = YOUTUBE_IFRAME_API_URL;
			const firstScriptTag = document.getElementsByTagName( 'script' )[ 0 ];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		}

		const checkYT = () => {
			if ( window.YT && window.YT.loaded ) {
				resolve( window.YT );
			} else {
				setTimeout( checkYT, 350 );
			}
		};
		checkYT();
	} );
};

// --- Vimeo API loader ---
const loadVimeoAPI = () => {
	return new Promise( ( resolve ) => {
		if ( window.Vimeo && window.Vimeo.Player ) {
			resolve( window.Vimeo );
			return;
		}

		const VIMEO_PLAYER_API_URL = 'https://player.vimeo.com/api/player.js';
		if ( ! document.querySelector( `script[src="${ VIMEO_PLAYER_API_URL }"]` ) ) {
			const tag = document.createElement( 'script' );
			tag.src = VIMEO_PLAYER_API_URL;
			document.head.appendChild( tag );
		}

		const checkVimeo = () => {
			if ( window.Vimeo && window.Vimeo.Player ) {
				resolve( window.Vimeo );
			} else {
				setTimeout( checkVimeo, 350 );
			}
		};
		checkVimeo();
	} );
};

// --- Create background iframe ---
const createBackgroundIframe = ( element ) => {
	const iframe = document.createElement( 'iframe' );
	Object.assign( iframe.style, {
		position: 'absolute',
		inset: '0',
		width: '100%',
		height: '100%',
		border: 'none',
		pointerEvents: 'none',
		zIndex: '0',
	} );
	iframe.setAttribute( 'allow', 'autoplay; fullscreen' );
	iframe.setAttribute( 'aria-hidden', 'true' );
	iframe.setAttribute( 'tabindex', '-1' );
	element.insertBefore( iframe, element.firstChild );
	return iframe;
};

// --- YouTube initialization ---
const initYouTube = ( iframe, settings ) => {
	const videoId = getYouTubeVideoId( settings.source );
	if ( ! videoId ) {
		return null;
	}

	return loadYouTubeAPI().then( ( YT ) => {
		const playerVars = {
			controls: 0,
			rel: 0,
			showinfo: 0,
			modestbranding: 1,
			autoplay: settings.autoplay ? 1 : 0,
			loop: settings.loop ? 1 : 0,
			mute: settings.mute ? 1 : 0,
			playsinline: 1,
		};

		if ( settings.loop ) {
			playerVars.playlist = videoId;
		}

		if ( settings.start_time ) {
			playerVars.start = Number( settings.start_time );
		}

		if ( settings.end_time ) {
			playerVars.end = Number( settings.end_time );
		}

		const playerOptions = {
			videoId,
			playerVars,
			events: {
				onReady: ( event ) => {
					if ( settings.mute ) {
						event.target.mute();
					}
					if ( settings.autoplay ) {
						event.target.playVideo();
					}
				},
				onStateChange: ( event ) => {
					if ( event.data === YT.PlayerState.ENDED && settings.loop ) {
						event.target.seekTo( settings.start_time ? Number( settings.start_time ) : 0 );
					}
				},
			},
		};

		if ( settings.privacy_mode ) {
			playerOptions.host = 'https://www.youtube-nocookie.com';
			playerOptions.origin = window.location.hostname;
		}

		return new YT.Player( iframe, playerOptions );
	} );
};

// --- Vimeo initialization ---
const initVimeo = ( iframe, settings ) => {
	const videoId = getVimeoVideoId( settings.source );
	if ( ! videoId ) {
		return null;
	}

	const params = new URLSearchParams( {
		background: 1,
		autoplay: 1,
		loop: settings.loop ? 1 : 0,
		muted: 1,
		dnt: settings.privacy_mode ? 1 : 0,
	} );

	iframe.src = `https://player.vimeo.com/video/${ videoId }?${ params.toString() }`;

	return loadVimeoAPI().then( ( Vimeo ) => {
		const player = new Vimeo.Player( iframe );

		if ( settings.start_time ) {
			player.setCurrentTime( Number( settings.start_time ) );
		}

		if ( settings.end_time ) {
			const endTime = Number( settings.end_time );
			player.on( 'timeupdate', ( data ) => {
				if ( data.seconds >= endTime ) {
					if ( settings.loop ) {
						player.setCurrentTime( settings.start_time ? Number( settings.start_time ) : 0 );
					} else {
						player.pause();
					}
				}
			} );
		}

		return player;
	} );
};

// --- Mobile detection ---
const isMobile = () => /Mobi|Android/i.test( navigator.userAgent );

// --- Show fallback image ---
const showFallback = ( element, fallbackUrl ) => {
	if ( fallbackUrl ) {
		element.style.backgroundImage = `url(${ fallbackUrl })`;
		element.style.backgroundSize = 'cover';
		element.style.backgroundPosition = 'center';
	}
};

// --- Main registration ---
register( {
	elementType: 'e-background-video-embed',
	id: 'e-background-video-embed-handler',
	callback: ( { element } ) => {
		const settings = JSON.parse( element.dataset.settings || '{}' );

		// In editor: show fallback only, no live video
		if ( window.elementorFrontend?.isEditMode() ) {
			showFallback( element, settings.fallback_image_url );
			return;
		}

		// Mobile: show fallback if play_on_mobile is off
		if ( isMobile() && ! settings.play_on_mobile ) {
			showFallback( element, settings.fallback_image_url );
			return;
		}

		const source = settings.source || '';
		const provider = detectProvider( source );

		if ( ! provider ) {
			return;
		}

		const iframe = createBackgroundIframe( element );

		// Hide the static twig-rendered preview now that the iframe is injected.
		const preview = element.querySelector( '.e-bve-preview' );
		if ( preview ) {
			preview.style.display = 'none';
		}

		let playerPromise;

		if ( 'youtube' === provider ) {
			playerPromise = initYouTube( iframe, settings );
		} else if ( 'vimeo' === provider ) {
			playerPromise = initVimeo( iframe, settings );
		}

		return () => {
			if ( playerPromise ) {
				playerPromise.then( ( player ) => {
					if ( player && 'function' === typeof player.destroy ) {
						player.destroy();
					}
				} );
			}

			if ( iframe && iframe.parentNode ) {
				iframe.parentNode.removeChild( iframe );
			}
		};
	},
} );
