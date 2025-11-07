<?php

namespace App\Imports;

use App\Models\SanPham;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SanPhamImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $doanh_nghiep_id;

    public function __construct($doanh_nghiep_id)
    {
        $this->doanh_nghiep_id = $doanh_nghiep_id;
    }
    public function model(array $row)
    {
        return new SanPham([
            'doanh_nghiep_id' => $this->doanh_nghiep_id,
            'ten_san_pham'    => $row['ten_san_pham'],
            'mo_ta'           => $row['mo_ta'],
            'hinh_anh'        => $row['hinh_anh'],
            'gia'             => $row['gia'],
            'so_luong'        => $row['so_luong'],
            'trang_thai'      => $row['trang_thai'],
            'loai_id'         => $row['loai_id'],
        ]);
    }
}
