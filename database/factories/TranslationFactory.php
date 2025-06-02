<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    public function definition(): array
    {
        // Create locale-specific faker instances
        $fakerEn = \Faker\Factory::create('en_US');
        $fakerFr = \Faker\Factory::create('fr_FR');
        $fakerEs = \Faker\Factory::create('es_ES');

        // Use the same 'type' of content (e.g. word/sentence) in all locales
        return [
            'key' => $fakerEn->unique()->word,
            'content' => [
                'en' => $fakerEn->sentence,
                'fr' => $fakerFr->sentence,
                'es' => $fakerEs->sentence,
            ],
        ];
    }
}
