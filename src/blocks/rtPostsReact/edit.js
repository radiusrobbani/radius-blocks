import RenderView from "./renderView";
import apiFetch from "@wordpress/api-fetch";

const {__} = wp.i18n;
const {InspectorControls} = wp.blockEditor
const {PanelBody, TextControl} = wp.components;
const {useState, useEffect} = wp.element;

export default function Edit(props) {
    const {attributes, setAttributes} = props;
    const [data, setData] = useState([]);
    useEffect(() => {
        if (!attributes.limit) {
            return;
        }
        apiFetch({path: '/wp/v2/posts?per_page=' + attributes.limit}).then((posts) => {
            setData(posts);
        });
    }, [attributes.limit]);
    return (
        <>
            <InspectorControls>
                <PanelBody title={__("Radius Posts", 'radius-blocks')}>
                    <TextControl
                        label={__('Limit', 'radius-blocks')}
                        value={attributes.limit}
                        onChange={(val) => setAttributes({limit: parseInt(val, 10)})}
                    />
                </PanelBody>
            </InspectorControls>
            <div className="rt-postsreact-editor">
                <RenderView {...attributes} data={data}/>
            </div>
        </>
    );
}