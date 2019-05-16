/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');

const inner = document.querySelector("#inner");

if (inner) {
    inner.innerHTML = 'This block is managed by Javascript 😍';
    inner.style = 'border: 2px dashed red; padding: 1rem';
}
