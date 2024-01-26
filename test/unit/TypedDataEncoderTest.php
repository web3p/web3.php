<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3\Utils;
use Web3\Contracts\TypedDataEncoder;

class TypedDataEncoderTest extends TestCase
{
    /**
     * typedDataEncoder
     * 
     * @var \Web3\Contracts\TypedDataEncoder
     */
    protected $typedDataEncoder;

    /**
     * hashDomainPassTests
     * 
     * @var array
     */
    protected $hashDomainPassTests = [
        [
            [
                "name" => "Ether Mail",
                "version" => "1",
                "chainId" => 1,
                "verifyingContract" => "0xCcCCccccCCCCcCCCCCCcCcCccCcCCCcCcccccccC",
            ],
            "0xf2cee375fa42b42143804025fc449deafd50cc031ca257e0b194a650a912090f",
        ],
        [
            [
                "name" => "Ether Mail",
                "version" => "1",
                "chainId" => "1",
                "verifyingContract" => "0xCcCCccccCCCCcCCCCCCcCcCccCcCCCcCcccccccC",
            ],
            "0xf2cee375fa42b42143804025fc449deafd50cc031ca257e0b194a650a912090f",
        ],
        [
            [
                "name" => "Ether Mail",
                "version" => 1,
                "chainId" => 1,
                "verifyingContract" => "0xCcCCccccCCCCcCCCCCCcCcCccCcCCCcCcccccccC",
            ],
            "0x902f609607aa38e1c768f260a84a1be97f3a9d65726d3e842fa5e36c6da393cb",
        ],
        [
            [
                "name" => "Ether Mail",
                "version" => "1",
                "chainId" => 1,
                "verifyingContract" => "0xcccccccccccccccccccccccccccccccccccccccc",
            ],
            "0xf2cee375fa42b42143804025fc449deafd50cc031ca257e0b194a650a912090f",
        ],
        [
            [
                "name" => "Ether Mail",
                "version" => "1",
                "chainId" => 1,
                "verifyingContract" => "0xCcCCccccCCCCcCCCCCCcCcCccCcCCCcCcccccccC",
                "salt" => "0xa9f4c8b7e576dc96308c361b46d32c04a00a0e5c2b0962d9f42be6891a95d139",  # noqa => E501
            ],
            "0x53d039704f24ce448de9dc98c5952dd85b7e7c22446a0b1cb47b43b901d00972",
        ],
        [
            [],
            "0x6192106f129ce05c9075d319c1fa6ea9b3ae37cbd0c1ef92e2be7137bb07baa1",
        ],
    ];

    /**
     * hashDomainFailTests
     * 
     * @var array
     */
    protected $hashDomainFailTests = [

        [
            [
                "name" => "Ether Mail",
                "classification" => "1",
                "chainId" => 1,
                "verifyingContract" => "0xCcCCccccCCCCcCCCCCCcCcCccCcCCCcCcccccccC",
            ],
            InvalidArgumentException::class,
        ],
        [
            [
                "name" => "Ether Mail",
                "version" => "1",
                "chainId" => 1,
                "verifyingContract" => "0xCcCCccccCCCC",
            ],
            InvalidArgumentException::class,
        ],
    ];

    /**
     * hashEIP712MessageTests
     * 
     * @var array
     */
    protected $hashEIP712MessageTests = [
        [
            [
                "from" => [
                    "name" => "Cow",
                ],
            ],
            [
                "Person" => [
                    ["name" => "name", "type" => "string"],
                ],
                "Mail" => [
                    ["name" => "from", "type" => "Person"],
                ],
            ],
            "0xdfa5fd27fea278587b6c6a56d8e6cf2853b6698a4244afc1f5f526f04b2b70b3",
        ],
        [
            [
                "who" => [
                    [
                        "name" => "Cow",
                    ],
                    [
                        "name" => "Dan",
                    ],
                    [
                        "name" => "Eve",
                    ],
                ],
            ],
            [
                "Person" => [
                    ["name" => "name", "type" => "string"],
                ],
                "People" => [
                    ["name" => "who", "type" => "Person[]"],
                ],
            ],
            "0x978fbd13a22cb2ced753b88943583080d6e2fa20d9f5818181dd85ee26438745",
        ],
        [
            [
                "what" => [
                    [
                        [
                            "name" => "Cow",
                        ],
                    ],
                    [
                        [
                            "name" => "Dan",
                        ],
                    ],
                    [
                        [
                            "name" => "Eve",
                        ],
                    ],
                ],
            ],
            [
                "Stuff" => [
                    ["name" => "name", "type" => "string"],
                ],
                "Things" => [
                    ["name" => "what", "type" => "Stuff[][]"],
                ],
            ],
            "0xb475420217c60fe1a7ad38c925c80f5d2c58e0fbb980684e4722f810ba9235d8",
        ],
        [
            [
                "what" => [
                    [
                        [
                            "name" => "Cow",
                        ],
                    ],
                    [
                        [
                            "name" => "Dan",
                        ],
                    ],
                    [
                        [
                            "name" => "Eve",
                        ],
                    ],
                ],
            ],
            [
                "Stuff" => [
                    ["name" => "name", "type" => "string"],
                ],
                "Things" => [
                    ["name" => "what", "type" => "Stuff[3][1]"],
                ],
            ],
            "0xcdbacf00da86992e9443d46aa0206e27d670a6140155f8c505a68ba733c7e639",
        ],
        [
            [
                "what" => [
                    [
                        [
                            "name" => "Cow",
                        ],
                    ],
                    [
                        [
                            "name" => "Dan",
                        ],
                    ],
                    [
                        [
                            "name" => "Eve",
                        ],
                    ],
                ],
            ],
            [
                "Stuff" => [
                    ["name" => "name", "type" => "string"],
                ],
                "Things" => [
                    ["name" => "what", "type" => "Stuff[8][5]"],
                ],
            ],
            "0xed3eb2f09fad610e8805f43a34704858e1ad7f3cd12b61e712b402be370b9001",
        ],
        [
            [
                "what" => "0x31323334353637383930616263646566",
            ],
            [
                "Things" => [
                    ["name" => "what", "type" => "bytes16"],
                ],
            ],
            "0x6825950a843718a846bf289599316a041180fd20d942ae0ca6106396ff797655",
        ],
    ];

    /**
     * setUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->typedDataEncoder = new TypedDataEncoder();
    }

    /**
     * testHashDomainPass
     * 
     * @return void
     */
    public function testHashDomainPass()
    {
        $typedDataEncoder = $this->typedDataEncoder;
        foreach ($this->hashDomainPassTests as $test) {
            $result = $typedDataEncoder->hashDomain($test[0]);
            $this->assertEquals($test[1], $result);
        }
    }

    /**
     * testHashDomainFail
     * 
     * @return void
     */
    public function testHashDomainFail()
    {
        $typedDataEncoder = $this->typedDataEncoder;
        foreach ($this->hashDomainFailTests as $test) {
            $this->expectException($test[1]);
            $result = $typedDataEncoder->hashDomain($test[0]);
        }
    }

    /**
     * testHashEIP712Message
     * 
     * @return void
     */
    public function testHashEIP712Message()
    {
        $typedDataEncoder = $this->typedDataEncoder;
        foreach ($this->hashEIP712MessageTests as $test) {
            $result = $typedDataEncoder->hashEIP712Message($test[1], $test[0]);
            $this->assertEquals($test[2], $result);
        }
    }

    /**
     * testEncodeTypedDataFixtures
     * 
     * @return void
     */
    public function testEncodeTypedDataFixtures()
    {
        // load test fixtures
        $testFixtures = $this->loadFixtureJsonFile(dirname(__DIR__) . '/fixtures/typed-data.json');
        $typedDataEncoder = $this->typedDataEncoder;
        foreach ($testFixtures as $test) {
            $result = $typedDataEncoder->encodeTypedData($test['domain'], $test['types'], $test['data']);
            $this->assertEquals($test['digest'], Utils::sha3($result));
        }
    }
}