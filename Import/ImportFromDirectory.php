<?php

namespace MauticPlugin\MauticCustomImportBundle\Import;

use Mautic\LeadBundle\Entity\Import;
use Mautic\LeadBundle\Model\ImportModel;
use MauticPlugin\MauticCustomImportBundle\Exception\InvalidImportException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImportFromDirectory
{
    /**
     * @var array
     */
    private $integrationOptions;

    /**
     * @var ImportModel
     */
    private $importModel;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * CreateImportFromDirectory constructor.
     *
     * @param ImportModel $importModel
     */
    public function __construct(
        ImportModel $importModel
    ) {
        $this->importModel        = $importModel;
    }

    /**
     * @param array $options
     *
     * @return array|\Iterator|SplFileInfo[]
     * @throws InvalidImportException
     */
    public function importFromFiles(array $options)
    {
        $files = $this->loadCsvFilesFromPath($options['path_to_directory_csv']);
        $importTemplate = $this->importModel->getEntity($options['template_from_import']);
        if (!$importTemplate) {
            throw new InvalidImportException('Import template entity doesn\'t exists');
        }

        $this->fileSystem         = new Filesystem();
        foreach ($files as $file) {
            $this->importFromFile($file, $importTemplate);
        }

        return $files;
    }

    /**
     * @param SplFileInfo $file
     * @param Import      $importTemplate
     */
    private function importFromFile(SplFileInfo $file, Import $importTemplate)
    {
        $importDir          = $this->importModel->getImportDir();
        $fileName    = $this->importModel->getUniqueFileName();
        $newFilePath = $importDir.'/'.$fileName;
        // remove If file already exists
        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }
        // move csv file to import directory
        $this->fileSystem->rename($file->getRealPath(), $newFilePath);

        // Create an import object
        $import = new Import();
        $import
            ->setProperties($importTemplate->getProperties())
            ->setDefault('owner', null)
            ->setHeaders($importTemplate->getHeaders())
            ->setParserConfig($importTemplate->getParserConfig())
            ->setDir($importDir)
            ->setLineCount($this->getLinesCountFromPath($newFilePath))
            ->setFile($fileName)
            ->setOriginalFile($file->getFilename())
            ->setStatus($import::QUEUED);
        $this->importModel->saveEntity($import);
    }

    /**
     * @param $path
     *
     * @return array|\Iterator|\Symfony\Component\Finder\SplFileInfo[]
     * @throws InvalidImportException
     */
    private function loadCsvFilesFromPath($path)
    {
        $finder = (new Finder())
            ->in($path)
            ->name('*.csv')
            ->getIterator();
        $files  = iterator_to_array($finder);
        if (empty($files)) {
            throw new InvalidImportException(sprintf("Not find any files in  %s directory", $path));
        }

        return $files;
    }

    /**
     * @param $path
     *
     * @return int
     */
    private function getLinesCountFromPath($path)
    {
        $fileData = new \SplFileObject($path);
        $fileData->seek(PHP_INT_MAX);
        return $fileData->key();
    }


}
