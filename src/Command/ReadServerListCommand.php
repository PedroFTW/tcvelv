<?php

namespace App\Command;

use App\Helper\ServerHelper;
use App\Objects\Server;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

#[AsCommand(
    'leaseweb:read-server-list',
    'Reads a server list into xxx'
)]
class ReadServerListCommand extends Command
{
    public const STATIC_JSON_PATH = 'static/server_list.json';

    private Filesystem $filesystem;
    private bool $firstLineIgnored = false;
    private array $serverList = [];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filepath');
        if (!$this->filesystem->exists($filePath)) {
            throw new FileNotFoundException("File $filePath not found!");
        }

        $output->writeln("====== Reading $filePath ======");

        $csvhandle = fopen($filePath, "r");

        $serverHelper = new ServerHelper();

        try {
            while (($data = fgetcsv($csvhandle)) !== false) {
                if ($input->getOption('ignore-first-line') && !$this->firstLineIgnored) {
                    $this->firstLineIgnored = true;
                    continue;
                }

                $serverObj = new Server($data);
                $serverHelper->validateServerObj($serverObj);

                $this->serverList[] = $serverObj;
            }
        } catch (\Exception $e) {
            $output->writeln("====== Fatal error! No registries were updated! ======");
            throw $e;
        }

        fclose($csvhandle);

        $output->writeln("====== Updating the Server List ======");

        if (!$this->filesystem->exists(self::STATIC_JSON_PATH)) {
            $this->filesystem->touch(self::STATIC_JSON_PATH);
        }

        $this->filesystem->copy(self::STATIC_JSON_PATH, self::STATIC_JSON_PATH . ".old");

        $jsonhandle = fopen(self::STATIC_JSON_PATH, "w");
        fwrite($jsonhandle, json_encode($this->serverList));
        fclose($jsonhandle);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument(
            'filepath',
            InputArgument::REQUIRED,
            'The filepath of the .csv file'
        );
        $this->addOption(
            'ignore-first-line',
            'i',
            InputOption::VALUE_NONE,
            'If passed, ignores the first line of the file'
        );
    }
}
