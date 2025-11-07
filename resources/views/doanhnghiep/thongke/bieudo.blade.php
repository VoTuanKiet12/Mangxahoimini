@extends('doanhnghiep.quanly')

@section('quanly')
<style>
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 30px;
        padding: 20px;
        font-family: "Poppins", Arial, sans-serif;
        background: #f7f9fc;
        min-height: 85vh;
        box-sizing: border-box;
        border-radius: 5px;
    }

    .chart-box {
        flex: 1 1 40%;
        background: #ffffff;
        padding: 30px;
        height: 300px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.34);
        min-width: 350px;
        transition: all 0.3s ease-in-out;

    }

    .chart-box.duoi {

        height: 340px;
    }

    .chart-box:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .chart-box h2,
    .chart-box h3 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    canvas {
        max-width: 100%;
        height: 250px !important;
    }

    @media (max-width: 900px) {
        .chart-container {
            flex-direction: column;
            align-items: center;
        }

        .chart-box {
            width: 90%;
        }
    }
</style>

<div class="chart-container">
    <div class="chart-box" data-aos="zoom-in">
        <h2>Biểu đồ doanh thu theo ngày</h2>
        <canvas id="doanhThuChart"></canvas>
    </div>

    <div class="chart-box" data-aos="fade-down-left">
        <h3>Biểu đồ số đơn hàng theo ngày</h3>
        <canvas id="donHangChart"></canvas>
    </div>

    <div class="chart-box duoi" data-aos="fade-down-left">
        <h3>Biểu đồ doanh thu theo tháng</h3>
        <canvas style="width: 100%; height: 400px;" id="doanhThuThangChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ===== DỮ LIỆU TỪ CONTROLLER =====
    const ngay = @json($ngay);
    const doanhThu = @json($doanhThu);
    const soDonHang = @json($soDonHang);

    const thang = @json($thang ?? []); // danh sách tháng, ví dụ: ["01", "02", "03"]
    const doanhThuThang = @json($doanhThuThang ?? []); // dữ liệu doanh thu theo tháng

    // ===== CHART 1: DOANH THU THEO NGÀY =====
    const ctx1 = document.getElementById('doanhThuChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ngay,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: doanhThu,
                backgroundColor: 'rgba(214, 252, 78, 0.8)',
                borderColor: '#415f75',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: getChartOptions('₫', true)
    });

    // ===== CHART 2: SỐ ĐƠN HÀNG THEO NGÀY =====
    const ctx2 = document.getElementById('donHangChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ngay,
            datasets: [{
                label: 'Số đơn hàng',
                data: soDonHang,
                borderColor: '#9be43b',
                backgroundColor: 'rgba(155, 228, 59, 0.3)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#9be43b',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: getChartOptions('', false)
    });

    // ===== CHART 3: DOANH THU THEO THÁNG =====
    const ctx3 = document.getElementById('doanhThuThangChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line', // có thể đổi sang 'line' nếu muốn
        data: {
            labels: thang,
            datasets: [{
                label: 'Doanh thu theo tháng (VNĐ)',
                data: doanhThuThang,
                backgroundColor: 'rgba(251, 255, 38, 0.37)',
                borderColor: '#eec16dff',
                pointBackgroundColor: '#e4953bff',
                borderWidth: 2,
                fill: true,
                borderRadius: 6
            }]
        },
        options: getChartOptions('₫', true)
    });

    // ====== HÀM CHUNG ======
    function getChartOptions(suffix = '', isCurrency = false) {
        return {
            responsive: true,
            scales: {
                x: {
                    ticks: {
                        color: '#000'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#000',
                        callback: value =>
                            isCurrency ? value.toLocaleString('vi-VN') + suffix : value
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#000'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255,255,255,0.95)',
                    titleColor: '#2c3e50',
                    bodyColor: '#2c3e50',
                    borderColor: '#415f75',
                    borderWidth: 1,
                    callbacks: {
                        label: context => isCurrency ?
                            context.parsed.y.toLocaleString('vi-VN') + ' ₫' : context.parsed.y
                    }
                }
            }
        };
    }
</script>

@endsection