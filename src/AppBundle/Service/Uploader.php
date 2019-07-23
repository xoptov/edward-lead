<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const DIRECTORY_AUDIO = 'audio';

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
     * @return array
     */
    public function store(UploadedFile $uploadedFile, string $directory): array
    {
        $fileName = md5($uploadedFile->getFilename() . $uploadedFile->getSize(), false) . '.' . $uploadedFile->guessExtension();
        $storePath = $this->storePath . DIRECTORY_SEPARATOR . $directory;

        $uploadedFile->move($storePath, $fileName);

        $result = [
            'filename' => $fileName,
            'path' => $directory . DIRECTORY_SEPARATOR . $fileName
        ];

        return $result;
    }
}