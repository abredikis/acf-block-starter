/*
 * Accordion styling and state.
 */
import './style.scss';

function init() {
	setTimeout(function(){
		document.body.style.backgroundcolor = "#AA0000";
		const countElement = document.getElementById('count');
		const incrementBtn = document.getElementById('incrementBtn');
		if(countElement && incrementBtn){
			let count = 0;

			incrementBtn.addEventListener('click', () => {
				count += 1;
				countElement.textContent = count;
			});
		}
	}, 300)
}

document.addEventListener('DOMContentLoaded', () => {
	if (window.acf) {
		window.acf.addAction('render_block_preview', init);
	} else {
		init();
	}
});

