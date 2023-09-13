import '../scripts/app.js';
import '../styles/app.scss';
import './style.scss';

function init() {
	console.log('block initialized')
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.acf) {
        window.acf.addAction('render_block_preview', init);
    } else {
        init();
    }
});

