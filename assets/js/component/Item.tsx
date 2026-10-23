export interface ItemProps {
    id: number
    title: string
    body: string
}

export default function Items({ id, title, body }: ItemProps) {
    return (
        <div className="col-md-4">
            <div className="card">
                <div className="card-body">
                    <h4 className="card-title">
                        #{id} - {title}
                    </h4>
                    <p className="card-text">{body}</p>
                </div>
            </div>
        </div>
    )
}
