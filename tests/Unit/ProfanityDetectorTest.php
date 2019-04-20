<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Naraki\Core\Support\Requests\Detectors\Profanity;
use Tests\TestCase;

class ProfanityDetectorTest extends TestCase
{
    use WithoutMiddleware;

    public function test_profanity_detector_with_profanity()
    {
        $result = Profanity::detect(
            'This is a random string, mother fucker, where everything is good.',
            'en');
        $this->assertNotEmpty($result);
    }

    public function test_profanity_detector_without_profanity()
    {
        $result = Profanity::detect(
            'This is a random string where everything is good.',
            'en');
        $this->assertFalse($result);
    }

    public function test_profanity_detector_without_supported_language()
    {
        $this->expectException(\UnexpectedValueException::class);
        Profanity::detect(
            'This is a random string where everything is good.',
            'tr');
    }


}
