<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\DevTools\Command;

use ErrorException;
use OpenApi\Generator;
use OrangeHRM\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateOpenApiDocCommand extends Command
{
    protected static $defaultName = 'generate-open-api-doc';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addOption('throw', null, InputOption::VALUE_NONE, 'Convert warnings to errors');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $command = $this->getApplication()->find('php-cs-fix');
        $command->run(new ArrayInput([]), $output);

        $paths = [Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/Controller/Rest/V2'];
        foreach (Config::get(Config::PLUGIN_PATHS) as $pluginAbsPath) {
            $pathToApiDir = realpath($pluginAbsPath . '/Api');
            if ($pathToApiDir !== false) {
                $paths[] = $pathToApiDir;
            }
        }

        if ($input->getOption('throw') === true) {
            set_error_handler(static function ($severity, $message, $file, $line) use ($io) {
                $io->error($message);
                throw new ErrorException($message, 0, $severity, $file, $line);
            });
        }

        $openapi = Generator::scan($paths);
        $buildDir = Config::get(Config::BASE_DIR) . '/build';
        $filePath = $buildDir . '/orangehrm-v2.json';

        $openApiDefinition = $openapi->toJson(
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
        );

        file_put_contents($filePath, $openApiDefinition);
        $io->success("File write to `$filePath`");

        $swaggerUI = file_get_contents(__DIR__ . '/swagger.tpl.html');

        $pos = strpos($openApiDefinition, '3.1.0'); // TODO::remove this
        if ($pos !== false) {
            $openApiDefinition = substr_replace($openApiDefinition, '3.0.3', $pos, strlen('3.1.0'));
        }
        $swaggerUI = str_replace('OPEN_API_DEFINITION', $openApiDefinition, $swaggerUI);
        $uiFilePath = $buildDir . '/index.html';
        file_put_contents($uiFilePath, $swaggerUI);

        $io->success("File write to `$uiFilePath`");
        return Command::SUCCESS;
    }
}
