const RenderView = (props) => {
    const {data} = props;
    return (
        <>
            <h2>Radius post react</h2>
            <ul>
                {data.length && data.map((post) => {
                    return <li key={post.id}>{post.title.rendered}</li>
                })}
            </ul>
        </>
    )
}

export default RenderView;