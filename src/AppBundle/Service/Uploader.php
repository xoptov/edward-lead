<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const DIRECTORY_AUDIO = 'audio';
    const DIRECTORY_IMAGE = 'image';

    const IMAGE_FORMAT = 'png';

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
     *
     * @throws \ImagickException
     */
    public function store(UploadedFile $uploadedFile, string $directory): array
    {
        $storePath = $this->storePath . DIRECTORY_SEPARATOR . $directory;

        if (in_array($uploadedFile->getMimeType(), ['image/jpeg', 'image/pjpeg'])) {
            $fileName = md5($uploadedFile->getFilename() . $uploadedFile->getSize(), false) . '.' . self::IMAGE_FORMAT;
            $imagick = new \Imagick($uploadedFile->getRealPath());
            $imagick->setFormat(self::IMAGE_FORMAT);
            $imagick->writeImage($storePath . DIRECTORY_SEPARATOR . $fileName);
        } else {
            $fileName = md5($uploadedFile->getFilename() . $uploadedFile->getSize(), false) . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move($storePath, $fileName);
        }

        $result = [
            'filename' => $fileName,
            'path' => $directory . DIRECTORY_SEPARATOR . $fileName
        ];

        return $result;
    }
}