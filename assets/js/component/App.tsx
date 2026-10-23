import React from 'react'
import Item, { type ItemProps } from '@/js/component/Item'

const ENDPOINT = 'https://jsonplaceholder.typicode.com/posts/'
const LIMIT = 6
// Stable keys for the loading cards
const PLACEHOLDERS = Array.from({ length: LIMIT }, (_, index) => `placeholder-${index}`)

interface Status {
    ok: boolean
    text: string
}

export default function App() {
    const [entries, setEntries] = React.useState<ItemProps[]>([])
    // null while loading, then the HTTP status line, or the network error
    const [status, setStatus] = React.useState<Status | null>(null)

    React.useEffect(() => {
        fetch(`${ENDPOINT}?_limit=${LIMIT}`)
            .then((response) =>
                response.json().then((entries: ItemProps[]) => {
                    setEntries(entries)
                    setStatus({ ok: response.ok, text: `${response.status} ${response.statusText || 'OK'}` })
                })
            )
            .catch((error: Error) => setStatus({ ok: false, text: error.message }))
    }, [])

    return (
        <div>
            <div className="section-header">
                <div className="d-flex align-items-center gap-2 flex-wrap">
                    <span className="tag-mono">React</span>
                    <h2 className="section-header-title">
                        React component with <a href={ENDPOINT}>{ENDPOINT}</a>
                    </h2>
                </div>
                <span className="section-header-meta">
                    Status:{' '}
                    {status ? (
                        <strong className={status.ok ? 'text-success' : 'text-danger'}>
                            {status.text} ({entries.length} posts)
                        </strong>
                    ) : (
                        <strong>loading…</strong>
                    )}
                </span>
            </div>
            <div className="row row-deck row-cards">
                {status
                    ? entries.map((entry) => <Item key={entry.id} entry={entry} />)
                    : PLACEHOLDERS.map((key) => <Placeholder key={key} />)}
            </div>
        </div>
    )
}

function Placeholder() {
    return (
        <div className="col-md-6 col-lg-4">
            <div className="card placeholder-glow" aria-hidden="true">
                <div className="card-body">
                    <div className="placeholder col-4 mb-3" />
                    <div className="placeholder col-10 mb-2" />
                    <div className="placeholder col-12" />
                    <div className="placeholder col-8" />
                </div>
            </div>
        </div>
    )
}
