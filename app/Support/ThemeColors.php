<?php

namespace App\Support;

/**
 * Daftar warna tampilan website (navbar, hero, footer) yang bisa diatur admin.
 * Satu sumber data: dipakai halaman Pengaturan (form), controller (validasi),
 * dan layout (menghasilkan variabel CSS).
 *
 * Simpan di: app/Support/ThemeColors.php
 */
class ThemeColors
{
    private const HEX = '/^#[0-9a-fA-F]{6}$/';

    /**
     * key pengaturan => label, kelompok, nama variabel CSS, warna bawaan template.
     * 'against' = warna latar yang dipakai untuk memeriksa kontras teks.
     * Kalau mau menambah warna baru: tambah satu baris di sini, lalu pakai variabelnya
     * di public/css/site-theme.css.
     */
    public static function fields(): array
    {
        return [
            'theme_navbar_bg'     => ['group' => 'Navbar & Hero', 'label' => 'Latar navbar',               'var' => '--site-navbar-bg',     'default' => '#3b5d50'],
            'theme_navbar_link'   => ['group' => 'Navbar & Hero', 'label' => 'Logo & menu navbar',         'var' => '--site-navbar-link',   'default' => '#ffffff', 'against' => 'theme_navbar_bg'],
            'theme_navbar_accent' => ['group' => 'Navbar & Hero', 'label' => 'Garis menu aktif',           'var' => '--site-navbar-accent', 'default' => '#f9bf29'],
            'theme_hero_bg'       => ['group' => 'Navbar & Hero', 'label' => 'Latar hero (judul halaman)', 'var' => '--site-hero-bg',       'default' => '#3b5d50'],

            'theme_footer_bg'     => ['group' => 'Footer', 'label' => 'Latar footer',       'var' => '--site-footer-bg',     'default' => '#ffffff'],
            'theme_footer_text'   => ['group' => 'Footer', 'label' => 'Teks footer',        'var' => '--site-footer-text',   'default' => '#6a6a6a', 'against' => 'theme_footer_bg'],
            'theme_footer_link'   => ['group' => 'Footer', 'label' => 'Tautan footer',      'var' => '--site-footer-link',   'default' => '#2f2f2f', 'against' => 'theme_footer_bg'],
            'theme_footer_accent' => ['group' => 'Footer', 'label' => 'Logo & ikon sosial', 'var' => '--site-footer-accent', 'default' => '#3b5d50', 'against' => 'theme_footer_bg'],
        ];
    }

    /** Dikelompokkan untuk tampilan form: ['Navbar & Hero' => [...], 'Footer' => [...]] */
    public static function groups(): array
    {
        $groups = [];

        foreach (self::fields() as $key => $field) {
            $groups[$field['group']][$key] = $field;
        }

        return $groups;
    }

    /** Aturan validasi: kosong (= warna bawaan) atau kode warna 6 digit seperti #3b5d50 */
    public static function rules(): array
    {
        $rules = [];

        foreach (array_keys(self::fields()) as $key) {
            $rules[$key] = ['nullable', 'regex:' . self::HEX];
        }

        return $rules;
    }

    /** Nama kolom untuk pesan error validasi */
    public static function attributes(): array
    {
        $names = [];

        foreach (self::fields() as $key => $field) {
            $names[$key] = $field['label'];
        }

        return $names;
    }

    /**
     * Membuat blok variabel CSS dari pengaturan yang tersimpan, misalnya
     * ":root{--site-navbar-bg:#112233;}". Hanya warna yang valid dan berbeda dari
     * bawaan yang ditulis; kalau tidak ada yang diubah hasilnya string kosong.
     * Nilai divalidasi ulang di sini, jadi aman dipasang langsung di <style>.
     */
    public static function css(array $settings): string
    {
        $fields = self::fields();
        $vars = [];

        foreach ($fields as $key => $field) {
            $value = $settings[$key] ?? null;

            if (! is_string($value) || ! preg_match(self::HEX, $value)) {
                continue;
            }

            $value = strtolower($value);

            if ($value !== $field['default']) {
                $vars[$field['var']] = $value;
            }
        }

        if ($vars === []) {
            return '';
        }

        // Ikon user & keranjang di navbar berupa gambar putih: dibalik jadi gelap bila latar navbar terang
        if (isset($vars['--site-navbar-bg']) && self::isLight($vars['--site-navbar-bg'])) {
            $vars['--site-navbar-icon-filter'] = 'invert(1)';
        }

        // Warna turunan footer
        if (isset($vars['--site-footer-accent'])) {
            $vars['--site-footer-accent-soft'] = self::mixWithWhite($vars['--site-footer-accent'], 0.84);
        }

        if (isset($vars['--site-footer-link'])) {
            [$r, $g, $b] = self::rgb($vars['--site-footer-link']);
            $vars['--site-footer-link-hover'] = "rgba($r, $g, $b, 0.5)";
        }

        $css = '';

        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }

        return ':root{' . $css . '}';
    }

    // ------------------------------------------------------------------

    private static function rgb(string $hex): array
    {
        return [hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2))];
    }

    private static function isLight(string $hex): bool
    {
        [$r, $g, $b] = self::rgb($hex);

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255 > 0.6;
    }

    private static function mixWithWhite(string $hex, float $whiteRatio): string
    {
        $mixed = array_map(function ($channel) use ($whiteRatio) {
            return (int) round($channel * (1 - $whiteRatio) + 255 * $whiteRatio);
        }, self::rgb($hex));

        return sprintf('#%02x%02x%02x', ...$mixed);
    }
}
