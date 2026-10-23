import { LucideStarIcon } from '@icons/index.js'
import { UXIcon } from '@silarhi/ux-icons-js/react'
import { useState } from 'react'

// Static import: the star is bundled with the component, and the other icons of the dump are tree-shaken
export default function Rating({ max = 5 }: { max?: number }) {
    const [rating, setRating] = useState(3)

    return (
        <div className="d-flex align-items-center gap-1">
            {Array.from({ length: max }, (_, index) => (
                <button
                    // biome-ignore lint/suspicious/noArrayIndexKey: the stars never move
                    key={index}
                    type="button"
                    className="btn btn-ghost-secondary btn-icon fs-2"
                    aria-label={`Rate ${index + 1} out of ${max}`}
                    aria-pressed={index < rating}
                    onClick={() => setRating(index + 1)}
                >
                    <UXIcon icon={LucideStarIcon} className={index < rating ? 'text-yellow' : 'text-muted'} />
                </button>
            ))}
            <span className="ms-2 text-muted">
                {rating} / {max}
            </span>
        </div>
    )
}
