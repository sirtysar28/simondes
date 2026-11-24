<?php

namespace App\Support;

class DataMaster
{
    public const DISTRICTS = [
        'Jailolo', 'Jailolo Selatan', 'Sahu', 'Sahu Timur',
        'Ibu', 'Ibu Selatan', 'Loloda', 'Tabaru',
    ];

    public const SPENDING_TYPES = [
        'Belanja Pegawai', 'Barang dan Jasa', 'Belanja Modal',
        'Belanja Tak Terduga', 'Lainnya',
    ];

    public const FUNDING_SOURCES = [
        'Dana Desa (DD)', 'Alokasi Dana Desa (ADD)', 'Bagi Hasil Pajak/Retribusi',
        'Pendapatan Asli Desa', 'Bantuan Keuangan', 'Lainnya',
    ];

    public const CATEGORIES = [
        'Penyelenggaraan Pemerintahan', 'Pembangunan Desa',
        'Pembinaan Kemasyarakatan', 'Pemberdayaan Masyarakat',
        'Penanggulangan Bencana',
    ];
}
