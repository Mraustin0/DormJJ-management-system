<?php

/*
|--------------------------------------------------------------------------
| Payment Configuration
|--------------------------------------------------------------------------
| แก้ค่าเหล่านี้ในไฟล์ .env เพื่อตั้งค่าช่องทางรับชำระเงินของหอพัก
|
| PAYMENT_BANK_NAME        = ชื่อธนาคาร
| PAYMENT_BANK_ACCOUNT     = เลขที่บัญชี
| PAYMENT_BANK_ACCOUNT_NAME= ชื่อบัญชี
| PAYMENT_PROMPTPAY_ID     = เบอร์โทรหรือเลขบัตรประชาชนสำหรับ PromptPay
*/

return [
    'bank_name'         => env('PAYMENT_BANK_NAME', ''),
    'bank_account'      => env('PAYMENT_BANK_ACCOUNT', ''),
    'bank_account_name' => env('PAYMENT_BANK_ACCOUNT_NAME', ''),
    'promptpay_id'      => env('PAYMENT_PROMPTPAY_ID', ''),
];
