<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DoanhNghiep;
use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Imports\SanPhamImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SanPhamExport;
use App\Exports\KhuyenMai;

class SanPhamController extends Controller
{
    /**
     * 🟢 Hiển thị form đăng sản phẩm cho doanh nghiệp đã được duyệt
     */
    public function create()
    {
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())
            ->where('trang_thai', 'hoat_dong')
            ->first();

        if (!$doanhNghiep) {
            return redirect()->route('trangchu')
                ->with('error', 'Tài khoản doanh nghiệp của bạn chưa được duyệt.');
        }

        $loaiSanPham = LoaiSanPham::all();

        return view('doanhnghiep.dangsanpham', compact('doanhNghiep', 'loaiSanPham'));
    }

    /**
     * 🟡 Lưu sản phẩm mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $request->validate([
            'loai_id' => 'required|integer|exists:loai_san_pham,id',
            'ten_san_pham' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
        ]);

        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())
            ->where('trang_thai', 'hoat_dong')
            ->first();

        if (!$doanhNghiep) {
            return redirect()->route('trangchu')->with('error', 'Bạn không phải là doanh nghiệp hợp lệ.');
        }

        $paths = [];
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $paths[] = $file->store('sanpham', 'public');
            }
        }

        SanPham::create([
            'doanh_nghiep_id' => $doanhNghiep->id,
            'ten_san_pham' => $request->ten_san_pham,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => json_encode($paths),
            'gia' => $request->gia,
            'so_luong' => $request->so_luong,
            'trang_thai' => 'con_hang',
            'loai_id' => $request->loai_id,
        ]);

        return redirect()->route('doanhnghiep.sanpham.index')
            ->with('success', 'Đăng sản phẩm thành công!');
    }

    /**
     * 🔵 Hiển thị danh sách toàn bộ sản phẩm (trang chung)
     */
    public function index(Request $request)
    {
        // 🟢 Lấy tất cả loại sản phẩm để hiển thị hàng ngang
        $loaiSanPhams = LoaiSanPham::all();

        // 🟢 Truy vấn danh sách sản phẩm
        $query = SanPham::with(['doanhNghiep', 'loaiSanPham'])
            ->where('trang_thai', 'con_hang');

        // Nếu có lọc theo loại sản phẩm (ví dụ ?loai=2)
        if ($request->has('loai')) {
            $query->where('loai_id', $request->loai);
        }
        if ($request->filled('keyword')) {
            $query->where('ten_san_pham', 'like', '%' . $request->keyword . '%');
        }
        $sanPhams = $query->orderBy('id', 'desc')->get();

        // 🟢 Trả dữ liệu ra view
        return view('sanpham.danhsach', compact('sanPhams', 'loaiSanPhams'));
    }


    /**
     * 🟠 Quản lý sản phẩm của doanh nghiệp (chỉ doanh nghiệp đã được duyệt)
     */
    public function indexQuanLy()
    {
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())
            ->where('trang_thai', 'hoat_dong')
            ->first();

        if (!$doanhNghiep) {
            return redirect()->route('trangchu')->with('error', 'Tài khoản doanh nghiệp của bạn chưa được duyệt.');
        }

        $sanPhams = SanPham::where('doanh_nghiep_id', $doanhNghiep->id)
            ->with('loaiSanPham')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('doanhnghiep.sanpham.index', compact('sanPhams', 'doanhNghiep'));
    }

    /**
     * 🟤 Hiển thị form sửa sản phẩm
     */
    public function edit($id)
    {
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())->firstOrFail();

        $sanPham = SanPham::where('id', $id)
            ->where('doanh_nghiep_id', $doanhNghiep->id)
            ->firstOrFail();

        $loaiSanPham = LoaiSanPham::all();

        return view('doanhnghiep.sanpham.edit', compact('sanPham', 'loaiSanPham'));
    }

    /**
     * 🔴 Xử lý cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'loai_id' => 'required|integer|exists:loai_san_pham,id',
            'ten_san_pham' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
            'trang_thai' => 'required|in:con_hang,het_hang,an',
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Lấy doanh nghiệp của người đăng nhập
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())
            ->where('trang_thai', 'hoat_dong')
            ->firstOrFail();

        // Tìm sản phẩm thuộc doanh nghiệp đó
        $sanPham = SanPham::where('id', $id)
            ->where('doanh_nghiep_id', $doanhNghiep->id)
            ->firstOrFail();

        // Cập nhật thông tin cơ bản
        $sanPham->fill([
            'ten_san_pham' => $request->ten_san_pham,
            'mo_ta' => $request->mo_ta,
            'gia' => $request->gia,
            'so_luong' => $request->so_luong,
            'trang_thai' => $request->trang_thai,
            'loai_id' => $request->loai_id,
        ]);

        // Nếu có ảnh mới → xóa ảnh cũ và cập nhật lại
        if ($request->hasFile('hinh_anh')) {
            // Xóa ảnh cũ (nếu có)
            if (!empty($sanPham->hinh_anh)) {
                $oldImages = json_decode($sanPham->hinh_anh, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $old) {
                        Storage::disk('public')->delete($old);
                    }
                }
            }

            // Lưu ảnh mới
            $paths = [];
            foreach ($request->file('hinh_anh') as $file) {
                $paths[] = $file->store('sanpham', 'public');
            }

            $sanPham->hinh_anh = json_encode($paths);
        }
        $sanPham->save();

        return redirect()
            ->route('doanhnghiep.sanpham.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }
    /**
     * ⚫ Xóa sản phẩm
     */
    public function destroy($id)
    {
        $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())->firstOrFail();

        $sanPham = SanPham::where('id', $id)
            ->where('doanh_nghiep_id', $doanhNghiep->id)
            ->firstOrFail();

        // Xóa ảnh trong storage
        if ($sanPham->hinh_anh) {
            $oldImages = json_decode($sanPham->hinh_anh, true);
            foreach ($oldImages as $old) {
                Storage::disk('public')->delete($old);
            }
        }

        $sanPham->delete();

        return redirect()->route('doanhnghiep.sanpham.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }
    /**
     * 🟢 Hiển thị chi tiết sản phẩm
     */
    public function show($id)
    {
        $sanPham = SanPham::with(['doanhNghiep', 'loaiSanPham', 'danhGia.user'])->findOrFail($id);


        // 🟢 Lấy sản phẩm khác cùng doanh nghiệp **và cùng loại**
        $goiY = SanPham::where('doanh_nghiep_id', $sanPham->doanh_nghiep_id)
            ->where('loai_id', $sanPham->loai_id)
            ->where('id', '!=', $sanPham->id)
            ->where('trang_thai', 'con_hang')
            ->orderByDesc('gia')
            ->take(4)
            ->get();

        // 🔄 Nếu chưa đủ 4 sản phẩm, lấy thêm cùng loại từ doanh nghiệp khác
        if ($goiY->count() < 4) {
            $thieu = 4 - $goiY->count();

            $boSung = SanPham::where('loai_id', $sanPham->loai_id)
                ->where('id', '!=', $sanPham->id)
                ->where('doanh_nghiep_id', '!=', $sanPham->doanh_nghiep_id)
                ->where('trang_thai', 'con_hang')
                ->inRandomOrder()
                ->take($thieu)
                ->get();

            // Gộp danh sách lại
            $goiY = $goiY->merge($boSung);
        }

        return view('sanpham.chitiet', compact('sanPham', 'goiY'));
    }


    public function postNhap(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {

            $doanhNghiep = DoanhNghiep::where('user_id', Auth::id())->first();

            if (!$doanhNghiep) {
                return back()->with('error', 'Tài khoản của bạn chưa có doanh nghiệp nào được duyệt!');
            }

            Excel::import(new SanPhamImport($doanhNghiep->id), $request->file('file_excel'));

            return redirect()->route('doanhnghiep.sanpham.index')
                ->with('success', 'Nhập dữ liệu thành công!');
        } catch (\Exception $e) {
            return redirect()->route('doanhnghiep.sanpham.index')
                ->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }


    public function getXuat()
    {
        return Excel::download(new SanPhamExport, 'danh-sach-san-pham.xlsx');
    }
    public function xemDanhGia($id)
    {
        $sanPham = SanPham::with('danhGia.user')->findOrFail($id);
        return view('doanhnghiep.sanpham.danhgia', compact('sanPham'));
    }
}
