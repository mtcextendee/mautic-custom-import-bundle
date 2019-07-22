<?php

namespace MauticPlugin\MauticCustomImportBundle\Import;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticCustomImportBundle\Exception\InvalidImportException;
use Symfony\Component\Finder\SplFileInfo;

class CustomImportFactory
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var ImportFromDirectory
     */
    private $importFromDirectory;

    /**
     * @var ParallelImport
     */
    private $parallelImport;

    /**
     * CustomImportFactory constructor.
     *
     * @param ImportFromDirectory $importFromDirectory
     * @param ParallelImport      $parallelImport
     * @param IntegrationHelper   $integrationHelper
     */
    public function __construct(
        ImportFromDirectory $importFromDirectory,
        ParallelImport $parallelImport,
        IntegrationHelper $integrationHelper
    ) {
        $this->importFromDirectory = $importFromDirectory;
        $this->parallelImport      = $parallelImport;
        $this->integrationHelper   = $integrationHelper;
    }

    /**
     * @return array|\Iterator|SplFileInfo[]
     * @throws InvalidImportException
     */
    public function createImportFromDirectory()
    {
        $options = $this->getIntegrationOptions();
        return $this->importFromDirectory->importFromFiles($options);
    }

    /**
     * @return array
     * @throws InvalidImportException
     */
    public function parallelImport()
    {
        $options = $this->getIntegrationOptions();

        return $this->parallelImport->parallelImport($options);
    }

    /**
     * @return array
     * @throws InvalidImportException
     */
    private function getIntegrationOptions()
    {
        $integration = $this->integrationHelper->getIntegrationObject('CustomImport');

        if (false === $integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new InvalidImportException('Integration is disabled');
        }

        return $integration->mergeConfigToFeatureSettings();
    }
}
