<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notification;
use App\Models\Survey;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@remedial.com',
            'password' => 'password123',
            'role' => 'admin',
            'department' => 'Administración',
            'email_verified_at' => now(),
        ]);

        // Residentes
        $residente1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@remedial.com',
            'password' => 'password123',
            'role' => 'residente',
            'department' => 'Torre A',
            'email_verified_at' => now(),
        ]);

        $residente2 = User::create([
            'name' => 'María López',
            'email' => 'maria@remedial.com',
            'password' => 'password123',
            'role' => 'residente',
            'department' => 'Torre B',
            'email_verified_at' => now(),
        ]);

        // Notificaciones de ejemplo
        Notification::create([
            'user_id' => $residente1->id,
            'type' => 'asamblea',
            'title' => 'Asamblea General',
            'body' => 'Se convoca a asamblea general el próximo sábado a las 10:00 AM.',
        ]);

        Notification::create([
            'user_id' => $residente1->id,
            'type' => 'pago_atrasado',
            'title' => 'Pago pendiente',
            'body' => 'Tienes un pago de mantenimiento pendiente del mes de marzo.',
        ]);

        Notification::create([
            'user_id' => $residente2->id,
            'type' => 'mensaje',
            'title' => 'Bienvenida',
            'body' => 'Bienvenida a la plataforma Remedial.',
        ]);

        // Encuesta de ejemplo
        $survey = Survey::create([
            'created_by' => $admin->id,
            'title' => '¿Aprobar remodelación del lobby?',
            'description' => 'Votación para aprobar la remodelación del área común del lobby principal.',
            'is_active' => true,
        ]);

        $survey->options()->createMany([
            ['option_text' => 'Sí, aprobar'],
            ['option_text' => 'No, rechazar'],
            ['option_text' => 'Necesito más información'],
        ]);
    }
}
