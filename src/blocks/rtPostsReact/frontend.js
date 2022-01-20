import RenderView from "./renderView";

const {render, useState, useEffect} = wp.element;
import apiFetch from '@wordpress/api-fetch';

const RtPostReact = (props) => {
    const [data, setData] = useState([]);
    const [perPage, setPerPage] = useState(props.limit || 10);
    useEffect(() => {
        apiFetch({path: '/wp/v2/posts?per_page='+perPage}).then((posts) => {
            setData(posts);
        });
    }, [perPage]);

    return (
        <div className="rt-postsreact-frontend">
            <RenderView {...props} data={data}/>
        </div>
    )
}

const divsToUpdate = document.querySelectorAll(".rt-postsreact")

divsToUpdate.forEach(div => {
    const data = JSON.parse(div.querySelector("pre").innerText)
    render(<RtPostReact {...data} />, div)
    div.classList.remove("rtrb-ph")
})
