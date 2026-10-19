(function () {
    var $ = function (id) { return document.getElementById(id); };
    var rupiah = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };

    function update() {
        var type = $('type').value;
        var value = parseFloat($('value').value);
        var min = parseFloat($('min_spend').value) || 0;
        var exp = $('expires_at').value;

        $('pvCode').textContent = $('code').value.trim().toUpperCase() || 'KODEKAMU';
        $('pvUnit').textContent = type === 'percentage' ? 'DISKON' : 'POTONGAN';
        $('pvValue').textContent = isNaN(value) ? '\u2014' : (type === 'percentage' ? value + '%' : rupiah(value));
        $('pvMin').textContent = min > 0 ? 'Min. belanja ' + rupiah(min) : 'Tanpa minimal belanja';

        $('pvExpiry').textContent = exp
            ? 'Berlaku sampai ' + new Date(exp).toLocaleString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
            })
            : 'Berlaku selamanya';
    }

    ['code', 'type', 'value', 'min_spend', 'expires_at'].forEach(function (id) {
        $(id).addEventListener('input', update);
        $(id).addEventListener('change', update);
    });
    update();
})();