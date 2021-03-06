<?php

/*
 * This file is part of the GeckoPackages.
 *
 * (c) GeckoPackages https://github.com/GeckoPackages
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class SITest extends AbstractFilterTest
{
    /**
     * @expectedException Twig_Error_Runtime
     * @expectedExceptionMessage Unsupported symbol "X".
     */
    public function testInvalidSymbol1()
    {
        $filter = $this->getFilter();
        $call = $filter->getCallable();
        $call(new Twig_Environment(), 1, 'X');
    }

    /**
     * @expectedException Twig_Error_Runtime
     * @expectedExceptionMessage Unsupported symbol "XYZ".
     */
    public function testInvalidSymbol2()
    {
        $filter = $this->getFilter();
        $call = $filter->getCallable();
        $call(new Twig_Environment(), 2, 'XYZ');
    }

    public function testRounding()
    {
        $filter = $this->getFilter();
        $call = $filter->getCallable();
        $result = $call(new Twig_Environment(), 0.0009999999999999999, 'auto', '%number% %symbol%', 16, ',', '');
        $this->assertTrue(is_string($result));
        $this->assertStringStartsWith('999,9999999999', $result);
        $this->assertStringEndsWith(' u', $result);
    }
}
