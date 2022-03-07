<?php

namespace Test\Unit;

use Test\TestCase;
use Web3\Formatters\PostFormatter;

class PostFormatterTest extends TestCase
{
    /**
     * formatter
     * 
     * @var \Web3\Formatters\PostFormatter
     */
    protected $formatter;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->formatter = new PostFormatter;
    }

    /**
     * testFormat
     * 
     * @return void
     */
    public function testFormat()
    {
        $formatter = $this->formatter;

        $post= $formatter->format([
            'from' => "0x776869737065722d636861742d636c69656e74",
            'to' => "0x4d5a695276454c39425154466b61693532",
            'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
            'payload' => "0x7b2274797065223a226d6",
            'priority' => 12,
            'ttl' => 50,
        ]);
        $this->assertEquals($post, [
            'from' => "0x776869737065722d636861742d636c69656e74",
            'to' => "0x4d5a695276454c39425154466b61693532",
            'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
            'payload' => "0x7b2274797065223a226d6",
            'priority' => '0xc',
            'ttl' => '0x32',
        ]);

        $post= $formatter->format([
            'from' => "0x776869737065722d636861742d636c69656e74",
            'to' => "0x4d5a695276454c39425154466b61693532",
            'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
            'payload' => "0x7b2274797065223a226d6",
            'priority' => '0xab',
            'ttl' => '0xcc',
        ]);
        $this->assertEquals($post, [
            'from' => "0x776869737065722d636861742d636c69656e74",
            'to' => "0x4d5a695276454c39425154466b61693532",
            'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
            'payload' => "0x7b2274797065223a226d6",
            'priority' => '0xab',
            'ttl' => '0xcc',
        ]);
    }
}