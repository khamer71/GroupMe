<?php
namespace BlueHerons\GroupMe\Bots;

abstract class CommandBot extends BaseBot {

    const COMMAND_CHAR = "! ";

    public function __construct($token) {
        parent::__construct($token);
        $this->registerCommand("help", array($this, "listCommands"));
    }

    private $commands = array();

    public function listen() {
        if (!$this->isSystemMessage() && $this->isCommandMessage()) {
            $command = $this->getCommand();
            echo $command;
            if ($this->isRegisteredCommand($command)) {
                $this->sendMessage($this->executeCommand($command));
                //echo $this->executeCommand($command);
            }
        }
    }

    private function listCommands() {
        asort($this->commands);
        $commands = array();
        foreach ($this->commands as $cmd => $c) {
            $commands[] = $cmd;
        }

        return "The following commands are available:\n\n" . implode("\n", $commands);
    }

    public function registerCommand($command, $function, $params = array()) {
        $this->commands[$command] = array($function, $params);
    }

    public function executeCommand($command, $params = array()) {
        return call_user_func_array($this->commands[$command][0], $params);
    }

    protected function getCommand() {
        $cmd = str_replace(self::COMMAND_CHAR, "", $this->getInput()['text']);
        $cmd = str_replace(self::COMMAND_CHAR, "", explode(" ", $cmd)[0]);
        return strtolower($cmd);
    }

    protected function isRegisteredCommand($command) {
        return array_key_exists($command, $this->commands);
    }

    protected function isCommandMessage() {
        return self::COMMAND_CHAR === "" || strrpos($this->getInput()['text'], self::COMMAND_CHAR, -strlen($this->getInput()['text'])) !== FALSE;
    }
}
?>
