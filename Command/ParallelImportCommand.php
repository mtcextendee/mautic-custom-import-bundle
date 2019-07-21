<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomImportBundle\Command;

use Mautic\CoreBundle\Command\ModeratedCommand;
use Mautic\CoreBundle\Helper\PathsHelper;
use Mautic\LeadBundle\Model\ImportModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class ParallelImportCommand extends ModeratedCommand
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
     * ParallelImportCommand constructor.
     *
     * @param ImportModel $importModel
     * @param PathsHelper $pathsHelper
     */
    public function __construct(ImportModel $importModel, PathsHelper $pathsHelper)
    {
        $this->importModel = $importModel;
        $this->pathsHelper = $pathsHelper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mautic:import:parallel')
            ->setDescription('Parallel import for Mautic')
            ->setHelp('This command processed parallel imports')
            ->addOption(
                '--limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Limit lines to import. Default 1000',
                1000
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = __CLASS__;
        if (!$this->checkRunStatus($input, $output, $key)) {
            return 0;
        }

        /** @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator */
        $translator = $this->getContainer()->get('translator');

        $limit         = $input->getOption('limit');
        $parallelLimit = $this->importModel->getParallelImportLimit();
        $processSet    = [];
        for ($i = 0; $i < $parallelLimit; $i++) {
            $builder = (new ProcessBuilder())
                ->setPrefix('php')
                ->add($this->pathsHelper->getSystemPath('app').'/console')
                ->add('mautic:import')
                ->add('--limit='.$limit)
                ->add('--env='.MAUTIC_ENV);;

            $process = $builder->getProcess();
            $process->start();
            $processSet[] = $process;
            sleep(1);
        }

        $output->writeln($translator->trans('mautic.custom.import.csv.import.parallel.start', ['%s'=>$parallelLimit]));
        while (!empty($processSet)) {
            foreach ($processSet as $index => &$process) {
                $process->checkTimeout();
                // Not running, let's display result
                if (!$process->isRunning()) {
                    unset($processSet[$index]);
                    if (!$process->isSuccessful()) {

                        $output->writeln($translator->trans('mautic.custom.import.csv.import.parallel.fail', ['%s'=>$index+1]));
                        $output->write($process->getErrorOutput());
                    } else {
                        $output->writeln($translator->trans('mautic.custom.import.csv.import.parallel.sucess',['%s'=>$index+1]));
                    }
                }
            }
            // Počkáme 100 ms do další kontroly běžících procesů
            usleep(100000);
        }

        return 0;
    }
}
