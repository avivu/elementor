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
// Chains with any existing onYouTubeIframeAPIReady handler to support multiple instances.
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

		const prevCallback = window.onYouTubeIframeAPIReady;
		window.onYouTubeIframeAPIReady = () => {
			prevCallback?.();
			resolve( window.YT );
		};
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

// --- Apply base positioning styles to a cover iframe ---
// Uses transform centering (same pattern as .elementor-background-video-embed CSS).
const applyIframeBaseStyles = ( iframe ) => {
	Object.assign( iframe.style, {
		position: 'absolute',
		top: '50%',
		left: '50%',
		transform: 'translate(-50%, -50%)',
		maxWidth: 'none',
		border: 'none',
		pointerEvents: 'none',
		zIndex: '-1',
	} );
};

// --- Resize iframe to cover the container (like background-size: cover) ---
const resizeIframeToCover = ( iframe, container, aspectRatio = 16 / 9 ) => {
	const containerWidth = container.offsetWidth;
	const containerHeight = container.offsetHeight;

	if ( ! containerWidth || ! containerHeight ) {
		return;
	}

	const isWidthFixed = containerWidth / containerHeight > aspectRatio;
	const width = isWidthFixed ? containerWidth : containerHeight * aspectRatio;
	const height = isWidthFixed ? containerWidth / aspectRatio : containerHeight;

	iframe.style.width = width + 'px';
	iframe.style.height = height + 'px';
};

// --- Hide the static preview div ---
const hidePreview = ( element ) => {
	const preview = element.querySelector( '.e-bve-preview' );
	if ( preview ) {
		preview.style.display = 'none';
	}
};

// --- Show fallback image ---
const showFallback = ( element, fallbackUrl ) => {
	if ( fallbackUrl ) {
		element.style.backgroundImage = `url(${ fallbackUrl })`;
		element.style.backgroundSize = 'cover';
		element.style.backgroundPosition = 'center';
	}
};

// --- YouTube initialization ---
// Uses a <div> placeholder — the proven pattern (see youtube-handler.js).
// YT.Player replaces the div with its own iframe; onReady gives us the real iframe.
const initYouTube = ( element, settings ) => {
	const videoId = getYouTubeVideoId( settings.source );
	if ( ! videoId ) {
		return null;
	}

	// Create a <div> placeholder; YT will replace it with its own <iframe>.
	const placeholder = document.createElement( 'div' );
	element.insertBefore( placeholder, element.firstChild );

	let player = null;
	let ytIframe = null;

	const onResize = () => {
		if ( ytIframe ) {
			resizeIframeToCover( ytIframe, element );
		}
	};

	loadYouTubeAPI().then( ( YT ) => {
		const playerVars = {
			controls: 0,
			rel: 0,
			modestbranding: 1,
			autoplay: 1,
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
					// YT has replaced the placeholder div with its own <iframe>.
					// Apply base positioning + cover sizing to the real YT iframe.
					ytIframe = event.target.getIframe();
					applyIframeBaseStyles( ytIframe );
					resizeIframeToCover( ytIframe, element );
					ytIframe.setAttribute( 'aria-hidden', 'true' );
					ytIframe.setAttribute( 'tabindex', '-1' );
					ytIframe.setAttribute( 'allow', 'autoplay; fullscreen' );

					window.addEventListener( 'resize', onResize );

					hidePreview( element );

					if ( settings.mute ) {
						event.target.mute();
					}
					event.target.playVideo();
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

		player = new YT.Player( placeholder, playerOptions );
	} );

	return {
		destroy: () => {
			window.removeEventListener( 'resize', onResize );
			if ( player && 'function' === typeof player.destroy ) {
				player.destroy();
			} else if ( placeholder && placeholder.parentNode ) {
				placeholder.parentNode.removeChild( placeholder );
			}
		},
	};
};

// --- Vimeo initialization ---
const initVimeo = ( element, settings ) => {
	const videoId = getVimeoVideoId( settings.source );
	if ( ! videoId ) {
		return null;
	}

	const iframe = document.createElement( 'iframe' );
	applyIframeBaseStyles( iframe );
	resizeIframeToCover( iframe, element );
	iframe.setAttribute( 'allow', 'autoplay; fullscreen' );
	iframe.setAttribute( 'aria-hidden', 'true' );
	iframe.setAttribute( 'tabindex', '-1' );
	element.insertBefore( iframe, element.firstChild );

	const onResize = () => resizeIframeToCover( iframe, element );
	window.addEventListener( 'resize', onResize );

	const params = new URLSearchParams( {
		background: 1,
		autoplay: 1,
		loop: settings.loop ? 1 : 0,
		dnt: settings.privacy_mode ? 1 : 0,
	} );

	iframe.src = `https://player.vimeo.com/video/${ videoId }?${ params.toString() }`;

	let player = null;

	loadVimeoAPI().then( ( Vimeo ) => {
		player = new Vimeo.Player( iframe );

		player.ready().then( () => {
			hidePreview( element );
		} );

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
	} );

	return {
		destroy: () => {
			window.removeEventListener( 'resize', onResize );
			if ( player && 'function' === typeof player.destroy ) {
				player.destroy();
			}
			if ( iframe && iframe.parentNode ) {
				iframe.parentNode.removeChild( iframe );
			}
		},
	};
};

// --- Main registration ---
register( {
	elementType: 'e-background-video-embed',
	id: 'e-background-video-embed-handler',
	callback: ( { element } ) => {
		const settings = JSON.parse( element.getAttribute( 'data-settings' ) || '{}' );

		// Mobile: show fallback if play_on_mobile is off.
		// Use Elementor's device mode (works in editor preview) with viewport-width fallback.
		const isMobile = () => {
			try {
				return 'mobile' === elementorFrontend.getCurrentDeviceMode();
			} catch ( e ) {
				const breakpoint = window.elementorFrontend?.config?.responsive?.activeBreakpoints?.mobile?.value ?? 767;
				return window.innerWidth <= breakpoint;
			}
		};
		if ( isMobile() && ! settings.play_on_mobile ) {
			showFallback( element, settings.fallback_image_url );
			return;
		}

		const source = settings.source || '';
		const provider = detectProvider( source );

		if ( ! provider ) {
			return;
		}

		// Skip re-initialization when only style controls changed (video settings are unchanged).
		// The element re-renders on every control change in the editor; re-creating the iframe
		// each time causes a visible jump. We detect this by caching a key of the video-relevant
		// settings on the element and comparing on each render.
		const videoKey = JSON.stringify( {
			source: settings.source,
			loop: settings.loop,
			mute: settings.mute,
			start_time: settings.start_time,
			end_time: settings.end_time,
			privacy_mode: settings.privacy_mode,
			play_on_mobile: settings.play_on_mobile,
		} );

		if ( element._bveKey === videoKey && element._bveHandle ) {
			return () => {
				// Only fully clean up when the element is actually removed from the DOM.
				// Re-renders (style changes) keep element.isConnected === true — skip cleanup.
				if ( ! element.isConnected ) {
					element._bveHandle?.destroy();
					delete element._bveHandle;
					delete element._bveKey;
				}
			};
		}

		// Video settings changed (or first render) — destroy any existing player and reinit.
		if ( element._bveHandle ) {
			element._bveHandle.destroy();
			element._bveHandle = null;
		}

		let handle = null;

		if ( 'youtube' === provider ) {
			handle = initYouTube( element, settings );
		} else if ( 'vimeo' === provider ) {
			handle = initVimeo( element, settings );
		}

		element._bveKey = videoKey;
		element._bveHandle = handle;

		return () => {
			if ( ! element.isConnected ) {
				element._bveHandle?.destroy();
				delete element._bveHandle;
				delete element._bveKey;
			}
		};
	},
} );
