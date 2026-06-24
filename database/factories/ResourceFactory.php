<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        $stored = Str::uuid() . '.pdf';

        return [
            'title'             => fake()->sentence(4),
            'description'       => fake()->paragraph(),
            'original_filename' => fake()->word() . '.pdf',
            'stored_filename'   => $stored,
            'file_path'         => 'resources/' . $stored,
            'file_type'         => 'application/pdf',
            'file_size'         => fake()->numberBetween(10240, 5242880),
            'file_hash'         => fake()->sha256(),
            'status'            => 'published',
            'download_count'    => 0,
            'uploaded_by'       => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function pendingReview(): static
    {
        return $this->state(['status' => 'pending_review']);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(['category_id' => $category->id]);
    }
}
