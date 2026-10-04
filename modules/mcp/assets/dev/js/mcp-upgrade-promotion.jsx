import Box from '@elementor/ui/Box';
import Button from '@elementor/ui/Button';
import Stack from '@elementor/ui/Stack';
import Typography from '@elementor/ui/Typography';
import { __ } from '@wordpress/i18n';

const UPGRADE_URL = 'https://go.elementor.com/go-pro-mcp-connector-page-upgrade/';

export default function McpUpgradePromotion() {
	return (
		<Box
			sx={ {
				mt: 4,
				px: 3,
				py: 2.5,
				border: '1px solid',
				borderColor: 'divider',
				borderRadius: 1,
				backgroundColor: 'background.paper',
			} }
		>
			<Stack
				direction={ { xs: 'column', sm: 'row' } }
				spacing={ 2 }
				alignItems={ { xs: 'stretch', sm: 'center' } }
				justifyContent="space-between"
			>
				<Stack spacing={ 0.5 } sx={ { minWidth: 0 } }>
					<Typography variant="subtitle1" fontWeight={ 600 }>
						{ __( 'Build more with your AI agent', 'elementor' ) }
					</Typography>
					<Typography variant="body2" color="text.secondary">
						{ __(
							'With Pro, your agent can build with Theme Builder, forms, popups, and more advanced Elementor capabilities.',
							'elementor'
						) }
					</Typography>
				</Stack>

				<Button
					variant="outlined"
					color="promotion"
					href={ UPGRADE_URL }
					target="_blank"
					rel="noopener noreferrer"
					sx={ { flexShrink: 0, alignSelf: { xs: 'flex-start', sm: 'center' } } }
				>
					{ __( 'Upgrade', 'elementor' ) }
				</Button>
			</Stack>
		</Box>
	);
}
