<?php

/**
 * Copyright (c) 2014 Pavel Kučera (http://github.com/pavelkucera)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace Kucera\Monolog\Handler;

use Monolog\Logger;


class BlueScreenHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    /** @var \Tracy\BlueScreen */
    private $blueScreen;

    /** @var string */
    private $logDirectory;


    /**
     * @param \Tracy\BlueScreen $blueScreen
     * @param bool $logDirectory
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(\Tracy\BlueScreen $blueScreen, $logDirectory, $level = Logger::DEBUG, $bubble = TRUE)
    {
        $logDirectory = realpath($logDirectory);
        if ($logDirectory === FALSE) {
            throw new \RuntimeException('Log directory not found or is not a directory.');
        }

        $this->blueScreen = $blueScreen;
        $this->logDirectory = $logDirectory;
        parent::__construct($level, $bubble);
    }


    /**
     * @param array $record
     */
    protected function write(array $record)
    {
        if (!isset($record['context']['exception']) || !$record['context']['exception'] instanceof \Exception) {
            return;
        }
        $exception = $record['context']['exception'];

        $datetime = @$record['datetime']->format('Y-m-d-H-i-s');
        $hash = $this->getExceptionHash($exception);
        $filename = "exception-$datetime-$hash.html";

        $save = TRUE;
        foreach (new \DirectoryIterator($this->logDirectory) as $entry) {
            // Exception already logged
            if (strpos($entry, $hash)) {
                $filename = $entry;
                $save = FALSE;
                break;
            }
        }

        if ($save === TRUE) {
            $this->save($filename, $exception);
        }
    }


    /**
     * @param \Exception $exception
     * @return string
     */
    public function getExceptionHash(\Exception $exception)
    {
        return md5(preg_replace('~(Resource id #)\d+~', '$1', $exception));
    }


    /**
     * @param string $filename
     * @param \Exception $exception
     */
    private function save($filename, \Exception $exception)
    {
        $path = $this->logDirectory . "/$filename";
        if ($logHandle = @fopen($path, 'w')) {
            ob_start(); // double buffer prevents sending HTTP headers in some PHP
            ob_start(function($buffer) use ($logHandle) { fwrite($logHandle, $buffer); }, 4096);
            $this->blueScreen->render($exception);
            ob_end_flush();
            ob_end_clean();
            fclose($logHandle);
        }
    }


    /**
     * @return string
     */
    public function getLogDirectory()
    {
        return $this->logDirectory;
    }
}
