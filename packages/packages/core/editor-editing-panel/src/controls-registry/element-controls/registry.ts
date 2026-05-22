import { type ControlComponent } from '@elementor/editor-controls';

import { type ControlRegistry, controlsRegistry } from '../controls-registry';
import { AccordionControl } from './accordion-control/accordion-control';
import { TabsControl } from './tabs-control/tabs-control';

const controlTypes = {
	tabs: { component: TabsControl as ControlComponent, layout: 'full' },
	accordion: { component: AccordionControl as ControlComponent, layout: 'full' },
} as const satisfies ControlRegistry;

export const registerElementControls = () => {
	Object.entries< ( typeof controlTypes )[ keyof typeof controlTypes ] >( controlTypes ).forEach(
		( [ type, { component, layout } ] ) => {
			controlsRegistry.register( type, component, layout );
		}
	);
};
