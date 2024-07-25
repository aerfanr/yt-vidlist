console.log("Main script loaded.");

const format_display = document.getElementById("format");
const download_display = document.getElementById("download-list");

format_current = []
downloads = []

update_format_display();
update_download_display();

function add_merge(format_id) {
	format_current.push({
		sign: '+', // The plus sign means merge, it will be useful later
		id: format_id
	});

	update_format_display();
}

function update_format_display() {
	if (format_current.length === 0) {
		format_display.innerText = "Default format";
		return;
	}

	format_display.replaceChildren(...format_current.map((f, i) => {
		const container = document.createElement('div');
		container.className = 'inline-flex'

		if (i) {
			const sign = document.createElement('span');
			sign.innerText = f.sign;
			sign.className = 'p-2';
			container.appendChild(sign);
		}

		const id = document.createElement('button');
		id.innerText = f.id;
		id.className = 'p-2 bg-rose-800'
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

function add_download() {
	query = document.getElementById('link');
	if (!query || query.innerText.length === 0) {
		alert("No video selected");
                return;
        }
	title = document.getElementById('title')?.innerText || "No title";

	downloads.push({
		title: title,
		query: query.innerText,
		format: format_current.reduce((a, b) => {
			if (a.length !== 0) {
				a += b.sign;
			}
			a += b.id;
			return a;
		}, '')
        })

	update_download_display();
}

function update_download_display() {
	download_display.replaceChildren(...downloads.map((d, i) => {
		const container = document.createElement('div');
		container.className = "mx-2 my-3 rounded-lg bg-rose-50";

		const title = document.createElement('div');
		title.innerText = d.title;
                title.className = "font-bold p-2 text-pretty";
                container.appendChild(title);

		const query = document.createElement('div');
		query.innerText = d.query;
		query.className = "p-2 text-nowrap overflow-x-auto";
		container.appendChild(query);

		const format = document.createElement('pre');
		if (d.format.length === 0) {
			format.innerText = 'Default format';
		} else {
			format.innerText = d.format;
			format.className = "p-2 text-nowrap overflow-x-auto";
		}
		container.appendChild(format);

		const remove = document.createElement('button');
		remove.innerText = 'Remove';
		remove.className = 'm-2';
                remove.onclick = () => {
                        downloads = downloads.filter((_it, idx) => {
                                return idx !== i
                        })
                        update_download_display();
                }
                container.appendChild(remove);

		return container;
	}))
}

function download_json() {
	const blob = new Blob([JSON.stringify(downloads)], {
		type: "application/json;charset=utf-8"
	});

	const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'video-list.json';
        link.click();
}

function load_json() {
        const input = document.createElement('input');
        input.type = 'file';
        input.onchange = (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.readAsText(file);
                reader.onload = () => {
                        const json = JSON.parse(reader.result);

			if (!Array.isArray(json)) {
				alert('Invalid JSON format');
				return;
			}

			for (v of json) {
                                if (!v.query) {
                                        alert('Invalid JSON format');
					return;
                                }

				if (!v.format) {
                                        v.format = '';
                                }
                        }

                        downloads = json;
                        update_download_display();
                }
	}
	input.click();
}

function download_script(e) {
	e.preventDefault();

	const exe = document.getElementById("exe")?.value || 'yt-dlp';
	const downloader_input = document.getElementById("downloader");
	const downloader = downloader_input.options[downloader_input.selectedIndex].value;

	let script = '#!/usr/bin/sh \n';

	for (video of downloads) {
		// TODO: write-subs as an option
		script += exe + ' --write-subs';

		if (video.format.length > 0) {
                        script += ' --format ' + video.format;
                }

		if (downloader.length > 0) {
			script += ' --downloader ' + downloader;
		}

                script += ' "' + video.query + '"\n';
        }

	const blob = new Blob([script], {
                type: "text/plain;charset=utf-8"
        });
	const url = URL.createObjectURL(blob);
	const link = document.createElement('a');
	link.href = url;
        link.download = 'video-download.sh';
        link.click();
}

function toggle_section(e) {
	const section = document.getElementById(e.target.dataset.toggle);
	section.hidden = !section.hidden;
}
