import ServerSideRender from '@wordpress/server-side-render';
import { SelectControl, PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

const optionIdOptions = [];

wp.apiFetch( {path: "/twitter-embed/v1/optionlist"} ).then( posts => {
    jQuery.each( posts, function( key, val ) {
        optionIdOptions.push( {label: 'Option #' + key + ': ' + val, value: key} );
    } );
} ).catch()

const edit = props => {
    const { attributes: { user_name, option_id }, className, setAttributes } = props;
    
    const setOptionID = option_id => {
        props.setAttributes( { option_id } );
    };    
    
    const setUserName = user_name => {
        props.setAttributes( { user_name } );
    }
    
    const inspectorControls = (
        <InspectorControls key="inspector">
            <PanelBody>
                <TextControl
                    label = "Twitter Account User Name"
                    value = { user_name }
                    onChange = { setUserName } />
                <SelectControl
                    label="Options" value={ option_id }
                    options= { optionIdOptions }
                    onChange = { setOptionID } />
            </PanelBody>
        </InspectorControls> );
    return [
            <div className={ props.className } key="returneddata">
                <div className="ch2te-block-warning">Twitter Embed Warning: List of tweets for selected user will only appear when viewing page</div>
                <ServerSideRender
                    block="ch2twe-twitter-embed/twitter-embed"
                    attributes = { props.attributes } />
                { inspectorControls }
            </div>
    ];
};
    
export default edit;
    