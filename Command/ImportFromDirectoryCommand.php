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
use MauticPlugin\MauticCustomImportBundle\Import\CustomImport;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFromDirectoryCommand extends ModeratedCommand
{

    /**
     * @var CustomImport
     */
    private $customImport;

    /**
     * CustomImportCommand constructor.
     *
     * @param CustomImport $customImport
     */
    public function __construct(CustomImport $customImport)
    {
        $this->customImport = $customImport;
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

        /** @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator */
        $translator = $this->getContainer()->get('translator');

        try {
            foreach ($this->customImport->getCsvFiles() as $csvFile) {
                $this->customImport->importFromFile($csvFile);
                $output->writeln(
                    $translator->trans('mautic.custom.import.csv.file.import.create', ['%s' => $csvFile->getRealPath()])
                );
            }
        } catch (InvalidImportException $importException) {
            $output->writeln($importException->getMessage());
        }

        return 0;
    }
}
