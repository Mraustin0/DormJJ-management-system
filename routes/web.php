<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RoomController;

// 1. ส่วน Login/Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. ส่วนของ Admin (ต้อง Login ก่อนถึงเข้าได้)
Route::middleware(['auth'])->group(function () {

    // หน้าหลัก Dashboard
    Route::get('/dashboard', [AuthController::class, 'index'])->name('rooms.index');

    // หน้าข้อมูลการเข้าพัก
    Route::get('/accommodation', [AuthController::class, 'accommodation'])->name('rooms.accommodation');

    // หน้าจัดการมิเตอร์ (รวม - เก็บไว้ backwards compat)
    Route::get('/meters', [MeterController::class, 'index'])->name('rooms.meters');
    Route::post('/meters/update', [MeterController::class, 'update'])->name('meters.update');

    // หน้ามิเตอร์น้ำ
    Route::get('/meters/water', [MeterController::class, 'waterIndex'])->name('meters.water');
    Route::post('/meters/water/update', [MeterController::class, 'updateWater'])->name('meters.water.update');

    // หน้ามิเตอร์ไฟฟ้า
    Route::get('/meters/electric', [MeterController::class, 'electricIndex'])->name('meters.electric');
    Route::post('/meters/electric/update', [MeterController::class, 'updateElectric'])->name('meters.electric.update');

    // หน้าประวัติบิล
    Route::get('/bills', [BillController::class, 'index'])->name('rooms.bills');

    // หน้าข้อมูลลูกค้า
    Route::get('/customers', [CustomerController::class, 'index'])->name('rooms.customers');
    Route::post('/customers/update', [CustomerController::class, 'update'])->name('customers.update');

    // แจ้งย้ายออก
    Route::get('/rooms/{id}/moveout', [RoomController::class, 'moveOutForm'])->name('rooms.moveout');
    Route::post('/contracts/moveout', [CustomerController::class, 'moveOut'])->name('contracts.moveout');


    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{id}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('rooms.update'); // เตรียมไว้สำหรับปุ่มบันทึก


    // หน้าฟอร์มทำสัญญาเช่า (สำหรับห้องว่าง)
    Route::get('/rooms/{id}/assign', [RoomController::class, 'assign'])->name('rooms.assign');

// ฟังก์ชันบันทึกข้อมูลสัญญาใหม่
    Route::post('/rooms/{id}/assign', [RoomController::class, 'storeAssignment'])->name('rooms.storeAssignment');

        // หน้าสร้างสัญญาเช่า (หน้าใหม่)
    Route::get('/rooms/{id}/contract/create', [RoomController::class, 'createContract'])->name('rooms.createContract');

// บันทึกสัญญาเช่า
    Route::post('/rooms/{id}/contract/store', [RoomController::class, 'storeContract'])->name('rooms.storeContract');

    });
