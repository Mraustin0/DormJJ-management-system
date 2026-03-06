<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่า ห้อง {{ $room->room_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .contract-document {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            transform-origin: top center;
            transition: transform 0.2s ease;
        }
        .contract-document p {
            text-indent: 3em;
            margin-bottom: 0.4em;
        }
        .contract-document .no-indent {
            text-indent: 0;
        }
        @media print {
            .no-print { display: none !important; }
            #mainContent { margin-left: 0 !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'rooms'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'สัญญาเช่า ห้อง ' . $room->room_number])

        <main class="p-6">

            {{-- Back + Actions --}}
            <div class="flex items-center justify-between mb-6 no-print">
                <a href="{{ route('rooms.index') }}"
                   class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex gap-2">
                    <button onclick="window.print()"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        พิมพ์สัญญา
                    </button>
                    <a href="{{ route('rooms.edit', $room->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#4A90E2] hover:bg-[#357abd] text-white font-semibold rounded-lg transition-colors text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        แก้ไขสัญญา
                    </a>
                </div>
            </div>

            {{-- Document Viewer --}}
            <div class="relative max-w-4xl mx-auto">

                {{-- Zoom Controls --}}
                <div class="fixed right-8 top-1/3 z-20 flex flex-col gap-1 no-print">
                    <button onclick="zoomIn()" class="w-10 h-10 bg-white border border-gray-300 rounded-t-lg flex items-center justify-center hover:bg-gray-50 shadow-sm text-xl font-bold text-gray-600">+</button>
                    <button onclick="zoomOut()" class="w-10 h-10 bg-white border border-gray-300 rounded-b-lg flex items-center justify-center hover:bg-gray-50 shadow-sm text-xl font-bold text-gray-600">-</button>
                </div>

                {{-- Contract Document --}}
                <div class="overflow-x-auto pb-8">
                    <div id="contractDoc" class="contract-document max-w-3xl mx-auto rounded-lg p-10 md:p-14 text-sm leading-relaxed text-gray-800" style="min-width: 600px;">

                        @php
                            $aptName        = $setting->apartment_name ?? 'เจเจพาร์ทเมนต์';
                            $aptAddress     = $setting->address ?? '';
                            $aptSubdistrict = $setting->subdistrict ?? '';
                            $aptDistrict    = $setting->district ?? '';
                            $aptProvince    = $setting->province ?? '';
                            $aptPostal      = $setting->postal_code ?? '';
                            $rentRate       = number_format($room->price ?? $setting->rent_per_month ?? 2500, 0);
                            $electricRate   = number_format($setting->electric_rate ?? 8, 0);
                            $waterRate      = number_format($setting->water_rate ?? 25, 0);
                            $deposit        = number_format($contract->deposit ?? 0, 0);
                            $duration       = $contract->contract_duration ?? 12;
                            $startDate      = $contract->check_in_date ? \Carbon\Carbon::parse($contract->check_in_date) : null;
                            $endDate        = $contract->end_date ? \Carbon\Carbon::parse($contract->end_date) : null;
                            $contractDate   = $contract->contract_date
                                                ? \Carbon\Carbon::parse($contract->contract_date)
                                                : ($startDate ?? now());
                            $tenantName     = $contract->tenant_name ?? '............';
                            $nidRaw         = $contract->nid ?? '';
                            $tenantNid      = $nidRaw
                                ? (substr($nidRaw,0,1).'-'.substr($nidRaw,1,4).'-'.substr($nidRaw,5,5).'-'.substr($nidRaw,10,2).'-'.substr($nidRaw,12))
                                : '............';
                            $tenantPhone    = $contract->phone ?? '............';
                            $floor          = $room->floor ?? substr($room->room_number, 0, 1);
                        @endphp

                        {{-- Header --}}
                        <h2 class="text-center text-xl font-bold mb-6">สัญญาเช่าห้อง {{ $aptName }}</h2>

                        <div class="text-right mb-1 text-sm">
                            {{ $aptAddress }}{{ $aptSubdistrict ? ' ตำบล'.$aptSubdistrict : '' }}
                        </div>
                        <div class="text-right mb-1 text-sm">
                            {{ $aptDistrict ? 'อำเภอ'.$aptDistrict : '' }} {{ $aptProvince ? 'จังหวัด'.$aptProvince : '' }} {{ $aptPostal }}
                        </div>
                        <div class="text-right mb-4 text-sm">
                            ทำ ณ วันที่ {{ $contractDate->format('j') }} เดือน {{ thaiMonth($contractDate->format('m')) }} ค.ศ. {{ $contractDate->format('Y') }}
                        </div>

                        {{-- Body --}}
                        <p>สัญญานี้ทำขึ้นระหว่าง <strong>{{ $aptName }}</strong> ตั้งอยู่ที่ {{ $aptAddress }}{{ $aptSubdistrict ? ' ตำบล'.$aptSubdistrict : '' }}{{ $aptDistrict ? ' อำเภอ'.$aptDistrict : '' }}{{ $aptProvince ? ' จังหวัด'.$aptProvince : '' }} {{ $aptPostal }} ซึ่งต่อไปในสัญญานี้จะเรียกว่า "ผู้ให้เช่า" กับอีกฝ่ายหนึ่งเรียก "ผู้เช่า" คือ</p>

                        <p class="no-indent mt-2 mb-1">นาย/นาง/นางสาว <strong>{{ $tenantName }}</strong> ถือบัตรประชาชน <strong>{{ $tenantNid }}</strong></p>
                        <p class="no-indent mb-1">เบอร์โทร <strong>{{ $tenantPhone }}</strong></p>

                        <p class="mt-3">ทั้งสองฝ่ายตกลงทำสัญญากัน โดยมีข้อความดังต่อไปนี้</p>

                        <p class="mt-3"><strong>ข้อ 1</strong> ผู้เช่าตกลงเช่าและผู้ให้เช่าตกลงให้เช่าห้องพักอาศัยเลขที่ <strong>{{ $room->room_number }}</strong> ชั้นที่ <strong>{{ $floor }}</strong> ของหอพัก{{ $aptName }} ซึ่งตั้งอยู่ที่ {{ $aptAddress }}{{ $aptSubdistrict ? ' ตำบล'.$aptSubdistrict : '' }}{{ $aptDistrict ? ' อำเภอ'.$aptDistrict : '' }}{{ $aptProvince ? ' จังหวัด'.$aptProvince : '' }} {{ $aptPostal }} เพื่อใช้เป็นที่พักอาศัย ในอัตราค่าเช่าเดือนละ <strong>{{ $rentRate }}</strong> บาท และอัตราค่าห้องปรับอากาศเดือนละ ............... บาท (ค่าเช่านี้ไม่รวมถึงค่าไฟฟ้า ค่าน้ำประปา ค่าบำรุงรักษาเฟอร์นิเจอร์ และค่าทำความสะอาด ส่วนกลาง ซึ่งผู้เช่าต้องชำระแก่ผู้ให้เช่าตามอัตราที่กำหนดไว้ในสัญญาข้อ 4</p>

                        <p class="mt-3"><strong>ข้อ 2</strong> สัญญานี้มีข้อตกลงกัน มีกำหนดระยะเวลา <strong>{{ $duration }}</strong> เดือน เริ่มต้นวันที่ <strong>{{ $startDate ? $startDate->format('j/m/Y') : '............' }}</strong> หมดอายุสัญญาวันที่ <strong>{{ $endDate ? $endDate->format('j/m/Y') : '............' }}</strong> หากไม่ครบระยะสัญญา ผู้ให้เช่ามีสิทธิรับเงินประกันทั้งหมด</p>

                        <p class="mt-3"><strong>ข้อ 3</strong> การชำระค่าเช่า ผู้เช่าตกลงจะชำระค่าเช่าแก่ผู้ให้เช่า โดยชำระภายในวันที่ 1-{{ $setting->payment_due_day ?? 5 }} ของทุกเดือนตลอดเวลาอายุสัญญาการเช่า กรณีที่ผู้เช่าชำระค่าเช่าล่าช้า ผู้เช่ายอมจ่ายค่าปรับล่าช้า {{ number_format($setting->late_fee_per_day ?? 50, 0) }} บาท/วัน นับตั้งแต่วันที่ครบกำหนดชำระ แต่ไม่เกิน 45 วัน หากชำระล่าช้าเกิน 45 วัน ผู้ให้เช่าสามารถมีสิทธิบอกเลิกสัญญาได้ทันทีโดยมีต้องบอกกล่าวล่วงหน้า</p>

                        <p class="mt-3"><strong>ข้อ 4</strong> เพื่อการเข้าพักที่อาศัย ผู้เช่ายินยอมชำระ ให้แก่ผู้ให้เช่า ดังนี้</p>
                        <div class="ml-12 mt-2 space-y-1 text-sm">
                            <div class="flex">
                                <span class="w-52">4.1 ค่าเช่าห้อง</span>
                                <span>เดือนละ <strong>{{ $rentRate }}</strong> บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.2 ค่าไฟฟ้า</span>
                                <span>ยูนิตละ <strong>{{ $electricRate }}</strong> บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.3 ค่าน้ำประปา</span>
                                <span>ยูนิตละ <strong>{{ $waterRate }}</strong> บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.4 ค่าทำความสะอาด</span>
                                <span>เริ่มต้น 300 บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.5 อื่นๆ</span>
                                <span>เดือนละ ............... บาท</span>
                            </div>
                        </div>

                        <p class="mt-3"><strong>ข้อ 5</strong> เพื่อเป็นการปฏิบัติตามสัญญาเช่า ผู้เช่าตกลงมอบเงินประกันแก่ผู้ให้เช่าไว้เป็นจำนวน <strong>{{ $deposit }}</strong> บาท เงินประกันนี้ผู้ให้เช่าจะคืนให้แก่ผู้เช่าเมื่อผู้เช่าไม่ได้ผิดสัญญา และมิได้ค้างชำระเงินต่างๆ ตามสัญญานี้</p>

                        <p class="mt-3"><strong>ข้อ 6</strong> ผู้เช่าต้องดูแลห้องพักอาศัยและ ทรัพย์สินต่างๆ ในห้องพักดังกล่าวเสมือนเป็นทรัพย์สินของตนเอง</p>

                        <p class="mt-3"><strong>ข้อ 7</strong> ผู้เช่ามีหน้าที่รักษาความสะอาดตามกฎหมาย ไม่เก็บวัตถุไวไฟหรือสิ่งอันตรายหรือสิ่งต้องห้ามตามกฎหมาย ผู้เช่ายินยอมให้ผู้ให้เช่าเข้าตรวจความถูกต้องเรียบร้อยในห้อง</p>

                        <p class="mt-3"><strong>ข้อ 8</strong> ผู้เช่าจะดัดแปลงต่อเติมหรือซ่อมถอนทรัพย์สินที่ทำขึ้นทั้งหมดหรือบางส่วนได้ ต่อเมื่อได้รับความยินยอมเป็นหนังสือจากผู้ให้เช่า</p>

                        <p class="mt-3"><strong>ข้อ 9</strong> ถ้าผู้เช่าผิดสัญญาไม่ชำระค่าเช่าตามกำหนดไว้ในข้อ 2 ผู้ให้เช่าต้องทวงสิทธิในการกลับเข้าครอบครอง ห้องเช่าที่ให้เช่าคืนทันที</p>

                        <p class="mt-3"><strong>ข้อ 10</strong> สัญญาฉบับนี้ทำขึ้นเป็นสองฉบับมีข้อความตรงกัน คู่สัญญาทั้งสองฝ่ายได้อ่านและเข้าใจข้อความโดยตลอดดีแล้ว จึงได้ลงลายมือชื่อและประทับตราไว้เป็นหลักฐานต่อหน้าพยาน</p>

                        {{-- Signatures --}}
                        <div class="grid grid-cols-2 gap-8 mt-12">
                            <div class="text-center">
                                <p class="no-indent mb-8">ลงชื่อ ........................................</p>
                                <p class="no-indent">(........................................)</p>
                                <p class="no-indent font-semibold">ผู้ให้เช่า</p>
                            </div>
                            <div class="text-center">
                                <p class="no-indent mb-8">ลงชื่อ ........................................</p>
                                <p class="no-indent">({{ $tenantName }})</p>
                                <p class="no-indent font-semibold">ผู้เช่า</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-8 mt-8">
                            <div class="text-center">
                                <p class="no-indent mb-8">ลงชื่อ ........................................</p>
                                <p class="no-indent">(........................................)</p>
                                <p class="no-indent font-semibold">พยาน</p>
                            </div>
                            <div class="text-center">
                                <p class="no-indent mb-8">ลงชื่อ ........................................</p>
                                <p class="no-indent">(........................................)</p>
                                <p class="no-indent font-semibold">พยาน</p>
                            </div>
                        </div>

                        {{-- Attached Files --}}
                        @if($contract->contract_file || $contract->idcard_file)
                        <div class="mt-10 pt-6 border-t border-gray-200 no-print">
                            <p class="no-indent font-semibold text-gray-600 mb-3">เอกสารแนบ</p>
                            <div class="flex flex-wrap gap-3">
                                @if($contract->contract_file)
                                <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank"
                                   class="flex items-center gap-2 px-4 py-2 border border-[#4A90E2] text-[#4A90E2] rounded-lg hover:bg-blue-50 transition-colors text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    ไฟล์สัญญาเช่า
                                </a>
                                @endif
                                @if($contract->idcard_file)
                                <a href="{{ asset('storage/' . $contract->idcard_file) }}" target="_blank"
                                   class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    สำเนาบัตรประชาชน
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        let currentZoom = 1;
        const contractDoc = document.getElementById('contractDoc');

        function zoomIn() {
            if (currentZoom < 1.5) {
                currentZoom = Math.round((currentZoom + 0.1) * 10) / 10;
                contractDoc.style.transform = 'scale(' + currentZoom + ')';
            }
        }

        function zoomOut() {
            if (currentZoom > 0.6) {
                currentZoom = Math.round((currentZoom - 0.1) * 10) / 10;
                contractDoc.style.transform = 'scale(' + currentZoom + ')';
            }
        }
    </script>
</body>
</html>
