<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use App\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use SebastianBergmann\Diff\Differ;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $app;

    public function __construct()
    {
        $this->app = new Application('test');
        $this->app->setAutoExit(false);
        $this->output = new BufferedOutput();


        $this->baseLogDir = tempnam(sys_get_temp_dir(),'behat_tt_');
        if (is_file($this->baseLogDir)) {
            unlink($this->baseLogDir);
        }
        mkdir($this->baseLogDir);
    }

    /**
     * @When I run the time to deliver command with the list of cargos :cargos
     */
    public function iRunTheTimeToDeliverCommandWithTheListOfCargos(string $cargos)
    {
        $input = new ArrayInput([
            'command' => 'time-to-deliver',
            'cargos' => $cargos,
        ]);

        $this->runTimeToDeliverCommand($input);
    }

    /**
     * @When I run the time to deliver command with the list of cargos :cargos with the dump option
     */
    public function iRunTheTimeToDeliverCommandWithTheListOfCargosWithTheDumpOption(string $cargos)
    {
        $input = new ArrayInput([
            'command' => 'time-to-deliver',
            'cargos' => $cargos,
            '--dump-log' => $this->baseLogDir,
        ]);

        $this->runTimeToDeliverCommand($input);
    }

    private function runTimeToDeliverCommand(InputInterface $input)
    {
        if (0 !== $exitCode = $this->app->run($input, $this->output)) {
            throw new \RuntimeException(sprintf(
                'Command stopped with non-zero exit code "%d".' . "\nOutput:\n%s",
                $exitCode,
                $this->output->fetch()
            ));
        }
    }

    /**
     * @Then I should read the result :result
     */
    public function iShouldReadTheResult(string $result)
    {
        $output = $this->output->fetch();
        if (rtrim($output, "\n") !== $result) {
            throw new \RuntimeException(sprintf(
                'Expected to see result "%s", but saw:' . "\n%s",
                $result,
                0 === strlen($output) ? '<empty>' : $output
            ));
        }
    }

    /**
     * @Then the dumped file :filename should contain:
     */
    public function theDumpedFileShouldContain(string $filename, PyStringNode $content)
    {
        $path = $this->baseLogDir . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path)) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" does not exist',
                $path
            ));
        }

        $actual = file_get_contents($path);
        if ($actual !== (string) $content) {
            $differ = (new Differ())->diff($actual, (string) $content);

            throw new \RuntimeException($differ);
        }
    }
}
