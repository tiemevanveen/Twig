<?php

/*
 * This file is part of the GeckoPackages.
 *
 * (c) GeckoPackages https://github.com/GeckoPackages
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace GeckoPackages\Twig\Text;

/**
 * Supports the Roman numerals in modern notation (strict) or loose notation.
 *
 * Roman | Value
 * ------+-------
 *  I    |    1
 *  IV   |    4
 *  V    |    5
 *  IX   |    9
 *  X    |   10
 *  XL   |   40
 *  L    |   50
 *  XC   |   90
 *  C    |  100
 *  CD   |  400
 *  D    |  500
 *  CM   |  900
 *  M    | 1000
 *
 * Note: large numbers, for example in 'apostrophus' and 'vinculum' are not supported.
 *
 * In 'strict' mode:
 * - Symbols are combined from left to right, high to low values.
 * - Symbols are not repeated more than 3 times.
 * - C may b placed after D or M.
 * - X may be placed before L or C.
 * - I may be placed before V or X.
 * - This makes the maximum number supported 'MMMCMXCIX'.
 *
 * In 'loose' mode, follows 'strict' mode with the following exceptions:
 * - Symbols may be repeated more than 3 times.
 * - There is no more maximum number.
 *
 * In 'loose-order', follows 'loose' mode with the following exception:
 * - Symbols may appear in any order.
 *
 * More details:
 * https://en.wikipedia.org/wiki/Roman_numerals
 *
 * @internal
 *
 * @author SpacePossum
 */
trait RomanNumeralsTrait
{
    /**
     * @param string   $string    String to modify.
     * @param string   $matchMode 'strict', 'loose' or 'loose-order'.
     * @param callable $callBack  Actual code that does the manipulation.
     *
     * @return string
     *
     * @throws \Twig_Error_Runtime
     */
    private function numeralRomanMatchCallBack($string, $matchMode, callable $callBack)
    {
        switch ($matchMode) {
            case 'strict' : {
                // TODO: Empty strings are also captured. Remove this without performance impact
                $matchMode = '#\b(M{0,3}(?:CM|CD|D?C{0,3})(?:XC|XL|L?X{0,3})(?:IX|IV|V?I{0,3})\b)#i';
                break;
            }
            case 'loose' : {
                // TODO: Empty strings are also captured. Remove this without performance impact
                $matchMode = '#\b(M*(?:CM|CD|D?C*)(?:XC|XL|L?X*)(?:IX|IV|V?I*)\b)#i';
                break;
            }
            case 'loose-order' : {
                $matchMode = '#\b([MDCLXVI]+)\b#i';
                break;
            }
            default : {
                throw new \Twig_Error_Runtime(sprintf('Unsupported match mode "%s".', $matchMode));
            }
        }

        return preg_replace_callback($matchMode, $callBack, $string);
    }
}
