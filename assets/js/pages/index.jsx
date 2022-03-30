import React from 'react';
import ReactDOM from 'react-dom';
import Item from '../component/Item';

class App extends React.Component {
    constructor() {
        super();

        this.state = {
            entries: [],
        };
    }

    componentDidMount() {
        fetch('https://jsonplaceholder.typicode.com/posts/?_limit=6')
            .then((response) => response.json())
            .then((entries) => {
                this.setState({
                    entries,
                });
            });
    }

    render() {
        return (
            <div>
                React component with{' '}
                <a href="https://jsonplaceholder.typicode.com/posts/">https://jsonplaceholder.typicode.com/posts/</a>
                <div className="row">
                    {this.state.entries.map(({ id, title, body }) => (
                        <Item key={id} id={id} title={title} body={body} />
                    ))}
                </div>
            </div>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('root'));
