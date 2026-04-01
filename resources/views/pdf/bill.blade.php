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
        * { box-sizing: border-box; }
        body {
            font-family: 'sarabun', sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px 25px;
        }
        h1 { font-size: 20px; margin: 0 0 4px 0; }
        h2 { font-size: 18px; margin: 8px 0; color: #2563eb; }
        p  { margin: 3px 0; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .font-bold   { font-weight: bold; }

        .header-address { font-size: 11px; }

        /* Info row: tenant left, bill-no right */
        .info-section { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .info-section td { vertical-align: top; }

        .info-table { border-collapse: collapse; width: 100%; }
        .info-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Main bill table */
        .bill-table { border-collapse: collapse; width: 100%; margin-bottom: 8px; }
        .bill-table th, .bill-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 12px;
        }
        .bill-table th { background-color: #f5f5f5; font-weight: bold; }
        .bill-table .sub { color: #777; font-size: 10px; display: block; }

        /* Total row table */
        .total-wrap { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .total-wrap td { vertical-align: top; }
        .total-table { border-collapse: collapse; width: 220px; }
        .total-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 13px;
        }

        /* Status badge */
        .status-paid    { color: #16a34a; font-weight: bold; font-size: 13px; }
        .status-pending { color: #dc2626; font-weight: bold; font-size: 13px; }
        .status-overdue { color: #ea580c; font-weight: bold; font-size: 13px; }

        .notes { font-size: 11px; margin-bottom: 10px; }
        .notes p { margin: 2px 0; }
        .ml-4 { margin-left: 14px; }
        .ml-8 { margin-left: 28px; }

        .signature { margin-top: 20px; text-align: right; font-size: 13px; }
    </style>
</head>
<body>
    @php
        $billNo  = $bill ? 'INV-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) : '-';
        $billDate = $bill ? \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y') : now()->format('d/m/Y');
        $apartmentAddress = ($setting->address ?? '140/12,1/1')
            . ' ถ.' . ($setting->subdistrict ?? 'มิตรภาพ')
            . ' ต.' . ($setting->district ?? 'ในเมือง')
            . ' อ.เมือง จ.' . ($setting->province ?? 'ขอนแก่น')
            . ' ' . ($setting->postal_code ?? '40000');
        $statusMap = ['paid' => 'ชำระแล้ว', 'pending' => 'รอชำระ', 'overdue' => 'เกินกำหนด', 'reviewing' => 'รอตรวจสอบ'];
        $statusLabel = $statusMap[$bill->status ?? ''] ?? ($bill->status ?? '-');
        $statusClass = match($bill->status ?? '') {
            'paid'      => 'status-paid',
            'pending', 'reviewing' => 'status-pending',
            'overdue'   => 'status-overdue',
            default     => '',
        };
    @endphp

    {{-- Header --}}
    <div class="text-center">
        <h1>{{ $setting->apartment_name ?? 'JJ Apartment' }}</h1>
        <p class="header-address">{{ $apartmentAddress }}</p>
        <h2>ใบแจ้งหนี้</h2>
    </div>

    {{-- Info Section --}}
    <table class="info-section">
        <tr>
            <td style="width:58%;">
                <p><span class="font-bold">ชื่อผู้เช่า / Name:</span> {{ $room->contract->tenant_name ?? '-' }}</p>
                <p><span class="font-bold">ที่อยู่ / Address:</span> {{ $apartmentAddress }}</p>
                <p style="margin-top:6px;">
                    <span class="font-bold">สถานะบิล / Status: </span>
                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                </p>
            </td>
            <td style="width:42%;">
                <table class="info-table">
                    <tr>
                        <td class="font-bold" style="width:45%;">เลขที่ No.</td>
                        <td>{{ $billNo }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">วันที่ Date</td>
                        <td>{{ $billDate }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">เดือน Month</td>
                        <td>{{ $bill->billing_month ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">ห้อง Room</td>
                        <td class="font-bold">{{ $room->room_number }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Main Table --}}
    <table class="bill-table">
        <thead>
            <tr>
                <th class="text-center" style="width:40px;">ลำดับ<span class="sub">Item</span></th>
                <th class="text-left">รายการ<span class="sub">Description</span></th>
                <th class="text-center" style="width:75px;">จำนวนหน่วย<span class="sub">Qty</span></th>
                <th class="text-center" style="width:95px;">ราคาต่อหน่วย<span class="sub">Unit Price</span></th>
                <th class="text-right" style="width:95px;">จำนวนเงิน<span class="sub">Amount</span></th>
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
    <table class="total-wrap">
        <tr>
            <td></td>
            <td style="text-align:right;">
                <table class="total-table">
                    <tr>
                        <td>จำนวนเงินรวม / Total</td>
                        <td class="text-right">{{ number_format($bill->total_amount ?? 0, 2) }} บาท</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Notes --}}
    <div class="notes">
        <p class="font-bold">หมายเหตุ</p>
        <p class="ml-4">1. กรุณาโอนเงินเข้าบัญชี "{{ $setting->apartment_name ?? 'JJ Apartment' }}" และนำสลิปโอนเงินแจ้งที่สำนักงาน</p>
        <p class="ml-8">- ธนาคารกสิกรไทย สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
        <p class="ml-8">- ธนาคารไทยพาณิชย์ สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
        <p class="ml-8">- ธนาคารกรุงเทพ สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
        <p class="ml-4">2. ค่าเช่าชำระไม่เกินวันที่ {{ $setting->payment_due_day ?? 5 }} ของเดือน เกินกำหนดปรับวันละ {{ number_format($setting->late_fee_per_day ?? 100) }} บาท</p>
    </div>

    {{-- Signature --}}
    <div class="signature">
        <p>ลงชื่อ _______________________</p>
        <p style="font-size:11px; color:#999;">ผู้รับเงิน / Authorized Signature</p>
    </div>
</body>
</html>
