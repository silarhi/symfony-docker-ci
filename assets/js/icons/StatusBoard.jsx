import { loaders } from '@icons/loaders.js'
import { UXLazyIcon } from '@silarhi/ux-icons-js/react'
import { useEffect, useState } from 'react'

const STATUS_COLORS = { Operational: 'text-success', Degraded: 'text-warning', Down: 'text-danger' }

const fallback = <span className="placeholder rounded-circle" style={{ width: '1em', height: '1em' }} />

// The icon names come from the API: each UXLazyIcon loads its icon alone, as its own chunk
export default function StatusBoard({ url }) {
    const [services, setServices] = useState(null)

    useEffect(() => {
        fetch(url)
            .then((response) => response.json())
            .then(setServices)
    }, [url])

    if (null === services) {
        return <p className="text-muted mb-0">Loading the status…</p>
    }

    return (
        <ul className="list-group list-group-flush">
            {services.map(({ label, icon, status, statusIcon }) => (
                <li key={label} className="list-group-item d-flex align-items-center gap-2">
                    <UXLazyIcon name={icon} loaders={loaders} fallback={fallback} className="fs-3" />
                    <span className="flex-grow-1">
                        {label} <code className="small">{icon}</code>
                    </span>
                    <span className={`d-flex align-items-center gap-1 ${STATUS_COLORS[status] ?? 'text-muted'}`}>
                        <UXLazyIcon name={statusIcon} loaders={loaders} fallback={fallback} />
                        {status}
                    </span>
                </li>
            ))}
        </ul>
    )
}
