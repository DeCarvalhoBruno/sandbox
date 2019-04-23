<?php namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Naraki\Core\Commands\GenerateLangFiles;
use Tests\TestCase;

class GenerateLangFileTest extends TestCase
{
    use WithoutMiddleware;

    public function test_generate_lang_file_array_to_string()
    {
        $cmd = new GenerateLangFiles();
        $result = $cmd->arrayToString([
            'content' => [
                'other' => ['string' => 'This don\'t work very well'],
                'email' => 'E-Mail Address',
                'send_link' => 'Send Password Reset Link'
            ]
        ]);
        $this->assertTrue(is_string($result));
    }

    public function test_generate_lang_file_translate_array()
    {
        $cmd = new GenerateLangFiles();
        $result = $cmd->translateArray(
            [
                'content' => [
                    'other' => ['string' => 'This don\'t work very well'],
                    'email' => 'E-Mail Address',
                    'send_link' => 'Send Password Reset Link'
                ]
            ]);
        $this->assertTrue(is_array($result));
    }

    public function test_generate_lang_file_proper_copy()
    {
        $cmd = new GenerateLangFiles();
        $result = $cmd->compareNCopy(
            [
                'content' => [
                    '501' => ['string' => 'This don\'t work very well'],
                    '502' => 'E-Mail Address',
                    '503' => 'Send Password Reset Link'
                ]
            ],
            [
                'content' => [
                    '502' => 'E-Mail Address new value',
                    '503' => 'Send Password Reset Link new value',
                    '504' => 'some string new value'
                ]
            ]);
        $this->assertArrayHasKey('501', $result['content']);
        $this->assertArrayNotHasKey('504', $result['content']);
    }


}
