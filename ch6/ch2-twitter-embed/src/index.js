import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';

registerBlockType( 'ch2twe-twitter-embed/twitter-embed', {
	title: 'Twitter Embed',
	icon: 'twitter',
	category: 'widgets',
	attributes: {
		user_name: {
			type: 'string',
			default: 'WordPress',
		},
		option_id: {
			type: 'string',
			default: '1',
		},        
	},
	edit: edit,
	save() {
		// Rendering in PHP
		return null;
	},
} );
