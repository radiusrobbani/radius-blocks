const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
import attributes from "./attributes"
import edit from "./edit"

registerBlockType("rtrb/posts", {
    title: __("Radius Post", "radius-blocks"),
    keywords: [
        __("Posts", "radius-blocks")
    ],
    attributes,
    edit,
    save: () => null
});