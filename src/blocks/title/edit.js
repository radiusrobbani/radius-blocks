const {__} = wp.i18n;
const {
    useBlockProps
} = wp.blockEditor

const { TextControl } = wp.components;
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
    return (
        <div { ...useBlockProps() }>
            <TextControl
                label={ __( 'First name', 'gutenpride' ) }
                value={ attributes.fname }
                onChange={ ( val ) => setAttributes( { fname: val } ) }
            />
        </div>
    );
}