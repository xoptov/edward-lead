<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const DIRECTORY_AUDIO = 'audio';

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string       $directory
     *
     * @return string
     */
    public function store(UploadedFile $uploadedFile, string $directory): string
    {
        $fileName = md5($uploadedFile->getFilename() . $uploadedFile->getSize(), false) . '.' . $uploadedFile->guessExtension();
        $storePath = $this->basePath . DIRECTORY_SEPARATOR . $directory;

        $uploadedFile->move($storePath, $fileName);

        return $storePath . DIRECTORY_SEPARATOR . $fileName;
    }
}