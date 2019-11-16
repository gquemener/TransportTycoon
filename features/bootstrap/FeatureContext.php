<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use App\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;

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

        ;
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
}
