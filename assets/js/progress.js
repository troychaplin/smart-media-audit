(function () {
	var cfg = window.wpMediaAudit || {};
	var pollTimer = null;

	function updateBar(data) {
		var wrap  = document.getElementById('media-audit-progress-wrap');
		var bar   = document.getElementById('media-audit-progress-bar');
		var label = document.getElementById('media-audit-progress-label');

		if (!wrap) return;

		if (data.status === 'scanning') {
			wrap.removeAttribute('hidden');
			var pct = data.total > 0 ? Math.round((data.progress / data.total) * 100) : 0;
			bar.style.width = pct + '%';
			label.textContent = 'Scanning… ' + data.progress + ' / ' + data.total + ' posts';
		} else {
			wrap.setAttribute('hidden', '');
			stopPolling();
			if (data.status === 'complete') {
				window.location.reload();
			}
		}
	}

	function poll() {
		fetch(cfg.ajaxUrl + '?action=media_audit_progress&nonce=' + cfg.nonce)
			.then(function (r) { return r.json(); })
			.then(function (json) {
				if (json.success) updateBar(json.data);
			})
			.catch(function () {});
	}

	function startPolling() {
		if (pollTimer) return;
		pollTimer = setInterval(poll, 2000);
	}

	function stopPolling() {
		clearInterval(pollTimer);
		pollTimer = null;
	}

	// If a scan is already in progress on page load, start polling immediately.
	(function initPoll() {
		var wrap = document.getElementById('media-audit-progress-wrap');
		if (wrap && !wrap.hasAttribute('hidden')) {
			startPolling();
		}
	}());

	// Scan Now button.
	document.addEventListener('DOMContentLoaded', function () {
		var btn = document.getElementById('media-audit-scan-btn');
		if (!btn) return;

		btn.addEventListener('click', function (e) {
			e.preventDefault();
			btn.disabled = true;

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'action=media_audit_scan&nonce=' + cfg.nonce,
			})
				.then(function (r) { return r.json(); })
				.then(function (json) {
					if (json.success) {
						var wrap = document.getElementById('media-audit-progress-wrap');
						if (wrap) wrap.removeAttribute('hidden');
						startPolling();
					} else {
						// Re-enable so the user can retry (e.g. expired nonce).
						btn.disabled = false;
					}
				})
				.catch(function () {
					btn.disabled = false;
				});
		});

		// Location toggle — inline expand via AJAX.
		document.addEventListener('click', function (e) {
			var toggle = e.target.closest('.media-audit-locations-toggle');
			if (!toggle) return;

			var id       = toggle.dataset.id;
			var expanded = toggle.getAttribute('aria-expanded') === 'true';
			var container = document.getElementById('media-audit-loc-' + id);

			if (!container) return;

			if (expanded) {
				container.hidden = true;
				toggle.setAttribute('aria-expanded', 'false');
				return;
			}

			if (container.dataset.loaded) {
				container.hidden = false;
				toggle.setAttribute('aria-expanded', 'true');
				return;
			}

			fetch(cfg.ajaxUrl + '?action=media_audit_locations&nonce=' + cfg.nonce + '&attachment_id=' + encodeURIComponent(id))
				.then(function (r) { return r.json(); })
				.then(function (json) {
					if (!json.success) return;

					// Build nodes with textContent — never innerHTML with server
					// strings — so a post title containing markup cannot inject script.
					var ul = document.createElement('ul');
					ul.className = 'media-audit-location-list';

					json.data.forEach(function (loc) {
						var li = document.createElement('li');

						if (loc.edit_url) {
							var a = document.createElement('a');
							a.href = loc.edit_url;
							a.textContent = loc.post_title || '(no title)';
							li.appendChild(a);
						} else {
							li.appendChild(document.createTextNode(loc.post_title || '(no title)'));
						}

						var span = document.createElement('span');
						span.className = 'media-audit-ref-type';
						span.textContent = ' (' + loc.reference_type + ')';
						li.appendChild(document.createTextNode(' '));
						li.appendChild(span);

						ul.appendChild(li);
					});

					container.textContent = '';
					container.appendChild(ul);
					container.dataset.loaded = '1';
					container.hidden = false;
					toggle.setAttribute('aria-expanded', 'true');
				});
		});
	});
}());
