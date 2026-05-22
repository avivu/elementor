import { type ItemsActionPayload } from '@elementor/editor-controls';
import {
	createElements,
	duplicateElements,
	getContainer,
	moveElements,
	removeElements,
} from '@elementor/editor-elements';
import { __ } from '@wordpress/i18n';

export type AccordionItem = {
	id: string;
	title?: string;
};

export const ACCORDION_ITEM_TYPE = 'e-accordion-item';

export const useActions = () => {
	const addItem = ( {
		accordionId,
		items,
	}: {
		accordionId: string;
		items: ItemsActionPayload< AccordionItem >;
	} ) => {
		const accordion = getContainer( accordionId );

		if ( ! accordion ) {
			throw new Error( 'Accordion container not found' );
		}

		items.forEach( ( { index } ) => {
			const position = index + 1;

			createElements( {
				title: __( 'Accordion', 'elementor' ),
				elements: [
					{
						container: accordion,
						model: {
							elType: ACCORDION_ITEM_TYPE,
							editor_settings: {
								title: `Accordion Item ${ position }`,
								initial_position: position,
							},
						},
					},
				],
			} );
		} );
	};

	const removeItem = ( {
		items,
	}: {
		items: ItemsActionPayload< AccordionItem >;
	} ) => {
		removeElements( {
			title: __( 'Accordion', 'elementor' ),
			elementIds: items.map( ( { item } ) => item.id as string ),
		} );
	};

	const duplicateItem = ( {
		items,
	}: {
		items: ItemsActionPayload< AccordionItem >;
	} ) => {
		items.forEach( ( { item } ) => {
			duplicateElements( {
				elementIds: [ item.id as string ],
				title: __( 'Duplicate Accordion Item', 'elementor' ),
			} );
		} );
	};

	const moveItem = ( {
		accordionId,
		movedElementId,
		toIndex,
	}: {
		accordionId: string;
		movedElementId: string;
		toIndex: number;
	} ) => {
		const accordion = getContainer( accordionId );
		const movedElement = getContainer( movedElementId );

		if ( ! accordion || ! movedElement ) {
			throw new Error( 'Accordion or item container not found' );
		}

		moveElements( {
			title: __( 'Reorder Accordion Items', 'elementor' ),
			moves: [
				{
					element: movedElement,
					targetContainer: accordion,
					options: { at: toIndex },
				},
			],
		} );
	};

	return { addItem, removeItem, duplicateItem, moveItem };
};
