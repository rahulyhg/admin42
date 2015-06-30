<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Command\Media;

use Admin42\Model\Media;
use Core42\Command\AbstractCommand;

class UploadCommand extends AbstractCommand
{
    /**
     * @var array
     */
    private $uploadData;

    /**
     * @param array $uploadData
     * @return $this
     */
    public function setUploadData(array $uploadData)
    {
        $this->uploadData = $uploadData;

        return $this;
    }

    /**
     * @param array $values
     * @throws \Exception
     */
    public function hydrate(array $values)
    {
        $this->setUploadData($values['file']);
    }

    /**
     * @return mixed|void
     */
    protected function execute()
    {
        $dateTime = new \DateTime();
        $source = $this->uploadData['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $media = new Media();
        $media->setFilename($this->uploadData['name'])
            ->setTitle($this->uploadData['name'])
            ->setMeta(json_encode([]))
            ->setDirectory(dirname($source) . DIRECTORY_SEPARATOR)
            ->setMimeType(finfo_file($finfo, $source))
            ->setSize(sprintf("%u", filesize($source)))
            ->setUpdated($dateTime)
            ->setCreated($dateTime);

        $this->getTableGateway('Admin42\Media')->insert($media);
    }
}