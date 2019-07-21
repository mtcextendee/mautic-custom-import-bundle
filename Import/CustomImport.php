<?php

namespace MauticPlugin\MauticCustomImportBundle\Import;

use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\LeadBundle\Entity\Import;
use Mautic\LeadBundle\Model\ImportModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticCustomImportBundle\Exception\InvalidImportException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CustomImport
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
     * @var array|\Iterator|SplFileInfo[]
     */
    private $csvFiles;

    /**
     * @var object|null
     */
    private $importTemplate;

    /**
     * @var string
     */
    private $importDir;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * CustomImport constructor.
     *
     * @param IntegrationHelper $integrationHelper
     * @param ImportModel       $importModel
     * @param CorePermissions   $corePermissions
     *
     * @throws InvalidImportException
     */
    public function __construct(
        IntegrationHelper $integrationHelper,
        ImportModel $importModel,
        CorePermissions $corePermissions
    ) {
        $integration = $integrationHelper->getIntegrationObject('CustomImport');

        if (false === $integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new InvalidImportException('Integration is disabled');
        }

        /*if (!$corePermissions->isGranted('lead:leads:create')) {
            throw new InvalidImportException('No permission to create contact');
        }*/

        $this->integrationOptions = $integration->mergeConfigToFeatureSettings();
        $this->importModel        = $importModel;

    }

    /**
     * @param SplFileInfo $file
     */
    public function importFromFile(SplFileInfo $file)
    {
        $this->csvFiles           = $this->getCsvFiles();
        $this->importTemplate     = $this->importModel->getEntity($this->integrationOptions['template_from_import']);
        $this->importDir          = $this->importModel->getImportDir();
        $this->fileSystem         = new Filesystem();
        if (!$this->importTemplate) {
            throw new InvalidImportException('Import template entity doesn\'t exists');
        }

        $fileName    = $this->importModel->getUniqueFileName();
        $newFilePath = $this->importDir.'/'.$fileName;
        // remove If file already exists
        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }
        // move csv file to import directory
        $this->fileSystem->rename($file->getRealPath(), $newFilePath);

        // Create an import object
        $import = new Import();
        $import
            ->setMatchedFields($this->importTemplate->getMatchedFields())
            ->setDefault('owner', null)
            ->setHeaders($this->importTemplate->getHeaders())
            ->setParserConfig($this->importTemplate->getParserConfig())
            ->setDir($this->importDir)
            ->setLineCount($this->getLinesCountFromPath($newFilePath))
            ->setFile($fileName)
            ->setOriginalFile($file->getFilename())
            ->setStatus($import::QUEUED);
        $this->importModel->saveEntity($import);
        sleep(1);
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


    /**
     * @return array|\Iterator|SplFileInfo[]
     */
    public function getCsvFiles()
    {
        return $this->loadCsvFilesFromPath($this->integrationOptions['path_to_directory_csv']);
    }

}
