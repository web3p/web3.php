<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Web3\Validators\QuantityValidator;
use Web3\Validators\TransactionValidator;
use Web3\Validators\CallValidator;

class QuantityValidatorInputTest extends TestCase
{
    /**
     * @dataProvider invalidQuantityProvider
     */
    public function testRejectsInvalidQuantities($value)
    {
        $this->assertFalse(QuantityValidator::validate($value));
    }

    public function invalidQuantityProvider()
    {
        return [
            'empty hex prefix' => ['0x'],
            'trailing newline' => ["0x1\n"],
            'null' => [null],
            'true' => [true],
            'false' => [false],
            'array' => [[]],
            'object' => [new \stdClass()],
            'non-hex digit' => ['0xg'],
        ];
    }

    /**
     * @dataProvider malformedTransactionQuantityProvider
     */
    public function testRejectsMalformedTransactionQuantities($field, $value)
    {
        $transaction = [
            'from' => '0x0000000000000000000000000000000000000000',
            'to' => '0x0000000000000000000000000000000000000001',
            $field => $value,
        ];

        $this->assertFalse(TransactionValidator::validate($transaction));
        $this->assertFalse(CallValidator::validate($transaction));
    }

    public function malformedTransactionQuantityProvider()
    {
        $cases = [];
        foreach (['gas', 'gasPrice', 'value', 'nonce'] as $field) {
            $cases[$field . ' with empty hex prefix'] = [$field, '0x'];
            $cases[$field . ' with trailing newline'] = [$field, "0x1\n"];
            $cases[$field . ' with array'] = [$field, []];
        }
        return $cases;
    }

    public function testPreservesEmptyHexDataAndZeroQuantities()
    {
        $transaction = [
            'from' => '0x0000000000000000000000000000000000000000',
            'to' => '0x0000000000000000000000000000000000000001',
            'gas' => '0x0',
            'value' => 0,
            'data' => '0x',
        ];

        $this->assertTrue(TransactionValidator::validate($transaction));
        $this->assertTrue(CallValidator::validate($transaction));
    }

    /**
     * @dataProvider supportedQuantityProvider
     */
    public function testPreservesSupportedQuantities($value)
    {
        $this->assertTrue(QuantityValidator::validate($value));
    }

    public function supportedQuantityProvider()
    {
        return [
            'zero' => [0],
            'integer' => [21000],
            'legacy float' => [1.5],
            'hex zero' => ['0x0'],
            'hex mixed case' => ['0xaBcD'],
            'large hex quantity' => ['0xffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'],
        ];
    }
}
