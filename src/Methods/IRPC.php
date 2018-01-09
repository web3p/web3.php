<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Methods;

interface IRPC
{
    /**
     * __toString
     * 
     * @return array
     */
    public function __toString();

    /**
     * toPayload
     * 
     * @return array
     */
    public function toPayload();
}