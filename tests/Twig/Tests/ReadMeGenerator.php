<?php

/*
 * This file is part of the GeckoPackages.
 *
 * (c) GeckoPackages https://github.com/GeckoPackages
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class ReadMeGenerator
{
    /**
     * @return string
     */
    public function generateReadMe()
    {
        $docDir = __DIR__.'/../../../docs/';
        $docs = array();
        foreach (new DirectoryIterator($docDir) as $item) {
            if (!$item->isDir() || $item->isDot()) {
                continue;
            }

            $package = $item->getFilename();
            $docs[$package] = array();
            foreach (new DirectoryIterator($item->getRealPath()) as $doc) {
                if ($doc->isDir() || $doc->isDot()) {
                    continue;
                }

                $classShort = ucfirst(substr($doc->getFilename(), 0, -3)).ucfirst(substr($package, 0, -1));
                $docs[$package][$doc->getRealPath()] = array(
                    'fileName' => $doc->getFilename(),
                    'file' => $doc->getRealPath(),
                    'doc' => $this->getDoc($doc->getRealPath()),
                    'class' => array(
                        'short' => $classShort,
                        'full' => sprintf('GeckoPackages\\Twig\\%s\\%s', ucfirst($package), $classShort),
                    ),
                );
            }

            uksort(
                $docs[$package],
                function ($file1, $file2) {
                    return strcasecmp($file1, $file2);
                }
            );
        }

        ksort($docs);

        $doc = '';
        $listing = '';
        foreach ($docs as $packageName => $package) {
            $packageName = ucfirst($packageName);
            $listing .= sprintf("\n#### %s\n", $packageName);
            $doc .= sprintf("\n## %s\n\n", $packageName);

            foreach ($package as $values) {
                $docs = $values['doc'];
                $listing .= sprintf("- **%s**\n  %s\n", $docs['title'], $docs['short']);
                $doc .= sprintf("### %s\n###### %s\n%s\n\n", $values['class']['short'], $values['class']['full'], $docs['full']);
            }
        }

        $readMeTemplate = <<<EOF
#### GeckoPackages

# Twig extensions

Provides additional filters and tests to be used with Twig (http://twig.sensiolabs.org).

[![Build Status](https://travis-ci.org/GeckoPackages/Twig.svg)](https://travis-ci.org/GeckoPackages/Twig)
#GENERATED_LISTING#
See below for details.

### Requirements

PHP 5.4.0 (for Traits and `callable` type)

### Install

The package can be installed using Composer (https://getcomposer.org/).
Add the package to your `composer.json`.

```
"require-dev": {
    "gecko-packages/twig" : "1.0"
}
```

# Package listing
#GENERATED_BODY#
### License

The project is released under the MIT license, see the LICENSE file.

EOF;
        $readMeTemplate = str_replace('#GENERATED_LISTING#', $listing, $readMeTemplate);

        return str_replace('#GENERATED_BODY#', $doc, $readMeTemplate);
    }

    private function getDoc($fileName)
    {
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new \UnexpectedValueException(sprintf('Cannot read doc file "%s".', $fileName));
        }

        $docs = array();
        $raw = file_get_contents($fileName);
        $lines = explode("\n", $raw);

        if (count($lines) < 1) {
            throw new \UnexpectedValueException(sprintf('Missing line 1 (title) in doc file "%s".', $fileName));
        }

        $docs['title'] = trim($lines[0]);
        if ('### ' !== substr($docs['title'], 0, 4)) {
            throw new \UnexpectedValueException(sprintf('Wrong format for title, should start with "### " in doc file "%s".', $fileName));
        }

        $docs['title'] = substr($docs['title'], 4);
        if (strlen($docs['title']) < 2 || '.' === $docs['title'][strlen($docs['title']) - 1]) {
            throw new \UnexpectedValueException(sprintf('Wrong format for title in doc file "%s".', $fileName));
        }

        if (count($lines) < 4) {
            throw new \UnexpectedValueException(sprintf('Missing line 3 (short description) in doc file "%s".', $fileName));
        }

        if ('' !== $lines[1]) {
            throw new \UnexpectedValueException(sprintf('Expected line 2 to be blank, got "%s".', $lines[1]));
        }

        $docs['short'] = trim($lines[2]);
        if (strlen($docs['short']) < 3 || '.' !== $docs['short'][strlen($docs['short']) - 1]) {
            throw new \UnexpectedValueException(sprintf('Wrong format for short description (line 3) in doc file "%s".', $fileName));
        }

        if ('' !== $lines[3]) {
            throw new \UnexpectedValueException(sprintf('Expected line 4 to be blank, got "%s".', $lines[1]));
        }

        $full = '';
        $linesCount = count($lines);
        if ($linesCount < 4) {
            throw new \UnexpectedValueException(sprintf('Wrong format for description (line 4) in doc file "%s".', $fileName));
        }

        $exists = array('#### Examples', '```Twig', '```');
        for ($i = 4; $i < $linesCount; ++$i) {
            if (false !== $key = array_search($lines[$i], $exists, true)) {
                unset($exists[$key]);
            }
            $full .= $lines[$i]."\n";
        }

        if (0 !== count($exists)) {
            throw new \UnexpectedValueException(sprintf('Missing lines "%s". In doc file "%s".', implode(',', $exists), $fileName));
        }

        if ('' !== $lines[$linesCount - 1]) {
            throw new \UnexpectedValueException(sprintf('Last line should be blank, got "%s". In doc file "%s".', $lines[$linesCount - 1], $fileName));
        }

        $docs['full'] = trim($full);

        return $docs;
    }
}
