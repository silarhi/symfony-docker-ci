import React from 'react'

export interface ItemProps {
    id: number
    userId: number
    title: string
    body: string
}

export default function Item({ entry }: { entry: ItemProps }) {
    const { id, userId, title, body } = entry
    // null until a copy, then whether it worked
    const [copied, setCopied] = React.useState<boolean | null>(null)

    React.useEffect(() => {
        if (null === copied) {
            return
        }
        const timeout = setTimeout(() => setCopied(null), 2000)
        return () => clearTimeout(timeout)
    }, [copied])

    const copy = () => {
        navigator.clipboard.writeText(JSON.stringify(entry, null, 2)).then(
            () => setCopied(true),
            () => setCopied(false)
        )
    }

    return (
        <div className="col-md-6 col-lg-4">
            <div className="card">
                <div className="card-body">
                    <div className="d-flex align-items-center justify-content-between mb-2">
                        <span className="label-mono text-secondary">Post #{String(id).padStart(3, '0')}</span>
                        <span className="status-dot-square bg-primary" aria-hidden="true" />
                    </div>
                    <h3 className="card-title">{title}</h3>
                    <p className="card-text text-secondary">{body}</p>
                </div>
                <div className="card-footer d-flex align-items-center justify-content-between font-monospace small">
                    <span className="text-secondary">userId: {userId}</span>
                    <button
                        type="button"
                        className={`btn btn-sm label-mono ${copied ? 'btn-ghost-success' : 'btn-ghost-primary'}`}
                        onClick={copy}
                    >
                        {null === copied ? 'Copy JSON' : copied ? 'Copied' : 'Copy failed'}
                    </button>
                </div>
            </div>
        </div>
    )
}
