<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'sarabun';
            src: url('{{ storage_path("fonts/Sarabun-Regular.ttf") }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'sarabun';
            src: url('{{ storage_path("fonts/Sarabun-Bold.ttf") }}') format('truetype');
            font-weight: bold;
        }
        body {
            font-family: 'sarabun', sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 30px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mb-8 { margin-bottom: 32px; }
        .mt-12 { margin-top: 48px; }
        .ml-4 { margin-left: 16px; }

        h1 { font-size: 22px; margin: 0; }
        h2 { font-size: 20px; margin: 20px 0; color: #2563eb; }
        p { margin: 4px 0; }

        .header-address { font-size: 12px; }

        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-section td { vertical-align: top; }
        .info-left { width: 60%; }
        .info-right { width: 40%; }

        .info-table {
            border-collapse: collapse;
            float: right;
        }
        .info-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            font-size: 13px;
        }

        .bill-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 16px;
        }
        .bill-table th, .bill-table td {
            border: 1px solid #000;
            padding: 7px 10px;
            font-size: 13px;
        }
        .bill-table th {
            background-color: #fff;
            font-weight: normal;
        }
        .bill-table .sub { color: #999; font-size: 11px; }

        .total-table {
            border-collapse: collapse;
            float: right;
        }
        .total-table td {
            border: 1px solid #000;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 14px;
        }

        .payment-method { font-size: 13px; }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-section td { vertical-align: bottom; font-size: 13px; }
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
    @php
        $receiptDate = $bill->receipt->receipt_date?->format('d/m/Y') ?? now()->format('d/m/Y');
        $receiptDateParts = [
            'day'   => $bill->receipt->receipt_date?->format('d') ?? '',
            'month' => $bill->receipt->receipt_date?->format('m') ?? '',
            'year'  => $bill->receipt->receipt_date?->format('Y') ?? '',
        ];
        $apartmentAddress = ($setting->address ?? '140/12,1/1') . ' ถ.' . ($setting->subdistrict ?? 'มิตรภาพ') . ' ต.' . ($setting->district ?? 'ในเมือง') . ' อ.เมือง จ.' . ($setting->province ?? 'ขอนแก่น') . ' ' . ($setting->postal_code ?? '40000');
        $paymentMethod = $bill->receipt->payment_method ?? 'เงินสด';
        $isCash = str_contains(strtolower($paymentMethod), 'cash') || str_contains($paymentMethod, 'เงินสด');
        $isTransfer = str_contains(strtolower($paymentMethod), 'transfer') || str_contains($paymentMethod, 'โอน');
        $isCheck = str_contains(strtolower($paymentMethod), 'check') || str_contains($paymentMethod, 'เช็ค');
    @endphp

    {{-- Header --}}
    <div class="text-center mb-2">
        <h1>{{ $setting->apartment_name ?? 'JJ Apartment' }}</h1>
        <p class="header-address">{{ $setting->address ?? '140/12,1/1' }} ถนน{{ $setting->subdistrict ?? 'มิตรภาพ' }} ตำบล{{ $setting->district ?? 'ในเมือง' }} อำเภอเมือง จังหวัด{{ $setting->province ?? 'ขอนแก่น' }} {{ $setting->postal_code ?? '40000' }}</p>
    </div>

    {{-- Title --}}
    <div class="text-center">
        <h2>ใบเสร็จรับเงิน/Receipt</h2>
    </div>

    {{-- Info Section --}}
    <table class="info-section">
        <tr>
            <td class="info-left">
                <p><span class="font-bold">ชื่อผู้เช่า</span> Name: <span class="font-bold">{{ $bill->room?->contract?->tenant_name ?? '-' }}</span></p>
                <p><span class="font-bold">ที่อยู่</span> Address: {{ $apartmentAddress }}</p>
            </td>
            <td class="info-right">
                <table class="info-table">
                    <tr>
                        <td class="font-bold">เลขที่ No.</td>
                        <td>{{ $bill->receipt->receipt_number }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">วันที่ Date</td>
                        <td>{{ $receiptDate }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">ห้อง Room</td>
                        <td class="font-bold">{{ $bill->room?->room_number ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Main Table --}}
    <table class="bill-table">
        <thead>
            <tr>
                <th class="text-center" style="width:50px;">ลำดับ<br><span class="sub">Item</span></th>
                <th class="text-left">รายการ<br><span class="sub">Description</span></th>
                <th class="text-center" style="width:80px;">จำนวนหน่วย<br><span class="sub">Qty</span></th>
                <th class="text-center" style="width:100px;">ราคาต่อหน่วย<br><span class="sub">Unit Price</span></th>
                <th class="text-right" style="width:100px;">จำนวนเงิน<br><span class="sub">Amount</span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>ค่าน้ำประปา</td>
                <td class="text-center">{{ $bill->water_units ?? 0 }}</td>
                <td class="text-center">{{ number_format($setting->water_rate ?? 18, 2) }}</td>
                <td class="text-right">{{ number_format($bill->water_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>ค่าไฟฟ้า</td>
                <td class="text-center">{{ $bill->electric_units ?? 0 }}</td>
                <td class="text-center">{{ number_format($setting->electric_rate ?? 8, 2) }}</td>
                <td class="text-right">{{ number_format($bill->electric_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>ค่าเช่าห้องพัก</td>
                <td class="text-center">1</td>
                <td class="text-center">{{ number_format($bill->room_rate ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($bill->room_rate ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>ค่าปรับ</td>
                <td class="text-center">1</td>
                <td class="text-center">{{ number_format($bill->other_fees ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($bill->other_fees ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Total --}}
    <div class="clearfix mb-6">
        <table class="total-table">
            <tr>
                <td>จำนวนเงินรวม</td>
                <td class="text-right">{{ number_format($bill->total_amount ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Payment Method --}}
    <div class="payment-method mb-8">
        <p class="font-bold">ชำระเงินโดย (Paid By)</p>
        <p class="ml-4">( {{ $isCash ? 'X' : '&nbsp;&nbsp;' }} ) Cash</p>
        <p class="ml-4">( {{ $isTransfer ? 'X' : '&nbsp;&nbsp;' }} ) Transfer Bank &nbsp;&nbsp; Date: {{ $isTransfer ? $receiptDate : '___________' }}</p>
        <p class="ml-4">( {{ $isCheck ? 'X' : '&nbsp;&nbsp;' }} ) Check Bank</p>
    </div>

    {{-- Signature --}}
    <table class="signature-section">
        <tr>
            <td style="width:50%;">
                <p>ผู้รับเงิน {{ $bill->receipt?->receiver?->username ?? '_______________' }}</p>
                <p style="color:#999; font-size:11px;">Collector</p>
            </td>
            <td style="width:50%; text-align:right;">
                <p>วันที่ {{ $receiptDateParts['day'] ?: '____' }}/{{ $receiptDateParts['month'] ?: '____' }}/{{ $receiptDateParts['year'] ?: '____' }}</p>
                <p style="color:#999; font-size:11px;">Date</p>
            </td>
        </tr>
    </table>
</body>
</html>
