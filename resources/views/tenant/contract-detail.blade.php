<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่า - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        .dotted {
            border-bottom: 1px dotted #666;
            display: inline;
            min-width: 80px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'contract'])

    {{-- Main Content --}}
    <main id="mainContent" class="md:ml-72 min-h-screen transition-all duration-300">
        {{-- Top Bar --}}
        <header class="bg-red-600 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="text-white font-semibold text-lg">ระบบจัดการหอพัก {{ $setting->apartment_name ?? 'JJ Apartment' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <button class="text-white relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="text-white text-sm hidden sm:block">
                            <p class="font-semibold leading-tight">{{ $contract->tenant_name }}</p>
                            <p class="text-white/80 text-xs">ห้อง {{ $contract->room->room_number }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            {{-- Back + Title --}}
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('tenant.contract') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800">สัญญาเช่า</h2>
            </div>

            {{-- Document Viewer --}}
            <div class="relative max-w-4xl mx-auto">
                {{-- Zoom Controls --}}
                <div class="fixed right-8 top-1/3 z-20 flex flex-col gap-1">
                    <button onclick="zoomIn()" class="w-10 h-10 bg-white border border-gray-300 rounded-t-lg flex items-center justify-center hover:bg-gray-50 shadow-sm text-xl font-bold text-gray-600">+</button>
                    <button onclick="zoomOut()" class="w-10 h-10 bg-white border border-gray-300 rounded-b-lg flex items-center justify-center hover:bg-gray-50 shadow-sm text-xl font-bold text-gray-600">-</button>
                </div>

                {{-- Contract Document --}}
                <div class="overflow-x-auto pb-8">
                    <div id="contractDoc" class="contract-document max-w-3xl mx-auto rounded-lg p-10 md:p-14 text-sm leading-relaxed text-gray-800" style="min-width: 600px;">

                        @php
                            $room = $contract->room;
                            $aptName = $setting->apartment_name ?? 'เจเจพาร์ทเมนต์';
                            $aptAddress = $setting->address ?? '412 หมู่ 1';
                            $aptSubdistrict = $setting->subdistrict ?? 'ไชยมงคล';
                            $aptDistrict = $setting->district ?? 'เมือง';
                            $aptProvince = $setting->province ?? 'นครราชสีมา';
                            $aptPostal = $setting->postal_code ?? '30000';
                            $aptPhone = $setting->phone ?? '-';
                            $rentRate = number_format($room->room_rate ?? $setting->rent_per_month ?? 2500, 0);
                            $electricRate = $setting->electric_rate ?? 8;
                            $waterRate = $setting->water_rate ?? 25;
                            $deposit = number_format($contract->deposit ?? 0, 0);
                            $duration = $contract->contract_duration ?? 12;
                            $startDate = $contract->start_date ? \Carbon\Carbon::parse($contract->start_date) : null;
                            $endDate = $contract->end_date ? \Carbon\Carbon::parse($contract->end_date) : null;
                            $contractDate = $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date) : ($startDate ?? now());
                            $tenantName = $contract->tenant_name ?? '............';
                            $tenantNid = $contract->nid ? (substr($contract->nid,0,1).'-'.substr($contract->nid,1,4).'-'.substr($contract->nid,5,5).'-'.substr($contract->nid,10,2).'-'.substr($contract->nid,12)) : '............';
                            $tenantPhone = $contract->phone ?? '............';
                        @endphp

                        {{-- Header --}}
                        <h2 class="text-center text-xl font-bold mb-6">สัญญาเช่าห้อง {{ $aptName }}</h2>

                        <div class="text-right mb-1 text-sm">
                            {{ $aptAddress }} {{ $aptSubdistrict ? 'ตำบล'.$aptSubdistrict : '' }}
                        </div>
                        <div class="text-right mb-1 text-sm">
                            {{ $aptDistrict ? 'อำเภอ'.$aptDistrict : '' }} {{ $aptProvince ? 'จังหวัด'.$aptProvince : '' }} {{ $aptPostal }}
                        </div>
                        <div class="text-right mb-4 text-sm">
                            ทำ ณ วันที่ {{ $contractDate->format('j') }} เดือน {{ thaiMonth($contractDate->format('m')) }} พ.ศ. {{ $contractDate->format('Y') + 543 }}
                        </div>

                        {{-- Body --}}
                        <p>สัญญานี้ทำขึ้นระหว่าง <strong>{{ $aptName }}</strong> ตั้งอยู่ที่ {{ $aptAddress }} {{ $aptSubdistrict ? 'ตำบล'.$aptSubdistrict : '' }} {{ $aptDistrict ? 'อำเภอ'.$aptDistrict : '' }} {{ $aptProvince ? 'จังหวัด'.$aptProvince : '' }} {{ $aptPostal }} ซึ่งต่อไปในสัญญานี้จะเรียกว่า "ผู้ให้เช่า" กับอีกฝ่ายหนึ่งเรียก "ผู้เช่า" คือ</p>

                        <p class="no-indent mt-2 mb-1">นาย/นาง/นางสาว <strong>{{ $tenantName }}</strong> ถือบัตรประชาชน <strong>{{ $tenantNid }}</strong></p>
                        <p class="no-indent mb-1">เบอร์โทร <strong>{{ $tenantPhone }}</strong></p>

                        <p class="mt-3">ทั้งสองฝ่ายตกลงทำสัญญากัน โดยมีข้อความดังต่อไปนี้</p>

                        <p class="mt-3"><strong>ข้อ 1</strong> ผู้เช่าตกลงเช่าและผู้ให้เช่าตกลงให้เช่าห้องพักอาศัยเลขที่ <strong>{{ $room->room_number }}</strong> ชั้นที่ <strong>{{ $room->floor }}</strong> ของหอพัก{{ $aptName }} ซึ่งตั้งอยู่ที่ {{ $aptAddress }} {{ $aptSubdistrict ? 'ตำบล'.$aptSubdistrict : '' }} {{ $aptDistrict ? 'อำเภอ'.$aptDistrict : '' }} {{ $aptProvince ? 'จังหวัด'.$aptProvince : '' }} {{ $aptPostal }} เพื่อใช้เป็นที่พักอาศัย ในอัตราค่าเช่าเดือนละ <strong>{{ $rentRate }}</strong> บาท และอัตราค่าห้องปรับอากาศเดือนละ ............... บาท (ค่าเช่านี้ไม่รวมถึงค่าไฟฟ้า ค่าน้ำประปา ค่าบำรุงรักษาเฟอร์นิเจอร์ และค่าทำความสะอาด ส่วนกลาง ซึ่งผู้เช่าต้องชำระแก่ผู้ให้เช่าตามอัตราที่กำหนดไว้ในสัญญาข้อ 4</p>

                        <p class="mt-3"><strong>ข้อ 2</strong> สัญญานี้มีข้อตกลงกัน มีกำหนดระยะเวลา <strong>{{ $duration }}</strong> เดือน เริ่มต้นวันที่ <strong>{{ $startDate ? $startDate->format('j/m/').($startDate->format('Y')+543) : '............' }}</strong> หมดอายุสัญญาวันที่ <strong>{{ $endDate ? $endDate->format('j/m/').($endDate->format('Y')+543) : '............' }}</strong> หากไม่ครบระยะสัญญา ผู้ให้เช่ามีสิทธิรับเงินประกันทั้งหมด</p>

                        <p class="mt-3"><strong>ข้อ 3</strong> การชำระค่าเช่า ผู้เช่าตกลงจะชำระค่าเช่าแก่ผู้ให้เช่า โดยชำระภายในวันที่ 1-{{ $setting->payment_due_day ?? 5 }} ของทุกเดือนตลอดเวลาอายุสัญญาการเช่า กรณีที่ผู้เช่าชำระค่าเช่าล่าช้า ผู้เช่ายอมจ่ายค่าปรับล่าช้า {{ number_format($setting->late_fee_per_day ?? 50, 0) }} บาท/วัน นับตั้งแต่วันที่ครบกำหนดชำระ แต่ไม่เกิน 45 วัน หากชำระล่าช้าเกิน 45 วัน ผู้ให้เช่าสามารถมีสิทธิบอกเลิกสัญญาได้ทันทีโดยมีต้องบอกกล่าวล่วงหน้า</p>

                        <p class="mt-3"><strong>ข้อ 4</strong> เพื่อการเข้าพักที่อาศัย ผู้เช่ายินยอมชำระ ให้แก่ผู้เช่า ดังนี้</p>
                        <div class="ml-12 mt-2 space-y-1 text-sm">
                            <div class="flex">
                                <span class="w-52">4.1 ค่าเช่าห้อง</span>
                                <span>เดือนละ <strong>{{ $rentRate }}</strong> บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.2 ค่าไฟฟ้า</span>
                                <span>ยูนิตละ <strong>{{ number_format($electricRate, 0) }}</strong> บาท</span>
                            </div>
                            <div class="flex">
                                <span class="w-52">4.3 ค่าน้ำประปา</span>
                                <span>ยูนิตละ <strong>{{ number_format($waterRate, 0) }}</strong> บาท</span>
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

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentZoom = 1;
        const contractDoc = document.getElementById('contractDoc');

        function zoomIn() {
            if (currentZoom < 1.5) {
                currentZoom += 0.1;
                contractDoc.style.transform = 'scale(' + currentZoom + ')';
            }
        }

        function zoomOut() {
            if (currentZoom > 0.6) {
                currentZoom -= 0.1;
                contractDoc.style.transform = 'scale(' + currentZoom + ')';
            }
        }
    </script>
</body>
</html>
