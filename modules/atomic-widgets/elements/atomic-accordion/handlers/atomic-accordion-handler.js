import { register } from '@elementor/frontend-handlers';

const SELECTED_CLASS = 'e--selected';

register( {
	elementType: 'e-accordion',
	id: 'e-accordion-handler',
	callback: ( { element } ) => {
		const syncState = ( details ) => {
			const isOpen = details.open;

			details.classList.toggle( SELECTED_CLASS, isOpen );

			const title = details.querySelector( ':scope > [data-e-type="e-accordion-item-title"]' );
			if ( title ) {
				title.classList.toggle( SELECTED_CLASS, isOpen );
			}
		};

		const items = element.querySelectorAll( '[data-e-type="e-accordion-item"]' );

		items.forEach( ( details ) => {
			syncState( details );
			details.addEventListener( 'toggle', () => syncState( details ) );
		} );
	},
} );
