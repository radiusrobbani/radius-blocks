const {__} = wp.i18n;
const {useBlockProps, InspectorControls} = wp.blockEditor
const {Fragment} = wp.element;
const {PanelBody, SelectControl, TextControl} = wp.components;

export default function Edit(props) {
    const {
        attributes,
        setAttributes
    } = props;
    return (
        <>
            <InspectorControls>
                <PanelBody title={__("Radius Posts", 'radius-blocks')}>
                    <TextControl
                        label={ __( 'Limit', 'radius-blocks' ) }
                        value={ attributes.limit }
                        onChange={ ( val ) => setAttributes( { limit: parseInt(val, 10) } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div>
                <h3>Radius post PHP</h3>
            </div>
        </>
    );
}