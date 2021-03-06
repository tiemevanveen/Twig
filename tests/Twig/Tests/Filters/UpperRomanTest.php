<?php

/*
 * This file is part of the GeckoPackages.
 *
 * (c) GeckoPackages https://github.com/GeckoPackages
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class UpperRomanTest extends AbstractFilterTest
{
    /**
     * @expectedException Twig_Error_Runtime
     * @expectedExceptionMessage Unsupported match mode "invalid".
     */
    public function testInvalidMatchMode()
    {
        $filter = $this->getFilter();
        $call = $filter->getCallable();
        $call('invalid', 'invalid');
    }
}
