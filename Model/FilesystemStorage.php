<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Model;

use DebugBar\Storage\StorageInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Stores collected data into files
 */
class FilesystemStorage implements StorageInterface
{
    protected $gc_lifetime = 24;     // Hours to keep collected data;
    protected $gc_probability = 5;   // Probability of GC being run on a save request. (5/100)
    protected $data_dir = 'phpdebuger';
    protected $var_dir;

    public function __construct(Filesystem $filesystem)
    {
        $this->var_dir = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->var_dir->create('phpdebuger');
    }

    /**
     * {@inheritDoc}
     */
    public function save($id, $data)
    {
        try {
            $this->var_dir->writeFile($this->makeFilename($id), json_encode($data));
        } catch (\Exception $e) {
            //TODO; error handling
        }

        // Randomly check if we should collect old files
        if (rand(1, 100) <= $this->gc_probability) {
            $this->garbageCollect();
        }
    }

    /**
     * Create the filename for the data, based on the id.
     *
     * @param $id
     * @return string
     */
    public function makeFilename($id)
    {
        return implode(DIRECTORY_SEPARATOR, [$this->data_dir, basename($id) . '.json']);
    }

    /**
     * Delete files older then a certain age (gc_lifetime)
     */
    protected function garbageCollect()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        return json_decode($this->var_dir->readFile($this->makeFilename($id)), true);
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $filters = array(), $max = 20, $offset = 0)
    {
       
    }

    /**
     * Filter the metadata for matches.
     *
     * @param $meta
     * @param $filters
     * @return bool
     */
    protected function filter($meta, $filters)
    {
      
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
       
    }
}
