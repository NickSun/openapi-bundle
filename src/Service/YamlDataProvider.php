<?php

namespace Nicksun\OpenApi\Service;

use DirectoryIterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDataProvider
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->params = $parameterBag;
    }

    public function getDefinitions(): array
    {
        $definitionsDir = sprintf(
            '%s/config/%s',
            $this->params->get('kernel.project_dir'),
            $this->params->get('open_api.definitions_dir'),
        );
        $data = $this->scanDir($definitionsDir);
        $data = $this->arrangeDefinitions($data);
        $content = $this->arrayToString($data);

        return Yaml::parse($content);
    }

    private function scanDir(string $dir): array
    {
        $fileContent = [];

        /** @var DirectoryIterator $f */
        foreach (new DirectoryIterator($dir) as $f) {
            if ($f->isDot()) {
                continue; // skip . and ..
            }

            if ($f->isFile() && \in_array($f->getExtension(), ['yaml', 'yml'], true)) {
                $fileContent = array_merge_recursive($fileContent, $this->parseFile($f->getPathname()));
            } elseif ($f->isDir()) {
                $fileContent = array_merge_recursive($fileContent, $this->scanDir($f->getPathname()));
            }
        }

        return $fileContent;
    }

    private function parseFile(string $filePath): array
    {
        $fileData = [];
        $initIndent = null;
        $prevIndent = 0;
        $prevPosition = [];
        $rawData = file($filePath);

        if (false === $rawData) {
            return $fileData;
        }

        foreach ($rawData as $rawLine) {
            $line = trim($rawLine);

            if ('' === $line || str_starts_with($line, '#')) {
                continue;
            }

            if (1 === preg_match('/^ +(?!\S)/', $rawLine, $matches)) {
                $indent = \strlen($matches[0]) + 1;
                $initIndent ??= $indent;
                $pos = $indent > $prevIndent ? $prevIndent : $indent - $initIndent;

                if (1 === preg_match('/:$|: +&/', $line)) {
                    $prevPosition[$pos][$rawLine] = [];
                    $prevPosition[$indent] = &$prevPosition[$pos][$rawLine];
                    $this->removeExcessLinks($prevPosition, $indent);
                } elseif (\array_key_exists($pos, $prevPosition)) {
                    $prevPosition[$pos][] = $rawLine;
                } else {
                    $prevPosition[$pos - $initIndent][] = $rawLine;
                }

                $prevIndent = $indent;
            } else {
                $fileData[$rawLine] = [];
                $prevIndent = 0;
                $prevPosition[$prevIndent] = &$fileData[$rawLine];
                $this->removeExcessLinks($prevPosition, $prevIndent);
            }
        }

        return $fileData;
    }

    private function arrayToString(array $data): string
    {
        $result = '';

        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $result .= $key.$this->arrayToString($value).\PHP_EOL;
            } else {
                $result .= $value.\PHP_EOL;
            }
        }

        return $result;
    }

    private function arrangeDefinitions(array $data): array
    {
        uksort($data, static function ($a, $b) {
            $aIsAnchor = preg_match('/: +&/', $a);
            $bIsAnchor = preg_match('/: +&/', $b);

            if (0 === $aIsAnchor && 1 === $bIsAnchor) {
                return 1;
            }

            if (1 === $aIsAnchor && 0 === $bIsAnchor) {
                return -1;
            }

            return 0;
        });

        return $data;
    }

    private function removeExcessLinks(array &$links, int $position): void
    {
        foreach ($links as $key => $value) {
            if ($key <= $position) {
                continue;
            }

            unset($links[$key]);
        }
    }
}
