import React from 'react';
import PropTypes from 'prop-types';

export default function Items({ id, title, body }) {
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
    );
};

Items.propTypes = {
    id:        PropTypes.number.isRequired,
    title:     PropTypes.string.isRequired,
    body:      PropTypes.string.isRequired,
};
