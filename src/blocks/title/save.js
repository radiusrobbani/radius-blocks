const {__} = wp.i18n;
const {
    useBlockProps
} = wp.blockEditor

const {TextControl} = wp.components;
import './editor.scss';

export default function save(props) {
    const {attributes} = props;
    return (
            <div>Your name is {attributes.fname}</div>
    );
}