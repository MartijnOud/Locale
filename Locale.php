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

    // Set with functions
    private $locale = null;
    private $source = null;
    private $file = null;

    public function __construct($locale)
    {
        $this->setLocale($locale);
        $this->setSource($locale);
        $this->setFile($this->source);
    }

    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);
    }

    public function setSource($locale)
    {
        $this->source = __DIR__.'/locale'.'/'.$locale.'/'.$locale.'.csv';
        $this->setFile($this->source);
    }

    public function setFile($source)
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

    /**
     * Main translate function
     * @param  string $input     [description]
     * @param  array  $variables [description]
     * @return [type]            [description]
     */
    public function __($input, $variables = null)
    {

        // Loop through file
        while (!feof($this->file)) {

            // Get every line in file
            $line = fgets($this->file);
            list($source, $translation) = explode($this->seperator, $line);

            // Check if translation is present
            if ($source == $input AND !empty($translation)) {

                // Replace variable
                if (!empty($variables)) {
                    foreach ($variables AS $k => $v) {
                        $translation = str_replace($k, $v, $translation);
                    }
                }

                return trim($translation);
            }

            $translation = null; // Reset
        }

        if ($this->debug === true) {
            throw new \Exception('No translation found for '.$input.' with locale '.$this->locale.'');
        }

        return $input;

    }
}