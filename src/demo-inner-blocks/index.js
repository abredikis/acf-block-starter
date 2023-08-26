/*
 * Accordion styling and state.
 */
import './style.scss';

function init() {

}

document.addEventListener('DOMContentLoaded', () => {
	if (window.acf) {
		window.acf.addAction('render_block_preview', init);
	} else {
		init();
	}
});

