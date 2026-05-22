import * as React from 'react';
import {
	ControlFormLabel,
	Repeater,
	type RepeaterItem,
	type SetRepeaterValuesMeta,
} from '@elementor/editor-controls';
import {
	getContainer,
	updateElementEditorSettings,
	useElementEditorSettings,
} from '@elementor/editor-elements';
import { commandEndEvent, v1ReadyEvent, __privateUseListenTo as useListenTo } from '@elementor/editor-v1-adapters';
import { Stack, TextField } from '@elementor/ui';
import { __ } from '@wordpress/i18n';

import { useElement } from '../../../contexts/element-context';
import { ACCORDION_ITEM_TYPE, type AccordionItem, useActions } from './use-actions';

type AccordionItemModel = {
	get: ( key: string ) => unknown;
};

export const AccordionControl = ( { label }: { label: string } ) => {
	const { element } = useElement();
	const { addItem, duplicateItem, moveItem, removeItem } = useActions();

	const accordionItems = useListenTo(
		[
			v1ReadyEvent(),
			commandEndEvent( 'document/elements/create' ),
			commandEndEvent( 'document/elements/delete' ),
			commandEndEvent( 'document/elements/update' ),
		],
		() => {
			const container = getContainer( element.id );
			const model = container?.model;

			if ( ! model ) {
				return [];
			}

			const elements = ( model.get( 'elements' ) ?? [] ) as AccordionItemModel[];

			return elements
				.filter( ( m ) => m.get( 'elType' ) === ACCORDION_ITEM_TYPE )
				.map( ( m ) => ( {
					id: m.get( 'id' ) as string,
					editorSettings: ( m.get( 'editor_settings' ) ?? {} ) as { title?: string },
				} ) );
		},
		[ element.id ]
	);

	const repeaterValues: RepeaterItem< AccordionItem >[] = accordionItems.map( ( item, index ) => ( {
		id: item.id,
		title: item.editorSettings?.title,
		index,
	} ) );

	const setValue = (
		_newValues: RepeaterItem< AccordionItem >[],
		_options: unknown,
		meta?: SetRepeaterValuesMeta< RepeaterItem< AccordionItem > >
	) => {
		if ( meta?.action?.type === 'add' ) {
			return addItem( { accordionId: element.id, items: meta.action.payload } );
		}

		if ( meta?.action?.type === 'remove' ) {
			return removeItem( { items: meta.action.payload } );
		}

		if ( meta?.action?.type === 'duplicate' ) {
			return duplicateItem( { items: meta.action.payload } );
		}

		if ( meta?.action?.type === 'reorder' ) {
			const { from, to } = meta.action.payload;

			return moveItem( {
				accordionId: element.id,
				movedElementId: accordionItems[ from ].id,
				toIndex: to,
			} );
		}
	};

	return (
		<Repeater
			showToggle={ false }
			values={ repeaterValues }
			setValues={ setValue }
			showRemove={ repeaterValues.length > 1 }
			label={ label }
			itemSettings={ {
				getId: ( { item } ) => item.id,
				initialValues: { id: '', title: __( 'Accordion Item', 'elementor' ) },
				Label: ItemLabel,
				Content: ItemContent,
				Icon: () => null,
			} }
		/>
	);
};

const ItemLabel = ( { value }: { value: AccordionItem } ) => {
	return <span>{ value?.title ?? __( 'Accordion Item', 'elementor' ) }</span>;
};

const ItemContent = ( { value }: { value: AccordionItem } ) => {
	if ( ! value.id ) {
		return null;
	}

	return (
		<Stack p={ 2 }>
			<AccordionItemLabelControl elementId={ value.id } />
		</Stack>
	);
};

const AccordionItemLabelControl = ( { elementId }: { elementId: string } ) => {
	const editorSettings = useElementEditorSettings( elementId );
	const label = editorSettings?.title ?? '';

	return (
		<Stack gap={ 1 }>
			<ControlFormLabel>{ __( 'Item name', 'elementor' ) }</ControlFormLabel>
			<TextField
				size="tiny"
				value={ label }
				onChange={ ( { target }: React.ChangeEvent< HTMLInputElement > ) => {
					updateElementEditorSettings( {
						elementId,
						settings: { title: target.value },
					} );
				} }
			/>
		</Stack>
	);
};
