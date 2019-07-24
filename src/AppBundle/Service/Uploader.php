<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const DIRECTORY_AUDIO = 'audio';
    const DIRECTORY_IMAGE = 'image';

    /**
     * @var string
     */
    private $storePath;

    /**
     * @param string $storePath
     */
    public function __construct(string $storePath)
    {
        $this->storePath = $storePath;
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
        $storePath = $this->storePath . DIRECTORY_SEPARATOR . $directory;

        $uploadedFile->move($storePath, $fileName);

        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }
}