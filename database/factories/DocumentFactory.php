<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'path' => 'private-uploads/' . fake()->uuid() . '.pdf',
            'original_name' => 'sample.pdf',
        ];
    }
}
