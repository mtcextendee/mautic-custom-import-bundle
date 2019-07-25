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
use MauticPlugin\MauticCustomImportBundle\Exception\InvalidImportException;
use MauticPlugin\MauticCustomImportBundle\Import\CustomImportFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ImportFromDirectoryCommand extends ModeratedCommand
{
    /**
     * @var CustomImportFactory
     */
    private $customImportFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ImportFromDirectoryCommand constructor.
     *
     * @param CustomImportFactory $customImportFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(CustomImportFactory $customImportFactory, TranslatorInterface $translator)
    {
        $this->customImportFactory = $customImportFactory;
        $this->translator = $translator;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mautic:import:directory')
            ->setDescription('Import CSV files from directory');

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

        try {
            $files = $this->customImportFactory->createImportFromDirectory();
            $output->writeln(
                $this->translator->trans('mautic.custom.import.csv.file.import.create', ['%s' => count($files)])
            );
        } catch (InvalidImportException $importException) {
            $output->writeln($importException->getMessage());
        }
        $this->completeRun();

        return 0;
    }
}
