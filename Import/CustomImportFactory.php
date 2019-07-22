<?php

namespace MauticPlugin\MauticCustomImportBundle\Import;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CustomImportFactory constructor.
     *
     * @param ImportFromDirectory $importFromDirectory
     * @param ParallelImport      $parallelImport
     * @param IntegrationHelper   $integrationHelper
     * @param EntityManager       $entityManager
     */
    public function __construct(
        ImportFromDirectory $importFromDirectory,
        ParallelImport $parallelImport,
        IntegrationHelper $integrationHelper,
        EntityManager $entityManager
    ) {
        $this->importFromDirectory = $importFromDirectory;
        $this->parallelImport      = $parallelImport;
        $this->integrationHelper   = $integrationHelper;
        $this->entityManager = $entityManager;
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
    public function processParallelImport()
    {
        $options = $this->getIntegrationOptions();

        return $this->parallelImport->parallelImport($options);
    }

    /**
     * @throws InvalidImportException
     */
    public function removeContactsTags()
    {
        $options = $this->getIntegrationOptions();
        if (!empty($options['tagsToRemove'])) {

            $qb= $this->entityManager->getConnection()->createQueryBuilder();

            $ids = $this->entityManager->getConnection()->createQueryBuilder()
                ->select('id')
                ->from(MAUTIC_TABLE_PREFIX.'lead_tags')
                ->where(
                    $qb->expr()->in('tag', ':tag')
                )
                ->setParameter('tag', $options['tagsToRemove'], Connection::PARAM_STR_ARRAY)
                ->execute()->fetchAll();

            $qb->delete(MAUTIC_TABLE_PREFIX.'lead_tags_xref')
                ->where(
                    $qb->expr()->in('tag_id', array_column($ids, 'id'))
                )
                ->execute();

            return $options['tagsToRemove'];
        }
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
