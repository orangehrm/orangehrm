<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Installer\Command;

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Utility\PasswordStrengthValidation;
use OrangeHRM\Installer\Exception\InvalidArgumentException;
use OrangeHRM\Installer\Util\Service\InstallerPasswordStrengthService;
use OrangeHRM\Installer\Util\SystemCheck;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

trait InstallerCommandHelperTrait
{
    /**
     * @param string $label
     * @param callable|null $validator
     * @return string
     */
    private function getRequiredField(string $label, ?callable $validator = null): string
    {
        $question = new Question($label . ' ' . self::REQUIRED_TAG);
        $question->setNormalizer(static function ($value) {
            return $value !== null ? trim($value) : null;
        });
        $question->setValidator(function ($answer) use ($validator) {
            $answer = $this->requiredValidator($answer);
            if ($validator === null) {
                return $answer;
            }
            return $validator($answer);
        });
        return $this->getIO()->askQuestion($question);
    }

    /**
     * @param string|null $answer
     * @return string|null
     */
    private function requiredValidator(?string $answer): ?string
    {
        if ($answer === null || strlen($answer) === 0) {
            throw new InvalidArgumentException(self::REQUIRED_WARNING);
        }
        return $answer;
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    private function databasePortValidator(?string $value): ?string
    {
        if (($value === null || strlen(trim($value)) === 0)
            || (is_numeric($value) && (int)$value >= 0 && (int)$value <= 65535)) {
            return $value;
        }
        throw new InvalidArgumentException('Enter a valid port number: 0 - 65535');
    }

    /**
     * @param string|null $value
     * @param string $message
     * @return string|null
     */
    private function alphanumericValidator(?string $value, string $message): ?string
    {
        if (preg_match('/^[a-zA-Z0-9_]*$/', $value) === 1) {
            return $value;
        }
        throw new InvalidArgumentException($message);
    }

    /**
     * @param string|null $value
     * @param string $message
     * @return string|null
     */
    private function emailValidator(?string $value, string $message): ?string
    {
        $match = preg_match(
            "/^[a-zA-Z0-9.!#$%&'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/",
            $value
        );
        if ($match === 1) {
            return $value;
        }
        throw new InvalidArgumentException($message);
    }

    /**
     * @param string|null $value
     * @param string $message
     * @return string|null
     */
    private function phoneNumberValidator(?string $value, string $message): ?string
    {
        if (preg_match('/^[0-9+\-\/() ]+$/', $value) === 1) {
            return $value;
        }
        throw new InvalidArgumentException($message);
    }

    /**
     * @param InputInterface $input
     * @param string $name
     * @return bool
     */
    private function hasOption(InputInterface $input, string $name): bool
    {
        $value = $input->getOption($name);
        if ($value == null) {
            return false;
        }
        return strlen(trim($value)) > 0;
    }

    /**
     * @param array $systemCheckResults
     */
    private function drawSystemCheckTable(array $systemCheckResults): void
    {
        $this->getIO()->title('System Check');
        $this->getIO()->block(
            'In order for your OrangeHRM installation to function properly, please ensure that all of the system check items listed below are green. If any are red, please take the necessary steps to fix them.'
        );
        foreach ($systemCheckResults as $category) {
            $rows = [];
            foreach ($category['checks'] as $check) {
                switch ($check['value']['status']) {
                    case SystemCheck::PASSED:
                        $style = '<fg=black;bg=green>%s</>';
                        break;
                    case SystemCheck::BLOCKER:
                        $style = '<fg=white;bg=red>%s</>';
                        break;
                    case SystemCheck::ACCEPTABLE:
                        $style = '<fg=black;bg=yellow>%s</>';
                        break;
                    default:
                        $style = '<fg=default;bg=default>%s</>';
                }
                $status = sprintf($style, $check['value']['message']);
                $rows[] = [$check['label'], $status];
            }
            $this->getIO()->table([$category['category']], $rows);
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $step
     * @param string $suffix
     * @return ConsoleSectionOutput
     */
    private function startSection(OutputInterface $output, string $step, string $suffix = ''): ConsoleSectionOutput
    {
        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->writeln("* $step$suffix");
        return $section;
    }

    /**
     * @param ConsoleSectionOutput $section
     * @param string $step
     * @param string $suffix
     */
    private function startStep(ConsoleSectionOutput $section, string $step, string $suffix = ''): void
    {
        $section->overwrite("* <comment>$step</comment>$suffix");
    }

    /**
     * @param ConsoleSectionOutput $section
     * @param string $step
     * @param string $suffix
     */
    private function completeStep(ConsoleSectionOutput $section, string $step, string $suffix = ''): void
    {
        $section->overwrite("<fg=green>* $step ✓</>$suffix");
    }

    /**
     * @param string|null $text
     * @param int $length
     * @return string|null
     */
    private function validateStrLength(?string $text, int $length): ?string
    {
        if ($text === null || mb_strlen($text) <= $length) {
            return $text;
        }
        throw InvalidArgumentException::shouldNotExceedCharacters($length);
    }

    /**
     * @param string $value
     * @return string
     * @throws InvalidArgumentException
     */
    private function validatePassword(string $value): string
    {
        $passwordStrengthValidation = new PasswordStrengthValidation();
        $passwordStrengthService = new InstallerPasswordStrengthService();

        $credential = new UserCredential('', $value);

        $passwordStrength = $passwordStrengthValidation->checkPasswordStrength($credential);
        $messages = $passwordStrengthService->checkPasswordPolicies($credential, $passwordStrength);

        if (count($messages) > 0) {
            throw new InvalidArgumentException($messages[0]);
        }

        return $value;
    }
}
