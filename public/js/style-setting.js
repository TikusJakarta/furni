(function () {
            var preview = document.getElementById('themePreview');
            var rows = Array.prototype.slice.call(document.querySelectorAll('[data-theme-field]'));
            if (!preview || !rows.length) { return; }

            var HEX = /^#[0-9a-fA-F]{6}$/;
            var byKey = {};
            rows.forEach(function (row) { byKey[row.getAttribute('data-key')] = row; });

            function lum(hex) {
                var c = [1, 3, 5].map(function (i) {
                    var v = parseInt(hex.substr(i, 2), 16) / 255;
                    return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
                });
                return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
            }
            function ratio(a, b) {
                var x = lum(a), y = lum(b);
                return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05);
            }
            // warna yang berlaku: isi kolom kalau valid, kalau tidak warna bawaan
            function effective(row) {
                var v = row.querySelector('.theme-hex').value.trim();
                return HEX.test(v) ? v.toLowerCase() : row.getAttribute('data-default');
            }

            function update() {
                rows.forEach(function (row) {
                    var value = effective(row);
                    preview.style.setProperty(row.getAttribute('data-var'), value);
                    row.querySelector('.theme-picker').value = value;
                });

                rows.forEach(function (row) {
                    var hint = row.querySelector('.theme-hint');
                    var against = byKey[row.getAttribute('data-against')];
                    if (!against) { return; }

                    var r = ratio(effective(row), effective(against));
                    hint.hidden = r >= 4.5;
                    if (r < 4.5) {
                        hint.textContent = 'Kontras rendah (' + r.toFixed(1) + ':1) \u2014 teks sulit dibaca di latar ini.';
                    }
                });
            }

            rows.forEach(function (row) {
                var text = row.querySelector('.theme-hex');
                var picker = row.querySelector('.theme-picker');

                text.addEventListener('input', update);
                picker.addEventListener('input', function () { text.value = picker.value; update(); });
                row.querySelector('.theme-reset').addEventListener('click', function () { text.value = ''; update(); });
            });

            document.getElementById('themeResetAll').addEventListener('click', function () {
                rows.forEach(function (row) { row.querySelector('.theme-hex').value = ''; });
                update();
            });

            update();
        })();