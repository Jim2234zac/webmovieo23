document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => navMenu.classList.toggle('open'));
    }

    // Banner close (top/left/right/bottom) — transient only (no persistence)
    document.querySelectorAll('.banner-close').forEach(function (btn) {
        const side = btn.dataset.close;
        const banner = document.getElementById('banner-' + side) || document.querySelector('.banner-' + side) || document.getElementById('banner-' + side);
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (banner) {
                banner.classList.add('hidden');
            }
        });
    });

    // External links new tab
    document.querySelectorAll('.banner-side a, .banner-top a, .banner-bottom a').forEach(function (link) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
    });

    // Video server & quality switch
    const sourcesEl = document.getElementById('video-sources-data');
    const videoFrame = document.getElementById('video-frame');
    if (sourcesEl && videoFrame) {
        const sources = JSON.parse(sourcesEl.textContent);
        let activeServer = document.querySelector('.server-btn.active')?.dataset.server || '';
        let activeQuality = document.querySelector('.quality-btn.active')?.dataset.quality || '720p';

        function updateVideo() {
            const match = sources.find(s => s.server_name === activeServer && s.quality === activeQuality)
                || sources.find(s => s.server_name === activeServer)
                || sources.find(s => s.quality === activeQuality)
                || sources[0];
            if (match) videoFrame.src = match.embed_url;
        }

        document.querySelectorAll('.server-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.server-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeServer = btn.dataset.server;
                updateVideo();
            });
        });

        document.querySelectorAll('.quality-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.quality-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeQuality = btn.dataset.quality;
                updateVideo();
            });
        });
    }

    // Search realtime suggest
    const searchInput = document.getElementById('search-input');
    const suggestBox = document.getElementById('search-suggest');
    if (searchInput && suggestBox) {
        let debounceTimer;
        const formAction = searchInput.closest('form')?.getAttribute('action') || '';
        const baseUrl = formAction.replace(/\/search\.php\/?.*$/, '');

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const q = this.value.trim();
            if (q.length < 1) {
                suggestBox.classList.remove('open');
                suggestBox.innerHTML = '';
                return;
            }
            debounceTimer = setTimeout(function () {
                fetch(baseUrl + '/search.php?ajax=1&q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(function (items) {
                        if (!items.length) {
                            suggestBox.classList.remove('open');
                            return;
                        }
                        suggestBox.innerHTML = items.map(function (item) {
                            return '<a href="' + item.url + '">' +
                                '<img src="' + (item.thumb || '') + '" alt="">' +
                                '<div><div class="s-title">' + item.title + '</div>' +
                                '<div class="s-ep">' + (item.episodes ? 'ตอนที่ ' + item.episodes : '') + '</div></div></a>';
                        }).join('');
                        suggestBox.classList.add('open');
                    })
                    .catch(function () { suggestBox.classList.remove('open'); });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
                suggestBox.classList.remove('open');
            }
        });
    }
});
