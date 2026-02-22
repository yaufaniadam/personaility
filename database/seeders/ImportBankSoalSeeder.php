<?php

namespace Database\Seeders;

use App\Enums\PersonalityTrait;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportBankSoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('docs/banksoal.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File not found: {$csvFile}");
            return;
        }

        // Truncate existing questions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($csvFile, 'r');
        
        // Skip header
        $header = fgetcsv($handle);
        
        $count = 0;
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0])) continue;

            $no = $data[0];
            $category = trim($data[1]);
            $questionText = $data[2];
            
            // Mapping Category to PersonalityTrait
            $trait = match ($category) {
                'Openness' => PersonalityTrait::Openness,
                'Conscient.' => PersonalityTrait::Conscientiousness,
                'Extraversion' => PersonalityTrait::Extraversion,
                'Agreeable.' => PersonalityTrait::Agreeableness,
                'Neuroticism' => PersonalityTrait::Neuroticism,
                default => null,
            };

            if (!$trait) {
                $this->command->warn("Unknown category: {$category} at row {$no}");
                continue;
            }

            // Options mapping (Score 1 to 5)
            // CSV: Jawaban 1 (Skor 5), Jawaban 2 (Skor 4), Jawaban 3 (Skor 3), Jawaban 4 (Skor 2), Jawaban 5 (Skor 1)
            // Index: 3, 4, 5, 6, 7
            $options = [
                $data[7], // Score 1
                $data[6], // Score 2
                $data[5], // Score 3
                $data[4], // Score 4
                $data[3], // Score 5
            ];

            Question::create([
                'trait' => $trait,
                'question_text' => $questionText,
                'options' => $options,
                'is_reverse' => false,
                'allow_note' => true, // Default to true as per current design in Create.vue
                'order_number' => $no,
                'active' => true,
            ]);

            $count++;
        }

        fclose($handle);

        $this->command->info("Imported {$count} questions from banksoal.csv");
    }
}
