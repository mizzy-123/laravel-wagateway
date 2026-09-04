<?php

namespace Database\Factories;

use App\Models\WaTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaTemplate>
 */
class WaTemplateFactory extends Factory
{
    protected $model = WaTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'category' => fake()->randomElement(['Appointment', 'Laboratorium', 'Follow-up', 'Informasi', 'Survey']),
            'body' => "Halo {nama},\n\nIni adalah pesan dari RS Roemani.\nTanggal: {tanggal}",
            'status' => 'draft',
            'usage_count' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}
