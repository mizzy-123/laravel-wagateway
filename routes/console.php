<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    DB::statement("
        INSERT INTO appointments (type, patient_id, phone, name, updated_at)
        SELECT
            'patient',
            pasien_id,
            no_hp,
            nama,
            NOW()
        FROM f_pasiens
        ON CONFLICT (patient_id)
        DO UPDATE SET
            phone = EXCLUDED.phone,
            name = EXCLUDED.name,
            updated_at = NOW()
    ");
})->name('sync-appointments-patients')
->dailyAt('01:00')
->withoutOverlapping();

Schedule::call(function () {
    DB::statement("
    INSERT INTO appointments (type, doctor_id, phone, name, updated_at)
    SELECT
        'doctor',
        id_pegawai,
        no_telp,
        nm_pegawai,
        NOW()
    FROM f_data_pegawai
    WHERE tipe_pegawai_d = 'dokter'
    ON CONFLICT (doctor_id)
    DO UPDATE SET
        phone = EXCLUDED.phone,
        name = EXCLUDED.name,
        updated_at = NOW()");
})->name('sync-appointments-doctors')
->dailyAt('01:00')
->withoutOverlapping();

Schedule::call(function () {
    DB::statement("
        INSERT INTO appointments (type, employee_id, phone, name, updated_at)
        SELECT
            'employee',
            id_pegawai,
            no_telp,
            nm_pegawai,
            NOW()
        FROM f_data_pegawai
        WHERE tipe_pegawai_d is NULL
        ON CONFLICT (employee_id)
        DO UPDATE SET
            phone = EXCLUDED.phone,
            name = EXCLUDED.name,
            updated_at = NOW()
    ");
})->name('sync-appointments-employees')
->dailyAt('01:00')
->withoutOverlapping();
