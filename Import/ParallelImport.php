<?php

namespace MauticPlugin\MauticCustomImportBundle\Import;

use Mautic\CoreBundle\Helper\PathsHelper;
use Mautic\LeadBundle\Model\ImportModel;
use Symfony\Component\Process\ProcessBuilder;

class ParallelImport
{
    /**
     * @var ImportModel
     */
    private $importModel;

    /**
     * @var PathsHelper
     */
    private $pathsHelper;

    /**
     * ParallelImport constructor.
     *
     * @param ImportModel $importModel
     * @param PathsHelper $pathsHelper
     */
    public function __construct(ImportModel $importModel, PathsHelper $pathsHelper)
    {
        $this->importModel = $importModel;
        $this->pathsHelper = $pathsHelper;
    }


    public function parallelImport(array $options)
    {
        $parallelLimit = $this->importModel->getParallelImportLimit();
        $processSet    = [];
        for ($i = 0; $i < $parallelLimit; $i++) {
            if (!$this->importModel->checkParallelImportLimit()) {
                break;
            }
            $builder = (new ProcessBuilder())
                ->setPrefix('php')
                ->setTimeout(9999)
                ->add($this->pathsHelper->getSystemPath('app', true).'/console')
                ->add('mautic:import')
                ->add('--limit='.$options['limit'])
                ->add('--env='.MAUTIC_ENV);;

            $process = $builder->getProcess();
            $process->start();
            $processSet[] = $process;
            sleep(5);
        }

        return $processSet;

    }
}
