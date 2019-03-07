<?php

namespace App\Listener\Storage;

use App\Entity\Storage\File;

/**
 * Class TreeListener
 *
 * @package App\Listener\Storage
 */
class FileListener
{
    /**
     * @param File $file
     */
    public function preUpdate(File $file)
    {
        $options = $file->getOptions();

        foreach ($options as $option) {
            $methodName = 'transform' . ucfirst($option);

            if (method_exists($this, $methodName)) {
                $file->setName($this->$methodName($file->getName()));
            }
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function transformTranslit(string $value): string
    {
        // First, transliterate any chars to latin UTF-8
        $value = (\Transliterator::create('Any-Latin'))->transliterate($value);

        // Second, tran
        return (\Transliterator::create('Latin-ASCII'))->transliterate($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function transformUnderscore(string $value): string
    {
        return preg_replace('/\s/', '_', $value);
    }
}