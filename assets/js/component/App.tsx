import React from 'react'
import Item, { type ItemProps } from '@/js/component/Item'

export default function App() {
    const [entries, setEntries] = React.useState<ItemProps[]>([])

    React.useEffect(() => {
        fetch('https://jsonplaceholder.typicode.com/posts/?_limit=6')
            .then((response) => response.json())
            .then((entries: ItemProps[]) => {
                setEntries(entries)
            })
    }, [])

    return (
        <div>
            React component with{' '}
            <a href="https://jsonplaceholder.typicode.com/posts/">https://jsonplaceholder.typicode.com/posts/</a>
            <div className="row row-deck row-cards">
                {entries.map(({ id, title, body }) => (
                    <Item key={id} id={id} title={title} body={body} />
                ))}
            </div>
        </div>
    )
}
