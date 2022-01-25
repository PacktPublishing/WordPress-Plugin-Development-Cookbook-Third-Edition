import { registerBlockType } from '@wordpress/blocks';
 
registerBlockType( 'ch6bt/twitter-feed', {
    title: 'Twitter Feed',
    icon: 'twitter',
    category: 'design',
    edit: () =>
<p><a href="https://twitter.com/ylefebvre">Twitter Feed</a></p>,
    save: () =>
<p><a href="https://twitter.com/ylefebvre">Twitter Feed</a></p>,
} );
