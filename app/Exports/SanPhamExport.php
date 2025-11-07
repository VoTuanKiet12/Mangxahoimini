<?php

namespace App\Exports;

use App\Models\SanPham;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SanPhamExport implements FromCollection, WithHeadings, WithMapping
{
    // 🧱 Tiêu đề cột trong Excel
    public function headings(): array
    {
        return [
            'ten_san_pham',
            'mo_ta',
            'hinh_anh',
            'gia',
            'so_luong',
            'trang_thai',
            'loai_id',
        ];
    }

    // 🧱 Dữ liệu mỗi dòng
    public function map($sp): array
    {
        return [
            $sp->ten_san_pham,
            $sp->mo_ta,
            $sp->hinh_anh,
            $sp->gia,
            $sp->so_luong,
            $sp->trang_thai,
            $sp->loai_id,
        ];
    }

    // 🧱 Lấy danh sách sản phẩm của doanh nghiệp hiện tại
    public function collection()
    {
        // Lấy ID doanh nghiệp từ user đang đăng nhập
        $doanhNghiep = \App\Models\DoanhNghiep::where('user_id', Auth::id())->first();

        // Nếu không có doanh nghiệp, trả về rỗng tránh lỗi
        if (!$doanhNghiep) {
            return collect([]);
        }

        return SanPham::where('doanh_nghiep_id', $doanhNghiep->id)->get();
    }
}
