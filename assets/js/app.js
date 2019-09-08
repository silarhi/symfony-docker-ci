import '../scss/app.scss';

import React from 'react';
import ReactDOM from 'react-dom';
import Item from './component/Item';

class App extends React.Component {
    constructor() {
        super();

        this.state = {
            entries: []
        };
    }

    componentDidMount() {
        fetch('https://jsonplaceholder.typicode.com/posts/?_limit=12')
            .then(response => response.json())
            .then(entries => {
                this.setState({
                    entries
                });
            });
    }

    render() {
        return (
            <div>
                React component with <a href="https://jsonplaceholder.typicode.com/posts/">from https://jsonplaceholder.typicode.com/posts/</a>
                <div className="row no-gutters">
                    {this.state.entries.map(
                        ({ id, title, body }) => (
                            <Item
                                key={id}
                                title={title}
                                body={body}
                            >
                            </Item>
                        )
                    )}
                </div>
            </div>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('root'));