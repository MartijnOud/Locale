<?php
/**
 * Translation functions (or the start of one...)
 * @version Alpha - No version numbering yet
 */

namespace MartijnOud;

class Locale
{

    // The seperator for the locale CSVs
    private $seperator = ",";

    // set to *true* to throw exceptions if no translation found
    private $debug = false;

    // set to *true* to write non translated inputs to .log file
    private $log = true;

    // Set with functions
    private $locale = null;
    private $source = null;
    private $file = null;
    private $errorSource = null;
    private $errorFile = null;

    public function __construct($locale)
    {
        $this->setLocale($locale);
        $this->setSource($this->locale);
        $this->setFile();

        if ($this->log === true) {
            $this->setErrorSource($this->locale);
            $this->setErrorFile($this->errorSource);
        }
    }

    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);
    }

    public function setSource($locale)
    {
        $this->source = __DIR__.'/locale'.'/'.$locale.'/'.$locale.'.csv';
        $this->setFile();
    }


    public function setFile()
    {

        try {
            if (!file_exists($this->source)) {
                throw new \Exception('Translation file for locale '.$this->locale.' not found');
            }

            $fp = fopen($this->source, 'r');
            if (!$fp) {
                throw new \Exception('Translation file failed to open for locale '.$this->locale);
            }

            $this->file = $fp;
        } catch (\Exception $e) {
            echo 'Caught Exception: ',  $e->getMessage(), "\n";
            exit();
        }

    }

    public function setErrorSource($locale)
    {
        $this->errorSource = __DIR__.'/locale'.'/'.$locale.'/errors.log';
        $this->setErrorFile();
    }

    public function setErrorFile()
    {
        try {
            // Open or create .log file
            $fp = fopen($this->errorSource, 'a');
            if (!$fp) {
                throw new \Exception('Error file failed to create/open for locale '.$this->locale);
            }

            $this->errorFile = $fp;
        } catch (\Exception $e) {
            echo 'Caught Exception: ',  $e->getMessage(), "\n";
            exit();
        }
    }

    private function logError($input)
    {

        $this->setErrorFile();

        // loop through log file
        while (!feof($this->errorFile)) {

            // Get every line in file
            $line = fgets($this->errorFile);
            $line = trim($line);

            // input with double quotes
            $line_error = '"'.$input.'"';

            if ($line == $line_error) {
                $found = 1;
            }

        }

        // write to .log if not found
        if ($found != 1) {
            fwrite($this->errorFile, $line_error."\n");
        }

        return true;
    }

    /**
     * Main translate function
     * @param  string $input
     * @param  array  $variables Key/Value pairs to be replaced
     * @return string $ouput OR $input
     */
    public function __($input, $variables = null)
    {

        $this->setFile();

        // Loop through file
        while (!feof($this->file)) {

            // Get every line in file
            $line = fgets($this->file);
            $line = trim($line);

            // # Skip comments
            if (substr($line, 0, 1) == "#") {
                continue;
            }

            list($source, $translation) = explode($this->seperator, $line);

            // Check if translation is present
            if ($source == $input AND !empty($translation)) {

                // Replace variable
                if (!empty($variables)) {
                    foreach ($variables AS $k => $v) {
                        $translation = str_replace($k, $v, $translation);
                    }
                }

                return $translation;
            }

             // Reset
            $translation = null;
            $source = null;
        }

        if ($this->log === true) {
            $this->logError($input);
        }

        if ($this->debug === true) {
            throw new \Exception('No translation found for '.$input.' with locale '.$this->locale.'');
        }

        return $input;

    }

}