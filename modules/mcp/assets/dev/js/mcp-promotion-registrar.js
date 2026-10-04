import McpUpgradePromotion from './mcp-upgrade-promotion';

const injectIntoMcpAdminPromotion = window.elementorMcpComposer?.injectIntoMcpAdminPromotion;

if ( injectIntoMcpAdminPromotion ) {
	injectIntoMcpAdminPromotion( {
		id: 'elementor-core-mcp-upgrade',
		component: McpUpgradePromotion,
	} );
}
