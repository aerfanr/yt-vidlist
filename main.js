console.log("Main script loaded.");

const display = document.getElementById("format");

format_current = []

function add_merge(format_id) {
	format_current.push({
		sign: '+', // The plus sign means merge, it will be useful later
		id: format_id
	});

	update_format_display();
}

function update_format_display() {
	display.replaceChildren(...format_current.map((f, i) => {
		const container = document.createElement('div');
		if (i) {
			const sign = document.createElement('span');
			sign.innerText = f.sign;
			container.appendChild(sign);
		}
		const id = document.createElement('button');
		id.innerText = f.id;
		id.onclick = () => {
			format_current = format_current.filter((_it, idx) => {
				return i !== idx
			});
			update_format_display();
		}
		container.appendChild(id);

		return container;
	}))
}
