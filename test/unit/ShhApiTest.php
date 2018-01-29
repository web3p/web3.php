<?php

namespace Test\Unit;

use RuntimeException;
use Test\TestCase;

class ShhApiTest extends TestCase
{
    /**
     * shh
     * 
     * @var Web3\Shh
     */
    protected $shh;

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->shh = $this->web3->shh;
    }

    /**
     * testVersion
     * 
     * @return void
     */    
    public function testVersion()
    {
        $shh = $this->shh;

        $shh->version(function ($err, $version) {
            if ($err !== null) {
                return $this->fail($err->getMessage());
            }
            $this->assertTrue(is_string($version));
        });
    }

    /**
     * testNewIdentity
     * Comment because ganache-cli only implement shh_version.
     * 
     * @return void
     */    
    // public function testNewIdentity()
    // {
    //     $shh = $this->shh;

    //     $shh->newIdentity(function ($err, $identity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });
    // }

    /**
     * testHasIdentity
     * Comment because ganache-cli only implement shh_version.
     * 
     * @return void
     */    
    // public function testHasIdentity()
    // {
    //     $shh = $this->shh;
    //     $newIdentity = '0x' . implode('', array_fill(0, 120, '0'));

    //     $shh->hasIdentity($newIdentity, function ($err, $hasIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertFalse($hasIdentity);
    //     });

    //     $shh->newIdentity(function ($err, $identity) use (&$newIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $newIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });

    //     $shh->hasIdentity($newIdentity, function ($err, $hasIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue($hasIdentity);
    //     });
    // }

    /**
     * testNewGroup
     * 
     * @return void
     */    
    // public function testNewGroup()
    // {
    //     $shh = $this->shh;

    //     $shh->newGroup(function ($err, $group) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertEquals(mb_strlen($group), 132);
    //     });
    // }

    /**
     * testAddToGroup
     * 
     * @return void
     */    
    // public function testAddToGroup()
    // {
    //     $shh = $this->shh;
    //     $newIdentity = '';

    //     $shh->newIdentity(function ($err, $identity) use (&$newIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $newIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });

    //     $shh->addToGroup($newIdentity, function ($err, $hasAdded) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue($hasAdded);
    //     });
    // }

    /**
     * testPost
     * Comment because ganache-cli only implement shh_version.
     * 
     * @return void
     */    
    // public function testPost()
    // {
    //     $shh = $this->shh;
    //     $fromIdentity = '';
    //     $toIdentity = '';

    //     // create fromIdentity and toIdentity to prevent unknown identity error
    //     $shh->newIdentity(function ($err, $identity) use (&$fromIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $fromIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });
    //     $shh->newIdentity(function ($err, $identity) use (&$toIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $toIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });

    //     $shh->post([
    //         'from' => $fromIdentity,
    //         'to' => $toIdentity,
    //         'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
    //         'payload' => "0x7b2274797065223a226d6",
    //         'priority' => "0x64",
    //         'ttl' => "0x64",
    //     ], function ($err, $isSent) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue($isSent);
    //     });

    //     $shh->post([
    //         'from' => $fromIdentity,
    //         'to' => $toIdentity,
    //         'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
    //         'payload' => "0x7b2274797065223a226d6",
    //         'priority' => 123,
    //         'ttl' => 123,
    //     ], function ($err, $isSent) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue($isSent);
    //     });
    // }

    /**
     * testNewFilter
     * Comment because ganache-cli only implement shh_version.
     * 
     * @return void
     */    
    // public function testNewFilter()
    // {
    //     $shh = $this->shh;
    //     $toIdentity = '';

    //     // create toIdentity to prevent unknown identity error
    //     $shh->newIdentity(function ($err, $identity) use (&$toIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $toIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });

    //     $shh->newFilter([
    //         'to' => $toIdentity,
    //         'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
    //     ], function ($err, $filterId) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue(is_string($filterId));
    //     });

    //     $shh->newFilter([
    //         'to' => $toIdentity,
    //         'topics' => [null, "0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
    //     ], function ($err, $filterId) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue(is_string($filterId));
    //     });

    //     $shh->newFilter([
    //         'to' => $toIdentity,
    //         'topics' => ["0x776869737065722d636861742d636c69656e74", ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"]],
    //     ], function ($err, $filterId) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue(is_string($filterId));
    //     });
    // }

    /**
     * testUninstallFilter
     * Comment because ganache-cli only implement shh_version.
     * 
     * @return void
     */    
    // public function testUninstallFilter()
    // {
    //     $shh = $this->shh;
    //     $toIdentity = '';
    //     $filter = '';

    //     // create toIdentity to prevent unknown identity error
    //     $shh->newIdentity(function ($err, $identity) use (&$toIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $toIdentity = $identity;

    //         $this->assertEquals(mb_strlen($identity), 132);
    //     });

    //     $shh->newFilter([
    //         'to' => $toIdentity,
    //         'topics' => ["0x776869737065722d636861742d636c69656e74", "0x4d5a695276454c39425154466b61693532"],
    //     ], function ($err, $filterId) use (&$filter) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $filter = $filterId;

    //         $this->assertTrue(is_string($filterId));
    //     });

    //     $shh->uninstallFilter($filter, function ($err, $uninstalled) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue($uninstalled);
    //     });
    // }

    /**
     * testWrongParam
     * We transform data and throw invalid argument exception
     * instead of runtime exception.
     * 
     * @return void
     */
    // public function testWrongParam()
    // {
    //     $this->expectException(RuntimeException::class);

    //     $shh = $this->shh;

    //     $shh->hasIdentity('0', function ($err, $hasIdentity) {
    //         if ($err !== null) {
    //             return $this->fail($err->getMessage());
    //         }
    //         $this->assertTrue(true);
    //     });
    // }
}